<?php
class AdminProductsController extends AdminController
{
    public function index(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $cat = (int)($_GET['category'] ?? 0);
        $where = ['1=1'];
        $params = [];
        if ($q !== '') {
            $where[] = '(p.name LIKE ? OR p.sku LIKE ?)';
            $params[] = "%$q%";
            $params[] = "%$q%";
        }
        if ($cat) { $where[] = 'p.category_id=?'; $params[] = $cat; }
        $rows = Database::all(
            'SELECT p.*, c.name AS category_name, b.name AS brand_name,
              (SELECT path FROM product_images WHERE product_id=p.id ORDER BY sort_order, id LIMIT 1) AS image,
              (SELECT COUNT(*) FROM product_variants v WHERE v.product_id=p.id) AS variant_count,
              (SELECT COALESCE(SUM(stock),0) FROM product_variants v WHERE v.product_id=p.id AND v.is_active=1) AS variant_stock
             FROM products p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN brands b ON b.id=p.brand_id
             WHERE ' . implode(' AND ', $where) . ' ORDER BY p.id DESC LIMIT 500', $params);
        $this->view('admin/products_index', [
            'title' => 'Products', 'rows' => $rows, 'q' => $q, 'cat' => $cat,
            'categories' => Database::all('SELECT id, name FROM categories ORDER BY name'),
        ]);
    }

    public function form(?string $id = null): void
    {
        $p = null; $images = []; $variants = [];
        if ($id !== null) {
            $p = Database::one('SELECT * FROM products WHERE id=?', [(int)$id]);
            if (!$p) { flash('danger', 'Product not found.'); redirect('admin/products'); }
            $images = Database::all('SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order, id', [$p['id']]);
            $variants = Database::all('SELECT * FROM product_variants WHERE product_id=? ORDER BY sort_order, id', [$p['id']]);
        }
        $this->view('admin/product_form', [
            'title' => $p ? 'Edit product' : 'New product', 'p' => $p, 'images' => $images, 'variants' => $variants,
            'categories' => Database::all('SELECT id, name FROM categories ORDER BY sort_order, name'),
            'brands' => Database::all('SELECT id, name FROM brands ORDER BY sort_order, name'),
        ]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $existing = $id ? Database::one('SELECT * FROM products WHERE id=?', [$id]) : null;
        $name = trim((string)($_POST['name'] ?? ''));
        $price = $_POST['price'] ?? '';
        $errors = [];
        if ($name === '') $errors[] = 'Product name is required.';
        if (!is_numeric($price) || (float)$price < 0) $errors[] = 'Please enter a valid price.';
        $compare = ($_POST['compare_price'] ?? '') !== '' ? $_POST['compare_price'] : null;
        if ($compare !== null && !is_numeric($compare)) $errors[] = 'Compare-at price must be a number.';
        if ($errors) {
            foreach ($errors as $e) flash('danger', $e);
            keep_old($_POST);
            redirect($id ? "admin/products/$id/edit" : 'admin/products/create');
        }

        $nz = fn($k) => (($v = trim((string)($_POST[$k] ?? ''))) === '') ? null : $v;
        $data = [
            'category_id' => (int)($_POST['category_id'] ?? 0) ?: null,
            'brand_id' => (int)($_POST['brand_id'] ?? 0) ?: null,
            'name' => $name,
            'slug' => unique_slug('products', trim((string)($_POST['slug'] ?? '')) ?: $name, $id),
            'sku' => $nz('sku'),
            'short_description' => $nz('short_description'),
            'description' => clean_html((string)($_POST['description'] ?? '')),
            'price' => round((float)$price, 2),
            'compare_price' => $compare !== null ? round((float)$compare, 2) : null,
            'puffs' => ($_POST['puffs'] ?? '') !== '' ? max(0, (int)$_POST['puffs']) : null,
            'nicotine' => $nz('nicotine'),
            'battery' => $nz('battery'),
            'capacity' => $nz('capacity'),
            'coil' => $nz('coil'),
            'specs_extra' => $nz('specs_extra'),
            'stock' => max(0, (int)($_POST['stock'] ?? 0)),
            'is_featured' => $this->checkbox('is_featured'),
            'is_bestseller' => $this->checkbox('is_bestseller'),
            'is_new' => $this->checkbox('is_new'),
            'is_active' => $this->checkbox('is_active'),
            'meta_title' => $nz('meta_title'),
            'meta_description' => $nz('meta_description'),
        ];
        // Only one featured "spotlight" product at a time
        if ($data['is_featured']) {
            Database::exec('UPDATE products SET is_featured=0 WHERE id<>?', [$id]);
        }

        try {
            Database::begin();
            if ($existing) {
                Database::update('products', $data, 'id=?', [$id]);
            } else {
                $id = Database::insert('products', $data);
            }

            // ---- Variants ----
            $vIds = $_POST['variant_id'] ?? [];
            $vNames = $_POST['variant_name'] ?? [];
            $vPrices = $_POST['variant_price'] ?? [];
            $vStocks = $_POST['variant_stock'] ?? [];
            $keep = [];
            foreach ($vNames as $i => $vn) {
                $vn = trim((string)$vn);
                if ($vn === '') continue;
                $vp = trim((string)($vPrices[$i] ?? ''));
                $row = [
                    'product_id' => $id, 'name' => mb_substr($vn, 0, 150),
                    'price' => ($vp !== '' && is_numeric($vp)) ? round((float)$vp, 2) : null,
                    'stock' => max(0, (int)($vStocks[$i] ?? 0)), 'sort_order' => $i, 'is_active' => 1,
                ];
                $vid = (int)($vIds[$i] ?? 0);
                if ($vid && Database::val('SELECT COUNT(*) FROM product_variants WHERE id=? AND product_id=?', [$vid, $id])) {
                    Database::update('product_variants', $row, 'id=?', [$vid]);
                    $keep[] = $vid;
                } else {
                    $keep[] = Database::insert('product_variants', $row);
                }
            }
            if ($keep) {
                Database::exec('DELETE FROM product_variants WHERE product_id=? AND id NOT IN (' . implode(',', array_map('intval', $keep)) . ')', [$id]);
            } else {
                Database::exec('DELETE FROM product_variants WHERE product_id=?', [$id]);
            }

            // ---- Images ----
            if (!empty($_FILES['images']['name'][0])) {
                $n = count($_FILES['images']['name']);
                $next = (int)Database::val('SELECT COALESCE(MAX(sort_order),0)+1 FROM product_images WHERE product_id=?', [$id]);
                for ($i = 0; $i < $n; $i++) {
                    $file = ['name' => $_FILES['images']['name'][$i], 'tmp_name' => $_FILES['images']['tmp_name'][$i], 'error' => $_FILES['images']['error'][$i], 'size' => $_FILES['images']['size'][$i]];
                    if ($file['error'] === UPLOAD_ERR_NO_FILE) continue;
                    $path = save_upload($file, 'products');
                    if ($path) {
                        Database::insert('product_images', ['product_id' => $id, 'path' => $path, 'alt' => $name, 'sort_order' => $next++]);
                    } else {
                        flash('warning', 'One image could not be uploaded (JPG, PNG, WEBP or GIF, max 6MB): ' . $file['name']);
                    }
                }
            }
            $main = (int)($_POST['main_image'] ?? 0);
            if ($main && Database::val('SELECT COUNT(*) FROM product_images WHERE id=? AND product_id=?', [$main, $id])) {
                Database::exec('UPDATE product_images SET sort_order = sort_order + 1 WHERE product_id=?', [$id]);
                Database::exec('UPDATE product_images SET sort_order = 0 WHERE id=?', [$main]);
            }
            Database::commit();
        } catch (Throwable $e) {
            Database::rollback();
            @file_put_contents(BASE_DIR . '/storage/php-error.log', '[' . date('c') . '] product save: ' . $e . PHP_EOL, FILE_APPEND);
            flash('danger', 'Could not save the product: ' . (str_contains($e->getMessage(), 'Duplicate') ? 'duplicate value (slug).' : 'database error.'));
            keep_old($_POST);
            redirect($existing ? "admin/products/$id/edit" : 'admin/products/create');
        }

        clear_old();
        flash('success', $existing ? 'Product updated.' : 'Product created.');
        redirect("admin/products/$id/edit");
    }

    public function deleteImage(string $imageId): void
    {
        $img = Database::one('SELECT * FROM product_images WHERE id=?', [(int)$imageId]);
        if ($img) {
            delete_upload($img['path']);
            Database::exec('DELETE FROM product_images WHERE id=?', [$img['id']]);
            flash('success', 'Image removed.');
            redirect('admin/products/' . $img['product_id'] . '/edit');
        }
        redirect('admin/products');
    }

    public function delete(string $id): void
    {
        $id = (int)$id;
        foreach (Database::all('SELECT path FROM product_images WHERE product_id=?', [$id]) as $img) {
            delete_upload($img['path']);
        }
        Database::exec('DELETE FROM products WHERE id=?', [$id]); // cascades images + variants
        flash('success', 'Product deleted.');
        redirect('admin/products');
    }
}

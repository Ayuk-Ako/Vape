<?php
class ProductController extends Controller
{
    public function show(string $slug): void
    {
        $p = Database::one('SELECT ' . Catalog::CARD_SELECT . Catalog::FROM . ' WHERE p.slug=? AND p.is_active=1', [$slug]);
        if (!$p) $this->notFound();

        Database::exec('UPDATE products SET views=views+1 WHERE id=?', [$p['id']]);

        $images = Database::all('SELECT * FROM product_images WHERE product_id=? ORDER BY sort_order, id', [$p['id']]);
        $variants = Database::all('SELECT * FROM product_variants WHERE product_id=? AND is_active=1 ORDER BY sort_order, id', [$p['id']]);

        $this->render('shop/product', [
            'title'    => ($p['meta_title'] ?: $p['name']) . ' | ' . setting('store_name', 'Store'),
            'metaDesc' => $p['meta_description'] ?: ($p['short_description'] ?: ''),
            'ogImage'  => $images ? upload_url($images[0]['path']) : null,
            'p'        => $p,
            'images'   => $images,
            'variants' => $variants,
            'related'  => Catalog::related($p, 4),
        ]);
    }
}

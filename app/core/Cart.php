<?php
/**
 * Session cart. Stores [key => ['product_id','variant_id','qty']] and always
 * re-prices from the database so prices cannot be tampered with.
 */
class Cart
{
    public static function raw(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    public static function key(int $pid, int $vid): string
    {
        return $pid . '-' . $vid;
    }

    public static function add(int $pid, int $vid, int $qty): array
    {
        $product = Database::one('SELECT * FROM products WHERE id=? AND is_active=1', [$pid]);
        if (!$product) {
            return ['ok' => false, 'msg' => 'Product not found.'];
        }
        $variants = Database::all('SELECT * FROM product_variants WHERE product_id=? AND is_active=1', [$pid]);
        $stock = (int)$product['stock'];
        if ($variants) {
            $v = null;
            foreach ($variants as $row) {
                if ((int)$row['id'] === $vid) $v = $row;
            }
            if (!$v) {
                return ['ok' => false, 'msg' => 'Please choose an option.'];
            }
            $stock = (int)$v['stock'];
        } else {
            $vid = 0;
        }
        if ($stock <= 0) {
            return ['ok' => false, 'msg' => 'Sorry, that item is out of stock.'];
        }
        $qty = max(1, min(20, $qty));
        $k = self::key($pid, $vid);
        $existing = $_SESSION['cart'][$k]['qty'] ?? 0;
        $newQty = min($existing + $qty, $stock, 20);
        $_SESSION['cart'][$k] = ['product_id' => $pid, 'variant_id' => $vid, 'qty' => $newQty];
        $msg = $newQty < $existing + $qty ? 'Added (limited to available stock).' : 'Added to cart.';
        return ['ok' => true, 'msg' => $msg];
    }

    public static function setQty(string $key, int $qty): void
    {
        if (!isset($_SESSION['cart'][$key])) return;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$key]);
            return;
        }
        $_SESSION['cart'][$key]['qty'] = min($qty, 20);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION['cart'][$key]);
    }

    public static function clear(): void
    {
        unset($_SESSION['cart']);
    }

    public static function count(): int
    {
        $n = 0;
        foreach (self::raw() as $l) $n += (int)$l['qty'];
        return $n;
    }

    /** Fully priced cart lines + totals. Silently drops unavailable lines and clamps qty to stock. */
    public static function items(): array
    {
        $lines = [];
        $subtotal = 0.0;
        foreach (self::raw() as $key => $l) {
            $p = Database::one('SELECT p.*, (SELECT path FROM product_images WHERE product_id=p.id ORDER BY sort_order, id LIMIT 1) AS image FROM products p WHERE p.id=? AND p.is_active=1', [(int)$l['product_id']]);
            if (!$p) { unset($_SESSION['cart'][$key]); continue; }
            $price = (float)$p['price'];
            $stock = (int)$p['stock'];
            $vname = null;
            if ((int)$l['variant_id'] > 0) {
                $v = Database::one('SELECT * FROM product_variants WHERE id=? AND product_id=? AND is_active=1', [(int)$l['variant_id'], (int)$p['id']]);
                if (!$v) { unset($_SESSION['cart'][$key]); continue; }
                $vname = $v['name'];
                $stock = (int)$v['stock'];
                if ($v['price'] !== null && (float)$v['price'] > 0) $price = (float)$v['price'];
            }
            if ($stock <= 0) { unset($_SESSION['cart'][$key]); continue; }
            $qty = min((int)$l['qty'], $stock, 20);
            $_SESSION['cart'][$key]['qty'] = $qty;
            $total = round($price * $qty, 2);
            $subtotal += $total;
            $lines[] = [
                'key' => $key, 'product_id' => (int)$p['id'], 'variant_id' => (int)$l['variant_id'],
                'name' => $p['name'], 'slug' => $p['slug'], 'variant' => $vname, 'image' => $p['image'],
                'price' => $price, 'qty' => $qty, 'stock' => $stock, 'total' => $total,
            ];
        }
        return ['lines' => $lines, 'subtotal' => round($subtotal, 2)];
    }

    public static function totals(?array $items = null): array
    {
        $items = $items ?? self::items();
        $sub = $items['subtotal'];
        $flat = (float)setting('shipping_flat', '9.99');
        $free = (float)setting('free_shipping_over', '100');
        $shipping = ($sub <= 0) ? 0.0 : (($free > 0 && $sub >= $free) ? 0.0 : $flat);
        $taxRate = (float)setting('tax_rate', '0');
        $tax = round($sub * $taxRate / 100, 2);
        return [
            'subtotal' => $sub,
            'shipping' => round($shipping, 2),
            'tax' => $tax,
            'total' => round($sub + $shipping + $tax, 2),
        ];
    }
}

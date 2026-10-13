<?php
/**
 * Product listing / lookup queries shared by the storefront.
 */
class Catalog
{
    public const CARD_SELECT = "p.*, c.name AS category_name, c.slug AS category_slug, b.name AS brand_name, b.slug AS brand_slug,
        (SELECT path FROM product_images WHERE product_id=p.id ORDER BY sort_order, id LIMIT 1) AS image,
        (SELECT path FROM product_images WHERE product_id=p.id ORDER BY sort_order, id LIMIT 1 OFFSET 1) AS image2,
        (SELECT COUNT(*) FROM product_variants v WHERE v.product_id=p.id AND v.is_active=1) AS variant_count,
        (SELECT COALESCE(SUM(v.stock),0) FROM product_variants v WHERE v.product_id=p.id AND v.is_active=1) AS variant_stock";

    public const FROM = " FROM products p LEFT JOIN categories c ON c.id=p.category_id LEFT JOIN brands b ON b.id=p.brand_id ";

    public static function puffRanges(): array
    {
        return [
            'u2000'  => ['Below 2,000 puffs', 0, 1999],
            '2k-5k'  => ['2,000 - 5,999 puffs', 2000, 5999],
            '6k-9k'  => ['6,000 - 9,999 puffs', 6000, 9999],
            '10k'    => ['10,000+ puffs', 10000, 9999999],
        ];
    }

    public static function sorts(): array
    {
        return [
            'newest'     => ['Newest', 'p.id DESC'],
            'popular'    => ['Most popular', 'p.views DESC, p.id DESC'],
            'price_asc'  => ['Price: low to high', 'p.price ASC'],
            'price_desc' => ['Price: high to low', 'p.price DESC'],
            'name'       => ['Name A-Z', 'p.name ASC'],
        ];
    }

    /** @return array{items:array,total:int,pages:int,page:int} */
    public static function search(array $f, int $page = 1, int $perPage = 12): array
    {
        $where = ['p.is_active=1'];
        $params = [];

        if (!empty($f['category'])) { $where[] = 'c.slug=?'; $params[] = $f['category']; }
        if (!empty($f['brand'])) { $where[] = 'b.slug=?'; $params[] = $f['brand']; }
        if (!empty($f['q'])) {
            $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $f['q']) . '%';
            $where[] = '(p.name LIKE ? OR b.name LIKE ? OR c.name LIKE ? OR p.sku LIKE ? OR p.short_description LIKE ?)';
            array_push($params, $like, $like, $like, $like, $like);
        }
        if (isset($f['min']) && $f['min'] !== '' && is_numeric($f['min'])) { $where[] = 'p.price>=?'; $params[] = (float)$f['min']; }
        if (isset($f['max']) && $f['max'] !== '' && is_numeric($f['max'])) { $where[] = 'p.price<=?'; $params[] = (float)$f['max']; }
        if (!empty($f['puffs']) && isset(self::puffRanges()[$f['puffs']])) {
            [, $lo, $hi] = self::puffRanges()[$f['puffs']];
            $where[] = 'p.puffs BETWEEN ? AND ?';
            array_push($params, $lo, $hi);
        }
        if (!empty($f['collection'])) {
            if ($f['collection'] === 'best-selling') $where[] = 'p.is_bestseller=1';
            elseif ($f['collection'] === 'new-arrivals') $where[] = 'p.is_new=1';
            elseif ($f['collection'] === 'deals') $where[] = 'p.compare_price IS NOT NULL AND p.compare_price>p.price';
        }
        if (!empty($f['in_stock'])) {
            $where[] = '(p.stock>0 OR EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.is_active=1 AND v.stock>0))';
        }

        $sortKey = $f['sort'] ?? 'newest';
        $order = self::sorts()[$sortKey][1] ?? self::sorts()['newest'][1];
        $w = implode(' AND ', $where);

        $total = (int)Database::val('SELECT COUNT(*) ' . self::FROM . ' WHERE ' . $w, $params);
        $pages = max(1, (int)ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;
        $items = Database::all('SELECT ' . self::CARD_SELECT . self::FROM . ' WHERE ' . $w . ' ORDER BY ' . $order . ' LIMIT ' . (int)$perPage . ' OFFSET ' . (int)$offset, $params);

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public static function flag(string $column, int $limit = 10): array
    {
        $allowed = ['is_bestseller', 'is_new', 'is_featured'];
        if (!in_array($column, $allowed, true)) return [];
        return Database::all('SELECT ' . self::CARD_SELECT . self::FROM . ' WHERE p.is_active=1 AND p.' . $column . '=1 ORDER BY p.id DESC LIMIT ' . (int)$limit);
    }

    public static function related(array $p, int $limit = 4): array
    {
        return Database::all(
            'SELECT ' . self::CARD_SELECT . self::FROM . ' WHERE p.is_active=1 AND p.id<>? AND (p.category_id<=>? OR p.brand_id<=>?) ORDER BY RAND() LIMIT ' . (int)$limit,
            [$p['id'], $p['category_id'], $p['brand_id']]
        );
    }

    public static function inStock(array $p): bool
    {
        if ((int)($p['variant_count'] ?? 0) > 0) {
            return (int)$p['variant_stock'] > 0;
        }
        return (int)$p['stock'] > 0;
    }

    public static function categories(): array
    {
        return Database::all('SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id=c.id AND p.is_active=1) AS product_count FROM categories c WHERE c.is_active=1 ORDER BY c.sort_order, c.name');
    }

    public static function brands(): array
    {
        return Database::all('SELECT b.*, (SELECT COUNT(*) FROM products p WHERE p.brand_id=b.id AND p.is_active=1) AS product_count FROM brands b WHERE b.is_active=1 ORDER BY b.sort_order, b.name');
    }
}

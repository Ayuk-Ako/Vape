<?php
class ShopController extends Controller
{
    private function listing(array $fixed, string $heading, string $intro = '', ?string $metaDesc = null): void
    {
        $filters = [
            'q'       => trim((string)($_GET['q'] ?? '')),
            'category'=> $fixed['category'] ?? trim((string)($_GET['category'] ?? '')),
            'brand'   => $fixed['brand'] ?? trim((string)($_GET['brand'] ?? '')),
            'collection' => $fixed['collection'] ?? '',
            'min'     => $_GET['min'] ?? '',
            'max'     => $_GET['max'] ?? '',
            'puffs'   => trim((string)($_GET['puffs'] ?? '')),
            'sort'    => trim((string)($_GET['sort'] ?? 'newest')),
            'in_stock'=> !empty($_GET['in_stock']),
        ];
        $page = (int)($_GET['page'] ?? 1);
        $result = Catalog::search($filters, $page, 12);

        $this->render('shop/listing', [
            'title'      => $heading . ' | ' . setting('store_name', 'Store'),
            'metaDesc'   => $metaDesc ?? $intro,
            'heading'    => $heading,
            'intro'      => $intro,
            'filters'    => $filters,
            'result'     => $result,
            'categories' => Catalog::categories(),
            'brands'     => Catalog::brands(),
            'fixed'      => $fixed,
        ]);
    }

    public function index(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $this->listing([], $q !== '' ? 'Search results for "' . $q . '"' : 'All Products', 'Browse our full range of disposable vapes, pod systems, devices and refills.');
    }

    public function category(string $slug): void
    {
        $c = Database::one('SELECT * FROM categories WHERE slug=? AND is_active=1', [$slug]);
        if (!$c) $this->notFound();
        $this->listing(['category' => $slug], $c['name'], (string)$c['description']);
    }

    public function brand(string $slug): void
    {
        $b = Database::one('SELECT * FROM brands WHERE slug=? AND is_active=1', [$slug]);
        if (!$b) $this->notFound();
        $this->listing(['brand' => $slug], $b['name'], (string)$b['description']);
    }

    public function collection(string $type): void
    {
        $map = [
            'best-selling' => ['Best Sellers', 'Our most popular products, picked by customers.'],
            'new-arrivals' => ['New Arrivals', 'The latest products to land in our store.'],
            'deals'        => ['Hot Deals', 'Limited-time pricing on selected products.'],
        ];
        if (!isset($map[$type])) $this->notFound();
        $this->listing(['collection' => $type], $map[$type][0], $map[$type][1]);
    }
}

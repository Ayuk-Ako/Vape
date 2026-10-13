<?php
class App
{
    private array $routes = [];

    public function __construct(private array $config)
    {
        $this->registerRoutes();
    }

    private function add(string $method, string $pattern, string $handler): void
    {
        $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '/?$#';
        $this->routes[] = [$method, $regex, $handler];
    }

    private function registerRoutes(): void
    {
        $g = fn($p, $h) => $this->add('GET', $p, $h);
        $p = fn($p, $h) => $this->add('POST', $p, $h);

        /* ----- Storefront ----- */
        $g('/', 'Home@index');
        $g('/shop', 'Shop@index');
        $g('/category/{slug}', 'Shop@category');
        $g('/brand/{slug}', 'Shop@brand');
        $g('/collection/{type}', 'Shop@collection');
        $g('/product/{slug}', 'Product@show');
        $g('/cart', 'CartPage@index');
        $p('/cart/add', 'CartPage@add');
        $p('/cart/update', 'CartPage@update');
        $p('/cart/remove', 'CartPage@remove');
        $g('/checkout', 'Checkout@index');
        $p('/checkout', 'Checkout@place');
        $g('/order/success/{number}', 'Checkout@success');
        $g('/track', 'Track@index');
        $p('/track', 'Track@lookup');
        $g('/blog', 'Blog@index');
        $g('/blog/{slug}', 'Blog@show');
        $g('/faq', 'Content@faq');
        $g('/contact', 'Content@contact');
        $p('/contact', 'Content@contactSend');
        $p('/subscribe', 'Content@subscribe');
        $g('/page/{slug}', 'Content@page');
        $g('/sitemap.xml', 'Content@sitemap');
        $g('/robots.txt', 'Content@robots');

        /* ----- Admin ----- */
        $g('/admin', 'AdminDashboard@index');
        $g('/admin/login', 'AdminAuth@loginForm');
        $p('/admin/login', 'AdminAuth@login');
        $g('/admin/logout', 'AdminAuth@logout');
        $g('/admin/account', 'AdminAuth@account');
        $p('/admin/account', 'AdminAuth@accountSave');

        foreach (['products' => 'AdminProducts', 'categories' => 'AdminCategories', 'brands' => 'AdminBrands',
                  'sliders' => 'AdminSliders', 'posts' => 'AdminPosts', 'pages' => 'AdminPages', 'faqs' => 'AdminFaqs'] as $path => $ctl) {
            $g("/admin/$path", "$ctl@index");
            $g("/admin/$path/create", "$ctl@form");
            $g("/admin/$path/{id}/edit", "$ctl@form");
            $p("/admin/$path/save", "$ctl@save");
            $p("/admin/$path/{id}/delete", "$ctl@delete");
        }
        $p('/admin/products/image/{id}/delete', 'AdminProducts@deleteImage');

        $g('/admin/orders', 'AdminOrders@index');
        $g('/admin/orders/{id}', 'AdminOrders@show');
        $p('/admin/orders/{id}/update', 'AdminOrders@update');
        $p('/admin/orders/{id}/delete', 'AdminOrders@delete');

        $g('/admin/messages', 'AdminMessages@index');
        $g('/admin/messages/{id}', 'AdminMessages@show');
        $p('/admin/messages/{id}/delete', 'AdminMessages@delete');
        $g('/admin/subscribers', 'AdminMessages@subscribers');
        $g('/admin/subscribers/export', 'AdminMessages@exportSubscribers');

        $g('/admin/settings', 'AdminSettings@index');
        $p('/admin/settings', 'AdminSettings@save');
        $p('/admin/settings/test-mail', 'AdminSettings@testMail');
    }

    public function run(): void
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $base = base_path();
        if ($base !== '' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim(rawurldecode($path), '/');
        if ($path === '/index.php') $path = '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        foreach ($this->routes as [$m, $regex, $handler]) {
            if ($m !== $method || !preg_match($regex, $path, $mm)) {
                continue;
            }
            $params = array_filter($mm, 'is_string', ARRAY_FILTER_USE_KEY);
            [$ctlName, $action] = explode('@', $handler);
            $file = $this->controllerFile($ctlName);
            require_once $file;
            $class = $ctlName . 'Controller';
            if ($method === 'POST') {
                csrf_check();
            }
            $ctl = new $class();
            call_user_func_array([$ctl, $action], array_values($params));
            return;
        }
        http_response_code(404);
        (new Controller())->renderNotFound();
    }

    private function controllerFile(string $name): string
    {
        $admin = BASE_DIR . '/app/controllers/admin/' . $name . 'Controller.php';
        if (str_starts_with($name, 'Admin') && is_file($admin)) {
            return $admin;
        }
        return BASE_DIR . '/app/controllers/' . $name . 'Controller.php';
    }
}

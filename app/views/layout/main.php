<?php
$storeName = setting('store_name', 'Vapor Haus');
$minAge = (int)setting('min_age', '21');
$ageOk = !empty($_COOKIE['vh_age']);
$tg = ltrim((string)setting('telegram_username', ''), '@');
$wa = preg_replace('/\D/', '', (string)setting('whatsapp_number', ''));
$footerPages = Database::all('SELECT title, slug FROM pages WHERE is_active=1 AND show_in_footer=1 ORDER BY sort_order, id');
$navCategories = Catalog::categories();
$navBrands = Catalog::brands();
$cartCount = Cart::count();
$metaDescription = $metaDesc ?? setting('seo_description', '');
$reqPath = (string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$canonical = full_url(ltrim(substr($reqPath, strlen(base_path())), '/'));
$logo = setting('logo');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? $storeName) ?></title>
    <?php if ($metaDescription): ?><meta name="description" content="<?= e(mb_substr(strip_tags($metaDescription), 0, 300)) ?>"><?php endif; ?>
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= e($storeName) ?>">
    <meta property="og:title" content="<?= e($title ?? $storeName) ?>">
    <?php if ($metaDescription): ?><meta property="og:description" content="<?= e(mb_substr(strip_tags($metaDescription), 0, 300)) ?>"><?php endif; ?>
    <?php if (!empty($ogImage)): ?><meta property="og:image" content="<?= e(preg_match('#^https?://#', $ogImage) ? $ogImage : full_url(ltrim(str_replace(base_path(), '', $ogImage), '/'))) ?>"><?php endif; ?>
    <meta name="theme-color" content="#ffffff">
    <?php if (setting('favicon')): ?><link rel="icon" href="<?= e(upload_url(setting('favicon'))) ?>"><?php else: ?><link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml"><?php endif; ?>
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>">
    <?= $headExtra ?? '' ?>
</head>
<body data-base="<?= e(base_path()) ?>" data-csrf="<?= e(csrf_token()) ?>" class="<?= $ageOk ? '' : 'age-locked' ?>">

<?php if (!$ageOk): ?>
<div id="age-gate" class="age-gate" role="dialog" aria-modal="true" aria-labelledby="age-title">
    <div class="age-card text-center">
        <div class="age-badge"><?= $minAge ?>+</div>
        <h2 id="age-title" class="h3 mt-3">Age verification</h2>
        <p class="text-muted-2 mb-1">This website sells nicotine products and is intended for adults only.</p>
        <p class="mb-4">Are you at least <strong><?= $minAge ?> years old</strong>?</p>
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-accent btn-lg" id="age-yes">Yes, I am <?= $minAge ?> or older</button>
            <button type="button" class="btn btn-outline-light" id="age-no">No, I am under <?= $minAge ?></button>
        </div>
        <p class="small text-muted-2 mt-3 mb-0">By entering you confirm you are of legal age to purchase nicotine products where you live. Adult signature is required on delivery.</p>
        <div id="age-denied" class="alert alert-danger mt-3 d-none mb-0">Sorry, you must be <?= $minAge ?> or older to enter this site.</div>
    </div>
</div>
<?php endif; ?>

<?php if (setting('announcement')): ?>
<div class="announce text-center"><?= e(setting('announcement')) ?></div>
<?php endif; ?>

<header class="site-header sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-label="Menu"><span class="navbar-toggler-icon"></span></button>
            <a class="navbar-brand brand" href="<?= e(url('')) ?>">
                <?php if ($logo): ?><img src="<?= e(upload_url($logo)) ?>" alt="<?= e($storeName) ?>" height="38"><?php else: ?><span class="brand-mark"><i class="bi bi-cloud-fill"></i></span><span class="brand-text"><?= e($storeName) ?></span><?php endif; ?>
            </a>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-lg-3 me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('shop')) ?>">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('collection/best-selling')) ?>">Best sellers</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('collection/new-arrivals')) ?>">New</a></li>
                    <li class="nav-item"><a class="nav-link text-accent" href="<?= e(url('collection/deals')) ?>">Deals</a></li>
                    <?php if ($navBrands): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Brands</a>
                        <ul class="dropdown-menu mega-brands">
                            <?php foreach ($navBrands as $b): ?>
                            <li><a class="dropdown-item" href="<?= e(url('brand/' . $b['slug'])) ?>"><?= e($b['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if ($navCategories): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Categories</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <?php foreach ($navCategories as $c): ?>
                            <li><a class="dropdown-item" href="<?= e(url('category/' . $c['slug'])) ?>"><?= e($c['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('blog')) ?>">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= e(url('track')) ?>">Track order</a></li>
                </ul>
                <form class="search-form d-none d-lg-flex" action="<?= e(url('shop')) ?>" method="get" role="search">
                    <input class="form-control" type="search" name="q" placeholder="Search products..." value="<?= e($_GET['q'] ?? '') ?>" aria-label="Search">
                    <button class="btn" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <a href="<?= e(url('cart')) ?>" class="cart-btn ms-2" aria-label="Cart">
                <i class="bi bi-bag"></i>
                <span class="cart-count" id="cart-count" <?= $cartCount ? '' : 'hidden' ?>><?= (int)$cartCount ?></span>
            </a>
        </div>
    </nav>
</header>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav">
    <div class="offcanvas-header"><h5 class="offcanvas-title"><?= e($storeName) ?></h5><button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button></div>
    <div class="offcanvas-body">
        <form class="search-form mb-3" action="<?= e(url('shop')) ?>" method="get">
            <input class="form-control" type="search" name="q" placeholder="Search products...">
            <button class="btn" type="submit"><i class="bi bi-search"></i></button>
        </form>
        <div class="list-group list-group-flush mobile-links">
            <a href="<?= e(url('shop')) ?>" class="list-group-item">Shop all</a>
            <a href="<?= e(url('collection/best-selling')) ?>" class="list-group-item">Best sellers</a>
            <a href="<?= e(url('collection/new-arrivals')) ?>" class="list-group-item">New arrivals</a>
            <a href="<?= e(url('collection/deals')) ?>" class="list-group-item">Deals</a>
            <?php foreach ($navCategories as $c): ?><a href="<?= e(url('category/' . $c['slug'])) ?>" class="list-group-item ps-4 small">&bull; <?= e($c['name']) ?></a><?php endforeach; ?>
            <?php foreach ($navBrands as $b): ?><a href="<?= e(url('brand/' . $b['slug'])) ?>" class="list-group-item ps-4 small">&bull; <?= e($b['name']) ?></a><?php endforeach; ?>
            <a href="<?= e(url('blog')) ?>" class="list-group-item">Blog</a>
            <a href="<?= e(url('track')) ?>" class="list-group-item">Track order</a>
            <a href="<?= e(url('faq')) ?>" class="list-group-item">FAQ</a>
            <a href="<?= e(url('contact')) ?>" class="list-group-item">Contact</a>
        </div>
    </div>
</div>

<main id="main">
    <?php $flashes = flash(); if ($flashes): ?>
    <div class="container mt-3">
        <?php foreach ($flashes as $f): ?>
        <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show" role="alert"><?= e($f['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?= $content ?>
</main>

<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="brand mb-3"><span class="brand-mark"><i class="bi bi-cloud-fill"></i></span><span class="brand-text"><?= e($storeName) ?></span></div>
                <p class="text-muted-2"><?= e(setting('footer_about', 'Premium vapes, pods and devices for adult customers. Fast, discreet US delivery.')) ?></p>
                <?php if ($tg || $wa): ?>
                <div class="d-flex gap-2">
                    <?php if ($tg): ?><a class="btn btn-outline-light btn-sm" href="https://t.me/<?= e($tg) ?>" target="_blank" rel="noopener"><i class="bi bi-telegram"></i> Telegram</a><?php endif; ?>
                    <?php if ($wa): ?><a class="btn btn-outline-light btn-sm" href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> WhatsApp</a><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-6 col-lg-2">
                <h6>Shop</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= e(url('shop')) ?>">All products</a></li>
                    <li><a href="<?= e(url('collection/best-selling')) ?>">Best sellers</a></li>
                    <li><a href="<?= e(url('collection/new-arrivals')) ?>">New arrivals</a></li>
                    <li><a href="<?= e(url('collection/deals')) ?>">Deals</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6>Help</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= e(url('track')) ?>">Track your order</a></li>
                    <li><a href="<?= e(url('faq')) ?>">FAQ</a></li>
                    <li><a href="<?= e(url('contact')) ?>">Contact us</a></li>
                    <?php foreach ($footerPages as $fp): ?><li><a href="<?= e(url('page/' . $fp['slug'])) ?>"><?= e($fp['title']) ?></a></li><?php endforeach; ?>
                </ul>
            </div>
            <div class="col-lg-3">
                <h6>Get deals in your inbox</h6>
                <form action="<?= e(url('subscribe')) ?>" method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">
                    <input type="email" name="email" class="form-control" placeholder="Email address" required aria-label="Email">
                    <button class="btn btn-accent" type="submit">Join</button>
                </form>
                <?php if (setting('store_email')): ?><p class="small text-muted-2 mt-3 mb-0"><i class="bi bi-envelope"></i> <?= e(setting('store_email')) ?></p><?php endif; ?>
                <?php if (setting('store_phone')): ?><p class="small text-muted-2 mb-0"><i class="bi bi-telephone"></i> <?= e(setting('store_phone')) ?></p><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="warning-bar text-center">
        <div class="container py-2 small">
            <strong>WARNING:</strong> <?= e(setting('nicotine_warning', 'This product contains nicotine. Nicotine is an addictive chemical.')) ?>
            Must be <?= $minAge ?>+ to purchase. Keep out of reach of children.
        </div>
    </div>
    <div class="container py-3 small text-muted-2 d-flex flex-wrap justify-content-between gap-2">
        <span>&copy; <?= date('Y') ?> <?= e($storeName) ?>. All rights reserved.</span>
        <span>Adult signature required on delivery.</span>
    </div>
</footer>

<?php if ($tg || $wa): ?>
<div class="float-contact">
    <?php if ($tg): ?><a href="https://t.me/<?= e($tg) ?>" target="_blank" rel="noopener" class="fc fc-tg" aria-label="Chat on Telegram"><i class="bi bi-telegram"></i></a><?php endif; ?>
    <?php if ($wa): ?><a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener" class="fc fc-wa" aria-label="Chat on WhatsApp"><i class="bi bi-whatsapp"></i></a><?php endif; ?>
</div>
<?php endif; ?>

<div class="toast-container position-fixed bottom-0 start-0 p-3" style="z-index:2000">
    <div id="site-toast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite">
        <div class="d-flex"><div class="toast-body" id="site-toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>
    </div>
</div>

<script src="<?= e(asset('vendor/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('js/site.js')) ?>"></script>
<?= $scriptExtra ?? '' ?>
</body>
</html>

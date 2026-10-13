<?php
$user = Auth::user();
$path = trim(substr((string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(base_path())), '/');
$nav = [
    ['admin', 'Dashboard', 'speedometer2'],
    ['admin/orders', 'Orders', 'receipt'],
    ['admin/products', 'Products', 'box-seam'],
    ['admin/categories', 'Categories', 'grid'],
    ['admin/brands', 'Brands', 'tags'],
    ['admin/sliders', 'Banners', 'images'],
    ['admin/posts', 'Blog', 'journal-text'],
    ['admin/pages', 'Pages', 'file-earmark-text'],
    ['admin/faqs', 'FAQs', 'question-circle'],
    ['admin/messages', 'Messages', 'envelope'],
    ['admin/subscribers', 'Subscribers', 'people'],
    ['admin/settings', 'Settings', 'gear'],
];
$unread = (int)Database::val('SELECT COUNT(*) FROM messages WHERE is_read=0');
$pending = (int)Database::val("SELECT COUNT(*) FROM orders WHERE status='pending'");
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'Admin') ?> - <?= e(setting('store_name', 'Store')) ?> Admin</title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="admin-shell">
    <aside class="admin-side" id="adminSide">
        <a href="<?= e(url('admin')) ?>" class="admin-brand"><span class="brand-mark"><i class="bi bi-cloud-fill"></i></span> <?= e(setting('store_name', 'Store')) ?></a>
        <nav class="nav flex-column">
            <?php foreach ($nav as [$href, $label, $icon]):
                $active = $href === 'admin' ? $path === 'admin' : (strpos($path, $href) === 0);
            ?>
            <a class="nav-link <?= $active ? 'active' : '' ?>" href="<?= e(url($href)) ?>">
                <i class="bi bi-<?= e($icon) ?>"></i> <?= e($label) ?>
                <?php if ($href === 'admin/messages' && $unread): ?><span class="badge text-bg-danger ms-auto"><?= $unread ?></span><?php endif; ?>
                <?php if ($href === 'admin/orders' && $pending): ?><span class="badge text-bg-warning ms-auto"><?= $pending ?></span><?php endif; ?>
            </a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-side-foot">
            <a class="nav-link" href="<?= e(url('')) ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> View store</a>
            <a class="nav-link" href="<?= e(url('admin/account')) ?>"><i class="bi bi-person-circle"></i> <?= e($user['name'] ?? 'Account') ?></a>
            <a class="nav-link" href="<?= e(url('admin/logout')) ?>"><i class="bi bi-box-arrow-left"></i> Log out</a>
        </div>
    </aside>
    <div class="admin-main">
        <div class="admin-top d-lg-none">
            <button class="btn btn-outline-secondary btn-sm" type="button" onclick="document.getElementById('adminSide').classList.toggle('open')"><i class="bi bi-list"></i></button>
            <strong class="ms-2"><?= e($title ?? 'Admin') ?></strong>
        </div>
        <div class="admin-content">
            <?php foreach (flash() ?? [] as $f): ?>
            <div class="alert alert-<?= e($f['type']) ?> alert-dismissible fade show"><?= e($f['msg']) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endforeach; ?>
            <?= $content ?>
        </div>
    </div>
</div>
<script src="<?= e(asset('vendor/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(asset('js/admin.js')) ?>"></script>
</body>
</html>

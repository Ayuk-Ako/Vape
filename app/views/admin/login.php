<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Admin login - <?= e(setting('store_name', 'Store')) ?></title>
    <link rel="icon" href="<?= e(asset('img/favicon.svg')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('vendor/bootstrap-icons.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="login-body">
<div class="login-card">
    <div class="text-center mb-4"><span class="brand-mark lg"><i class="bi bi-cloud-fill"></i></span><h1 class="h4 mt-3 mb-0"><?= e(setting('store_name', 'Store')) ?></h1><small class="text-secondary">Admin panel</small></div>
    <?php foreach (flash() ?? [] as $f): ?><div class="alert alert-<?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endforeach; ?>
    <form method="post" action="<?= e(url('admin/login')) ?>">
        <?= csrf_field() ?>
        <div class="mb-3"><label class="form-label" for="email">Email</label><input class="form-control" id="email" type="email" name="email" required autofocus autocomplete="username"></div>
        <div class="mb-3"><label class="form-label" for="password">Password</label><input class="form-control" id="password" type="password" name="password" required autocomplete="current-password"></div>
        <button class="btn btn-primary w-100 btn-lg">Sign in</button>
    </form>
    <p class="text-center small mt-3 mb-0"><a href="<?= e(url('')) ?>" class="text-secondary">&larr; Back to store</a></p>
</div>
</body>
</html>

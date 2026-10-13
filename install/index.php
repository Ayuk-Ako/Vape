<?php
/**
 * Web installer. Creates tables, admin user, settings and (optionally) demo data,
 * then writes config/config.php. After installation this page refuses to run.
 */
define('BASE_DIR', dirname(__DIR__));
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();

$configFile = BASE_DIR . '/config/config.php';
$root = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

if (is_file($configFile)) {
    http_response_code(403);
    echo '<!doctype html><meta charset="utf-8"><title>Installed</title><body style="font-family:sans-serif;background:#0b0d14;color:#e9ecf5;display:grid;place-items:center;min-height:100vh;margin:0"><div style="text-align:center;max-width:460px;padding:24px"><h1>Already installed</h1><p>The store is installed. For security, delete the <code>/install</code> folder from your server.</p><p><a style="color:#a99bff" href="' . $h($root) . '/">Go to the store</a> &middot; <a style="color:#a99bff" href="' . $h($root) . '/admin">Admin</a></p></div></body>';
    exit;
}

// Requirements
$checks = [
    'PHP 8.0 or newer (' . PHP_VERSION . ')' => version_compare(PHP_VERSION, '8.0.0', '>='),
    'PDO MySQL extension' => extension_loaded('pdo_mysql'),
    'mbstring extension' => extension_loaded('mbstring'),
    'GD or image functions (getimagesize)' => function_exists('getimagesize'),
    'config/ folder is writable' => is_writable(BASE_DIR . '/config'),
    'uploads/ folder is writable' => is_writable(BASE_DIR . '/uploads'),
    'storage/ folder is writable' => is_writable(BASE_DIR . '/storage'),
];
$ok = !in_array(false, $checks, true);

if (empty($_SESSION['inst_csrf'])) $_SESSION['inst_csrf'] = bin2hex(random_bytes(16));

$errors = [];
$done = false;
$v = ['db_host' => 'localhost', 'db_port' => '3306', 'db_name' => '', 'db_user' => '', 'store_name' => 'Vapor Haus', 'admin_name' => '', 'admin_email' => '', 'demo' => '1'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $ok) {
    foreach ($v as $k => $_) $v[$k] = trim((string)($_POST[$k] ?? ''));
    $dbPass = (string)($_POST['db_pass'] ?? '');
    $adminPass = (string)($_POST['admin_pass'] ?? '');
    $v['demo'] = !empty($_POST['demo']) ? '1' : '0';

    if (!hash_equals($_SESSION['inst_csrf'], (string)($_POST['csrf'] ?? ''))) $errors[] = 'Session expired. Reload the page and try again.';
    if ($v['db_name'] === '' || $v['db_user'] === '') $errors[] = 'Database name and user are required.';
    if ($v['store_name'] === '') $errors[] = 'Store name is required.';
    if ($v['admin_name'] === '') $errors[] = 'Admin name is required.';
    if (!filter_var($v['admin_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid admin email.';
    if (strlen($adminPass) < 10) $errors[] = 'Admin password must be at least 10 characters.';

    if (!$errors) {
        try {
            $pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $v['db_host'], (int)$v['db_port'], $v['db_name']), $v['db_user'], $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        } catch (Throwable $e) {
            $errors[] = 'Could not connect to the database: ' . $e->getMessage();
        }
    }

    if (!$errors) {
        try {
            $existing = $pdo->query("SHOW TABLES LIKE 'products'")->fetchColumn();
            if ($existing) throw new RuntimeException('This database already contains store tables. Use an empty database, or drop the existing tables first.');

            $sql = file_get_contents(BASE_DIR . '/database/schema.sql');
            $sql = preg_replace('/^--.*$/m', '', $sql);
            foreach (preg_split('/;\s*[\r\n]+/', $sql) as $stmt) {
                $stmt = trim($stmt);
                if ($stmt !== '') $pdo->exec($stmt);
            }

            require __DIR__ . '/seed.php';
            $opts = ['store_name' => $v['store_name'], 'admin_email' => strtolower($v['admin_email'])];
            seed_settings($pdo, $opts);
            seed_pages($pdo);
            seed_faqs($pdo);
            if ($v['demo'] === '1') seed_demo($pdo, BASE_DIR);

            $pdo->prepare('INSERT INTO admins (name, email, password) VALUES (?,?,?)')->execute([$v['admin_name'], strtolower($v['admin_email']), password_hash($adminPass, PASSWORD_DEFAULT)]);

            $cfg = "<?php\nreturn " . var_export([
                'db' => ['host' => $v['db_host'], 'port' => (int)$v['db_port'], 'name' => $v['db_name'], 'user' => $v['db_user'], 'pass' => $dbPass],
                'debug' => false,
                'installed_at' => date('c'),
            ], true) . ";\n";
            if (file_put_contents($configFile, $cfg) === false) throw new RuntimeException('Could not write config/config.php. Check folder permissions.');
            @chmod($configFile, 0640);
            $done = true;
            unset($_SESSION['inst_csrf']);
        } catch (Throwable $e) {
            $errors[] = 'Installation failed: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Install store</title>
<link rel="stylesheet" href="<?= $h($root) ?>/assets/vendor/bootstrap.min.css">
<style>body{background:radial-gradient(900px 500px at 70% 0,rgba(124,92,255,.25),transparent 60%),#0b0d14;min-height:100vh}.card{background:#141926;border-color:#242c3f;border-radius:18px}.form-control{background:#0f1420;border-color:#242c3f}.btn-primary{background:linear-gradient(135deg,#7c5cff,#22d3ee);border:0;font-weight:600}</style>
</head>
<body>
<div class="container py-5" style="max-width:720px">
    <h1 class="h3 mb-1">Store installer</h1>
    <p class="text-secondary">Takes about a minute. Create an empty MySQL database in your hosting panel first.</p>

<?php if ($done): ?>
    <div class="card card-body">
        <h2 class="h4 text-success">Installation complete</h2>
        <ol class="mb-3">
            <li><strong>Delete the <code>/install</code> folder</strong> from your server now.</li>
            <li>Log in to the admin panel and open <em>Settings</em>: set your payment methods, shipping, Telegram/WhatsApp and email (SMTP).</li>
            <li>Replace the demo products, banners and pages with your own<?= $v['demo'] === '1' ? ' (demo content was installed)' : '' ?>.</li>
            <li>Read the compliance note under Settings &rarr; Compliance before going live.</li>
        </ol>
        <div class="d-flex gap-2"><a class="btn btn-primary" href="<?= $h($root) ?>/admin">Open admin</a><a class="btn btn-outline-light" href="<?= $h($root) ?>/">View store</a></div>
    </div>
<?php else: ?>
    <div class="card card-body mb-3">
        <h2 class="h6 text-uppercase text-secondary">Server check</h2>
        <?php foreach ($checks as $label => $pass): ?><div><?= $pass ? '<span class="text-success">&#10003;</span>' : '<span class="text-danger">&#10007;</span>' ?> <?= $h($label) ?></div><?php endforeach; ?>
    </div>

    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= $h($e) ?></div><?php endforeach; ?>

    <?php if ($ok): ?>
    <form method="post" class="card card-body">
        <input type="hidden" name="csrf" value="<?= $h($_SESSION['inst_csrf']) ?>">
        <h2 class="h6 text-uppercase text-secondary">Database</h2>
        <div class="row g-3 mb-3">
            <div class="col-md-8"><label class="form-label">Host</label><input class="form-control" name="db_host" value="<?= $h($v['db_host']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Port</label><input class="form-control" name="db_port" value="<?= $h($v['db_port']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Database name</label><input class="form-control" name="db_name" value="<?= $h($v['db_name']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Username</label><input class="form-control" name="db_user" value="<?= $h($v['db_user']) ?>" required></div>
            <div class="col-md-4"><label class="form-label">Password</label><input class="form-control" type="password" name="db_pass" autocomplete="off"></div>
        </div>
        <h2 class="h6 text-uppercase text-secondary">Store &amp; admin account</h2>
        <div class="row g-3 mb-3">
            <div class="col-12"><label class="form-label">Store name</label><input class="form-control" name="store_name" value="<?= $h($v['store_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Your name</label><input class="form-control" name="admin_name" value="<?= $h($v['admin_name']) ?>" required></div>
            <div class="col-md-6"><label class="form-label">Admin email (login)</label><input class="form-control" type="email" name="admin_email" value="<?= $h($v['admin_email']) ?>" required></div>
            <div class="col-12"><label class="form-label">Admin password (min. 10 characters)</label><input class="form-control" type="password" name="admin_pass" minlength="10" required autocomplete="new-password"></div>
        </div>
        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="demo" id="demo" value="1" <?= $v['demo'] === '1' ? 'checked' : '' ?>><label class="form-check-label" for="demo">Install demo products, banners and blog posts (fictional - delete later)</label></div>
        <button class="btn btn-primary btn-lg">Install</button>
    </form>
    <?php else: ?>
    <div class="alert alert-warning">Fix the items marked with a red cross above, then reload this page.</div>
    <?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>

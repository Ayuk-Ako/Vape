<?php
/**
 * Front controller.
 */
define('BASE_DIR', __DIR__);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_DIR . '/storage/php-error.log');

require BASE_DIR . '/app/core/helpers.php';
require BASE_DIR . '/app/core/Database.php';
require BASE_DIR . '/app/core/Controller.php';
require BASE_DIR . '/app/core/Cart.php';
require BASE_DIR . '/app/core/Catalog.php';
require BASE_DIR . '/app/core/Auth.php';
require BASE_DIR . '/app/core/AdminController.php';
require BASE_DIR . '/app/core/App.php';

$configFile = BASE_DIR . '/config/config.php';
if (!is_file($configFile)) {
    header('Location: ' . base_path() . '/install/');
    exit;
}
$config = require $configFile;

if (!empty($config['debug'])) {
    ini_set('display_errors', '1');
}

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name('vh_session');
session_start();

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

try {
    Database::connect($config['db']);
    require BASE_DIR . '/app/core/Mailer.php';
    (new App($config))->run();
} catch (Throwable $e) {
    @file_put_contents(BASE_DIR . '/storage/php-error.log', '[' . date('c') . '] ' . $e . PHP_EOL, FILE_APPEND);
    http_response_code(500);
    if (!empty($config['debug'])) {
        echo '<pre style="padding:20px;white-space:pre-wrap">' . htmlspecialchars((string)$e) . '</pre>';
    } else {
        echo '<!doctype html><meta charset="utf-8"><title>Error</title><body style="font-family:sans-serif;text-align:center;padding:80px"><h1>Something went wrong</h1><p>Please try again in a moment.</p></body>';
    }
}

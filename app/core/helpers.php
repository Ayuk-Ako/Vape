<?php
/**
 * Global helper functions.
 */

function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function base_path(): string
{
    static $b = null;
    if ($b === null) {
        $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        $b = rtrim($dir, '/');
    }
    return $b;
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function full_url(string $path = ''): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return ($https ? 'https://' : 'http://') . $host . url($path);
}

function asset(string $path): string
{
    $file = BASE_DIR . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? filemtime($file) : time();
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function upload_url(?string $path): string
{
    if (!$path) {
        return asset('img/placeholder.svg');
    }
    return url('uploads/' . ltrim($path, '/'));
}

function money($n): string
{
    return '$' . number_format((float)$n, 2);
}

function redirect(string $path): void
{
    $target = preg_match('#^https?://#', $path) ? $path : url($path);
    header('Location: ' . $target);
    exit;
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = @iconv('UTF-8', 'ASCII//TRANSLIT', $text) ?: $text;
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'item';
}

function unique_slug(string $table, string $base, int $ignoreId = 0): string
{
    $slug = slugify($base);
    $i = 1;
    $candidate = $slug;
    while (Database::val("SELECT COUNT(*) FROM `$table` WHERE slug=? AND id<>?", [$candidate, $ignoreId]) > 0) {
        $i++;
        $candidate = $slug . '-' . $i;
    }
    return $candidate;
}

/* ---------- Session / flash / CSRF ---------- */

function flash(?string $type = null, ?string $msg = null)
{
    if ($type !== null && $msg !== null) {
        $_SESSION['_flash'][] = ['type' => $type, 'msg' => $msg];
        return null;
    }
    $f = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $f;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $t = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($t) || !hash_equals($_SESSION['_csrf'] ?? '', $t)) {
        http_response_code(419);
        exit('Security token expired. Please go back, refresh the page and try again.');
    }
}

function old(string $key, $default = '')
{
    return $_SESSION['_old'][$key] ?? $default;
}

function keep_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

/* ---------- Settings ---------- */

function settings_all(): array
{
    static $s = null;
    if ($s === null) {
        $s = [];
        try {
            foreach (Database::all('SELECT skey, svalue FROM settings') as $r) {
                $s[$r['skey']] = $r['svalue'];
            }
        } catch (Throwable $e) {
            $s = [];
        }
    }
    return $s;
}

function setting(string $key, $default = '')
{
    $s = settings_all();
    return isset($s[$key]) && $s[$key] !== '' ? $s[$key] : $default;
}

function set_setting(string $key, ?string $value): void
{
    Database::exec('INSERT INTO settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)', [$key, $value]);
}

/* ---------- Images ---------- */

/**
 * Save an uploaded image into /uploads/<folder>. Returns relative path or null.
 */
function save_upload(array $file, string $folder = 'products'): ?string
{
    if (empty($file['tmp_name']) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > 6 * 1024 * 1024) {
        return null;
    }
    $info = @getimagesize($file['tmp_name']);
    if (!$info) {
        return null;
    }
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if (!isset($allowed[$info[2]])) {
        return null;
    }
    $dir = BASE_DIR . '/uploads/' . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(6)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
        return null;
    }
    return $folder . '/' . $name;
}

function delete_upload(?string $path): void
{
    if (!$path) return;
    $full = realpath(BASE_DIR . '/uploads/' . $path);
    $root = realpath(BASE_DIR . '/uploads');
    if ($full && $root && strpos($full, $root) === 0 && is_file($full)) {
        @unlink($full);
    }
}

/* ---------- Misc ---------- */

function client_ip(): string
{
    return substr($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', 0, 64);
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function page_url_with(array $extra): string
{
    $q = array_merge($_GET, $extra);
    unset($q['route']);
    foreach ($q as $k => $v) {
        if ($v === '' || $v === null) unset($q[$k]);
    }
    $path = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return $path . ($q ? '?' . http_build_query($q) : '');
}

function us_states(): array
{
    return [
        'AL'=>'Alabama','AK'=>'Alaska','AZ'=>'Arizona','AR'=>'Arkansas','CA'=>'California','CO'=>'Colorado','CT'=>'Connecticut',
        'DE'=>'Delaware','DC'=>'District of Columbia','FL'=>'Florida','GA'=>'Georgia','HI'=>'Hawaii','ID'=>'Idaho','IL'=>'Illinois',
        'IN'=>'Indiana','IA'=>'Iowa','KS'=>'Kansas','KY'=>'Kentucky','LA'=>'Louisiana','ME'=>'Maine','MD'=>'Maryland',
        'MA'=>'Massachusetts','MI'=>'Michigan','MN'=>'Minnesota','MS'=>'Mississippi','MO'=>'Missouri','MT'=>'Montana',
        'NE'=>'Nebraska','NV'=>'Nevada','NH'=>'New Hampshire','NJ'=>'New Jersey','NM'=>'New Mexico','NY'=>'New York',
        'NC'=>'North Carolina','ND'=>'North Dakota','OH'=>'Ohio','OK'=>'Oklahoma','OR'=>'Oregon','PA'=>'Pennsylvania',
        'RI'=>'Rhode Island','SC'=>'South Carolina','SD'=>'South Dakota','TN'=>'Tennessee','TX'=>'Texas','UT'=>'Utah',
        'VT'=>'Vermont','VA'=>'Virginia','WA'=>'Washington','WV'=>'West Virginia','WI'=>'Wisconsin','WY'=>'Wyoming',
    ];
}

function order_statuses(): array
{
    return [
        'pending'    => 'Pending',
        'confirmed'  => 'Confirmed',
        'processing' => 'Processing',
        'shipped'    => 'Shipped',
        'delivered'  => 'Delivered',
        'cancelled'  => 'Cancelled',
    ];
}

function status_badge(string $status): string
{
    $map = ['pending' => 'warning', 'confirmed' => 'info', 'processing' => 'primary', 'shipped' => 'info', 'delivered' => 'success', 'cancelled' => 'danger', 'paid' => 'success', 'unpaid' => 'secondary'];
    $c = $map[$status] ?? 'secondary';
    return '<span class="badge text-bg-' . $c . '">' . e(ucfirst($status)) . '</span>';
}

/** Whitelist-based HTML cleaner for CMS content (admin-authored). */
function clean_html(?string $html): string
{
    $html = (string)$html;
    $html = preg_replace('#<(script|style|iframe|object|embed)\b[^>]*>.*?</\1>#is', '', $html);
    $html = preg_replace('#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $html);
    $html = preg_replace('#(href|src)\s*=\s*([\'"])\s*javascript:[^\'"]*\2#i', '$1="#"', $html);
    return $html;
}

/** Include a view partial with variables. */
function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    include BASE_DIR . '/app/views/' . $name . '.php';
}

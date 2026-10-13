<?php
class Auth
{
    public static function check(): bool
    {
        return !empty($_SESSION['admin_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        static $u = null;
        if ($u === null) {
            $u = Database::one('SELECT id, name, email FROM admins WHERE id=?', [(int)$_SESSION['admin_id']]);
        }
        return $u;
    }

    public static function require(): void
    {
        if (!self::check() || !self::user()) {
            redirect('admin/login');
        }
    }

    public static function tooManyAttempts(): bool
    {
        $n = (int)Database::val('SELECT COUNT(*) FROM login_attempts WHERE ip=? AND created_at > (NOW() - INTERVAL 15 MINUTE)', [client_ip()]);
        return $n >= 6;
    }

    public static function attempt(string $email, string $password): bool
    {
        if (self::tooManyAttempts()) {
            return false;
        }
        $a = Database::one('SELECT * FROM admins WHERE email=?', [strtolower($email)]);
        if ($a && password_verify($password, $a['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$a['id'];
            Database::update('admins', ['last_login' => date('Y-m-d H:i:s')], 'id=?', [$a['id']]);
            Database::exec('DELETE FROM login_attempts WHERE ip=?', [client_ip()]);
            return true;
        }
        Database::insert('login_attempts', ['ip' => client_ip()]);
        return false;
    }

    public static function logout(): void
    {
        unset($_SESSION['admin_id']);
        session_regenerate_id(true);
    }
}

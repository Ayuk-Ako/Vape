<?php
class AdminAuthController extends Controller
{
    public function loginForm(): void
    {
        if (Auth::check()) redirect('admin');
        $this->render('admin/login', ['title' => 'Admin login'], '');
    }

    public function login(): void
    {
        $email = trim((string)($_POST['email'] ?? ''));
        $pass = (string)($_POST['password'] ?? '');
        if (Auth::tooManyAttempts()) {
            flash('danger', 'Too many failed attempts. Please wait 15 minutes and try again.');
            redirect('admin/login');
        }
        if (Auth::attempt($email, $pass)) {
            redirect('admin');
        }
        flash('danger', 'Invalid email or password.');
        redirect('admin/login');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('admin/login');
    }

    public function account(): void
    {
        Auth::require();
        $this->render('admin/account', ['title' => 'My account', 'user' => Auth::user()], 'layout/admin');
    }

    public function accountSave(): void
    {
        Auth::require();
        $user = Database::one('SELECT * FROM admins WHERE id=?', [(int)$_SESSION['admin_id']]);
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'Please enter a name and a valid email.');
            redirect('admin/account');
        }
        if (Database::val('SELECT COUNT(*) FROM admins WHERE email=? AND id<>?', [$email, $user['id']]) > 0) {
            flash('danger', 'That email is already used by another admin.');
            redirect('admin/account');
        }
        $data = ['name' => $name, 'email' => $email];
        if ($new !== '') {
            if (!password_verify($current, $user['password'])) {
                flash('danger', 'Your current password is incorrect.');
                redirect('admin/account');
            }
            if (strlen($new) < 10) {
                flash('danger', 'New password must be at least 10 characters.');
                redirect('admin/account');
            }
            if ($new !== $confirm) {
                flash('danger', 'New password and confirmation do not match.');
                redirect('admin/account');
            }
            $data['password'] = password_hash($new, PASSWORD_DEFAULT);
        }
        Database::update('admins', $data, 'id=?', [$user['id']]);
        flash('success', 'Account updated.');
        redirect('admin/account');
    }
}

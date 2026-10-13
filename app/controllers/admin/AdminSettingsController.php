<?php
class AdminSettingsController extends AdminController
{
    /** key => [type, group] */
    public const KEYS = [
        // Store
        'store_name' => 'text', 'store_tagline' => 'text', 'store_email' => 'text', 'order_notify_email' => 'text', 'store_phone' => 'text', 'store_address' => 'text',
        'support_hours' => 'text', 'announcement' => 'text', 'footer_about' => 'text',
        // Contact
        'telegram_username' => 'text', 'whatsapp_number' => 'text',
        // Compliance
        'min_age' => 'int', 'nicotine_warning' => 'text', 'restricted_states' => 'text',
        // Commerce
        'payment_methods' => 'textarea', 'shipping_flat' => 'money', 'free_shipping_over' => 'money', 'tax_rate' => 'money', 'min_order' => 'money', 'delivery_note' => 'text',
        // SEO
        'seo_title' => 'text', 'seo_description' => 'text',
        // Mail
        'mail_from_name' => 'text', 'mail_from_email' => 'text', 'smtp_host' => 'text', 'smtp_port' => 'int', 'smtp_user' => 'text', 'smtp_pass' => 'secret', 'smtp_encryption' => 'text',
    ];

    public function index(): void
    {
        $this->view('admin/settings', ['title' => 'Settings', 's' => settings_all()]);
    }

    public function save(): void
    {
        foreach (self::KEYS as $key => $type) {
            if (!array_key_exists($key, $_POST)) continue;
            $v = trim((string)$_POST[$key]);
            if ($type === 'int') $v = (string)max(0, (int)$v);
            elseif ($type === 'money') $v = $v === '' ? '' : (string)max(0, round((float)$v, 2));
            elseif ($type === 'secret' && $v === '') continue; // keep the stored password when left blank
            if ($key === 'telegram_username') $v = ltrim($v, '@ ');
            if ($key === 'restricted_states') $v = strtoupper(preg_replace('/[^A-Za-z,]/', '', $v));
            if ($key === 'smtp_encryption' && !in_array($v, ['tls', 'ssl', 'none'], true)) $v = 'tls';
            set_setting($key, $v);
        }

        foreach (['logo' => 'logo', 'favicon' => 'favicon'] as $field => $key) {
            if (!empty($_POST['remove_' . $field])) {
                delete_upload(setting($key) ?: null);
                set_setting($key, '');
            }
            if (!empty($_FILES[$field]['name'])) {
                $path = save_upload($_FILES[$field], 'branding');
                if ($path) {
                    delete_upload(setting($key) ?: null);
                    set_setting($key, $path);
                } else {
                    flash('warning', ucfirst($field) . ' upload failed (JPG, PNG, WEBP or GIF up to 6MB).');
                }
            }
        }
        flash('success', 'Settings saved.');
        redirect('admin/settings');
    }

    public function testMail(): void
    {
        $to = trim((string)($_POST['test_to'] ?? '')) ?: Auth::user()['email'];
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'Please enter a valid email address.');
            redirect('admin/settings');
        }
        $ok = Mailer::send($to, 'Test email from ' . setting('store_name', 'your store'), '<p>If you can read this, your email settings work.</p>');
        flash($ok ? 'success' : 'danger', $ok ? "Test email sent to $to." : 'Sending failed. Check SMTP settings, then storage/mail.log for details.');
        redirect('admin/settings');
    }
}

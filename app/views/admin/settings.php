<?php
/** @var array $s */
$g = fn($k, $d = '') => $s[$k] ?? $d;
$field = function (string $key, string $label, string $help = '', string $type = 'text', string $ph = '') use ($g) {
    echo '<div class="mb-3"><label class="form-label" for="s_' . e($key) . '">' . e($label) . '</label>';
    if ($type === 'textarea') {
        echo '<textarea class="form-control" id="s_' . e($key) . '" name="' . e($key) . '" rows="5" placeholder="' . e($ph) . '">' . e($g($key)) . '</textarea>';
    } else {
        echo '<input class="form-control" id="s_' . e($key) . '" type="' . e($type) . '" name="' . e($key) . '" value="' . e($type === 'password' ? '' : $g($key)) . '" placeholder="' . e($ph) . '"' . ($type === 'number' ? ' step="0.01" min="0"' : '') . ($type === 'password' ? ' autocomplete="new-password"' : '') . '>';
    }
    if ($help) echo '<div class="form-text">' . $help . '</div>';
    echo '</div>';
};
?>
<h1 class="h3 mb-3">Settings</h1>
<form method="post" action="<?= e(url('admin/settings')) ?>" enctype="multipart/form-data">
<?= csrf_field() ?>
<ul class="nav nav-tabs mb-3" role="tablist">
    <?php foreach (['store' => 'Store', 'commerce' => 'Payment & shipping', 'compliance' => 'Compliance', 'contact' => 'Contact channels', 'seo' => 'SEO', 'mail' => 'Email'] as $k => $l): ?>
    <li class="nav-item"><button class="nav-link <?= $k === 'store' ? 'active' : '' ?>" type="button" data-bs-toggle="tab" data-bs-target="#tab-<?= $k ?>"><?= e($l) ?></button></li>
    <?php endforeach; ?>
</ul>
<div class="tab-content card card-body" style="max-width:860px">
    <div class="tab-pane fade show active" id="tab-store">
        <?php $field('store_name', 'Store name'); $field('store_tagline', 'Tagline'); $field('announcement', 'Announcement bar text', 'Shown above the header. Leave empty to hide.', 'text', 'Free shipping over $100'); ?>
        <?php $field('footer_about', 'Footer description'); $field('store_email', 'Store email', 'Shown in the footer and used as the sender address.', 'email'); $field('store_phone', 'Phone'); $field('store_address', 'Business address'); $field('support_hours', 'Support hours', '', 'text', 'Support hours: 9am - 9pm (ET), 7 days'); ?>
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Logo</label><?php if ($g('logo')): ?><div class="mb-2"><img src="<?= e(upload_url($g('logo'))) ?>" alt="" style="height:44px"> <label class="ms-2 small"><input type="checkbox" name="remove_logo" value="1"> Remove</label></div><?php endif; ?><input type="file" class="form-control" name="logo" accept="image/*"><div class="form-text">Leave empty to use the text logo.</div></div>
            <div class="col-md-6"><label class="form-label">Favicon</label><?php if ($g('favicon')): ?><div class="mb-2"><img src="<?= e(upload_url($g('favicon'))) ?>" alt="" style="height:32px"> <label class="ms-2 small"><input type="checkbox" name="remove_favicon" value="1"> Remove</label></div><?php endif; ?><input type="file" class="form-control" name="favicon" accept="image/*"></div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-commerce">
        <?php $field('payment_methods', 'Payment methods', 'One per line: <code>Name | instructions shown to the customer</code>. Customers choose one at checkout and see the instructions on the confirmation page and in their email. Example: <code>Zelle | Send to pay@yourstore.com with your order number</code>', 'textarea', "Zelle | Send payment to pay@yourstore.com\nCash App | Send to \$YourTag\nBank transfer | Account details are sent by email"); ?>
        <div class="row">
            <div class="col-md-4"><?php $field('shipping_flat', 'Flat shipping rate ($)', '', 'number'); ?></div>
            <div class="col-md-4"><?php $field('free_shipping_over', 'Free shipping over ($)', '0 = never free', 'number'); ?></div>
            <div class="col-md-4"><?php $field('min_order', 'Minimum order ($)', '0 = no minimum', 'number'); ?></div>
            <div class="col-md-4"><?php $field('tax_rate', 'Sales tax rate (%)', 'Flat rate applied to the subtotal.', 'number'); ?></div>
        </div>
        <?php $field('delivery_note', 'Delivery note', 'Shown on the home page and product pages.', 'text', 'Orders ship in 1-2 business days'); ?>
        <?php $field('order_notify_email', 'Send new-order alerts to', 'Defaults to the store email.', 'email'); ?>
    </div>

    <div class="tab-pane fade" id="tab-compliance">
        <div class="alert alert-warning small">Rules for selling nicotine products differ by state and by product (age limits, flavor bans, FDA marketing authorization, PACT Act shipping requirements, taxes and licenses). You are responsible for operating legally. Please get advice from a qualified attorney before going live.</div>
        <?php $field('min_age', 'Minimum age', 'Used by the age gate, checkout and emails. Federal minimum is 21.', 'number'); ?>
        <?php $field('restricted_states', 'States you will NOT ship to', 'Comma-separated two-letter codes, e.g. <code>CA,NY,MA,NJ</code>. Checkout blocks these states.', 'text', 'CA,NY'); ?>
        <?php $field('nicotine_warning', 'Nicotine warning text', 'Displayed in the footer, on product pages and in emails.'); ?>
    </div>

    <div class="tab-pane fade" id="tab-contact">
        <?php $field('telegram_username', 'Telegram username', 'Without the @. Shows a floating Telegram button and a "message us" button after checkout.', 'text', 'yourstore'); ?>
        <?php $field('whatsapp_number', 'WhatsApp number', 'International format, digits only, e.g. 15551234567. Leave empty to hide the WhatsApp button.'); ?>
    </div>

    <div class="tab-pane fade" id="tab-seo">
        <?php $field('seo_title', 'Home page title'); $field('seo_description', 'Home page meta description'); ?>
    </div>

    <div class="tab-pane fade" id="tab-mail">
        <p class="text-secondary small">Without SMTP the server's built-in mail is used (often lands in spam on shared hosting). For best deliverability create a mailbox on your domain (e.g. <code>orders@yourdomain.com</code>) and enter its SMTP details.</p>
        <?php $field('mail_from_name', 'From name'); $field('mail_from_email', 'From email', 'Must be an address on your domain when using SMTP.', 'email'); $field('smtp_host', 'SMTP host', '', 'text', 'smtp.hostinger.com'); ?>
        <div class="row"><div class="col-md-4"><?php $field('smtp_port', 'SMTP port', '', 'text', '465'); ?></div>
        <div class="col-md-4"><div class="mb-3"><label class="form-label" for="s_smtp_encryption">Encryption</label><select class="form-select" id="s_smtp_encryption" name="smtp_encryption"><?php foreach (['ssl' => 'SSL (port 465)', 'tls' => 'TLS / STARTTLS (port 587)', 'none' => 'None'] as $k => $l): ?><option value="<?= $k ?>" <?= $g('smtp_encryption', 'tls') === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div></div></div>
        <?php $field('smtp_user', 'SMTP username'); $field('smtp_pass', 'SMTP password', 'Leave blank to keep the current password.', 'password'); ?>
    </div>
    <div class="mt-3"><button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save settings</button></div>
</div>
</form>

<form method="post" action="<?= e(url('admin/settings/test-mail')) ?>" class="card card-body mt-3" style="max-width:860px">
    <?= csrf_field() ?>
    <h2 class="h6">Send a test email</h2>
    <div class="input-group"><input class="form-control" type="email" name="test_to" placeholder="you@example.com (defaults to your admin email)"><button class="btn btn-outline-primary">Send test</button></div>
    <div class="form-text">Save your settings first, then test.</div>
</form>

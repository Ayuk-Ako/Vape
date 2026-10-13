<?php
$tg = ltrim((string)setting('telegram_username', ''), '@');
$wa = preg_replace('/\D/', '', (string)setting('whatsapp_number', ''));
?>
<div class="page-hero"><div class="container"><h1>Contact us</h1><p class="text-muted-2 mb-0">Questions about an order or a product? We usually reply within a few hours.</p></div></div>
<div class="container">
    <div class="row g-4">
        <div class="col-lg-7">
            <form action="<?= e(url('contact')) ?>" method="post" class="form-card">
                <?= csrf_field() ?>
                <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label" for="cn">Name *</label><input class="form-control" id="cn" name="name" required value="<?= e(old('name')) ?>"></div>
                    <div class="col-md-6"><label class="form-label" for="ce">Email *</label><input class="form-control" id="ce" type="email" name="email" required value="<?= e(old('email')) ?>"></div>
                    <div class="col-12"><label class="form-label" for="cs">Subject</label><input class="form-control" id="cs" name="subject" value="<?= e(old('subject')) ?>"></div>
                    <div class="col-12"><label class="form-label" for="cm">Message *</label><textarea class="form-control" id="cm" name="message" rows="6" required><?= e(old('message')) ?></textarea></div>
                    <div class="col-12"><button class="btn btn-accent btn-lg">Send message</button></div>
                </div>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="form-card">
                <h2 class="h5">Reach us directly</h2>
                <ul class="list-unstyled contact-list mb-0">
                    <?php if ($tg): ?><li><i class="bi bi-telegram"></i> <a href="https://t.me/<?= e($tg) ?>" target="_blank" rel="noopener">Telegram: @<?= e($tg) ?></a></li><?php endif; ?>
                    <?php if ($wa): ?><li><i class="bi bi-whatsapp"></i> <a href="https://wa.me/<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp: +<?= e($wa) ?></a></li><?php endif; ?>
                    <?php if (setting('store_email')): ?><li><i class="bi bi-envelope"></i> <a href="mailto:<?= e(setting('store_email')) ?>"><?= e(setting('store_email')) ?></a></li><?php endif; ?>
                    <?php if (setting('store_phone')): ?><li><i class="bi bi-telephone"></i> <?= e(setting('store_phone')) ?></li><?php endif; ?>
                    <?php if (setting('store_address')): ?><li><i class="bi bi-geo-alt"></i> <?= e(setting('store_address')) ?></li><?php endif; ?>
                    <li><i class="bi bi-clock"></i> <?= e(setting('support_hours', 'Support hours: 9am - 9pm (ET), 7 days')) ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

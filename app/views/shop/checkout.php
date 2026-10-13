<?php
/** @var array $items $totals $methods $states */
$minAge = (int)setting('min_age', '21');
$tg = ltrim((string)setting('telegram_username', ''), '@');
$chosen = old('payment_method', array_key_first($methods) ?? '');
?>
<div class="page-hero"><div class="container"><h1>Checkout</h1></div></div>
<div class="container">
<form action="<?= e(url('checkout')) ?>" method="post" class="row g-4" novalidate>
    <?= csrf_field() ?>
    <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off">

    <div class="col-lg-7">
        <div class="form-card">
            <h2 class="h5">Contact</h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label" for="name">Full name *</label><input class="form-control" id="name" name="name" required autocomplete="name" value="<?= e(old('name')) ?>"></div>
                <div class="col-md-6"><label class="form-label" for="email">Email *</label><input class="form-control" id="email" type="email" name="email" required autocomplete="email" value="<?= e(old('email')) ?>"></div>
                <div class="col-md-6"><label class="form-label" for="phone">Phone *</label><input class="form-control" id="phone" type="tel" name="phone" required autocomplete="tel" value="<?= e(old('phone')) ?>"></div>
                <?php if ($tg): ?><div class="col-12"><label class="form-label" for="telegram">Telegram username <span class="text-muted-2">(optional - faster support)</span></label><input class="form-control" id="telegram" name="telegram" placeholder="@yourname" value="<?= e(old('telegram')) ?>"></div><?php endif; ?>
            </div>
        </div>

        <div class="form-card">
            <h2 class="h5">Shipping address <span class="text-muted-2 fs-6 fw-normal">(United States)</span></h2>
            <div class="row g-3">
                <div class="col-12"><label class="form-label" for="address1">Street address *</label><input class="form-control" id="address1" name="address1" required autocomplete="address-line1" value="<?= e(old('address1')) ?>"></div>
                <div class="col-12"><label class="form-label" for="address2">Apartment, suite, etc.</label><input class="form-control" id="address2" name="address2" autocomplete="address-line2" value="<?= e(old('address2')) ?>"></div>
                <div class="col-md-5"><label class="form-label" for="city">City *</label><input class="form-control" id="city" name="city" required autocomplete="address-level2" value="<?= e(old('city')) ?>"></div>
                <div class="col-md-4"><label class="form-label" for="state">State *</label>
                    <select class="form-select" id="state" name="state" required autocomplete="address-level1">
                        <option value="">Select...</option>
                        <?php foreach ($states as $code => $name): ?><option value="<?= e($code) ?>" <?= old('state') === $code ? 'selected' : '' ?>><?= e($name) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label" for="zip">ZIP *</label><input class="form-control" id="zip" name="zip" required inputmode="numeric" autocomplete="postal-code" value="<?= e(old('zip')) ?>"></div>
                <div class="col-12"><label class="form-label" for="notes">Order notes</label><textarea class="form-control" id="notes" name="notes" rows="2"><?= e(old('notes')) ?></textarea></div>
            </div>
        </div>

        <div class="form-card">
            <h2 class="h5">Payment method</h2>
            <?php if (!$methods): ?><div class="alert alert-warning mb-0">No payment methods are configured yet. Please contact the store.</div><?php endif; ?>
            <?php foreach ($methods as $label => $info): ?>
            <label class="pay-option">
                <input type="radio" name="payment_method" value="<?= e($label) ?>" <?= $chosen === $label ? 'checked' : '' ?> required>
                <span><strong><?= e($label) ?></strong><?php if ($info): ?><small class="d-block text-muted-2"><?= e($info) ?></small><?php endif; ?></span>
            </label>
            <?php endforeach; ?>
            <p class="small text-muted-2 mt-2 mb-0">You won't be charged on this page. After placing the order, you'll see exactly how to complete payment<?= $tg ? ' and can message us on Telegram' : '' ?>.</p>
        </div>

        <div class="form-card">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="age_confirm" id="age_confirm" value="1" required>
                <label class="form-check-label" for="age_confirm">I confirm I am at least <strong><?= $minAge ?> years old</strong> and legally allowed to purchase nicotine products in my state. I understand an adult signature (valid ID) is required on delivery. *</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="terms" id="terms" value="1" required>
                <label class="form-check-label" for="terms">I agree to the <a href="<?= e(url('page/terms-of-service')) ?>" target="_blank">Terms of Service</a> and <a href="<?= e(url('page/shipping-policy')) ?>" target="_blank">Shipping Policy</a>. *</label>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="summary-box">
            <h2 class="h5 mb-3">Your order</h2>
            <?php foreach ($items['lines'] as $l): ?>
            <div class="d-flex gap-3 mb-3">
                <img src="<?= e(upload_url($l['image'])) ?>" alt="" class="cart-img sm">
                <div class="flex-grow-1 small"><div class="fw-semibold"><?= e($l['name']) ?></div><?php if ($l['variant']): ?><div class="text-muted-2"><?= e($l['variant']) ?></div><?php endif; ?><div class="text-muted-2">Qty <?= (int)$l['qty'] ?></div></div>
                <div class="small"><?= e(money($l['total'])) ?></div>
            </div>
            <?php endforeach; ?>
            <hr>
            <div class="d-flex justify-content-between"><span class="text-muted-2">Subtotal</span><span><?= e(money($totals['subtotal'])) ?></span></div>
            <div class="d-flex justify-content-between"><span class="text-muted-2">Shipping</span><span><?= $totals['shipping'] > 0 ? e(money($totals['shipping'])) : 'Free' ?></span></div>
            <?php if ($totals['tax'] > 0): ?><div class="d-flex justify-content-between"><span class="text-muted-2">Tax</span><span><?= e(money($totals['tax'])) ?></span></div><?php endif; ?>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span><?= e(money($totals['total'])) ?></span></div>
            <button type="submit" class="btn btn-accent btn-lg w-100 mt-3" <?= $methods ? '' : 'disabled' ?>>Place order</button>
            <a href="<?= e(url('cart')) ?>" class="btn btn-link w-100 text-muted-2 mt-1">Back to cart</a>
        </div>
        <div class="nic-warning mt-3"><strong>WARNING:</strong> <?= e(setting('nicotine_warning', 'This product contains nicotine. Nicotine is an addictive chemical.')) ?></div>
    </div>
</form>
</div>

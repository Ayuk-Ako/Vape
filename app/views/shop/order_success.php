<?php
/** @var array $order $lines $paymentInfo */
$tg = ltrim((string)setting('telegram_username', ''), '@');
$tgText = rawurlencode('Hi! I just placed order ' . $order['order_number'] . ' (' . money($order['total']) . '). Payment method: ' . $order['payment_method'] . '.');
?>
<div class="container py-5" style="max-width:820px">
    <div class="text-center mb-4">
        <div class="success-icon"><i class="bi bi-check-lg"></i></div>
        <h1 class="h2 mt-3">Thank you, <?= e(explode(' ', $order['customer_name'])[0]) ?>!</h1>
        <p class="text-muted-2 mb-0">Your order <strong><?= e($order['order_number']) ?></strong> was received. A confirmation was sent to <?= e($order['customer_email']) ?>.</p>
    </div>

    <div class="form-card">
        <h2 class="h5"><i class="bi bi-wallet2"></i> Complete your payment</h2>
        <p class="mb-2">Payment method: <strong><?= e($order['payment_method']) ?></strong> &middot; Amount due: <strong><?= e(money($order['total'])) ?></strong></p>
        <?php if ($paymentInfo): ?><div class="pay-info"><?= nl2br(e($paymentInfo)) ?></div><?php endif; ?>
        <p class="small text-muted-2 mt-3 mb-0">Please include your order number <strong><?= e($order['order_number']) ?></strong> as the payment reference. Your order ships once payment is confirmed.</p>
        <?php if ($tg): ?>
        <a class="btn btn-accent mt-3" href="https://t.me/<?= e($tg) ?>?text=<?= $tgText ?>" target="_blank" rel="noopener"><i class="bi bi-telegram"></i> Message us on Telegram</a>
        <?php endif; ?>
    </div>

    <div class="form-card">
        <h2 class="h5">Order details</h2>
        <?php foreach ($lines as $l): ?>
        <div class="d-flex justify-content-between small py-1"><span><?= e($l['product_name']) ?><?= $l['variant_name'] ? ' - ' . e($l['variant_name']) : '' ?> &times; <?= (int)$l['qty'] ?></span><span><?= e(money($l['line_total'])) ?></span></div>
        <?php endforeach; ?>
        <hr>
        <div class="d-flex justify-content-between small"><span class="text-muted-2">Shipping</span><span><?= $order['shipping'] > 0 ? e(money($order['shipping'])) : 'Free' ?></span></div>
        <?php if ($order['tax'] > 0): ?><div class="d-flex justify-content-between small"><span class="text-muted-2">Tax</span><span><?= e(money($order['tax'])) ?></span></div><?php endif; ?>
        <div class="d-flex justify-content-between fw-bold mt-1"><span>Total</span><span><?= e(money($order['total'])) ?></span></div>
        <hr>
        <div class="small text-muted-2">Shipping to:<br><?= e($order['customer_name']) ?>, <?= e($order['address1']) ?><?= $order['address2'] ? ', ' . e($order['address2']) : '' ?>, <?= e($order['city']) ?>, <?= e($order['state']) ?> <?= e($order['zip']) ?></div>
    </div>
    <div class="text-center"><a href="<?= e(url('track')) ?>" class="btn btn-outline-light">Track this order</a> <a href="<?= e(url('shop')) ?>" class="btn btn-accent">Keep shopping</a></div>
</div>

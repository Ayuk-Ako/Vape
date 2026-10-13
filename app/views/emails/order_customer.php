<h2 style="margin:0 0 8px">Thanks for your order, <?= e(explode(' ', $order['customer_name'])[0]) ?>!</h2>
<p style="margin:0 0 14px">We received order <strong><?= e($order['order_number']) ?></strong>. It will ship as soon as your payment is confirmed.</p>

<div style="background:#f3f0ff;border-radius:10px;padding:14px 16px;margin:14px 0">
  <strong>How to pay (<?= e($order['payment_method']) ?>)</strong><br>
  Amount due: <strong><?= e(money($order['total'])) ?></strong><br>
  <?php if ($paymentInfo): ?><span style="white-space:pre-line"><?= e($paymentInfo) ?></span><br><?php endif; ?>
  <span style="color:#6b7280;font-size:13px">Use <?= e($order['order_number']) ?> as the payment reference.</span>
  <?php $tg = ltrim((string)setting('telegram_username', ''), '@'); if ($tg): ?><br><a href="https://t.me/<?= e($tg) ?>" style="color:#5b3df5">Message us on Telegram</a><?php endif; ?>
</div>

<?php include __DIR__ . '/_items.php'; ?>

<p style="margin:14px 0 4px"><strong>Shipping to</strong></p>
<p style="margin:0;color:#4b5563"><?= e($order['customer_name']) ?><br><?= e($order['address1']) ?><?= $order['address2'] ? '<br>' . e($order['address2']) : '' ?><br><?= e($order['city']) ?>, <?= e($order['state']) ?> <?= e($order['zip']) ?></p>
<p style="margin:16px 0 0;color:#6b7280;font-size:13px">An adult (<?= (int)setting('min_age', '21') ?>+) must sign for the delivery and may be asked to show valid ID.</p>
<p style="margin:16px 0 0"><a href="<?= e(full_url('track')) ?>" style="color:#5b3df5">Track your order</a></p>

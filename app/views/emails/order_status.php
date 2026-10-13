<h2 style="margin:0 0 8px">Order <?= e($order['order_number']) ?> update</h2>
<p style="margin:0 0 14px">Hi <?= e(explode(' ', $order['customer_name'])[0]) ?>, your order status is now <strong><?= e(ucfirst($order['status'])) ?></strong>.</p>
<?php if ($order['tracking_number']): ?><p style="margin:0 0 14px">Carrier tracking number: <strong><?= e($order['tracking_number']) ?></strong></p><?php endif; ?>
<?php include __DIR__ . '/_items.php'; ?>
<p style="margin:16px 0 0"><a href="<?= e(full_url('track')) ?>" style="color:#5b3df5">Track your order</a></p>

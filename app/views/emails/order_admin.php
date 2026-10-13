<h2 style="margin:0 0 8px">New order <?= e($order['order_number']) ?></h2>
<p style="margin:0 0 6px"><strong><?= e($order['customer_name']) ?></strong> &middot; <?= e($order['customer_email']) ?> &middot; <?= e($order['customer_phone']) ?><?= $order['telegram_handle'] ? ' &middot; Telegram @' . e($order['telegram_handle']) : '' ?></p>
<p style="margin:0 0 6px">Payment method: <strong><?= e($order['payment_method']) ?></strong> (unpaid)</p>
<?php include __DIR__ . '/_items.php'; ?>
<p style="margin:10px 0 4px"><strong>Ship to</strong></p>
<p style="margin:0;color:#4b5563"><?= e($order['address1']) ?><?= $order['address2'] ? ', ' . e($order['address2']) : '' ?><br><?= e($order['city']) ?>, <?= e($order['state']) ?> <?= e($order['zip']) ?></p>
<?php if ($order['notes']): ?><p style="margin:10px 0 0"><strong>Notes:</strong> <?= nl2br(e($order['notes'])) ?></p><?php endif; ?>
<p style="margin:10px 0 0;color:#6b7280;font-size:13px">Age confirmation: <?= $order['age_confirmed'] ? 'Yes' : 'No' ?> &middot; IP: <?= e($order['ip']) ?></p>
<p style="margin:16px 0 0"><a href="<?= e(full_url('admin/orders/' . $order['id'])) ?>" style="color:#5b3df5">Open in admin</a></p>

<?php /** @var ?array $order $lines */
$steps = ['pending' => 'Order placed', 'confirmed' => 'Payment confirmed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
$keys = array_keys($steps);
$cur = $order ? array_search($order['status'], $keys, true) : false;
?>
<div class="page-hero"><div class="container"><h1>Track your order</h1><p class="text-muted-2 mb-0">Enter the order number from your confirmation email and the email you used at checkout.</p></div></div>
<div class="container" style="max-width:760px">
    <form action="<?= e(url('track')) ?>" method="post" class="form-card">
        <?= csrf_field() ?>
        <div class="row g-3 align-items-end">
            <div class="col-md-5"><label class="form-label" for="order_number">Order number</label><input class="form-control" id="order_number" name="order_number" placeholder="VH2610131234" required value="<?= e($_POST['order_number'] ?? '') ?>"></div>
            <div class="col-md-5"><label class="form-label" for="temail">Email</label><input type="email" class="form-control" id="temail" name="email" required value="<?= e($_POST['email'] ?? '') ?>"></div>
            <div class="col-md-2"><button class="btn btn-accent w-100">Track</button></div>
        </div>
    </form>

    <?php if ($order): ?>
    <div class="form-card">
        <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
            <div><div class="text-muted-2 small">Order</div><strong><?= e($order['order_number']) ?></strong></div>
            <div><div class="text-muted-2 small">Placed</div><?= e(date('M j, Y', strtotime($order['created_at']))) ?></div>
            <div><div class="text-muted-2 small">Total</div><?= e(money($order['total'])) ?></div>
            <div><div class="text-muted-2 small">Payment</div><?= status_badge($order['payment_status']) ?></div>
        </div>
        <?php if ($order['status'] === 'cancelled'): ?>
            <div class="alert alert-danger mb-0">This order was cancelled.</div>
        <?php else: ?>
        <ol class="tracker">
            <?php foreach ($steps as $k => $label): $i = array_search($k, $keys, true); ?>
            <li class="<?= $cur !== false && $i <= $cur ? 'done' : '' ?> <?= $cur === $i ? 'current' : '' ?>"><span class="dot"></span><span><?= e($label) ?></span></li>
            <?php endforeach; ?>
        </ol>
        <?php endif; ?>
        <?php if ($order['tracking_number']): ?><div class="alert alert-info mt-3 mb-0"><i class="bi bi-box-seam"></i> Carrier tracking number: <strong><?= e($order['tracking_number']) ?></strong></div><?php endif; ?>
        <hr>
        <?php foreach ($lines as $l): ?>
        <div class="d-flex justify-content-between small py-1"><span><?= e($l['product_name']) ?><?= $l['variant_name'] ? ' - ' . e($l['variant_name']) : '' ?> &times; <?= (int)$l['qty'] ?></span><span><?= e(money($l['line_total'])) ?></span></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

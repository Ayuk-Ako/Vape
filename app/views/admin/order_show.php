<?php
/** @var array $order $lines */
$tg = $order['telegram_handle'] ? ltrim($order['telegram_handle'], '@') : '';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Order <?= e($order['order_number']) ?> <?= status_badge($order['status']) ?> <?= status_badge($order['payment_status']) ?></h1>
    <a href="<?= e(url('admin/orders')) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Orders</a>
</div>
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card mb-3"><div class="card-header"><strong>Items</strong></div>
        <div class="table-responsive"><table class="table align-middle mb-0">
            <thead><tr><th>Product</th><th class="text-end">Price</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead>
            <tbody>
            <?php foreach ($lines as $l): ?><tr><td><?= e($l['product_name']) ?><?= $l['variant_name'] ? '<div class="small text-secondary">' . e($l['variant_name']) . '</div>' : '' ?></td><td class="text-end"><?= e(money($l['price'])) ?></td><td class="text-center"><?= (int)$l['qty'] ?></td><td class="text-end"><?= e(money($l['line_total'])) ?></td></tr><?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-end text-secondary">Subtotal</td><td class="text-end"><?= e(money($order['subtotal'])) ?></td></tr>
                <tr><td colspan="3" class="text-end text-secondary">Shipping</td><td class="text-end"><?= e(money($order['shipping'])) ?></td></tr>
                <?php if ($order['tax'] > 0): ?><tr><td colspan="3" class="text-end text-secondary">Tax</td><td class="text-end"><?= e(money($order['tax'])) ?></td></tr><?php endif; ?>
                <tr><td colspan="3" class="text-end fw-bold">Total</td><td class="text-end fw-bold"><?= e(money($order['total'])) ?></td></tr>
            </tfoot>
        </table></div></div>

        <div class="card"><div class="card-body">
            <h2 class="h6 text-secondary text-uppercase">Customer</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div><strong><?= e($order['customer_name']) ?></strong></div>
                    <div><a href="mailto:<?= e($order['customer_email']) ?>"><?= e($order['customer_email']) ?></a></div>
                    <div><a href="tel:<?= e($order['customer_phone']) ?>"><?= e($order['customer_phone']) ?></a></div>
                    <?php if ($tg): ?><div><a href="https://t.me/<?= e($tg) ?>" target="_blank" rel="noopener"><i class="bi bi-telegram"></i> @<?= e($tg) ?></a></div><?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="text-secondary small">Ship to</div>
                    <?= e($order['address1']) ?><?= $order['address2'] ? '<br>' . e($order['address2']) : '' ?><br><?= e($order['city']) ?>, <?= e($order['state']) ?> <?= e($order['zip']) ?><br><?= e($order['country']) ?>
                </div>
                <?php if ($order['notes']): ?><div class="col-12"><div class="text-secondary small">Customer notes</div><?= nl2br(e($order['notes'])) ?></div><?php endif; ?>
                <div class="col-12 small text-secondary">Payment method chosen: <strong class="text-body"><?= e($order['payment_method']) ?></strong> &middot; Age confirmed: <?= $order['age_confirmed'] ? '<span class="text-success">yes</span>' : 'no' ?> &middot; IP <?= e($order['ip']) ?></div>
            </div>
        </div></div>
    </div>

    <div class="col-xl-4">
        <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/update')) ?>" class="card card-body mb-3">
            <?= csrf_field() ?>
            <h2 class="h6 text-secondary text-uppercase">Update order</h2>
            <div class="mb-3"><label class="form-label">Order status</label><select class="form-select" name="status"><?php foreach (order_statuses() as $k => $l): ?><option value="<?= e($k) ?>" <?= $order['status'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Payment</label><select class="form-select" name="payment_status"><option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option><option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option></select></div>
            <div class="mb-3"><label class="form-label">Carrier tracking number</label><input class="form-control" name="tracking_number" value="<?= e($order['tracking_number']) ?>"></div>
            <div class="mb-3"><label class="form-label">Internal note</label><textarea class="form-control" name="admin_note" rows="3"><?= e($order['admin_note']) ?></textarea></div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="notify" id="notify" value="1" checked><label class="form-check-label" for="notify">Email the customer about status/tracking changes</label></div>
            <button class="btn btn-primary">Save changes</button>
            <div class="form-text mt-2">Cancelling an order returns its items to stock.</div>
        </form>
        <form method="post" action="<?= e(url('admin/orders/' . $order['id'] . '/delete')) ?>" onsubmit="return confirm('Permanently delete this order?')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete order</button></form>
    </div>
</div>

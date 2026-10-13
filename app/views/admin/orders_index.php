<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Orders <span class="text-secondary fs-6">(<?= (int)$total ?>)</span></h1></div>
<form class="row g-2 mb-3" method="get">
    <div class="col-sm-5"><input class="form-control" name="q" placeholder="Order #, name, email, phone" value="<?= e($q) ?>"></div>
    <div class="col-sm-4"><select class="form-select" name="status"><option value="">All statuses</option><?php foreach (order_statuses() as $k => $l): ?><option value="<?= e($k) ?>" <?= $status === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select></div>
    <div class="col-sm-3"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>Order</th><th>Customer</th><th>Payment method</th><th>Total</th><th>Status</th><th>Payment</th><th class="text-end"></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $o): ?>
        <tr>
            <td><a href="<?= e(url('admin/orders/' . $o['id'])) ?>"><strong><?= e($o['order_number']) ?></strong></a><div class="small text-secondary"><?= e(date('M j, Y H:i', strtotime($o['created_at']))) ?></div></td>
            <td><?= e($o['customer_name']) ?><div class="small text-secondary"><?= e($o['customer_email']) ?></div></td>
            <td class="small"><?= e($o['payment_method']) ?></td>
            <td><?= e(money($o['total'])) ?></td>
            <td><?= status_badge($o['status']) ?></td>
            <td><?= status_badge($o['payment_status']) ?></td>
            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/orders/' . $o['id'])) ?>">Open</a></td>
        </tr>
    <?php endforeach; if (!$rows): ?><tr><td colspan="7" class="text-center text-secondary py-5">No orders found.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>
<?php if ($pages > 1): ?>
<nav class="mt-3"><ul class="pagination justify-content-center">
    <?php for ($i = 1; $i <= $pages; $i++): ?><li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= e(page_url_with(['page' => $i])) ?>"><?= $i ?></a></li><?php endfor; ?>
</ul></nav>
<?php endif; ?>

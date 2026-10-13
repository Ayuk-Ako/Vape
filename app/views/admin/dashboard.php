<?php /** @var array $stats $recent $lowStock $lowVariants */ ?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 mb-0">Dashboard</h1><a href="<?= e(url('admin/products/create')) ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add product</a></div>

<div class="row g-3 mb-4">
    <?php foreach ([
        ['Orders', $stats['orders_total'], 'receipt', 'primary', 'admin/orders'],
        ['Pending orders', $stats['orders_pending'], 'hourglass-split', 'warning', 'admin/orders?status=pending'],
        ['Revenue (paid)', money($stats['revenue']), 'cash-stack', 'success', 'admin/orders'],
        ['This month', money($stats['revenue_month']), 'calendar-check', 'info', 'admin/orders'],
        ['Awaiting payment', money($stats['awaiting']), 'wallet2', 'danger', 'admin/orders'],
        ['Products', $stats['products'], 'box-seam', 'secondary', 'admin/products'],
        ['Unread messages', $stats['messages'], 'envelope', 'secondary', 'admin/messages'],
        ['Subscribers', $stats['subscribers'], 'people', 'secondary', 'admin/subscribers'],
    ] as [$label, $val, $icon, $color, $link]): ?>
    <div class="col-6 col-xl-3"><a href="<?= e(url($link)) ?>" class="stat-card text-<?= $color ?>-emphasis"><i class="bi bi-<?= $icon ?> text-<?= $color ?>"></i><div><div class="stat-val"><?= e((string)$val) ?></div><div class="stat-label"><?= e($label) ?></div></div></a></div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card"><div class="card-header d-flex justify-content-between"><strong>Recent orders</strong><a href="<?= e(url('admin/orders')) ?>" class="small">View all</a></div>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($recent as $o): ?>
                <tr><td><a href="<?= e(url('admin/orders/' . $o['id'])) ?>"><?= e($o['order_number']) ?></a><div class="small text-secondary"><?= e(date('M j, H:i', strtotime($o['created_at']))) ?></div></td>
                <td><?= e($o['customer_name']) ?></td><td><?= e(money($o['total'])) ?></td><td><?= status_badge($o['status']) ?></td><td><?= status_badge($o['payment_status']) ?></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/orders/' . $o['id'])) ?>">Open</a></td></tr>
            <?php endforeach; if (!$recent): ?><tr><td colspan="6" class="text-center text-secondary py-4">No orders yet.</td></tr><?php endif; ?>
            </tbody></table></div></div>
    </div>
    <div class="col-xl-4">
        <div class="card"><div class="card-header"><strong>Low stock</strong></div>
        <ul class="list-group list-group-flush">
            <?php foreach ($lowStock as $p): ?><li class="list-group-item d-flex justify-content-between"><a href="<?= e(url('admin/products/' . $p['id'] . '/edit')) ?>"><?= e($p['name']) ?></a><span class="badge text-bg-<?= $p['stock'] <= 0 ? 'danger' : 'warning' ?>"><?= (int)$p['stock'] ?></span></li><?php endforeach; ?>
            <?php foreach ($lowVariants as $v): ?><li class="list-group-item d-flex justify-content-between"><a href="<?= e(url('admin/products/' . $v['pid'] . '/edit')) ?>"><?= e($v['name']) ?> <span class="text-secondary">- <?= e($v['variant']) ?></span></a><span class="badge text-bg-<?= $v['stock'] <= 0 ? 'danger' : 'warning' ?>"><?= (int)$v['stock'] ?></span></li><?php endforeach; ?>
            <?php if (!$lowStock && !$lowVariants): ?><li class="list-group-item text-secondary">All stocked up.</li><?php endif; ?>
        </ul></div>
    </div>
</div>

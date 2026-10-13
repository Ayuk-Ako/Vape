<h1 class="h3 mb-3">Messages</h1>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th>From</th><th>Subject</th><th>Received</th><th class="text-end"></th></tr></thead>
    <tbody>
    <?php foreach ($rows as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'fw-bold' ?>">
            <td><?= e($m['name']) ?><div class="small text-secondary fw-normal"><?= e($m['email']) ?></div></td>
            <td><a href="<?= e(url('admin/messages/' . $m['id'])) ?>"><?= e($m['subject'] ?: '(no subject)') ?></a><div class="small text-secondary fw-normal"><?= e(mb_strimwidth($m['message'], 0, 80, '...')) ?></div></td>
            <td class="small text-nowrap fw-normal"><?= e(date('M j, Y H:i', strtotime($m['created_at']))) ?></td>
            <td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/messages/' . $m['id'])) ?>">Read</a>
                <form method="post" action="<?= e(url('admin/messages/' . $m['id'] . '/delete')) ?>" class="d-inline" onsubmit="return confirm('Delete this message?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
        </tr>
    <?php endforeach; if (!$rows): ?><tr><td colspan="4" class="text-center text-secondary py-5">No messages yet.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>

<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Subscribers <span class="text-secondary fs-6">(<?= count($rows) ?>)</span></h1><a class="btn btn-outline-secondary btn-sm" href="<?= e(url('admin/subscribers/export')) ?>"><i class="bi bi-download"></i> Export CSV</a></div>
<div class="card"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Email</th><th>Subscribed</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['email']) ?></td><td class="small text-secondary"><?= e(date('M j, Y H:i', strtotime($r['created_at']))) ?></td></tr><?php endforeach; if (!$rows): ?><tr><td colspan="2" class="text-center text-secondary py-5">No subscribers yet.</td></tr><?php endif; ?>
</tbody></table></div></div>

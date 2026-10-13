<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= e($plural) ?></h1>
    <a href="<?= e(url('admin/' . $route . '/create')) ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New <?= e(strtolower($label)) ?></a>
</div>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><?php foreach ($columns as $h): ?><th><?= e($h) ?></th><?php endforeach; ?><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
        <tr>
            <?php foreach ($columns as $col => $h): ?><td><?= $ctl->cell($col, $r) ?></td><?php endforeach; ?>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/' . $route . '/' . $r['id'] . '/edit')) ?>"><i class="bi bi-pencil"></i> Edit</a>
                <form method="post" action="<?= e(url('admin/' . $route . '/' . $r['id'] . '/delete')) ?>" class="d-inline" onsubmit="return confirm('Delete this <?= e(strtolower($label)) ?>? This cannot be undone.')">
                    <?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; if (!$rows): ?>
        <tr><td colspan="<?= count($columns) + 1 ?>" class="text-center text-secondary py-5">Nothing here yet. <a href="<?= e(url('admin/' . $route . '/create')) ?>">Create the first one</a>.</td></tr>
    <?php endif; ?>
    </tbody>
</table></div></div>

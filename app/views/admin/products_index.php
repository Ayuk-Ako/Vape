<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Products <span class="text-secondary fs-6">(<?= count($rows) ?>)</span></h1>
    <a href="<?= e(url('admin/products/create')) ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New product</a>
</div>
<form class="row g-2 mb-3" method="get">
    <div class="col-sm-5"><input class="form-control" name="q" placeholder="Search name or SKU" value="<?= e($q) ?>"></div>
    <div class="col-sm-4"><select class="form-select" name="category"><option value="">All categories</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-sm-3"><button class="btn btn-outline-secondary w-100">Filter</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead><tr><th></th><th>Name</th><th>Category / Brand</th><th>Price</th><th>Stock</th><th>Flags</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): $stock = (int)$r['variant_count'] > 0 ? (int)$r['variant_stock'] : (int)$r['stock']; ?>
        <tr>
            <td style="width:56px"><img src="<?= e(upload_url($r['image'])) ?>" alt="" style="width:44px;height:44px;object-fit:contain;border-radius:8px;background:#1b2030"></td>
            <td><a href="<?= e(url('admin/products/' . $r['id'] . '/edit')) ?>"><?= e($r['name']) ?></a><?php if ($r['variant_count']): ?><div class="small text-secondary"><?= (int)$r['variant_count'] ?> options</div><?php endif; ?></td>
            <td class="small"><?= e($r['category_name'] ?: '-') ?><div class="text-secondary"><?= e($r['brand_name'] ?: '-') ?></div></td>
            <td><?= e(money($r['price'])) ?><?php if ($r['compare_price']): ?><div class="small text-secondary text-decoration-line-through"><?= e(money($r['compare_price'])) ?></div><?php endif; ?></td>
            <td><span class="badge text-bg-<?= $stock <= 0 ? 'danger' : ($stock <= 3 ? 'warning' : 'success') ?>"><?= $stock ?></span></td>
            <td class="small"><?= $r['is_bestseller'] ? '<span class="badge text-bg-warning">Hot</span> ' : '' ?><?= $r['is_new'] ? '<span class="badge text-bg-info">New</span> ' : '' ?><?= $r['is_featured'] ? '<span class="badge text-bg-primary">Spotlight</span>' : '' ?></td>
            <td><?= $r['is_active'] ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Hidden</span>' ?></td>
            <td class="text-end text-nowrap">
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('product/' . $r['slug'])) ?>" target="_blank" title="View"><i class="bi bi-eye"></i></a>
                <a class="btn btn-sm btn-outline-secondary" href="<?= e(url('admin/products/' . $r['id'] . '/edit')) ?>"><i class="bi bi-pencil"></i> Edit</a>
                <form method="post" action="<?= e(url('admin/products/' . $r['id'] . '/delete')) ?>" class="d-inline" onsubmit="return confirm('Delete this product and its images?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
            </td>
        </tr>
    <?php endforeach; if (!$rows): ?><tr><td colspan="8" class="text-center text-secondary py-5">No products found.</td></tr><?php endif; ?>
    </tbody>
</table></div></div>

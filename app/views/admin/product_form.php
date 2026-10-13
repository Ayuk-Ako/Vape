<?php
/** @var ?array $p $images $variants $categories $brands */
$v = fn($k, $d = '') => old($k, $p[$k] ?? $d);
$chk = fn($k, $d = 0) => (int)old($k, $p[$k] ?? $d);
// If the form bounced on validation errors, rebuild variant rows from the posted arrays.
$rows = $variants;
if (isset($_SESSION['_old']['variant_name'])) {
    $rows = [];
    foreach ($_SESSION['_old']['variant_name'] as $i => $n) {
        $rows[] = ['id' => $_SESSION['_old']['variant_id'][$i] ?? 0, 'name' => $n, 'price' => $_SESSION['_old']['variant_price'][$i] ?? '', 'stock' => $_SESSION['_old']['variant_stock'][$i] ?? 0];
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= $p ? 'Edit product' : 'New product' ?></h1>
    <div class="d-flex gap-2"><?php if ($p): ?><a href="<?= e(url('product/' . $p['slug'])) ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> View</a><?php endif; ?><a href="<?= e(url('admin/products')) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a></div>
</div>

<form method="post" action="<?= e(url('admin/products/save')) ?>" enctype="multipart/form-data">
<?= csrf_field() ?>
<?php if ($p): ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>"><?php endif; ?>
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">Basics</h2>
            <div class="mb-3"><label class="form-label">Product name *</label><input class="form-control" name="name" required value="<?= e($v('name')) ?>"></div>
            <div class="mb-3"><label class="form-label">Short description</label><textarea class="form-control" name="short_description" rows="2" maxlength="500"><?= e($v('short_description')) ?></textarea></div>
            <div class="mb-0"><label class="form-label">Full description</label><textarea class="form-control font-monospace" name="description" rows="9"><?= e($v('description')) ?></textarea><div class="form-text">HTML allowed (p, ul, li, strong, h3, img...).</div></div>
        </div>

        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">Pricing &amp; stock</h2>
            <div class="row g-3">
                <div class="col-sm-4"><label class="form-label">Price (USD) *</label><input class="form-control" type="number" step="0.01" min="0" name="price" required value="<?= e($v('price', '0.00')) ?>"></div>
                <div class="col-sm-4"><label class="form-label">Compare-at price</label><input class="form-control" type="number" step="0.01" min="0" name="compare_price" value="<?= e($v('compare_price')) ?>"><div class="form-text">Shows a sale badge when higher than price.</div></div>
                <div class="col-sm-4"><label class="form-label">Stock (no options)</label><input class="form-control" type="number" min="0" name="stock" value="<?= e($v('stock', 0)) ?>"><div class="form-text">Ignored when you add options below.</div></div>
                <div class="col-sm-6"><label class="form-label">SKU</label><input class="form-control" name="sku" value="<?= e($v('sku')) ?>"></div>
            </div>
        </div>

        <div class="card card-body mb-3">
            <div class="d-flex justify-content-between align-items-center"><h2 class="h6 text-secondary text-uppercase mb-0">Options / flavors</h2><button type="button" class="btn btn-sm btn-outline-primary" id="addVariant"><i class="bi bi-plus-lg"></i> Add option</button></div>
            <p class="small text-secondary mt-2">Add one row per flavor or variant. Each has its own stock. Leave price empty to use the product price.</p>
            <div id="variantRows">
                <?php foreach ($rows as $r): ?>
                <div class="row g-2 variant-row mb-2">
                    <input type="hidden" name="variant_id[]" value="<?= (int)($r['id'] ?? 0) ?>">
                    <div class="col-sm-6"><input class="form-control" name="variant_name[]" placeholder="e.g. Blue Razz Ice" value="<?= e($r['name']) ?>"></div>
                    <div class="col-5 col-sm-2"><input class="form-control" type="number" step="0.01" min="0" name="variant_price[]" placeholder="Price" value="<?= e($r['price'] ?? '') ?>"></div>
                    <div class="col-5 col-sm-3"><input class="form-control" type="number" min="0" name="variant_stock[]" placeholder="Stock" value="<?= e($r['stock'] ?? 0) ?>"></div>
                    <div class="col-2 col-sm-1"><button type="button" class="btn btn-outline-danger w-100 rm-variant" title="Remove"><i class="bi bi-x-lg"></i></button></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">Specifications</h2>
            <div class="row g-3">
                <div class="col-sm-4"><label class="form-label">Puffs</label><input class="form-control" type="number" min="0" name="puffs" value="<?= e($v('puffs')) ?>"></div>
                <div class="col-sm-4"><label class="form-label">Nicotine</label><input class="form-control" name="nicotine" placeholder="5% (50mg)" value="<?= e($v('nicotine')) ?>"></div>
                <div class="col-sm-4"><label class="form-label">Battery</label><input class="form-control" name="battery" placeholder="650mAh rechargeable" value="<?= e($v('battery')) ?>"></div>
                <div class="col-sm-4"><label class="form-label">E-liquid capacity</label><input class="form-control" name="capacity" placeholder="15ml" value="<?= e($v('capacity')) ?>"></div>
                <div class="col-sm-4"><label class="form-label">Coil</label><input class="form-control" name="coil" placeholder="Mesh coil" value="<?= e($v('coil')) ?>"></div>
                <div class="col-12"><label class="form-label">Extra specs</label><textarea class="form-control" name="specs_extra" rows="3" placeholder="One per line, like:&#10;Charging: USB-C&#10;Airflow: Adjustable"><?= e($v('specs_extra')) ?></textarea></div>
            </div>
        </div>

        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">SEO</h2>
            <div class="mb-3"><label class="form-label">Meta title</label><input class="form-control" name="meta_title" maxlength="200" value="<?= e($v('meta_title')) ?>"></div>
            <div class="mb-3"><label class="form-label">Meta description</label><textarea class="form-control" name="meta_description" rows="2" maxlength="300"><?= e($v('meta_description')) ?></textarea></div>
            <?php if ($p): ?><div class="mb-0"><label class="form-label">URL slug</label><input class="form-control" name="slug" value="<?= e($p['slug']) ?>"></div><?php endif; ?>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">Organization</h2>
            <div class="mb-3"><label class="form-label">Category</label><select class="form-select" name="category_id"><option value="">None</option><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$v('category_id') === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Brand</label><select class="form-select" name="brand_id"><option value="">None</option><?php foreach ($brands as $b): ?><option value="<?= (int)$b['id'] ?>" <?= (int)$v('brand_id') === (int)$b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?></select></div>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $chk('is_active', 1) ? 'checked' : '' ?>><label class="form-check-label" for="is_active">Visible in store</label></div>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_bestseller" id="is_bestseller" value="1" <?= $chk('is_bestseller') ? 'checked' : '' ?>><label class="form-check-label" for="is_bestseller">Best seller</label></div>
            <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" name="is_new" id="is_new" value="1" <?= $chk('is_new') ? 'checked' : '' ?>><label class="form-check-label" for="is_new">New arrival</label></div>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= $chk('is_featured') ? 'checked' : '' ?>><label class="form-check-label" for="is_featured">Home page spotlight</label><div class="form-text">Only one product can be the spotlight.</div></div>
        </div>

        <div class="card card-body mb-3">
            <h2 class="h6 text-secondary text-uppercase">Images</h2>
            <?php if ($images): ?>
            <div class="img-grid mb-3">
                <?php foreach ($images as $i => $img): ?>
                <div class="img-item">
                    <img src="<?= e(upload_url($img['path'])) ?>" alt="">
                    <label class="small d-block text-center"><input type="radio" name="main_image" value="<?= (int)$img['id'] ?>"> <?= $i === 0 ? 'Main' : 'Make main' ?></label>
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 mt-1" formaction="<?= e(url('admin/products/image/' . $img['id'] . '/delete')) ?>" formnovalidate onclick="return confirm('Remove this image?')"><i class="bi bi-trash"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <input type="file" class="form-control" name="images[]" multiple accept="image/jpeg,image/png,image/webp,image/gif">
            <div class="form-text">JPG, PNG, WEBP or GIF, up to 6MB each. The first image is the main one; the second shows on hover.</div>
        </div>

        <div class="d-grid"><button class="btn btn-primary btn-lg"><i class="bi bi-check-lg"></i> Save product</button></div>
    </div>
</div>
</form>

<template id="variantTpl">
    <div class="row g-2 variant-row mb-2">
        <input type="hidden" name="variant_id[]" value="0">
        <div class="col-sm-6"><input class="form-control" name="variant_name[]" placeholder="e.g. Blue Razz Ice"></div>
        <div class="col-5 col-sm-2"><input class="form-control" type="number" step="0.01" min="0" name="variant_price[]" placeholder="Price"></div>
        <div class="col-5 col-sm-3"><input class="form-control" type="number" min="0" name="variant_stock[]" placeholder="Stock" value="10"></div>
        <div class="col-2 col-sm-1"><button type="button" class="btn btn-outline-danger w-100 rm-variant" title="Remove"><i class="bi bi-x-lg"></i></button></div>
    </div>
</template>

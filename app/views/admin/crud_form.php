<?php
/** @var ?array $row $fields */
$val = function (array $f) use ($row) {
    $o = old($f['name'], null);
    if ($o !== null) return $o;
    if ($row) return $row[$f['name']] ?? '';
    return $f['default'] ?? '';
};
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0"><?= $row ? 'Edit' : 'New' ?> <?= e(strtolower($label)) ?></h1>
    <a href="<?= e(url('admin/' . $route)) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<form method="post" action="<?= e(url('admin/' . $route . '/save')) ?>" enctype="multipart/form-data" class="card card-body" style="max-width:860px">
    <?= csrf_field() ?>
    <?php if ($row): ?><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><?php endif; ?>
    <?php foreach ($fields as $f): $n = $f['name']; $v = $val($f); ?>
    <div class="mb-3">
        <?php if ($f['type'] === 'checkbox'): ?>
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" name="<?= e($n) ?>" id="f_<?= e($n) ?>" value="1" <?= $v ? 'checked' : '' ?>><label class="form-check-label" for="f_<?= e($n) ?>"><?= e($f['label']) ?></label></div>
        <?php else: ?>
            <label class="form-label" for="f_<?= e($n) ?>"><?= e($f['label']) ?><?= !empty($f['required']) ? ' *' : '' ?></label>
            <?php if ($f['type'] === 'textarea' || $f['type'] === 'html'): ?>
                <textarea class="form-control <?= $f['type'] === 'html' ? 'font-monospace' : '' ?>" id="f_<?= e($n) ?>" name="<?= e($n) ?>" rows="<?= (int)($f['rows'] ?? 4) ?>"><?= e($v) ?></textarea>
            <?php elseif ($f['type'] === 'number'): ?>
                <input type="number" class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($v === '' ? 0 : $v) ?>">
            <?php elseif ($f['type'] === 'select'): ?>
                <select class="form-select" id="f_<?= e($n) ?>" name="<?= e($n) ?>"><?php foreach ($f['options'] as $ov => $ol): ?><option value="<?= e($ov) ?>" <?= (string)$v === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option><?php endforeach; ?></select>
            <?php elseif ($f['type'] === 'image'): ?>
                <?php if ($row && !empty($row[$n])): ?>
                    <div class="mb-2 d-flex align-items-center gap-3"><img src="<?= e(upload_url($row[$n])) ?>" alt="" style="height:70px;border-radius:8px"><div class="form-check"><input class="form-check-input" type="checkbox" name="remove_<?= e($n) ?>" id="rm_<?= e($n) ?>" value="1"><label class="form-check-label" for="rm_<?= e($n) ?>">Remove image</label></div></div>
                <?php endif; ?>
                <input type="file" class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>" accept="image/jpeg,image/png,image/webp,image/gif">
            <?php else: ?>
                <input type="text" class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>">
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($f['help'])): ?><div class="form-text"><?= e($f['help']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if ($row && isset($row['slug'])): ?>
    <div class="mb-3"><label class="form-label" for="f_slug">URL slug</label><input type="text" class="form-control" id="f_slug" name="slug" value="<?= e($row['slug']) ?>"><div class="form-text">Leave as is unless you know you need to change it.</div></div>
    <?php endif; ?>
    <div><button class="btn btn-primary"><i class="bi bi-check-lg"></i> Save</button></div>
</form>

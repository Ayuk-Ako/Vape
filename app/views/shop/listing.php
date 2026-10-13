<?php
/** @var array $filters $result $categories $brands $fixed */
$items = $result['items'];
$hasFilters = $filters['min'] !== '' || $filters['max'] !== '' || $filters['puffs'] !== '' || !empty($filters['in_stock']) || (empty($fixed['category']) && $filters['category'] !== '') || (empty($fixed['brand']) && $filters['brand'] !== '');
?>
<div class="page-hero">
    <div class="container">
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li><li class="breadcrumb-item active"><?= e($heading) ?></li></ol></nav>
        <h1><?= e($heading) ?></h1>
        <?php if ($intro): ?><p class="text-muted-2 mb-0 col-lg-8"><?= e($intro) ?></p><?php endif; ?>
    </div>
</div>

<div class="container">
    <div class="row g-4">
        <aside class="col-lg-3">
            <button class="btn btn-outline-light w-100 d-lg-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#filters"><i class="bi bi-funnel"></i> Filters <?= $hasFilters ? '(active)' : '' ?></button>
            <form id="filters" class="filter-box collapse d-lg-block" method="get">
                <?php if ($filters['q'] !== ''): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>
                <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>">

                <?php if (empty($fixed['category'])): ?>
                <h6>Category</h6>
                <select name="category" class="form-select form-select-sm mb-3">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $c): ?><option value="<?= e($c['slug']) ?>" <?= $filters['category'] === $c['slug'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= (int)$c['product_count'] ?>)</option><?php endforeach; ?>
                </select>
                <?php endif; ?>

                <?php if (empty($fixed['brand'])): ?>
                <h6>Brand</h6>
                <select name="brand" class="form-select form-select-sm mb-3">
                    <option value="">All brands</option>
                    <?php foreach ($brands as $b): ?><option value="<?= e($b['slug']) ?>" <?= $filters['brand'] === $b['slug'] ? 'selected' : '' ?>><?= e($b['name']) ?> (<?= (int)$b['product_count'] ?>)</option><?php endforeach; ?>
                </select>
                <?php endif; ?>

                <h6>Puff count</h6>
                <select name="puffs" class="form-select form-select-sm mb-3">
                    <option value="">Any</option>
                    <?php foreach (Catalog::puffRanges() as $k => $r): ?><option value="<?= e($k) ?>" <?= $filters['puffs'] === $k ? 'selected' : '' ?>><?= e($r[0]) ?></option><?php endforeach; ?>
                </select>

                <h6>Price</h6>
                <div class="d-flex gap-2 mb-3">
                    <input type="number" min="0" step="1" name="min" class="form-control form-control-sm" placeholder="Min" value="<?= e($filters['min']) ?>">
                    <input type="number" min="0" step="1" name="max" class="form-control form-control-sm" placeholder="Max" value="<?= e($filters['max']) ?>">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="in_stock" value="1" id="inStock" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="inStock">In stock only</label>
                </div>
                <button class="btn btn-accent w-100 btn-sm" type="submit">Apply filters</button>
                <?php if ($hasFilters): ?><a class="btn btn-link w-100 btn-sm text-muted-2" href="<?= e(strtok($_SERVER['REQUEST_URI'], '?')) ?>">Clear filters</a><?php endif; ?>
            </form>
        </aside>

        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="text-muted-2"><?= (int)$result['total'] ?> product<?= $result['total'] === 1 ? '' : 's' ?></div>
                <form method="get" class="d-flex align-items-center gap-2">
                    <?php foreach (['q', 'category', 'brand', 'puffs', 'min', 'max', 'in_stock'] as $k): if (isset($_GET[$k]) && $_GET[$k] !== ''): ?><input type="hidden" name="<?= e($k) ?>" value="<?= e($_GET[$k]) ?>"><?php endif; endforeach; ?>
                    <label for="sort" class="small text-muted-2 mb-0">Sort</label>
                    <select id="sort" name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach (Catalog::sorts() as $k => $s): ?><option value="<?= e($k) ?>" <?= $filters['sort'] === $k ? 'selected' : '' ?>><?= e($s[0]) ?></option><?php endforeach; ?>
                    </select>
                </form>
            </div>

            <?php if ($items): ?>
            <div class="row g-3 row-cols-2 row-cols-md-3">
                <?php foreach ($items as $p): ?><div class="col"><?php partial('shop/_card', ['p' => $p]); ?></div><?php endforeach; ?>
            </div>
            <?php partial('shop/_pagination', ['page' => $result['page'], 'pages' => $result['pages']]); ?>
            <?php else: ?>
            <div class="empty-state text-center py-5">
                <i class="bi bi-search display-4"></i>
                <h3 class="mt-3">No products found</h3>
                <p class="text-muted-2">Try removing some filters or searching for something else.</p>
                <a href="<?= e(url('shop')) ?>" class="btn btn-accent">Browse all products</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

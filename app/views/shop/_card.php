<?php
/** @var array $p product row from Catalog::CARD_SELECT */
$inStock = Catalog::inStock($p);
$onSale = $p['compare_price'] !== null && (float)$p['compare_price'] > (float)$p['price'];
$pct = $onSale ? (int)round(100 - ((float)$p['price'] / (float)$p['compare_price']) * 100) : 0;
$href = url('product/' . $p['slug']);
?>
<article class="p-card h-100">
    <a href="<?= e($href) ?>" class="p-media" aria-label="<?= e($p['name']) ?>">
        <img src="<?= e(upload_url($p['image'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy" class="img-main">
        <?php if (!empty($p['image2'])): ?><img src="<?= e(upload_url($p['image2'])) ?>" alt="" loading="lazy" class="img-hover"><?php endif; ?>
        <div class="p-badges">
            <?php if (!$inStock): ?><span class="pb pb-out">Sold out</span><?php else: ?>
                <?php if ($onSale): ?><span class="pb pb-sale">-<?= $pct ?>%</span><?php endif; ?>
                <?php if ($p['is_new']): ?><span class="pb pb-new">New</span><?php endif; ?>
                <?php if ($p['is_bestseller']): ?><span class="pb pb-hot">Hot</span><?php endif; ?>
            <?php endif; ?>
        </div>
    </a>
    <div class="p-body">
        <?php if ($p['brand_name']): ?><div class="p-brand"><?= e($p['brand_name']) ?></div><?php endif; ?>
        <h3 class="p-title"><a href="<?= e($href) ?>"><?= e($p['name']) ?></a></h3>
        <?php if ($p['puffs']): ?><div class="p-meta"><i class="bi bi-wind"></i> <?= number_format((int)$p['puffs']) ?> puffs<?= $p['nicotine'] ? ' &middot; ' . e($p['nicotine']) : '' ?></div><?php endif; ?>
        <div class="p-price">
            <span class="now"><?= (int)$p['variant_count'] > 0 ? 'From ' : '' ?><?= e(money($p['price'])) ?></span>
            <?php if ($onSale): ?><span class="was"><?= e(money($p['compare_price'])) ?></span><?php endif; ?>
        </div>
        <a href="<?= e($href) ?>" class="btn <?= $inStock ? 'btn-accent' : 'btn-outline-light disabled' ?> w-100 btn-sm mt-2"><?= $inStock ? ((int)$p['variant_count'] > 0 ? 'Choose options' : 'View product') : 'Sold out' ?></a>
    </div>
</article>

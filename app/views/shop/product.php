<?php
/** @var array $p $images $variants $related */
$inStock = Catalog::inStock($p);
$onSale = $p['compare_price'] !== null && (float)$p['compare_price'] > (float)$p['price'];
$hasVariants = count($variants) > 0;
$minAge = (int)setting('min_age', '21');
$tg = ltrim((string)setting('telegram_username', ''), '@');

$specs = [];
if ($p['puffs'])    $specs['Puffs'] = number_format((int)$p['puffs']);
if ($p['nicotine']) $specs['Nicotine'] = $p['nicotine'];
if ($p['battery'])  $specs['Battery'] = $p['battery'];
if ($p['capacity']) $specs['E-liquid capacity'] = $p['capacity'];
if ($p['coil'])     $specs['Coil'] = $p['coil'];
if ($p['brand_name']) $specs['Brand'] = $p['brand_name'];
if ($p['sku'])      $specs['SKU'] = $p['sku'];
foreach (preg_split('/\R/', (string)$p['specs_extra']) as $line) {
    if (strpos($line, ':') !== false) { [$k, $v] = array_map('trim', explode(':', $line, 2)); if ($k !== '' && $v !== '') $specs[$k] = $v; }
}

$ld = [
    '@context' => 'https://schema.org', '@type' => 'Product', 'name' => $p['name'],
    'description' => strip_tags((string)($p['short_description'] ?: $p['name'])),
    'sku' => $p['sku'] ?: (string)$p['id'],
    'offers' => ['@type' => 'Offer', 'priceCurrency' => 'USD', 'price' => number_format((float)$p['price'], 2, '.', ''),
        'availability' => $inStock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock', 'url' => full_url('product/' . $p['slug'])],
];
if ($p['brand_name']) $ld['brand'] = ['@type' => 'Brand', 'name' => $p['brand_name']];
if ($images) $ld['image'] = array_map(fn($i) => full_url('uploads/' . $i['path']), $images);
$headExtra = '<script type="application/ld+json">' . json_encode($ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) . '</script>';
?>
<div class="container pt-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li>
        <?php if ($p['category_name']): ?><li class="breadcrumb-item"><a href="<?= e(url('category/' . $p['category_slug'])) ?>"><?= e($p['category_name']) ?></a></li><?php endif; ?>
        <li class="breadcrumb-item active"><?= e($p['name']) ?></li>
    </ol></nav>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="gallery">
                <div class="gallery-main">
                    <img id="galleryMain" src="<?= e(upload_url($images[0]['path'] ?? null)) ?>" alt="<?= e($p['name']) ?>">
                    <?php if ($onSale && $inStock): ?><span class="pb pb-sale lg">-<?= (int)round(100 - ((float)$p['price'] / (float)$p['compare_price']) * 100) ?>%</span><?php endif; ?>
                </div>
                <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                    <button type="button" class="thumb <?= $i === 0 ? 'active' : '' ?>" data-src="<?= e(upload_url($img['path'])) ?>" aria-label="Image <?= $i + 1 ?>"><img src="<?= e(upload_url($img['path'])) ?>" alt="" loading="lazy"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <?php if ($p['brand_name']): ?><a class="p-brand" href="<?= e(url('brand/' . $p['brand_slug'])) ?>"><?= e($p['brand_name']) ?></a><?php endif; ?>
            <h1 class="h2 mt-1"><?= e($p['name']) ?></h1>
            <div class="p-price my-3">
                <span class="now fs-2" id="priceNow" data-base="<?= e((string)$p['price']) ?>"><?= $hasVariants ? 'From ' : '' ?><?= e(money($p['price'])) ?></span>
                <?php if ($onSale): ?><span class="was fs-5"><?= e(money($p['compare_price'])) ?></span><?php endif; ?>
            </div>
            <?php if ($p['short_description']): ?><p class="text-muted-2"><?= e($p['short_description']) ?></p><?php endif; ?>

            <?php if ($p['puffs'] || $p['nicotine']): ?>
            <ul class="spec-pills list-unstyled">
                <?php if ($p['puffs']): ?><li><i class="bi bi-wind"></i> <?= number_format((int)$p['puffs']) ?> puffs</li><?php endif; ?>
                <?php if ($p['nicotine']): ?><li><i class="bi bi-droplet"></i> <?= e($p['nicotine']) ?></li><?php endif; ?>
                <?php if ($p['battery']): ?><li><i class="bi bi-battery-charging"></i> <?= e($p['battery']) ?></li><?php endif; ?>
            </ul>
            <?php endif; ?>

            <?php if ($inStock): ?>
            <form action="<?= e(url('cart/add')) ?>" method="post" id="addForm" class="mt-3">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <?php if ($hasVariants): ?>
                <label class="form-label fw-semibold d-block">Choose option / flavor <span class="text-accent">*</span></label>
                <div class="variant-grid" role="radiogroup">
                    <?php foreach ($variants as $v): $out = (int)$v['stock'] <= 0; ?>
                    <label class="variant <?= $out ? 'is-out' : '' ?>">
                        <input type="radio" name="variant_id" value="<?= (int)$v['id'] ?>" data-price="<?= e($v['price'] !== null && (float)$v['price'] > 0 ? (string)$v['price'] : (string)$p['price']) ?>" <?= $out ? 'disabled' : '' ?> required>
                        <span><?= e($v['name']) ?><?= $out ? ' <em>(sold out)</em>' : '' ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="d-flex flex-wrap gap-3 align-items-end mt-4">
                    <div>
                        <label class="form-label fw-semibold" for="qty">Quantity</label>
                        <div class="qty-box">
                            <button type="button" class="qty-btn" data-d="-1" aria-label="Decrease">&minus;</button>
                            <input type="number" id="qty" name="qty" value="1" min="1" max="20" class="form-control">
                            <button type="button" class="qty-btn" data-d="1" aria-label="Increase">+</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-accent btn-lg flex-grow-1" id="addBtn"><i class="bi bi-bag-plus"></i> Add to cart</button>
                </div>
                <button type="submit" name="buy_now" value="1" class="btn btn-outline-light w-100 mt-2">Buy it now</button>
            </form>
            <?php else: ?>
            <div class="alert alert-secondary mt-3"><i class="bi bi-x-circle"></i> This product is currently sold out.<?php if ($tg): ?> Message us on <a href="https://t.me/<?= e($tg) ?>" target="_blank" rel="noopener">Telegram</a> to be notified when it returns.<?php endif; ?></div>
            <?php endif; ?>

            <div class="mini-trust mt-4">
                <div><i class="bi bi-truck"></i> Ships from the US &middot; <?= e(setting('delivery_note', 'Orders ship in 1-2 business days')) ?></div>
                <div><i class="bi bi-person-badge"></i> <?= $minAge ?>+ only &middot; adult signature required</div>
                <?php if ((float)setting('free_shipping_over', '100') > 0): ?><div><i class="bi bi-gift"></i> Free shipping over <?= e(money(setting('free_shipping_over', '100'))) ?></div><?php endif; ?>
            </div>
            <div class="nic-warning mt-3"><strong>WARNING:</strong> <?= e(setting('nicotine_warning', 'This product contains nicotine. Nicotine is an addictive chemical.')) ?></div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-7">
            <h2 class="h4">Description</h2>
            <div class="rich"><?= $p['description'] ? clean_html($p['description']) : '<p class="text-muted-2">No description yet.</p>' ?></div>
        </div>
        <div class="col-lg-5">
            <?php if ($specs): ?>
            <h2 class="h4">Specifications</h2>
            <table class="table spec-table align-middle"><tbody>
                <?php foreach ($specs as $k => $v): ?><tr><th scope="row"><?= e($k) ?></th><td><?= e($v) ?></td></tr><?php endforeach; ?>
            </tbody></table>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($related): ?>
    <section class="section">
        <div class="section-head"><h2>You may also like</h2></div>
        <div class="row g-3 row-cols-2 row-cols-lg-4">
            <?php foreach ($related as $r): ?><div class="col"><?php partial('shop/_card', ['p' => $r]); ?></div><?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

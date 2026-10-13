<?php
/** Home page */
?>
<?php if ($heroSlides): ?>
<section class="hero-wrap">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5500">
        <?php if (count($heroSlides) > 1): ?>
        <div class="carousel-indicators">
            <?php foreach ($heroSlides as $i => $s): ?><button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?>
        </div>
        <?php endif; ?>
        <div class="carousel-inner">
            <?php foreach ($heroSlides as $i => $s): ?>
            <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                <div class="hero-slide" style="<?= $s['image'] ? 'background-image:url(' . e(upload_url($s['image'])) . ')' : '' ?>">
                    <div class="hero-overlay"></div>
                    <div class="container position-relative">
                        <div class="hero-copy">
                            <?php if ($s['subtitle']): ?><span class="hero-kicker"><?= e($s['subtitle']) ?></span><?php endif; ?>
                            <?php if ($s['title']): ?><h1 class="hero-title"><?= e($s['title']) ?></h1><?php endif; ?>
                            <?php if ($s['link'] && $s['button_text']): ?><a href="<?= e(preg_match('#^https?://#', $s['link']) ? $s['link'] : url(ltrim($s['link'], '/'))) ?>" class="btn btn-accent btn-lg mt-3"><?= e($s['button_text']) ?> <i class="bi bi-arrow-right"></i></a><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($heroSlides) > 1): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span><span class="visually-hidden">Previous</span></button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span></button>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<section class="trust-strip">
    <div class="container">
        <div class="row g-3 text-center text-md-start">
            <div class="col-6 col-md-3"><div class="trust"><i class="bi bi-truck"></i><div><strong>Fast US shipping</strong><span><?= e(setting('delivery_note', 'Orders ship in 1-2 business days')) ?></span></div></div></div>
            <div class="col-6 col-md-3"><div class="trust"><i class="bi bi-patch-check"></i><div><strong>Authentic products</strong><span>Sourced from trusted suppliers</span></div></div></div>
            <div class="col-6 col-md-3"><div class="trust"><i class="bi bi-person-badge"></i><div><strong><?= (int)setting('min_age', '21') ?>+ only</strong><span>Adult signature on delivery</span></div></div></div>
            <div class="col-6 col-md-3"><div class="trust"><i class="bi bi-headset"></i><div><strong>Real support</strong><span>Telegram, email &amp; phone</span></div></div></div>
        </div>
    </div>
</section>

<?php if ($categories): ?>
<section class="container section">
    <div class="section-head"><h2>Shop by category</h2><a href="<?= e(url('shop')) ?>" class="link-accent">View all <i class="bi bi-arrow-right"></i></a></div>
    <div class="row g-3">
        <?php foreach ($categories as $c): ?>
        <div class="col-6 col-md-3">
            <a class="cat-tile" href="<?= e(url('category/' . $c['slug'])) ?>" <?= $c['image'] ? 'style="background-image:url(' . e(upload_url($c['image'])) . ')"' : '' ?>>
                <span class="cat-name"><?= e($c['name']) ?></span>
                <span class="cat-count"><?= (int)$c['product_count'] ?> products</span>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($bestsellers): ?>
<section class="container section">
    <div class="section-head"><h2>Best sellers</h2><a href="<?= e(url('collection/best-selling')) ?>" class="link-accent">View all <i class="bi bi-arrow-right"></i></a></div>
    <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach (array_slice($bestsellers, 0, 8) as $p): ?><div class="col"><?php partial('shop/_card', ['p' => $p]); ?></div><?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($promoBanners): ?>
<section class="container section">
    <div class="row g-3">
        <?php foreach ($promoBanners as $b): ?>
        <div class="col-md-<?= 12 / max(1, count($promoBanners)) ?>">
            <a class="promo-tile" href="<?= e(preg_match('#^https?://#', (string)$b['link']) ? $b['link'] : url(ltrim((string)$b['link'], '/'))) ?>" style="<?= $b['image'] ? 'background-image:url(' . e(upload_url($b['image'])) . ')' : '' ?>">
                <div class="promo-overlay"></div>
                <div class="promo-copy"><small><?= e($b['subtitle']) ?></small><h3><?= e($b['title']) ?></h3><?php if ($b['button_text']): ?><span class="btn btn-sm btn-light"><?= e($b['button_text']) ?></span><?php endif; ?></div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($newArrivals): ?>
<section class="container section">
    <div class="section-head"><h2>New arrivals</h2><a href="<?= e(url('collection/new-arrivals')) ?>" class="link-accent">View all <i class="bi bi-arrow-right"></i></a></div>
    <div class="row g-3 row-cols-2 row-cols-md-3 row-cols-lg-4">
        <?php foreach (array_slice($newArrivals, 0, 8) as $p): ?><div class="col"><?php partial('shop/_card', ['p' => $p]); ?></div><?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($featured): $f = $featured[0]; $fOn = $f['compare_price'] !== null && (float)$f['compare_price'] > (float)$f['price']; ?>
<section class="container section">
    <div class="spotlight">
        <div class="row g-0 align-items-center">
            <div class="col-md-5"><a href="<?= e(url('product/' . $f['slug'])) ?>"><img src="<?= e(upload_url($f['image'])) ?>" alt="<?= e($f['name']) ?>" class="spot-img" loading="lazy"></a></div>
            <div class="col-md-7 p-4 p-lg-5">
                <span class="hero-kicker">Top pick this month</span>
                <h2 class="mt-2"><?= e($f['name']) ?></h2>
                <?php if ($f['short_description']): ?><p class="text-muted-2"><?= e($f['short_description']) ?></p><?php endif; ?>
                <ul class="spec-pills list-unstyled">
                    <?php if ($f['puffs']): ?><li><i class="bi bi-wind"></i> <?= number_format((int)$f['puffs']) ?> puffs</li><?php endif; ?>
                    <?php if ($f['nicotine']): ?><li><i class="bi bi-droplet"></i> <?= e($f['nicotine']) ?> nicotine</li><?php endif; ?>
                    <?php if ($f['battery']): ?><li><i class="bi bi-battery-charging"></i> <?= e($f['battery']) ?></li><?php endif; ?>
                    <?php if ($f['capacity']): ?><li><i class="bi bi-beaker"></i> <?= e($f['capacity']) ?></li><?php endif; ?>
                </ul>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="p-price"><span class="now fs-3"><?= e(money($f['price'])) ?></span><?php if ($fOn): ?><span class="was"><?= e(money($f['compare_price'])) ?></span><?php endif; ?></div>
                    <a href="<?= e(url('product/' . $f['slug'])) ?>" class="btn btn-accent btn-lg">Shop now</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($brands): ?>
<section class="container section">
    <div class="section-head"><h2>Shop by brand</h2></div>
    <div class="brand-cloud">
        <?php foreach ($brands as $b): ?>
        <a href="<?= e(url('brand/' . $b['slug'])) ?>" class="brand-chip"><?= $b['logo'] ? '<img src="' . e(upload_url($b['logo'])) . '" alt="' . e($b['name']) . '" height="26">' : e($b['name']) ?></a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($faqs): ?>
<section class="container section">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="section-head"><h2>Questions?</h2></div>
            <p class="text-muted-2">Quick answers about ordering, shipping and payment. Need more help? Our team is one message away.</p>
            <a href="<?= e(url('contact')) ?>" class="btn btn-outline-light">Contact support</a>
        </div>
        <div class="col-lg-8">
            <div class="accordion faq-acc" id="homeFaq">
                <?php foreach ($faqs as $i => $q): ?>
                <div class="accordion-item">
                    <h3 class="accordion-header"><button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#hf<?= (int)$q['id'] ?>"><?= e($q['question']) ?></button></h3>
                    <div id="hf<?= (int)$q['id'] ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#homeFaq"><div class="accordion-body"><?= nl2br(e($q['answer'])) ?></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($posts): ?>
<section class="container section">
    <div class="section-head"><h2>From the blog</h2><a href="<?= e(url('blog')) ?>" class="link-accent">All articles <i class="bi bi-arrow-right"></i></a></div>
    <div class="row g-3">
        <?php foreach ($posts as $post): partial('shop/_post_card', ['post' => $post]); endforeach; ?>
    </div>
</section>
<?php endif; ?>

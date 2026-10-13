<div class="container py-4" style="max-width:820px">
    <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="<?= e(url('')) ?>">Home</a></li><li class="breadcrumb-item"><a href="<?= e(url('blog')) ?>">Blog</a></li><li class="breadcrumb-item active"><?= e($post['title']) ?></li></ol></nav>
    <h1 class="h2"><?= e($post['title']) ?></h1>
    <p class="text-muted-2"><?= e(date('F j, Y', strtotime($post['created_at']))) ?></p>
    <?php if ($post['image']): ?><img src="<?= e(upload_url($post['image'])) ?>" alt="<?= e($post['title']) ?>" class="img-fluid rounded-4 mb-4 w-100"><?php endif; ?>
    <div class="rich"><?= clean_html($post['body']) ?></div>
    <div class="nic-warning mt-4"><strong>WARNING:</strong> <?= e(setting('nicotine_warning', 'This product contains nicotine. Nicotine is an addictive chemical.')) ?></div>
</div>
<?php if ($more): ?>
<div class="container section"><div class="section-head"><h2>More articles</h2></div><div class="row g-3"><?php foreach ($more as $post): partial('shop/_post_card', ['post' => $post]); endforeach; ?></div></div>
<?php endif; ?>

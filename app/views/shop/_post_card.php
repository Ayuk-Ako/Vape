<div class="col-md-4">
    <a class="post-card" href="<?= e(url('blog/' . $post['slug'])) ?>">
        <div class="post-img" style="<?= $post['image'] ? 'background-image:url(' . e(upload_url($post['image'])) . ')' : '' ?>"></div>
        <div class="post-body">
            <small class="text-muted-2"><?= e(date('M j, Y', strtotime($post['created_at']))) ?></small>
            <h3><?= e($post['title']) ?></h3>
            <p class="text-muted-2 mb-0"><?= e(mb_substr((string)$post['excerpt'], 0, 120)) ?></p>
        </div>
    </a>
</div>

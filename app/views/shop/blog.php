<div class="page-hero"><div class="container"><h1>Blog</h1><p class="text-muted-2 mb-0">Guides, reviews and news from the team.</p></div></div>
<div class="container">
    <?php if ($posts): ?>
    <div class="row g-3"><?php foreach ($posts as $post): partial('shop/_post_card', ['post' => $post]); endforeach; ?></div>
    <?php partial('shop/_pagination', ['page' => $page, 'pages' => $pages]); ?>
    <?php else: ?>
    <div class="empty-state text-center py-5"><i class="bi bi-journal-text display-4"></i><h3 class="mt-3">No articles yet</h3></div>
    <?php endif; ?>
</div>

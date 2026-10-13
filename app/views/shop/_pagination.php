<?php
/** @var int $page @var int $pages */
if ($pages <= 1) return;
$start = max(1, $page - 2);
$end = min($pages, $page + 2);
?>
<nav aria-label="Pagination" class="mt-4">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(page_url_with(['page' => $page - 1])) ?>">&laquo;</a></li>
        <?php if ($start > 1): ?><li class="page-item"><a class="page-link" href="<?= e(page_url_with(['page' => 1])) ?>">1</a></li><?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; endif; ?>
        <?php for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="<?= e(page_url_with(['page' => $i])) ?>"><?= $i ?></a></li>
        <?php endfor; ?>
        <?php if ($end < $pages): ?><?php if ($end < $pages - 1): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?><li class="page-item"><a class="page-link" href="<?= e(page_url_with(['page' => $pages])) ?>"><?= $pages ?></a></li><?php endif; ?>
        <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= e(page_url_with(['page' => $page + 1])) ?>">&raquo;</a></li>
    </ul>
</nav>

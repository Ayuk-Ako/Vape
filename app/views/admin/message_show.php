<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0"><?= e($m['subject'] ?: '(no subject)') ?></h1><a href="<?= e(url('admin/messages')) ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a></div>
<div class="card card-body" style="max-width:780px">
    <div class="mb-3 small text-secondary">From <strong class="text-body"><?= e($m['name']) ?></strong> &lt;<a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>&gt; &middot; <?= e(date('M j, Y H:i', strtotime($m['created_at']))) ?></div>
    <div style="white-space:pre-wrap"><?= e($m['message']) ?></div>
    <div class="mt-4"><a class="btn btn-primary" href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . $m['subject']) ?>"><i class="bi bi-reply"></i> Reply by email</a></div>
</div>

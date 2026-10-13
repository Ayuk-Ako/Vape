<div class="page-hero"><div class="container"><h1>Frequently asked questions</h1></div></div>
<div class="container" style="max-width:860px">
    <?php if ($faqs): ?>
    <div class="accordion faq-acc" id="faqAcc">
        <?php foreach ($faqs as $i => $q): ?>
        <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button <?= $i ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#f<?= (int)$q['id'] ?>"><?= e($q['question']) ?></button></h2>
            <div id="f<?= (int)$q['id'] ?>" class="accordion-collapse collapse <?= $i ? '' : 'show' ?>" data-bs-parent="#faqAcc"><div class="accordion-body"><?= nl2br(e($q['answer'])) ?></div></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?><p class="text-muted-2">No questions yet.</p><?php endif; ?>
    <div class="text-center mt-4"><p class="text-muted-2">Still need help?</p><a href="<?= e(url('contact')) ?>" class="btn btn-accent">Contact us</a></div>
</div>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(fn($q) => ['@type' => 'Question', 'name' => $q['question'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q['answer']]], $faqs)], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>

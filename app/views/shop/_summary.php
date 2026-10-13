<div class="summary-box">
    <h2 class="h5 mb-3">Order summary</h2>
    <div class="d-flex justify-content-between"><span class="text-muted-2">Subtotal</span><span><?= e(money($totals['subtotal'])) ?></span></div>
    <div class="d-flex justify-content-between"><span class="text-muted-2">Shipping</span><span><?= $totals['shipping'] > 0 ? e(money($totals['shipping'])) : 'Free' ?></span></div>
    <?php if ($totals['tax'] > 0): ?><div class="d-flex justify-content-between"><span class="text-muted-2">Tax</span><span><?= e(money($totals['tax'])) ?></span></div><?php endif; ?>
    <hr>
    <div class="d-flex justify-content-between fw-bold fs-5"><span>Total</span><span><?= e(money($totals['total'])) ?></span></div>
</div>

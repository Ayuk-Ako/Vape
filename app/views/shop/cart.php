<?php /** @var array $items $totals */ ?>
<div class="page-hero"><div class="container"><h1>Your cart</h1></div></div>
<div class="container">
<?php if (!$items['lines']): ?>
    <div class="empty-state text-center py-5">
        <i class="bi bi-bag display-4"></i>
        <h3 class="mt-3">Your cart is empty</h3>
        <p class="text-muted-2">Looks like you haven't added anything yet.</p>
        <a href="<?= e(url('shop')) ?>" class="btn btn-accent">Start shopping</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <form action="<?= e(url('cart/update')) ?>" method="post" id="cartForm">
                <?= csrf_field() ?>
                <div class="cart-lines">
                    <?php foreach ($items['lines'] as $l): ?>
                    <div class="cart-line">
                        <a href="<?= e(url('product/' . $l['slug'])) ?>"><img src="<?= e(upload_url($l['image'])) ?>" alt="" class="cart-img"></a>
                        <div class="flex-grow-1">
                            <a class="cart-name" href="<?= e(url('product/' . $l['slug'])) ?>"><?= e($l['name']) ?></a>
                            <?php if ($l['variant']): ?><div class="small text-muted-2"><?= e($l['variant']) ?></div><?php endif; ?>
                            <div class="small text-muted-2"><?= e(money($l['price'])) ?> each</div>
                            <button type="submit" formaction="<?= e(url('cart/remove')) ?>" name="key" value="<?= e($l['key']) ?>" class="btn btn-link btn-sm p-0 text-danger">Remove</button>
                        </div>
                        <div class="qty-box sm">
                            <input type="number" name="qty[<?= e($l['key']) ?>]" value="<?= (int)$l['qty'] ?>" min="0" max="<?= (int)min(20, $l['stock']) ?>" class="form-control form-control-sm" aria-label="Quantity">
                        </div>
                        <div class="cart-total"><?= e(money($l['total'])) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                    <a href="<?= e(url('shop')) ?>" class="btn btn-outline-light"><i class="bi bi-arrow-left"></i> Continue shopping</a>
                    <button type="submit" class="btn btn-outline-light">Update cart</button>
                </div>
            </form>
        </div>
        <div class="col-lg-4">
            <?php partial('shop/_summary', ['totals' => $totals]); ?>
            <a href="<?= e(url('checkout')) ?>" class="btn btn-accent btn-lg w-100 mt-3">Proceed to checkout</a>
            <?php $free = (float)setting('free_shipping_over', '100'); if ($free > 0 && $totals['subtotal'] < $free): ?>
            <p class="small text-muted-2 mt-3 text-center">Add <strong><?= e(money($free - $totals['subtotal'])) ?></strong> more for free shipping.</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</div>

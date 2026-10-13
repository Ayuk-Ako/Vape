<?php
class CartPageController extends Controller
{
    public function index(): void
    {
        $items = Cart::items();
        $this->render('shop/cart', [
            'title'  => 'Your Cart | ' . setting('store_name', 'Store'),
            'items'  => $items,
            'totals' => Cart::totals($items),
        ]);
    }

    public function add(): void
    {
        $res = Cart::add($this->int('product_id'), $this->int('variant_id'), max(1, $this->int('qty', 1)));
        if ($this->isAjax()) {
            $res['count'] = Cart::count();
            $this->json($res, $res['ok'] ? 200 : 422);
        }
        flash($res['ok'] ? 'success' : 'danger', $res['msg']);
        if ($res['ok'] && ($_POST['buy_now'] ?? '') === '1') {
            redirect('checkout');
        }
        $back = $_SERVER['HTTP_REFERER'] ?? '';
        redirect($res['ok'] ? 'cart' : ($back ?: 'cart'));
    }

    public function update(): void
    {
        foreach (($_POST['qty'] ?? []) as $key => $qty) {
            Cart::setQty((string)$key, (int)$qty);
        }
        flash('success', 'Cart updated.');
        redirect('cart');
    }

    public function remove(): void
    {
        Cart::remove((string)($_POST['key'] ?? ''));
        flash('success', 'Item removed.');
        redirect('cart');
    }
}

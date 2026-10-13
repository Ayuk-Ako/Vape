<?php
class TrackController extends Controller
{
    public function index(): void
    {
        $this->render('shop/track', ['title' => 'Track your order | ' . setting('store_name', 'Store'), 'order' => null, 'lines' => []]);
    }

    public function lookup(): void
    {
        $number = strtoupper(trim((string)($_POST['order_number'] ?? '')));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $order = null;
        $lines = [];
        if ($number !== '' && $email !== '') {
            $order = Database::one('SELECT * FROM orders WHERE order_number=? AND customer_email=?', [$number, $email]);
            if ($order) {
                $lines = Database::all('SELECT * FROM order_items WHERE order_id=?', [$order['id']]);
            } else {
                flash('danger', 'We could not find an order with those details.');
            }
        } else {
            flash('danger', 'Please enter your order number and email.');
        }
        $this->render('shop/track', ['title' => 'Track your order | ' . setting('store_name', 'Store'), 'order' => $order, 'lines' => $lines]);
    }
}

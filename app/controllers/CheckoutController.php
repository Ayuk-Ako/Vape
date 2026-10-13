<?php
class CheckoutController extends Controller
{
    /** Parse "Label | Instructions" lines from settings into [label => instructions]. */
    public static function paymentMethods(): array
    {
        $out = [];
        foreach (preg_split('/\R/', (string)setting('payment_methods', '')) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            [$label, $info] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
            if ($label !== '') $out[$label] = $info;
        }
        return $out;
    }

    public function index(): void
    {
        $items = Cart::items();
        if (!$items['lines']) {
            flash('warning', 'Your cart is empty.');
            redirect('cart');
        }
        $this->render('shop/checkout', [
            'title'    => 'Checkout | ' . setting('store_name', 'Store'),
            'items'    => $items,
            'totals'   => Cart::totals($items),
            'methods'  => self::paymentMethods(),
            'states'   => us_states(),
        ]);
    }

    public function place(): void
    {
        $items = Cart::items();
        if (!$items['lines']) {
            redirect('cart');
        }
        $methods = self::paymentMethods();
        $states = us_states();
        $minAge = (int)setting('min_age', '21');

        $d = [
            'customer_name'   => $this->input('name'),
            'customer_email'  => strtolower($this->input('email')),
            'customer_phone'  => $this->input('phone'),
            'telegram_handle' => ltrim($this->input('telegram'), '@'),
            'address1'        => $this->input('address1'),
            'address2'        => $this->input('address2'),
            'city'            => $this->input('city'),
            'state'           => strtoupper($this->input('state')),
            'zip'             => $this->input('zip'),
            'notes'           => $this->input('notes'),
            'payment_method'  => $this->input('payment_method'),
        ];
        keep_old($_POST);

        $errors = [];
        if (!empty($_POST['website'])) { // honeypot
            redirect('');
        }
        if (mb_strlen($d['customer_name']) < 2) $errors[] = 'Please enter your full name.';
        if (!filter_var($d['customer_email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (strlen(preg_replace('/\D/', '', $d['customer_phone'])) < 7) $errors[] = 'Please enter a valid phone number.';
        if ($d['address1'] === '') $errors[] = 'Please enter your street address.';
        if ($d['city'] === '') $errors[] = 'Please enter your city.';
        if (!isset($states[$d['state']])) $errors[] = 'Please select your state.';
        if (!preg_match('/^\d{5}(-\d{4})?$/', $d['zip'])) $errors[] = 'Please enter a valid ZIP code.';
        if (!isset($methods[$d['payment_method']])) $errors[] = 'Please choose a payment method.';
        if (empty($_POST['age_confirm'])) $errors[] = "You must confirm that you are at least {$minAge} years old.";
        if (empty($_POST['terms'])) $errors[] = 'You must accept the Terms of Service.';

        $restricted = array_filter(array_map('trim', explode(',', strtoupper((string)setting('restricted_states', '')))));
        if ($d['state'] !== '' && in_array($d['state'], $restricted, true)) {
            $errors[] = 'Sorry, we are unable to ship to ' . ($states[$d['state']] ?? $d['state']) . ' due to local regulations.';
        }

        $minOrder = (float)setting('min_order', '0');
        if ($minOrder > 0 && $items['subtotal'] < $minOrder) {
            $errors[] = 'The minimum order is ' . money($minOrder) . '.';
        }

        if ($errors) {
            foreach ($errors as $err) flash('danger', $err);
            redirect('checkout');
        }

        $totals = Cart::totals($items);
        $orderNumber = null;
        $orderId = 0;

        try {
            Database::begin();
            // Reserve stock atomically
            foreach ($items['lines'] as $l) {
                if ($l['variant_id'] > 0) {
                    $n = Database::exec('UPDATE product_variants SET stock=stock-? WHERE id=? AND stock>=?', [$l['qty'], $l['variant_id'], $l['qty']]);
                } else {
                    $n = Database::exec('UPDATE products SET stock=stock-? WHERE id=? AND stock>=?', [$l['qty'], $l['product_id'], $l['qty']]);
                }
                if ($n < 1) {
                    throw new RuntimeException('Sorry, "' . $l['name'] . ($l['variant'] ? ' - ' . $l['variant'] : '') . '" just went out of stock or has limited quantity left. Please update your cart.');
                }
            }
            do {
                $orderNumber = 'VH' . date('ymd') . random_int(1000, 9999);
            } while (Database::val('SELECT COUNT(*) FROM orders WHERE order_number=?', [$orderNumber]) > 0);

            $orderId = Database::insert('orders', $d + [
                'order_number'  => $orderNumber,
                'country'       => 'United States',
                'subtotal'      => $totals['subtotal'],
                'shipping'      => $totals['shipping'],
                'tax'           => $totals['tax'],
                'total'         => $totals['total'],
                'status'        => 'pending',
                'payment_status'=> 'unpaid',
                'age_confirmed' => 1,
                'ip'            => client_ip(),
            ]);
            foreach ($items['lines'] as $l) {
                Database::insert('order_items', [
                    'order_id'     => $orderId,
                    'product_id'   => $l['product_id'],
                    'variant_id'   => $l['variant_id'] ?: null,
                    'product_name' => $l['name'],
                    'variant_name' => $l['variant'],
                    'price'        => $l['price'],
                    'qty'          => $l['qty'],
                    'line_total'   => $l['total'],
                ]);
            }
            Database::commit();
        } catch (Throwable $e) {
            Database::rollback();
            flash('danger', $e instanceof RuntimeException ? $e->getMessage() : 'We could not place your order. Please try again.');
            if (!($e instanceof RuntimeException)) {
                @file_put_contents(BASE_DIR . '/storage/php-error.log', '[' . date('c') . '] checkout: ' . $e . PHP_EOL, FILE_APPEND);
            }
            redirect('cart');
        }

        Cart::clear();
        clear_old();
        $_SESSION['last_order'] = $orderNumber;

        $this->sendEmails($orderId);
        redirect('order/success/' . $orderNumber);
    }

    private function sendEmails(int $orderId): void
    {
        $order = Database::one('SELECT * FROM orders WHERE id=?', [$orderId]);
        $lines = Database::all('SELECT * FROM order_items WHERE order_id=?', [$orderId]);
        $methods = self::paymentMethods();
        $data = ['order' => $order, 'lines' => $lines, 'paymentInfo' => $methods[$order['payment_method']] ?? ''];

        $notify = setting('order_notify_email', setting('store_email', ''));
        if ($notify) {
            Mailer::send($notify, 'New order ' . $order['order_number'] . ' - ' . money($order['total']), Mailer::render('order_admin', $data), $order['customer_email']);
        }
        Mailer::send($order['customer_email'], 'Order received ' . $order['order_number'], Mailer::render('order_customer', $data));
    }

    public function success(string $number): void
    {
        $order = Database::one('SELECT * FROM orders WHERE order_number=?', [$number]);
        // Only the browser session that placed the order may view the confirmation page.
        if (!$order || ($_SESSION['last_order'] ?? '') !== $number) {
            redirect('track');
        }
        $lines = Database::all('SELECT * FROM order_items WHERE order_id=?', [$order['id']]);
        $methods = self::paymentMethods();
        $this->render('shop/order_success', [
            'title'       => 'Order received | ' . setting('store_name', 'Store'),
            'order'       => $order,
            'lines'       => $lines,
            'paymentInfo' => $methods[$order['payment_method']] ?? '',
        ]);
    }
}

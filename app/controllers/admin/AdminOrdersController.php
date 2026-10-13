<?php
class AdminOrdersController extends AdminController
{
    public function index(): void
    {
        $status = trim((string)($_GET['status'] ?? ''));
        $q = trim((string)($_GET['q'] ?? ''));
        $where = ['1=1'];
        $params = [];
        if ($status !== '' && isset(order_statuses()[$status])) { $where[] = 'status=?'; $params[] = $status; }
        if ($q !== '') {
            $where[] = '(order_number LIKE ? OR customer_name LIKE ? OR customer_email LIKE ? OR customer_phone LIKE ?)';
            array_push($params, "%$q%", "%$q%", "%$q%", "%$q%");
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = 25;
        $w = implode(' AND ', $where);
        $total = (int)Database::val("SELECT COUNT(*) FROM orders WHERE $w", $params);
        $pages = max(1, (int)ceil($total / $per));
        $page = min($page, $pages);
        $rows = Database::all("SELECT * FROM orders WHERE $w ORDER BY id DESC LIMIT $per OFFSET " . (($page - 1) * $per), $params);
        $this->view('admin/orders_index', ['title' => 'Orders', 'rows' => $rows, 'status' => $status, 'q' => $q, 'page' => $page, 'pages' => $pages, 'total' => $total]);
    }

    public function show(string $id): void
    {
        $order = Database::one('SELECT * FROM orders WHERE id=?', [(int)$id]);
        if (!$order) { flash('danger', 'Order not found.'); redirect('admin/orders'); }
        $lines = Database::all('SELECT * FROM order_items WHERE order_id=?', [$order['id']]);
        $this->view('admin/order_show', ['title' => 'Order ' . $order['order_number'], 'order' => $order, 'lines' => $lines]);
    }

    public function update(string $id): void
    {
        $order = Database::one('SELECT * FROM orders WHERE id=?', [(int)$id]);
        if (!$order) redirect('admin/orders');

        $status = (string)($_POST['status'] ?? $order['status']);
        if (!isset(order_statuses()[$status])) $status = $order['status'];
        $pay = ($_POST['payment_status'] ?? 'unpaid') === 'paid' ? 'paid' : 'unpaid';
        $tracking = trim((string)($_POST['tracking_number'] ?? '')) ?: null;
        $note = trim((string)($_POST['admin_note'] ?? '')) ?: null;

        // Restock when an order is cancelled (once), re-reserve if it is reopened.
        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            $this->adjustStock((int)$order['id'], +1);
        } elseif ($order['status'] === 'cancelled' && $status !== 'cancelled') {
            $this->adjustStock((int)$order['id'], -1);
        }

        Database::update('orders', ['status' => $status, 'payment_status' => $pay, 'tracking_number' => $tracking, 'admin_note' => $note], 'id=?', [$order['id']]);

        $notified = false;
        if (!empty($_POST['notify']) && ($status !== $order['status'] || $tracking !== $order['tracking_number'])) {
            $fresh = Database::one('SELECT * FROM orders WHERE id=?', [$order['id']]);
            $lines = Database::all('SELECT * FROM order_items WHERE order_id=?', [$order['id']]);
            $notified = Mailer::send($order['customer_email'], 'Order ' . $order['order_number'] . ' - ' . ucfirst($status), Mailer::render('order_status', ['order' => $fresh, 'lines' => $lines]));
        }
        flash('success', 'Order updated.' . (!empty($_POST['notify']) ? ($notified ? ' Customer notified by email.' : ' (Email could not be sent - check mail settings.)') : ''));
        redirect('admin/orders/' . $order['id']);
    }

    private function adjustStock(int $orderId, int $dir): void
    {
        foreach (Database::all('SELECT * FROM order_items WHERE order_id=?', [$orderId]) as $it) {
            $qty = (int)$it['qty'] * $dir;
            if ($it['variant_id']) {
                Database::exec('UPDATE product_variants SET stock = GREATEST(0, stock + ?) WHERE id=?', [$qty, $it['variant_id']]);
            } elseif ($it['product_id']) {
                Database::exec('UPDATE products SET stock = GREATEST(0, stock + ?) WHERE id=?', [$qty, $it['product_id']]);
            }
        }
    }

    public function delete(string $id): void
    {
        $order = Database::one('SELECT * FROM orders WHERE id=?', [(int)$id]);
        if ($order) {
            if (!in_array($order['status'], ['cancelled', 'delivered'], true)) {
                $this->adjustStock((int)$order['id'], +1);
            }
            Database::exec('DELETE FROM orders WHERE id=?', [$order['id']]);
            flash('success', 'Order deleted.');
        }
        redirect('admin/orders');
    }
}

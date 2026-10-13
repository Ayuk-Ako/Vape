<?php
class AdminDashboardController extends AdminController
{
    public function index(): void
    {
        $stats = [
            'orders_total'   => (int)Database::val('SELECT COUNT(*) FROM orders'),
            'orders_pending' => (int)Database::val("SELECT COUNT(*) FROM orders WHERE status='pending'"),
            'revenue'        => (float)Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='paid' AND status<>'cancelled'"),
            'revenue_month'  => (float)Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='paid' AND status<>'cancelled' AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"),
            'awaiting'       => (float)Database::val("SELECT COALESCE(SUM(total),0) FROM orders WHERE payment_status='unpaid' AND status<>'cancelled'"),
            'products'       => (int)Database::val('SELECT COUNT(*) FROM products'),
            'messages'       => (int)Database::val('SELECT COUNT(*) FROM messages WHERE is_read=0'),
            'subscribers'    => (int)Database::val('SELECT COUNT(*) FROM subscribers'),
        ];
        $recent = Database::all('SELECT * FROM orders ORDER BY id DESC LIMIT 8');
        $lowStock = Database::all("SELECT p.id, p.name, p.stock, (SELECT COUNT(*) FROM product_variants v WHERE v.product_id=p.id) AS vc FROM products p WHERE p.is_active=1 AND p.stock<=3 AND NOT EXISTS (SELECT 1 FROM product_variants v WHERE v.product_id=p.id AND v.is_active=1) ORDER BY p.stock ASC LIMIT 6");
        $lowVariants = Database::all('SELECT v.id, v.name AS variant, v.stock, p.id AS pid, p.name FROM product_variants v JOIN products p ON p.id=v.product_id WHERE v.is_active=1 AND p.is_active=1 AND v.stock<=3 ORDER BY v.stock ASC LIMIT 8');
        $this->view('admin/dashboard', ['title' => 'Dashboard', 'stats' => $stats, 'recent' => $recent, 'lowStock' => $lowStock, 'lowVariants' => $lowVariants]);
    }
}

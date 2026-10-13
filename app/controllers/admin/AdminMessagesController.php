<?php
class AdminMessagesController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/messages_index', ['title' => 'Messages', 'rows' => Database::all('SELECT * FROM messages ORDER BY id DESC LIMIT 300')]);
    }

    public function show(string $id): void
    {
        $m = Database::one('SELECT * FROM messages WHERE id=?', [(int)$id]);
        if (!$m) redirect('admin/messages');
        Database::exec('UPDATE messages SET is_read=1 WHERE id=?', [$m['id']]);
        $this->view('admin/message_show', ['title' => 'Message', 'm' => $m]);
    }

    public function delete(string $id): void
    {
        Database::exec('DELETE FROM messages WHERE id=?', [(int)$id]);
        flash('success', 'Message deleted.');
        redirect('admin/messages');
    }

    public function subscribers(): void
    {
        $this->view('admin/subscribers', ['title' => 'Subscribers', 'rows' => Database::all('SELECT * FROM subscribers ORDER BY id DESC LIMIT 1000')]);
    }

    public function exportSubscribers(): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="subscribers-' . date('Y-m-d') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['email', 'subscribed_at']);
        foreach (Database::all('SELECT email, created_at FROM subscribers ORDER BY id DESC') as $r) {
            // prefix formula-like values to prevent CSV injection in spreadsheets
            $email = preg_match('/^[=+\-@]/', $r['email']) ? "'" . $r['email'] : $r['email'];
            fputcsv($out, [$email, $r['created_at']]);
        }
        fclose($out);
        exit;
    }
}

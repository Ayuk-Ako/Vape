<?php
class ContentController extends Controller
{
    public function faq(): void
    {
        $this->render('shop/faq', [
            'title' => 'FAQ | ' . setting('store_name', 'Store'),
            'faqs'  => Database::all('SELECT * FROM faqs WHERE is_active=1 ORDER BY sort_order, id'),
        ]);
    }

    public function page(string $slug): void
    {
        $page = Database::one('SELECT * FROM pages WHERE slug=? AND is_active=1', [$slug]);
        if (!$page) $this->notFound();
        $this->render('shop/page', [
            'title' => $page['title'] . ' | ' . setting('store_name', 'Store'),
            'metaDesc' => $page['meta_description'],
            'page' => $page,
        ]);
    }

    public function contact(): void
    {
        $this->render('shop/contact', ['title' => 'Contact us | ' . setting('store_name', 'Store')]);
    }

    public function contactSend(): void
    {
        keep_old($_POST);
        if (!empty($_POST['website'])) { redirect('contact'); } // honeypot
        $name = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $subject = trim((string)($_POST['subject'] ?? ''));
        $message = trim((string)($_POST['message'] ?? ''));

        if (mb_strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 5) {
            flash('danger', 'Please fill in your name, a valid email and a message.');
            redirect('contact');
        }
        // Basic flood control: max 5 messages / hour / session
        $_SESSION['msg_times'] = array_values(array_filter($_SESSION['msg_times'] ?? [], fn($t) => $t > time() - 3600));
        if (count($_SESSION['msg_times']) >= 5) {
            flash('danger', 'You have sent several messages recently. Please try again later.');
            redirect('contact');
        }
        $_SESSION['msg_times'][] = time();

        Database::insert('messages', [
            'name' => mb_substr($name, 0, 150), 'email' => $email,
            'subject' => mb_substr($subject, 0, 200), 'message' => mb_substr($message, 0, 5000),
        ]);
        $notify = setting('order_notify_email', setting('store_email', ''));
        if ($notify) {
            $html = '<p><strong>New contact message</strong></p><p>From: ' . e($name) . ' (' . e($email) . ')<br>Subject: ' . e($subject) . '</p><p>' . nl2br(e($message)) . '</p>';
            Mailer::send($notify, 'Contact form: ' . ($subject ?: 'New message'), $html, $email);
        }
        clear_old();
        flash('success', 'Thanks! Your message was sent. We will reply as soon as possible.');
        redirect('contact');
    }

    public function subscribe(): void
    {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        if (!empty($_POST['website'])) { redirect(''); }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('danger', 'Please enter a valid email address.');
        } else {
            Database::exec('INSERT IGNORE INTO subscribers (email) VALUES (?)', [$email]);
            flash('success', 'You are subscribed. Thank you!');
        }
        $back = $_SERVER['HTTP_REFERER'] ?? '';
        redirect($back ?: '');
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $urls = [full_url(''), full_url('shop'), full_url('blog'), full_url('faq'), full_url('contact')];
        foreach (Database::all('SELECT slug FROM products WHERE is_active=1') as $r) $urls[] = full_url('product/' . $r['slug']);
        foreach (Database::all('SELECT slug FROM categories WHERE is_active=1') as $r) $urls[] = full_url('category/' . $r['slug']);
        foreach (Database::all('SELECT slug FROM brands WHERE is_active=1') as $r) $urls[] = full_url('brand/' . $r['slug']);
        foreach (Database::all('SELECT slug FROM posts WHERE is_published=1') as $r) $urls[] = full_url('blog/' . $r['slug']);
        foreach (Database::all('SELECT slug FROM pages WHERE is_active=1') as $r) $urls[] = full_url('page/' . $r['slug']);
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) echo '  <url><loc>' . e($u) . '</loc></url>' . "\n";
        echo '</urlset>';
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nDisallow: /order\nSitemap: " . full_url('sitemap.xml') . "\n";
    }
}

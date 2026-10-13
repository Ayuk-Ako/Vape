<?php
class BlogController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per = 9;
        $total = (int)Database::val('SELECT COUNT(*) FROM posts WHERE is_published=1');
        $pages = max(1, (int)ceil($total / $per));
        $page = min($page, $pages);
        $posts = Database::all('SELECT * FROM posts WHERE is_published=1 ORDER BY id DESC LIMIT ' . $per . ' OFFSET ' . (($page - 1) * $per));
        $this->render('shop/blog', [
            'title' => 'Blog | ' . setting('store_name', 'Store'),
            'metaDesc' => 'Vape guides, product reviews and news.',
            'posts' => $posts, 'page' => $page, 'pages' => $pages,
        ]);
    }

    public function show(string $slug): void
    {
        $post = Database::one('SELECT * FROM posts WHERE slug=? AND is_published=1', [$slug]);
        if (!$post) $this->notFound();
        $more = Database::all('SELECT * FROM posts WHERE is_published=1 AND id<>? ORDER BY id DESC LIMIT 3', [$post['id']]);
        $this->render('shop/post', [
            'title' => ($post['meta_title'] ?: $post['title']) . ' | ' . setting('store_name', 'Store'),
            'metaDesc' => $post['meta_description'] ?: $post['excerpt'],
            'ogImage' => $post['image'] ? upload_url($post['image']) : null,
            'post' => $post, 'more' => $more,
        ]);
    }
}

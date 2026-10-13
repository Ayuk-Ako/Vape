<?php
class AdminPostsController extends AdminCrudController
{
    protected string $table = 'posts';
    protected string $label = 'Article';
    protected string $plural = 'Blog articles';
    protected string $route = 'posts';
    protected string $orderBy = 'id DESC';
    protected ?string $slugFrom = 'title';
    protected string $uploadFolder = 'blog';
    protected array $columns = ['image' => 'Image', 'title' => 'Title', 'slug' => 'Slug', 'created_at' => 'Created', 'is_published' => 'Published'];
    protected array $fields = [
        ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
        ['name' => 'excerpt', 'label' => 'Short excerpt', 'type' => 'textarea', 'rows' => 2, 'nullable' => true],
        ['name' => 'body', 'label' => 'Content', 'type' => 'html', 'rows' => 14, 'help' => 'HTML is allowed (h2, h3, p, ul, a, img, strong...).'],
        ['name' => 'image', 'label' => 'Cover image', 'type' => 'image'],
        ['name' => 'meta_title', 'label' => 'SEO title', 'type' => 'text', 'nullable' => true],
        ['name' => 'meta_description', 'label' => 'SEO description', 'type' => 'textarea', 'rows' => 2, 'nullable' => true],
        ['name' => 'is_published', 'label' => 'Published', 'type' => 'checkbox', 'default' => 1],
    ];
}

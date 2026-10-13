<?php
class AdminPagesController extends AdminCrudController
{
    protected string $table = 'pages';
    protected string $label = 'Page';
    protected string $plural = 'Pages';
    protected string $route = 'pages';
    protected string $orderBy = 'sort_order, id';
    protected ?string $slugFrom = 'title';
    protected array $columns = ['title' => 'Title', 'slug' => 'Slug', 'show_in_footer' => 'In footer', 'is_active' => 'Active'];
    protected array $fields = [
        ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'required' => true],
        ['name' => 'body', 'label' => 'Content', 'type' => 'html', 'rows' => 16, 'help' => 'HTML is allowed.'],
        ['name' => 'meta_description', 'label' => 'SEO description', 'type' => 'textarea', 'rows' => 2, 'nullable' => true],
        ['name' => 'sort_order', 'label' => 'Footer order', 'type' => 'number'],
        ['name' => 'show_in_footer', 'label' => 'Show in footer', 'type' => 'checkbox', 'default' => 1],
        ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ];
}

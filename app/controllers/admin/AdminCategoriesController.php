<?php
class AdminCategoriesController extends AdminCrudController
{
    protected string $table = 'categories';
    protected string $label = 'Category';
    protected string $plural = 'Categories';
    protected string $route = 'categories';
    protected string $orderBy = 'sort_order, name';
    protected ?string $slugFrom = 'name';
    protected string $uploadFolder = 'categories';
    protected array $columns = ['image' => 'Image', 'name' => 'Name', 'slug' => 'Slug', 'sort_order' => 'Order', 'is_active' => 'Active'];
    protected array $fields = [
        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
        ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'nullable' => true],
        ['name' => 'image', 'label' => 'Image', 'type' => 'image'],
        ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'help' => 'Lower numbers appear first.'],
        ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ];
}

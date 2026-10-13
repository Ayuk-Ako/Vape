<?php
class AdminBrandsController extends AdminCrudController
{
    protected string $table = 'brands';
    protected string $label = 'Brand';
    protected string $plural = 'Brands';
    protected string $route = 'brands';
    protected string $orderBy = 'sort_order, name';
    protected ?string $slugFrom = 'name';
    protected string $uploadFolder = 'brands';
    protected array $columns = ['logo' => 'Logo', 'name' => 'Name', 'slug' => 'Slug', 'sort_order' => 'Order', 'is_active' => 'Active'];
    protected array $fields = [
        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
        ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 3, 'nullable' => true],
        ['name' => 'logo', 'label' => 'Logo (optional)', 'type' => 'image'],
        ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
        ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ];
}

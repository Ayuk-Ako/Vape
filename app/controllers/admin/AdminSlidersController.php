<?php
class AdminSlidersController extends AdminCrudController
{
    protected string $table = 'sliders';
    protected string $label = 'Banner';
    protected string $plural = 'Banners & Hero slides';
    protected string $route = 'sliders';
    protected string $orderBy = 'placement, sort_order, id';
    protected string $uploadFolder = 'sliders';
    protected array $columns = ['image' => 'Image', 'title' => 'Title', 'placement' => 'Placement', 'sort_order' => 'Order', 'is_active' => 'Active'];
    protected array $fields = [
        ['name' => 'placement', 'label' => 'Placement', 'type' => 'select', 'options' => ['hero' => 'Hero slider (top of home page)', 'promo' => 'Promo tile (home page, up to 3)'], 'required' => true],
        ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'nullable' => true],
        ['name' => 'subtitle', 'label' => 'Small text above title', 'type' => 'text', 'nullable' => true],
        ['name' => 'button_text', 'label' => 'Button text', 'type' => 'text', 'nullable' => true],
        ['name' => 'link', 'label' => 'Button link', 'type' => 'text', 'nullable' => true, 'help' => 'Example: collection/best-selling or https://example.com'],
        ['name' => 'image', 'label' => 'Background image', 'type' => 'image', 'help' => 'Recommended 1920x700 for hero, 900x500 for promo.'],
        ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
        ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ];
}

<?php
class AdminFaqsController extends AdminCrudController
{
    protected string $table = 'faqs';
    protected string $label = 'FAQ';
    protected string $plural = 'FAQs';
    protected string $route = 'faqs';
    protected string $orderBy = 'sort_order, id';
    protected array $columns = ['question' => 'Question', 'sort_order' => 'Order', 'is_active' => 'Active'];
    protected array $fields = [
        ['name' => 'question', 'label' => 'Question', 'type' => 'text', 'required' => true],
        ['name' => 'answer', 'label' => 'Answer', 'type' => 'textarea', 'rows' => 5, 'required' => true],
        ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number'],
        ['name' => 'is_active', 'label' => 'Active', 'type' => 'checkbox', 'default' => 1],
    ];
}

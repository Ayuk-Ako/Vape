<?php
/**
 * Base class for admin controllers: auth guard + admin layout.
 */
class AdminController extends Controller
{
    public function __construct()
    {
        Auth::require();
    }

    protected function view(string $view, array $data = []): void
    {
        $this->render($view, $data, 'layout/admin');
    }

    protected function back(string $fallback): void
    {
        redirect($fallback);
    }

    protected function checkbox(string $key): int
    {
        return !empty($_POST[$key]) ? 1 : 0;
    }
}

/**
 * Config-driven CRUD for simple tables.
 * Child classes define $table, $label, $fields, $columns.
 */
abstract class AdminCrudController extends AdminController
{
    protected string $table;
    protected string $label;       // singular, e.g. "Category"
    protected string $plural;      // e.g. "Categories"
    protected string $route;       // e.g. "categories"
    protected string $orderBy = 'id DESC';
    protected ?string $slugFrom = null;     // field used for slug (null => no slug)
    protected string $uploadFolder = 'misc';
    /** @var array<int,array<string,mixed>> */
    protected array $fields = [];
    /** @var array<string,string> column => heading */
    protected array $columns = [];

    public function index(): void
    {
        $rows = Database::all("SELECT * FROM `{$this->table}` ORDER BY {$this->orderBy}");
        $this->view('admin/crud_index', [
            'title' => $this->plural, 'rows' => $rows, 'ctl' => $this,
            'columns' => $this->columns, 'route' => $this->route, 'label' => $this->label, 'plural' => $this->plural,
            'fields' => $this->fields,
        ]);
    }

    public function form(?string $id = null): void
    {
        $row = null;
        if ($id !== null) {
            $row = Database::one("SELECT * FROM `{$this->table}` WHERE id=?", [(int)$id]);
            if (!$row) { flash('danger', $this->label . ' not found.'); redirect('admin/' . $this->route); }
        }
        $this->view('admin/crud_form', [
            'title' => ($row ? 'Edit ' : 'New ') . $this->label, 'row' => $row, 'fields' => $this->fields,
            'route' => $this->route, 'label' => $this->label, 'ctl' => $this,
        ]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $existing = $id ? Database::one("SELECT * FROM `{$this->table}` WHERE id=?", [$id]) : null;
        $data = [];
        $errors = [];

        foreach ($this->fields as $f) {
            $n = $f['name'];
            switch ($f['type']) {
                case 'checkbox':
                    $data[$n] = $this->checkbox($n);
                    break;
                case 'number':
                    $data[$n] = (int)($_POST[$n] ?? 0);
                    break;
                case 'image':
                    $data[$n] = $existing[$n] ?? null;
                    if (!empty($_POST['remove_' . $n])) {
                        delete_upload($data[$n]);
                        $data[$n] = null;
                    }
                    if (!empty($_FILES[$n]['name'])) {
                        $path = save_upload($_FILES[$n], $this->uploadFolder);
                        if ($path) {
                            delete_upload($existing[$n] ?? null);
                            $data[$n] = $path;
                        } else {
                            $errors[] = ($f['label'] ?? $n) . ': upload failed (JPG, PNG, WEBP or GIF up to 6MB).';
                        }
                    }
                    break;
                case 'html':
                    $data[$n] = clean_html((string)($_POST[$n] ?? ''));
                    break;
                default:
                    $v = trim((string)($_POST[$n] ?? ''));
                    $data[$n] = ($v === '' && !empty($f['nullable'])) ? null : $v;
            }
            if (!empty($f['required']) && ($data[$n] === '' || $data[$n] === null) && $f['type'] !== 'image') {
                $errors[] = ($f['label'] ?? $n) . ' is required.';
            }
        }

        if ($errors) {
            foreach ($errors as $e) flash('danger', $e);
            keep_old($_POST);
            redirect('admin/' . $this->route . ($id ? "/$id/edit" : '/create'));
        }

        if ($this->slugFrom) {
            $slugSource = trim((string)($_POST['slug'] ?? '')) ?: (string)$data[$this->slugFrom];
            $data['slug'] = unique_slug($this->table, $slugSource, $id);
        }
        $data = $this->beforeSave($data, $existing);

        if ($id && $existing) {
            Database::update($this->table, $data, 'id=?', [$id]);
            flash('success', $this->label . ' updated.');
        } else {
            Database::insert($this->table, $data);
            flash('success', $this->label . ' created.');
        }
        clear_old();
        redirect('admin/' . $this->route);
    }

    public function delete(string $id): void
    {
        $row = Database::one("SELECT * FROM `{$this->table}` WHERE id=?", [(int)$id]);
        if ($row) {
            foreach ($this->fields as $f) {
                if ($f['type'] === 'image') delete_upload($row[$f['name']] ?? null);
            }
            Database::exec("DELETE FROM `{$this->table}` WHERE id=?", [(int)$id]);
            flash('success', $this->label . ' deleted.');
        }
        redirect('admin/' . $this->route);
    }

    /** Hook for subclasses. */
    protected function beforeSave(array $data, ?array $existing): array
    {
        return $data;
    }

    /** Render a list cell. */
    public function cell(string $col, array $row): string
    {
        $v = $row[$col] ?? '';
        if (in_array($col, ['is_active', 'is_published', 'show_in_footer'], true)) {
            return $v ? '<span class="badge text-bg-success">Yes</span>' : '<span class="badge text-bg-secondary">No</span>';
        }
        if (in_array($col, ['image', 'logo'], true)) {
            return $v ? '<img src="' . e(upload_url($v)) . '" alt="" style="height:38px;border-radius:6px">' : '<span class="text-muted">-</span>';
        }
        return e(mb_strimwidth((string)$v, 0, 70, '...'));
    }
}

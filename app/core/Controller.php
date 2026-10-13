<?php
class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'layout/main'): void
    {
        $viewFile = BASE_DIR . '/app/views/' . $view . '.php';
        if (!is_file($viewFile)) {
            throw new RuntimeException("View not found: $view");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        if ($layout === '') {
            echo $content;
            return;
        }
        include BASE_DIR . '/app/views/' . $layout . '.php';
    }

    protected function json($data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    protected function notFound(): void
    {
        $this->renderNotFound();
        exit;
    }

    public function renderNotFound(): void
    {
        http_response_code(404);
        $this->render('pages/404', ['title' => 'Page not found']);
    }

    protected function input(string $key, $default = '')
    {
        $v = $_POST[$key] ?? $default;
        return is_string($v) ? trim($v) : $v;
    }

    protected function int(string $key, int $default = 0): int
    {
        return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
    }

    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }
}

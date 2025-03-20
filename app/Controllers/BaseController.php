<?php

namespace App\Controllers;

class BaseController
{
    protected function view($path, array $data = [])
    {
        // Extract data into variables
        if (!empty($data) && is_array($data)) {
            extract($data);
        }

        require __DIR__ . '/../Views/' . $path . '.php';
    }

    protected function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    protected function renderView($view, array $data = [])
    {
        // Extract data into variables
        if (!empty($data) && is_array($data)) {
            extract($data);
        }

        // Em renderView(), mude para:
        $viewPath = __DIR__ . '/../Views/';

        // Verifica se o arquivo existe
        if (file_exists($viewPath . $view . '.php')) {
            // Inicia o buffer de saída
            ob_start();

            // Inclui a view
            include $viewPath . $view . '.php';

            // Retorna o conteúdo do buffer e limpa
            return ob_get_clean();
        }

        throw new \Exception("View {$view} not found");
    }

    protected function render($view, array $data = [])
    {
        error_log("Rendering view: $view");
        error_log("SESSION user: " . (isset($_SESSION['user']) ? 'YES' : 'NO'));
        error_log("Public page check: " . (in_array($view, ['auth/login', 'auth/register']) ? 'YES' : 'NO'));

        // Extract data into variables
        if (!empty($data) && is_array($data)) {
            extract($data);
        }

        // Inicia o buffer de saída
        ob_start();

        // Inclui a view específica
        require_once __DIR__ . '/../Views/' . $view . '.php';

        // Captura o conteúdo da view
        $content = ob_get_clean();

        // Verifica se é uma página pública (login/register) ou protegida
        if (!isset($_SESSION['user']) && in_array($view, ['auth/login', 'auth/register'])) {
            // Renderiza diretamente sem o layout principal
            echo $content;
        } else {
            // Renderiza com o layout principal
            require_once __DIR__ . '/../Views/layouts/main.php';
        }

        error_log("About to check layout condition");

        if (!isset($_SESSION['user']) && in_array($view, ['auth/login', 'auth/register'])) {
            error_log("Using direct rendering (no layout)");
            echo $content;
        } else {
            error_log("Using main layout");
            require_once __DIR__ . '/../Views/layouts/main.php';
        }
    }
}

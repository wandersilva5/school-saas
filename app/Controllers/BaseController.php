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
        error_log("RENDER CALLED: View: $view, Backtrace: " . json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)));
        
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
    }

    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}

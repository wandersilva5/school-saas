<?php

namespace App\Controllers;

class BaseController {
    protected function view($path, $data = []) {
        extract($data);
        require __DIR__ . '/../../Views/' . $path . '.php';
    }

    protected function redirect($path) {
        header('Location: ' . $path);
        exit;
    }

    protected function renderView($view, $data = []) {
        // Extrair dados para serem usados na view
        extract($data);
        
        // Caminho base para as views
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

    protected function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../../Views/' . $view . '.php';
    }
}
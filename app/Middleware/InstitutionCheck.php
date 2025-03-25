<?php

namespace App\Middleware;

class InstitutionCheck {
    public function handle() {
        // Debug
        error_log("InstitutionCheck: Verificando middleware");
        
        // Se não estiver logado, não precisa verificar instituição
        if (!isset($_SESSION['user'])) {
            error_log("InstitutionCheck: Usuário não está logado");
            return;
        }
        
        // Obtém a URL atual
        $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currentRoute = trim($currentUrl, '/');
        
        // Rotas que não precisam de verificação de instituição
        $exemptRoutes = [
            'institution/list',
            'institution/select',
            'logout'
        ];
        
        // Verifica se a rota atual está na lista de exceções
        foreach ($exemptRoutes as $route) {
            if (strpos($currentRoute, $route) === 0) {
                error_log("InstitutionCheck: Rota {$currentRoute} isenta de verificação de instituição");
                return;
            }
        }

        // Verifica se o usuário tem o perfil 'Responsavel'
        $isResponsavel = isset($_SESSION['user']['roles']) && in_array('Responsavel', $_SESSION['user']['roles']);
        
        // Se não tiver institution_id na sessão, redireciona para tela de seleção de instituição
        if (!isset($_SESSION['user']['institution_id']) || empty($_SESSION['user']['institution_id'])) {
            error_log("InstitutionCheck: Institution_id não encontrado");
            
            if ($isResponsavel) {
                error_log("InstitutionCheck: Usuário é Responsavel, redirecionando para lista de instituições");
                $_SESSION['toast'] = [
                    'type' => 'info',
                    'message' => 'Por favor, selecione uma instituição'
                ];
                header('Location: /institution/list');
                exit;
            } else {
                // Outros usuários (não responsáveis) devem ter uma instituição definida
                error_log("InstitutionCheck: Usuário não é Responsavel e não tem instituição definida");
                session_destroy();
                header('Location: /login');
                exit;
            }
        }

        error_log("InstitutionCheck: Verificação concluída com sucesso");
    }
}
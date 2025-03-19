<?php

namespace App\Middleware;

class InstitutionCheck {
    public function handle() {
        // Debug
        error_log("InstitutionCheck: Verificando middleware");
        error_log("InstitutionCheck: Sessão atual: " . print_r($_SESSION, true));

        // Se não estiver logado, não precisa verificar instituição
        if (!isset($_SESSION['user'])) {
            error_log("InstitutionCheck: Usuário não está logado");
            return;
        }

        // Se não tiver institution_id na sessão, redireciona para login
        if (!isset($_SESSION['user']['institution_id'])) {
            error_log("InstitutionCheck: Institution_id não encontrado");
            session_destroy();
            header('Location: /login');
            exit;
        }

        error_log("InstitutionCheck: Verificação concluída com sucesso");
    }
}
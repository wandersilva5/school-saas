<?php

namespace App\Middleware;

class InstitutionCheck {
    public function handle() {
        session_start();
        
        if (!isset($_SESSION['institution_id'])) {
            header('Location: /login');
            exit;
        }

        // Verifica se o usuário está tentando acessar dados de outra instituição
        if (isset($_GET['institution_id']) && $_GET['institution_id'] != $_SESSION['institution_id']) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Acesso não autorizado a dados de outra instituição']);
            exit;
        }

        return true;
    }
}
<?php

namespace App\Controllers;

use App\Models\Institution;

use \Exception;
use \PDO;

class InstitutionController extends BaseController
{
    private $db;
    private Institution $roleModel;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->roleModel = new Institution();
    }

    public function index()
    {
        // Verifica se o usuário tem permissão
        if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
            header('Location: /dashboard');
            exit;
        }

        $institutions = $this->roleModel->getInstitutions();
        return $this->render('institution/index', [
            'institutions' => $institutions,
            'currentPage' => 'institution',
            'pageTitle' => 'Gerenciar Acessos'
        ]);
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $logo_url = $_POST['logo_url'] ?? [];

            $this->db->beginTransaction();

            // Insere o usuário
            $stmt = $this->db->prepare(
                "INSERT INTO institutions (name, domain, logo_url, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );
            
            $stmt->execute([
                $name,
                $domain,
                $logo_url,
            ]);
            

            $this->db->commit();
            header('Location: /access-management?success=1');

        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /access-management?error=' . urlencode($e->getMessage()));
        }
    }
} 
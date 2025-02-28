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
} 
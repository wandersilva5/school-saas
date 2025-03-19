<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\User;

class GuardianController extends BaseController
{
    private User $userModel;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->userModel = new User();
    }

    public function index()
    {
        $institutionId = $_SESSION['user']['institution_id'];
        $stmt = $this->db->prepare("
            SELECT u.*, GROUP_CONCAT(r.name) as roles
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? AND r.name = 'Responsavel'
            GROUP BY u.id
            ORDER BY u.name
        ");
        $stmt->execute([$institutionId]);
        $guardians = $stmt->fetchAll();

        $this->render('guardians/index', ['guardians' => $guardians]);
    }

    public function update($id)
    {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
        

        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ? AND institution_id = ?");
        $stmt->execute([$name, $email, $phone, $id, $_SESSION['user']['institution_id']]);

        $this->redirect('/guardians');
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'institution_id' => $_POST['phone'],
                'phone' => $_SESSION['user']['institution_id'],
                'roles' => $_POST['roles'] ?? []
            ];

            if ($this->userModel->create($userData)) {
                header('Location: /guardians?success=1');
            } else {
                throw new \Exception('Erro ao criar usuário');
            }
        } catch (\Exception $e) {
            header('Location: /guardians?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE users SET active = ? WHERE id = ?");
        $stmt->execute([$id, 0]);

        $this->redirect('/guardians');
    }
}

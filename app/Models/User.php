<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Remove a senha dos dados da sessão
            return $user;
        }

        return false;
    }

    public function getUsers($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, 
                u.name, 
                u.email, 
                u.created_at,
                u.active,
                i.name as institution_name,
                GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN institutions i ON u.institution_id = i.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? 
            AND u.deleted_at IS NULL
            GROUP BY u.id, u.name, u.email, u.created_at, u.active, i.name
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUsers($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function create($userData)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, institution_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userData['name'],
                $userData['email'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
                $userData['institution_id']
            ]);

            $userId = $this->db->lastInsertId();

            // Inserir roles do usuário
            if (!empty($userData['roles'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id) 
                    VALUES (?, ?)
                ");

                foreach ($userData['roles'] as $roleId) {
                    $stmt->execute([$userId, $roleId]);
                }
            }

            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    

}

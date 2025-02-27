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

    public function create($userData)
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );

            return $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['password']
            ]);
        } catch (\PDOException $e) {
            // Log do erro
            return false;
        }
    }
}

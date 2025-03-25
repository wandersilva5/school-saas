<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Menu {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getMenusByRole($roles) {
        $placeholders = str_repeat('?,', count($roles) - 1) . '?';
        return $this->db->query(
            "SELECT DISTINCT m.* FROM menus m
            INNER JOIN menu_roles mr ON m.id = mr.menu_id
            INNER JOIN roles r ON mr.role_id = r.id
            WHERE r.name IN ($placeholders)
            AND r.name != 'SupUser'
            ORDER BY m.order_index",
            $roles
        )->fetchAll();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM menus ORDER BY order_index")->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO menus (name, url, icon, header, route, required_roles, order_index) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->db->query($sql, [
            $data['name'], $data['url'], $data['icon'], $data['header'],
            $data['route'], $data['required_roles'], $data['order_index']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE menus SET name = ?, url = ?, icon = ?, header = ?, 
                route = ?, required_roles = ?, order_index = ? WHERE id = ?";
        return $this->db->query($sql, [
            $data['name'], $data['url'], $data['icon'], $data['header'],
            $data['route'], $data['required_roles'], $data['order_index'], $id
        ]);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM menu_roles WHERE menu_id = ?", [$id]);
        return $this->db->query("DELETE FROM menus WHERE id = ?", [$id]);
    }

    public function batchUpdate($items) {
        $success = true;
        $this->db->getConnection()->beginTransaction();
        
        try {
            foreach ($items as $item) {
                $sql = "UPDATE menus SET order_index = ?, header = ? WHERE id = ?";
                $success = $success && $this->db->query($sql, [
                    $item['order_index'], 
                    $item['header'], 
                    $item['id']
                ]);
            }
            
            if ($success) {
                $this->db->getConnection()->commit();
                return true;
            }
            
            $this->db->getConnection()->rollBack();
            return false;
        } catch (\Exception $e) {
            $this->db->getConnection()->rollBack();
            return false;
        }
    }

    public function getAllRoles() {
        return $this->db->query("SELECT * FROM roles where name != 'SupUser' ORDER BY name")->fetchAll();
    }

    public function getAllHeaders() {
        return $this->db->query("SELECT DISTINCT header FROM menus WHERE header IS NOT NULL ORDER BY header")->fetchAll();
    }


    public function getRoutePermissions()
    {
        try {
            
            $db = Database::getInstance();
            $stmt = $db->query("
                SELECT route, required_roles 
                FROM menus 
                WHERE active = 1
            ");
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $routePermissions = [];
            
            foreach ($results as $row) {
                // Extract the base route from URL
                $baseRoute = explode('/', trim($row['route'], '/'))[0];
                
                // Parse roles from JSON or comma-separated string
                if (strpos($row['required_roles'], '[') === 0) {
                    // It's likely JSON
                    $roles = json_decode($row['required_roles'], true);
                } else {
                    // It's likely comma-separated
                    $roles = array_map('trim', explode(',', $row['required_roles']));
                }
                
                $routePermissions[$baseRoute] = $roles;
            }
            
            return $routePermissions;
            
        } catch (\PDOException $e) {
            // Log the error
            error_log("Error loading menu permissions: " . $e->getMessage());
            
            // Set toast error message
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar permissões do menu. Por favor, contate o administrador.'
            ];
            
            // Return empty array
            return [];
        }
    }
    
}

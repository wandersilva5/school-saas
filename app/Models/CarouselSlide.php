<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class CarouselSlide {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data) {
        $sql = "INSERT INTO carousel_slides (image_url, institution_id, order_num) VALUES (:image_url, :institution_id, :order_num)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':image_url' => $data['image_url'],
                ':institution_id' => $data['institution_id'],
                ':order_num' => $data['order_num'] ?? $this->getNextOrderNum($data['institution_id'])
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao criar slide do carrossel: " . $e->getMessage());
        }
    }

    private function getNextOrderNum($institutionId) {
        $sql = "SELECT MAX(order_num) as max_order FROM carousel_slides WHERE institution_id = :institution_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':institution_id' => $institutionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($result['max_order'] ?? 0) + 1;
        } catch (\PDOException $e) {
            return 1; // fallback to 1 if there's an error
        }
    }

    public function getById(int $id) {
        $sql = "SELECT * FROM carousel_slides WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar slide: " . $e->getMessage());
        }
    }

    public function getByInstitutionId(int $institutionId) {
        $sql = "SELECT * FROM carousel_slides WHERE institution_id = :institution_id ORDER BY order_num ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':institution_id' => $institutionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar slides: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data) {
        $sql = "UPDATE carousel_slides SET 
                image_url = :image_url,
                order_num = :order_num
                WHERE id = :id AND institution_id = :institution_id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':image_url' => $data['image_url'],
                ':order_num' => $data['order_num'] ?? 0,
                ':institution_id' => $data['institution_id']
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar slide: " . $e->getMessage());
        }
    }

    public function delete(int $id, int $institutionId) {
        $sql = "DELETE FROM carousel_slides WHERE id = :id AND institution_id = :institution_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':institution_id' => $institutionId
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao deletar slide: " . $e->getMessage());
        }
    }

    public function reorder(array $slideIds, int $institutionId) {
        try {
            $this->db->beginTransaction();
            
            foreach ($slideIds as $order => $id) {
                $sql = "UPDATE carousel_slides SET order_num = :order_num 
                        WHERE id = :id AND institution_id = :institution_id";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([
                    ':order_num' => $order + 1,
                    ':id' => $id,
                    ':institution_id' => $institutionId
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new \Exception("Erro ao reordenar slides: " . $e->getMessage());
        }
    }
}

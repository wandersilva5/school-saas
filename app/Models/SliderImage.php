<?php

namespace App\Models;

use App\Config\Database;

class SliderImage
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($institutionId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM slider_images 
            WHERE institution_id = ? 
            ORDER BY order_position"
        );
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO slider_images (image_url, institution_id, order_position) 
            VALUES (?, ?, ?)"
        );
        return $stmt->execute([
            $data['image_url'],
            $data['institution_id'],
            $data['order_position']
        ]);
    }

    public function delete($id, $institutionId)
    {
        $stmt = $this->db->prepare(
            "DELETE FROM slider_images 
            WHERE id = ? 
            AND institution_id = ?"
        );
        return $stmt->execute([$id, $institutionId]);
    }

    public function updateOrder($id, $position)
    {
        $stmt = $this->db->prepare(
            "UPDATE slider_images 
            SET order_position = ? 
            WHERE id = ?"
        );
        return $stmt->execute([$position, $id]);
    }

    public function getById($id, $institutionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM slider_images WHERE id = ? AND institution_id = ?");
        $stmt->execute([$id, $institutionId]);
        return $stmt->fetch();
    }

    public function getSliderImagesByInstitution($institutionId) {
        $sql = "SELECT * FROM slider_images WHERE institution_id = ? ORDER BY `order_position` ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll();
    }
}

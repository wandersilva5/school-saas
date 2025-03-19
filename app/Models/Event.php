<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Event
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getEvents($institutionId, $startDate = null, $endDate = null)
    {
        $sql = "SELECT * FROM events WHERE institution_id = ?";
        $params = [$institutionId];

        if ($startDate && $endDate) {
            $sql .= " AND date BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }

        $sql .= " ORDER BY date, start_time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getEventsByDay($institutionId, $date)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, title, description, date, 
                       TIME_FORMAT(start_time, '%H:%i') as start_time,
                       TIME_FORMAT(end_time, '%H:%i') as end_time
                FROM events 
                WHERE institution_id = ? 
                AND date = ? 
                ORDER BY start_time
            ");
            $stmt->execute([$institutionId, $date]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar eventos do dia: " . $e->getMessage());
            return [];
        }
    }

    public function create($data)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO events (title, description, date, start_time, end_time, institution_id) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $data['institution_id']
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare(
            "UPDATE events 
            SET title = ?, description = ?, date = ?, start_time = ?, end_time = ? 
            WHERE id = ? AND institution_id = ?"
        );
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $id,
            $data['institution_id']
        ]);
    }

    public function delete($id, $institutionId)
    {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ? AND institution_id = ?");
        return $stmt->execute([$id, $institutionId]);
    }
}

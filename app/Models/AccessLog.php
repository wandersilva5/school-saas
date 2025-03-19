<?php

namespace App\Models;

use App\Config\Database;

class AccessLog 
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getTodayEntriesCount($institution_id)
    {
        $sql = "SELECT COUNT(*) as count 
                FROM access_logs 
                WHERE institution_id = ? 
                AND type = 'entrada'
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$institution_id]);
        return $stmt->fetch()['count'];
    }

    public function getTodayExitsCount($institution_id)
    {
        $sql = "SELECT COUNT(*) as count FROM access_logs 
                WHERE institution_id = ? 
                AND type = 'saída'
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$institution_id]);
        return $stmt->fetch()['count'];
    }

    public function getTodayVisitorsCount($institution_id)
    {
        $sql = "SELECT COUNT(*) as count FROM access_logs 
                WHERE institution_id = ? 
                AND status = 'visitante'
                AND DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$institution_id]);
        return $stmt->fetch()['count'];
    }

    public function getRecentLogs($institution_id, $limit = 10)
    {
        $sql = "SELECT 
                    DATE_FORMAT(al.created_at, '%H:%i') as time,
                    al.type,
                    p.name,
                    al.status,
                    al.institution_id
                FROM access_logs al
                JOIN people p ON al.person_id = p.id
                WHERE al.institution_id = ?
                ORDER BY al.created_at DESC
                LIMIT ?";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(1, $institution_id, \PDO::PARAM_INT);
        $stmt->bindParam(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        error_log("SQL executado: " . $sql);
        error_log("Institution ID: " . $institution_id);
        error_log("Limit: " . $limit);
        
        $logs = $stmt->fetchAll();
        error_log("Resultados: " . print_r($logs, true));
        
        if (empty($logs)) {
            error_log("Nenhum registro encontrado");
            return [];
        }

        return array_map(function($row) {
            return [
                'time' => $row['time'],
                'type' => ucfirst($row['type']),
                'name' => $row['name'],
                'status' => ucfirst($row['status'])
            ];
        }, $logs);
    }
}

<?php

namespace App\Models;

use App\Config\Database;

class Alert 
{
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getActiveAlertsCount($institution_id) 
    {
        $sql = "SELECT COUNT(*) as count 
                FROM alerts 
                WHERE institution_id = ? 
                AND status = 'ativo'";
        return $this->db->query($sql, [$institution_id])->fetch()['count'];
    }
}

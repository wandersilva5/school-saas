<?php

namespace App\Models;

use App\Config\Database;

class PendingAuthorization
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // app/Models/PendingAuthorization.php
    public function getPendingAuthorizations($institution_id)
    {
        $sql = "SELECT 
                    person_name as name,
                    person_type as type,
                    reason,
                    requested_by
                FROM pending_authorizations 
                WHERE institution_id = ? 
                ORDER BY created_at DESC";
        return $this->db->query($sql, [$institution_id])->fetchAll();
    }
}

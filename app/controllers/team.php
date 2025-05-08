<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class Team
{
    private $db;

    public function __construct()
    {
        $this->db = new Database(require 'config/config.php');
    }

    public function getAllTeamMembers()
    {
        $query = "SELECT * FROM team";
        $stmt = $this->db->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


$team = new Team();
$teamMembers = $team->getAllTeamMembers();

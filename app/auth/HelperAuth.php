<?php
namespace App\Auth;

use PDO;

class HelperAuth {
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Get helper by email
    public function getHelperByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM helpers WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if helper email exists
    public function emailExists($email)
    {
        $stmt = $this->db->prepare('SELECT id FROM helpers WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }
} 
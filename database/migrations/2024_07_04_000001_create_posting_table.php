<?php
require_once __DIR__ . '/../../config/database.php';

$db = Database::connect();

$sql = "CREATE TABLE IF NOT EXISTS posting (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description TEXT NOT NULL,
    image1 VARCHAR(255) DEFAULT NULL,
    image2 VARCHAR(255) DEFAULT NULL,
    image3 VARCHAR(255) DEFAULT NULL,
    image4 VARCHAR(255) DEFAULT NULL,
    image5 VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$db->exec($sql);

echo "Created 'posting' table successfully.\n"; 
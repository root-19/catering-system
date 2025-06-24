<?php

class CreateServicesTable {
    public function up($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS services");
        $query = "
            CREATE TABLE IF NOT EXISTS services (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(100) NOT NULL,
                item VARCHAR(255) NOT NULL,
                packs INT NOT NULL,
                location VARCHAR(100) NOT NULL,
                description TEXT,
                image VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($query);
    }

    public function down($pdo) {
        $pdo->exec("DROP TABLE IF EXISTS services");
    }
} 
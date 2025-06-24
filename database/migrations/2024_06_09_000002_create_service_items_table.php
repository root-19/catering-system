<?php
// Migration for service_items table
return [
    'up' => function($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS service_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            service_id INT NOT NULL,
            category VARCHAR(100) NOT NULL,
            item VARCHAR(255) NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        )");
    },
    'down' => function($db) {
        $db->exec("DROP TABLE IF EXISTS service_items");
    }
]; 
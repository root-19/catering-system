<?php
// Migration for orders table
return [
    'up' => function($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            service_id INT NOT NULL,
            package_name VARCHAR(255) NOT NULL,
            category VARCHAR(100),
            item VARCHAR(255),
            reservation_date DATE NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        )");
    },
    'down' => function($db) {
        $db->exec("DROP TABLE IF EXISTS orders");
    }
]; 
<?php
// Migration for reviews table
return [
    'up' => function($db) {
        $db->exec("CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            service_id INT NOT NULL,
            review_text TEXT NOT NULL,
            rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
        )");
    },
    'down' => function($db) {
        $db->exec("DROP TABLE IF EXISTS reviews");
    }
]; 
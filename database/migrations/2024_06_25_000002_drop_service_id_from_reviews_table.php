<?php
// Migration to drop service_id column from reviews table
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE reviews DROP FOREIGN KEY reviews_ibfk_2");
        $db->exec("ALTER TABLE reviews DROP COLUMN service_id");
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE reviews ADD COLUMN service_id INT");
        $db->exec("ALTER TABLE reviews ADD CONSTRAINT reviews_ibfk_2 FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE");
    }
]; 
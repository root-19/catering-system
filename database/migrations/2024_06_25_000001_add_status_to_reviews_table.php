<?php
// Migration to add status column to reviews table
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE reviews ADD COLUMN status ENUM('pending', 'approved') NOT NULL DEFAULT 'pending'");
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE reviews DROP COLUMN status");
    }
]; 
<?php
// Migration to add amount column to orders table
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE orders ADD COLUMN amount DECIMAL(10,2) NOT NULL DEFAULT 0");
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE orders DROP COLUMN amount");
    }
]; 
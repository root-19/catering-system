<?php
// Migration to add payment_method and amount columns to orders table
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(32) DEFAULT 'downpayment' AFTER notes");
        $db->exec("ALTER TABLE orders ADD COLUMN amount DECIMAL(10,2) DEFAULT 0 AFTER payment_method");
        $db->exec("ALTER TABLE orders ADD COLUMN category VARCHAR(100) AFTER package_name");
        $db->exec("ALTER TABLE orders ADD COLUMN item VARCHAR(255) AFTER category");
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE orders DROP COLUMN payment_method");
        $db->exec("ALTER TABLE orders DROP COLUMN amount");
        $db->exec("ALTER TABLE orders DROP COLUMN category");
        $db->exec("ALTER TABLE orders DROP COLUMN item");
    }
]; 
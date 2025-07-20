<?php
return [
    'up' => function($db) {
        $db->exec("ALTER TABLE orders ADD COLUMN email_notified TINYINT(1) DEFAULT 0");
    },
    'down' => function($db) {
        $db->exec("ALTER TABLE orders DROP COLUMN email_notified");
    }
]; 
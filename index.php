<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the router to handle all requests
require_once __DIR__ . '/public/router.php';
?>

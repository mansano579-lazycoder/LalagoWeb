<?php
// inc/check_session.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$isLoggedIn = isset($_SESSION['uid']) && !empty($_SESSION['uid']);

echo json_encode([
    'isLoggedIn' => $isLoggedIn,
    'user' => $isLoggedIn ? [
        'uid' => $_SESSION['uid'],
        'email' => $_SESSION['email'] ?? ''
    ] : null
]);
?>
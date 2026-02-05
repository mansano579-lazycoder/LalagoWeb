<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['order_data'])) {
        $_SESSION['current_order'] = $data['order_data'];
        echo json_encode(['success' => true]);
    }
}
?>
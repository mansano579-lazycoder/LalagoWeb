<?php
session_start();
unset($_SESSION['current_order']);
echo json_encode(['success' => true]);
?>
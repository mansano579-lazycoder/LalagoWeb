<?php
// save-location.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['lat']) && isset($input['lng'])) {
        $_SESSION['user_location'] = [
            'lat' => $input['lat'],
            'lng' => $input['lng'],
            'address' => $input['address'] ?? '',
            'timestamp' => time()
        ];
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No location data']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>
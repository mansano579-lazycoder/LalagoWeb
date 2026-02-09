<?php
// get-user-location.php
session_start();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$response = [
    'success' => false,
    'hasLocation' => false,
    'location' => null,
    'message' => ''
];

// Check if user has location in session
if (isset($_SESSION['user_location'])) {
    $response['success'] = true;
    $response['hasLocation'] = true;
    $response['location'] = $_SESSION['user_location'];
    $response['message'] = 'Location found in session';
} else {
    // If not in session, check database (if user is logged in)
    if (isset($_SESSION['uid'])) {
        require_once 'config/database.php'; // Adjust path as needed
        
        try {
            $userId = $_SESSION['uid'];
            $stmt = $pdo->prepare("SELECT latitude, longitude, address, location FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData) {
                $latitude = null;
                $longitude = null;
                $address = '';
                
                // Try to extract location from various fields
                if (!empty($userData['latitude']) && !empty($userData['longitude'])) {
                    $latitude = floatval($userData['latitude']);
                    $longitude = floatval($userData['longitude']);
                    $address = $userData['address'] ?? '';
                } 
                // Check JSON location field
                elseif (!empty($userData['location'])) {
                    $locationData = json_decode($userData['location'], true);
                    if (json_last_error() === JSON_ERROR_NONE && 
                        isset($locationData['latitude']) && 
                        isset($locationData['longitude'])) {
                        $latitude = floatval($locationData['latitude']);
                        $longitude = floatval($locationData['longitude']);
                        $address = $locationData['address'] ?? $userData['address'] ?? '';
                    }
                }
                
                if ($latitude !== null && $longitude !== null) {
                    $_SESSION['user_location'] = [
                        'lat' => $latitude,
                        'lng' => $longitude,
                        'address' => $address,
                        'saved_at' => time()
                    ];
                    
                    $response['success'] = true;
                    $response['hasLocation'] = true;
                    $response['location'] = $_SESSION['user_location'];
                    $response['message'] = 'Location retrieved from database';
                }
            }
        } catch (Exception $e) {
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'User not logged in';
    }
}

echo json_encode($response);
?>
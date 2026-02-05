<?php
session_start();
header('Content-Type: application/json');

// Database configuration (if using MySQL alongside Firebase)
$host = 'localhost';
$dbname = 'your_database';
$username = 'your_username';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

$isLoggedIn = isset($_SESSION['uid']);
$userId = $_SESSION['uid'] ?? null;
$userEmail = $_SESSION['email'] ?? '';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No data received']);
    exit;
}

$productId = $data['product_id'] ?? null;
$quantity = intval($data['quantity'] ?? 1);
$restaurantId = $data['restaurant_id'] ?? null;

if (!$productId || !$restaurantId) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    // Check if user has existing cart with different restaurant
    $stmt = $pdo->prepare("SELECT restaurant_id FROM restaurant_orders WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId ?: $userEmail]);
    $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingOrder && $existingOrder['restaurant_id'] !== $restaurantId) {
        // User has items from different restaurant
        echo json_encode([
            'success' => false, 
            'message' => 'different_restaurant',
            'current_restaurant' => $existingOrder['restaurant_id'],
            'new_restaurant' => $restaurantId
        ]);
        exit;
    }
    
    // Check if product already exists in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM restaurant_orders WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId ?: $userEmail, $productId]);
    $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingItem) {
        // Update existing item
        $newQuantity = $existingItem['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE restaurant_orders SET quantity = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$newQuantity, $existingItem['id']]);
    } else {
        // Insert new item
        // First, get product details (you might want to fetch from Firebase or have a local cache)
        $productName = $data['product_name'] ?? 'Product';
        $productPrice = floatval($data['product_price'] ?? 0);
        $productImage = $data['product_image'] ?? '';
        
        $stmt = $pdo->prepare("INSERT INTO restaurant_orders 
            (user_id, restaurant_id, product_id, product_name, product_price, quantity, product_image, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        
        $stmt->execute([
            $userId ?: $userEmail,
            $restaurantId,
            $productId,
            $productName,
            $productPrice,
            $quantity,
            $productImage
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
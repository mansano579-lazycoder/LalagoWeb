<?php
session_start();
$orderId = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation - Food Delivery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .confirmation-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .success-icon {
            font-size: 80px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .order-id {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 18px;
            font-weight: 600;
            color: #27ae60;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 10px 5px;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #219955;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: #27ae60;
            border: 2px solid #27ae60;
        }
        
        .btn-secondary:hover {
            background: #f0f9f4;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Order Confirmed!</h1>
        <p>Your order has been placed successfully.</p>
        
        <?php if ($orderId): ?>
        <div class="order-id">
            <i class="fas fa-receipt"></i> Order ID: <?php echo htmlspecialchars($orderId); ?>
        </div>
        <?php endif; ?>
        
        <p>You will receive updates about your order status.</p>
        
        <div style="margin-top: 30px;">
            <a href="index.php" class="btn">
                <i class="fas fa-home"></i> Continue Shopping
            </a>
            <a href="my-orders.php" class="btn btn-secondary">
                <i class="fas fa-list-alt"></i> View My Orders
            </a>
        </div>
    </div>
</body>
</html>
<?php
session_start();

// Initialize cart from session if exists (for guests)
if (!isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['uid']);
$userEmail = $isLoggedIn ? ($_SESSION['email'] ?? '') : '';
$userId = $isLoggedIn ? $_SESSION['uid'] : null;
$hasSavedLocation = $isLoggedIn && isset($_SESSION['user_location']);
$userLocationData = $hasSavedLocation ? $_SESSION['user_location'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LalaGO - Shopping Cart</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* ================= VARIABLES ================= */
        :root {
            --primary: #FF6B35;
            --primary-dark: #E55A2B;
            --secondary: #2D3436;
            --light: #F8F9FA;
            --gray-light: #E9ECEF;
            --gray: #6C757D;
            --gray-dark: #495057;
            --success: #27AE60;
            --warning: #F39C12;
            --danger: #E74C3C;
            --info: #3498DB;
            --white: #FFFFFF;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
            --radius: 16px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        /* ================= MOBILE FIRST STYLES ================= */
        .cart-container {
            padding: 80px 15px 40px;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 70vh;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--gray-light);
        }

        .cart-header h1 {
            font-size: 1.8rem;
            color: var(--secondary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .cart-header p {
            color: var(--gray);
            font-size: 1rem;
        }

        .cart-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ================= CART ITEMS SECTION ================= */
        .cart-items-section {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .restaurant-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 2px solid var(--gray-light);
        }

        .restaurant-logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary);
            flex-shrink: 0;
        }

        .restaurant-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .restaurant-info {
            flex: 1;
            min-width: 0;
        }

        .restaurant-info h3 {
            font-size: 1.2rem;
            color: var(--secondary);
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .restaurant-info p {
            color: var(--gray);
            font-size: 0.85rem;
            margin-bottom: 5px;
        }

        .restaurant-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--warning);
            font-size: 0.85rem;
        }

        .cart-items-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        /* ================= CART ITEM ================= */
        .cart-item {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 15px;
            background: var(--light);
            border-radius: var(--radius);
            border: 2px solid transparent;
            transition: var(--transition);
            position: relative;
        }

        .cart-item:hover {
            border-color: var(--primary);
        }

        .cart-item-main {
            display: flex;
            gap: 15px;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
        }

        .cart-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cart-item-details {
            flex: 1;
            min-width: 0;
        }

        .cart-item-name {
            font-size: 1rem;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cart-item-description {
            color: var(--gray);
            font-size: 0.85rem;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cart-item-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-item-controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            padding: 4px;
            border-radius: 50px;
            box-shadow: var(--shadow);
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: var(--primary);
            color: var(--white);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .quantity-btn:hover {
            background: var(--primary-dark);
            transform: scale(1.1);
        }

        .quantity-display {
            min-width: 30px;
            text-align: center;
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .remove-item {
            background: transparent;
            border: none;
            color: var(--danger);
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
            padding: 5px 10px;
            border-radius: var(--radius-sm);
        }

        .remove-item:hover {
            background: rgba(231, 76, 60, 0.1);
        }

        /* ================= ADDONS SECTION ================= */
        .addons-section {
            margin-top: 10px;
            padding: 10px;
            background: var(--white);
            border-radius: var(--radius-sm);
            border: 1px solid var(--gray-light);
        }

        .addons-title {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 8px;
            font-weight: 600;
        }

        .addons-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .addon-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--gray-light);
            color: var(--gray-dark);
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .addon-badge i {
            font-size: 0.7rem;
        }

        /* ================= EMPTY CART ================= */
        .empty-cart {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-cart i {
            font-size: 3rem;
            color: var(--gray-light);
            margin-bottom: 15px;
        }

        .empty-cart h3 {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .empty-cart p {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 25px;
        }

        .empty-cart-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary);
            color: var(--white);
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
        }

        .empty-cart-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        /* ================= LOGIN REQUIRED MESSAGE ================= */
        .login-required {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
            border: 2px solid var(--info);
            border-radius: var(--radius);
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .login-required-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .login-required i {
            font-size: 2.5rem;
            color: var(--info);
        }

        .login-required h3 {
            color: var(--secondary);
            margin-bottom: 5px;
            font-size: 1.3rem;
        }

        .login-required p {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .login-required-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .login-required-btn {
            padding: 10px 25px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .login-required-btn.login {
            background: var(--info);
            color: white;
            border: 2px solid var(--info);
        }

        .login-required-btn.login:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .login-required-btn.continue {
            background: transparent;
            color: var(--info);
            border: 2px solid var(--info);
        }

        .login-required-btn.continue:hover {
            background: var(--info);
            color: white;
            transform: translateY(-2px);
        }

        .login-required-btn.guest {
            background: var(--gray);
            color: white;
            border: 2px solid var(--gray);
        }

        .login-required-btn.guest:hover {
            background: var(--gray-dark);
            border-color: var(--gray-dark);
            transform: translateY(-2px);
        }

        /* ================= ORDER SUMMARY ================= */
        .order-summary {
            background: var(--white);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            position: sticky;
            top: 80px;
            height: fit-content;
        }

        .order-summary h3 {
            font-size: 1.3rem;
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--gray-light);
        }

        .summary-rows {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 1px dashed var(--gray-light);
        }

        .summary-row.total {
            padding-top: 12px;
            border-top: 2px solid var(--primary);
            border-bottom: none;
            margin-top: 5px;
        }

        .summary-label {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .summary-value {
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .summary-total .summary-label {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .summary-total .summary-value {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.2rem;
        }

        .summary-note {
            background: var(--light);
            padding: 12px;
            border-radius: var(--radius-sm);
            margin: 15px 0;
            font-size: 0.85rem;
            color: var(--gray);
            line-height: 1.4;
        }

        .summary-note i {
            color: var(--info);
            margin-right: 5px;
        }

        .checkout-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
            position: relative;
        }

        .checkout-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .checkout-btn:disabled {
            background: var(--gray) !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
            opacity: 0.7;
        }

        .checkout-btn:disabled:hover::after {
            content: 'Please login to checkout';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--secondary);
            color: white;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            white-space: nowrap;
            margin-bottom: 8px;
            z-index: 1000;
            box-shadow: var(--shadow);
        }

        .checkout-btn:disabled:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: var(--secondary);
            margin-bottom: -6px;
            z-index: 1000;
        }

        .continue-shopping {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            font-size: 0.95rem;
            padding: 10px;
        }

        .continue-shopping:hover {
            text-decoration: underline;
        }

        /* ================= CART ACTIONS ================= */
        .cart-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--gray-light);
        }

        .cart-action-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid var(--gray-light);
            background: var(--white);
            color: var(--gray-dark);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .cart-action-btn:hover {
            background: var(--gray-light);
            border-color: var(--gray);
        }

        .cart-action-btn.danger {
            color: var(--danger);
            border-color: rgba(231, 76, 60, 0.3);
        }

        .cart-action-btn.danger:hover {
            background: rgba(231, 76, 60, 0.1);
            border-color: var(--danger);
        }

        /* ================= LOADING STATE ================= */
        .cart-loading {
            text-align: center;
            padding: 50px 20px;
        }

        .cart-loading i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 15px;
            animation: spin 1s linear infinite;
        }

        .cart-loading h3 {
            font-size: 1.3rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .cart-loading p {
            color: var(--gray);
            font-size: 0.95rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ================= RESTAURANT SWITCH MODAL ================= */
        .switch-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
            padding: 15px;
        }

        .switch-modal.active {
            display: flex;
        }

        .switch-modal-content {
            background: var(--white);
            border-radius: var(--radius);
            width: 100%;
            max-width: 500px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s ease;
        }

        .switch-modal-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .switch-modal-header i {
            font-size: 2.5rem;
            color: var(--warning);
            margin-bottom: 10px;
            background: rgba(243, 156, 18, 0.1);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .switch-modal-header h3 {
            font-size: 1.5rem;
            color: var(--secondary);
            margin-bottom: 8px;
        }

        .switch-modal-header p {
            color: var(--gray);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .restaurant-comparison {
            background: var(--light);
            border-radius: var(--radius);
            padding: 15px;
            margin: 15px 0;
        }

        .restaurant-compare {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            margin-bottom: 10px;
            background: var(--white);
            border-radius: var(--radius-sm);
            border: 2px solid var(--gray-light);
        }

        .restaurant-compare.current {
            border-color: var(--primary);
        }

        .restaurant-compare.new {
            border-color: var(--info);
        }

        .restaurant-compare-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .restaurant-compare-logo {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .restaurant-compare-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .restaurant-compare-name {
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.95rem;
        }

        .restaurant-compare-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-current {
            background: var(--primary);
            color: var(--white);
        }

        .badge-new {
            background: var(--info);
            color: var(--white);
        }

        .switch-modal-footer {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 20px;
        }

        .switch-modal-btn {
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 2px solid transparent;
            width: 100%;
        }

        .switch-modal-btn.cancel {
            background: transparent;
            color: var(--gray);
            border-color: var(--gray-light);
        }

        .switch-modal-btn.cancel:hover {
            background: var(--gray-light);
            color: var(--gray-dark);
        }

        .switch-modal-btn.confirm {
            background: var(--info);
            color: var(--white);
            border-color: var(--info);
        }

        .switch-modal-btn.confirm:hover {
            background: var(--primary);
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= NOTIFICATION ================= */
        .custom-notification {
            position: fixed;
            top: 15px;
            right: 15px;
            left: 15px;
            background: white;
            padding: 12px 15px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-hover);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
            border-left: 4px solid var(--primary);
        }

        .custom-notification.success {
            border-left-color: var(--success);
        }

        .custom-notification.warning {
            border-left-color: var(--warning);
        }

        .custom-notification.error {
            border-left-color: var(--danger);
        }

        .custom-notification.info {
            border-left-color: var(--info);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-100%);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= TABLET STYLES ================= */
        @media (min-width: 768px) {
            .cart-container {
                padding: 100px 20px 50px;
            }
            
            .cart-header h1 {
                font-size: 2.2rem;
            }
            
            .cart-content {
                flex-direction: row;
                gap: 30px;
            }
            
            .cart-items-section {
                flex: 1;
            }
            
            .order-summary {
                flex: 0 0 350px;
            }
            
            .cart-item {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
            
            .cart-item-main {
                flex: 1;
            }
            
            .cart-item-controls {
                flex-direction: row;
                gap: 20px;
            }
            
            .switch-modal-footer {
                flex-direction: row;
            }
            
            .switch-modal-btn {
                width: auto;
            }
            
            .custom-notification {
                left: auto;
                right: 20px;
                width: auto;
                max-width: 400px;
            }
        }

        /* ================= DESKTOP STYLES ================= */
        @media (min-width: 1024px) {
            .cart-container {
                padding: 100px 0 50px;
            }
            
            .cart-header h1 {
                font-size: 2.5rem;
            }
            
            .order-summary {
                flex: 0 0 380px;
            }
            
            .cart-item-image {
                width: 100px;
                height: 100px;
            }
            
            .restaurant-logo {
                width: 60px;
                height: 60px;
            }
            
            .restaurant-info h3 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

<?php include 'assets/header.php'; ?>

<!-- ================= RESTAURANT SWITCH MODAL ================= -->
<div class="switch-modal" id="switchModal">
    <div class="switch-modal-content">
        <div class="switch-modal-header">
            <i class="fas fa-exchange-alt"></i>
            <h3>Switch Restaurant?</h3>
            <p>Your cart contains items from a different restaurant. Adding this item will remove all current items from your cart.</p>
        </div>
        
        <div class="restaurant-comparison">
            <div class="restaurant-compare current">
                <div class="restaurant-compare-content">
                    <div class="restaurant-compare-logo">
                        <img id="currentRestaurantLogo" src="" alt="">
                    </div>
                    <div class="restaurant-compare-name" id="currentRestaurantName"></div>
                </div>
                <span class="restaurant-compare-badge badge-current">Current</span>
            </div>
            
            <div class="restaurant-compare new">
                <div class="restaurant-compare-content">
                    <div class="restaurant-compare-logo">
                        <img id="newRestaurantLogo" src="" alt="">
                    </div>
                    <div class="restaurant-compare-name" id="newRestaurantName"></div>
                </div>
                <span class="restaurant-compare-badge badge-new">New</span>
            </div>
        </div>
        
        <div class="switch-modal-footer">
            <button class="switch-modal-btn cancel" onclick="closeSwitchModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="switch-modal-btn confirm" onclick="confirmRestaurantSwitch()">
                <i class="fas fa-check"></i> Switch Restaurant
            </button>
        </div>
    </div>
</div>

<div class="cart-container">
    <div class="cart-header">
        <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
        <p>Review your items and proceed to checkout</p>
    </div>

    <div id="loginRequiredMessage" style="display: none;">
        <!-- Login message will be shown here via JavaScript -->
    </div>

    <div class="cart-content">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            <div id="restaurantHeader" style="display: none;">
                <div class="restaurant-header">
                    <div class="restaurant-logo">
                        <img id="restaurantLogo" src="" alt="">
                    </div>
                    <div class="restaurant-info">
                        <h3 id="restaurantName"></h3>
                        <p id="restaurantCategory"></p>
                        <div class="restaurant-rating">
                            <i class="fas fa-star"></i>
                            <span id="restaurantRating"></span>
                            <span id="restaurantReviews"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div id="cartItemsContainer">
                <!-- Cart items will be loaded here -->
                <div class="cart-loading">
                    <i class="fas fa-spinner"></i>
                    <h3>Loading your cart...</h3>
                    <p>Please wait while we fetch your items</p>
                </div>
            </div>

            <div id="emptyCart" style="display: none;">
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Your cart is empty</h3>
                    <p>Add delicious foods from our restaurants to get started</p>
                    <a href="index.php" class="empty-cart-btn">
                        <i class="fas fa-utensils"></i> Start Shopping
                    </a>
                </div>
            </div>

            <!-- Cart Actions -->
            <div class="cart-actions" id="cartActions" style="display: none;">
                <button class="cart-action-btn" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </button>
                <button class="cart-action-btn danger" onclick="removeAllFromRestaurant()">
                    <i class="fas fa-store-slash"></i> Remove All from Restaurant
                </button>
            </div>
        </div>

        <!-- Order Summary Section -->
        <div class="order-summary">
            <h3>Order Summary</h3>
            
            <div class="summary-rows">
                <div class="summary-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="subtotal">₱0.00</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Delivery Fee</span>
                    <span class="summary-value" id="deliveryFee">₱0.00</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Service Fee</span>
                    <span class="summary-value" id="serviceFee">₱0.00</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">Tax</span>
                    <span class="summary-value" id="tax">₱0.00</span>
                </div>
                
                <div class="summary-row total summary-total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value" id="total">₱0.00</span>
                </div>
            </div>

            <div class="summary-note">
                <i class="fas fa-info-circle"></i>
                All prices include VAT. Delivery fee may vary based on distance.
            </div>

            <button class="checkout-btn" id="checkoutBtn" disabled onclick="proceedToCheckout()">
                <i class="fas fa-lock"></i> Proceed to Checkout
            </button>

            <a href="index.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<!-- ================= INCLUDE FIREBASE CONNECTION ================= -->
<?php include 'inc/firebase.php'; ?>

<script>
// ================= GLOBAL VARIABLES =================
let cart = [];
let currentRestaurant = null;
let restaurantDetails = null;
let pendingItemToAdd = null;
let isUserLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
let continueAsGuest = false;

// ================= INITIALIZE CART =================
document.addEventListener('DOMContentLoaded', async function() {
    console.log('Cart page initializing...');
    console.log('User logged in:', isUserLoggedIn);
    
    // Check if user chose to continue as guest
    continueAsGuest = localStorage.getItem('continueAsGuest') === 'true';
    
    // Load cart from localStorage first
    loadCartFromLocalStorage();
    
    // Check if we have cart data from URL (browser cache)
    checkUrlForCartData();
    
    // If user is logged in, load cart from Firebase
    if (isUserLoggedIn) {
        await loadCartFromFirebase();
    }
    
    // Display cart items
    await displayCartItems();
    
    // Update cart count in header
    updateCartCount();
    
    // Show/hide login message based on cart status
    showLoginMessageIfNeeded();
    
    // Listen for auth state changes
    auth.onAuthStateChanged(async (user) => {
        if (user) {
            isUserLoggedIn = true;
            continueAsGuest = false;
            localStorage.removeItem('continueAsGuest');
            await loadCartFromFirebase();
            await displayCartItems();
            showLoginMessageIfNeeded();
        } else {
            isUserLoggedIn = false;
            // User logged out, keep local cart but disable checkout
            document.getElementById('checkoutBtn').disabled = true;
            showLoginMessageIfNeeded();
        }
    });
});

// ================= SHOW LOGIN MESSAGE IF NEEDED =================
function showLoginMessageIfNeeded() {
    const loginMessageContainer = document.getElementById('loginRequiredMessage');
    
    // Don't show if user is logged in or if cart is empty
    if (isUserLoggedIn || cart.length === 0 || continueAsGuest) {
        loginMessageContainer.style.display = 'none';
        return;
    }
    
    // Show login message
    loginMessageContainer.innerHTML = `
        <div class="login-required">
            <div class="login-required-content">
                <i class="fas fa-exclamation-circle"></i>
                <h3>Login Required for Checkout</h3>
                <p>You have ${cart.length} item(s) in your cart. Please login to save your cart and proceed to checkout</p>
                <div class="login-required-buttons">
                    <a href="login.php?redirect=${encodeURIComponent(window.location.href)}" class="login-required-btn login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                    <a href="register.php?redirect=${encodeURIComponent(window.location.href)}" class="login-required-btn continue">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                    <a href="#" class="login-required-btn guest" onclick="continueAsGuestFunction()">
                        <i class="fas fa-user-clock"></i> Continue as Guest
                    </a>
                </div>
            </div>
        </div>
    `;
    loginMessageContainer.style.display = 'block';
}

// ================= CONTINUE AS GUEST FUNCTION =================
function continueAsGuestFunction() {
    continueAsGuest = true;
    localStorage.setItem('continueAsGuest', 'true');
    showNotification('You can continue shopping as a guest. Login will be required for checkout.', 'info');
    document.getElementById('loginRequiredMessage').style.display = 'none';
}

// ================= LOAD CART FROM LOCAL STORAGE =================
function loadCartFromLocalStorage() {
    const savedCart = localStorage.getItem('cart');
    if (savedCart) {
        try {
            cart = JSON.parse(savedCart);
            console.log('Loaded cart from localStorage:', cart.length, 'items');
        } catch (error) {
            console.error('Error parsing cart from localStorage:', error);
            cart = [];
        }
    } else {
        cart = [];
    }
}

// ================= LOAD CART FROM FIREBASE =================
async function loadCartFromFirebase() {
    try {
        const user = auth.currentUser;
        if (!user) {
            console.log('No user logged in, skipping Firebase cart load');
            return;
        }
        
        const userDoc = await db.collection("users").doc(user.uid).get();
        if (userDoc.exists) {
            const userData = userDoc.data();
            if (userData.cart && Array.isArray(userData.cart)) {
                console.log('Loaded cart from Firebase:', userData.cart.length, 'items');
                cart = userData.cart;
                localStorage.setItem('cart', JSON.stringify(cart));
            } else {
                console.log('No cart found in Firebase user data');
                // Save current local cart to Firebase
                await saveCartToFirebase();
            }
        } else {
            console.log('User document not found in Firebase');
            // Create user document with current cart
            await saveCartToFirebase();
        }
    } catch (error) {
        console.error('Error loading cart from Firebase:', error);
    }
}

// ================= SAVE CART TO FIREBASE =================
async function saveCartToFirebase() {
    try {
        const user = auth.currentUser;
        if (!user) {
            console.log('No user logged in, skipping Firebase save');
            return;
        }
        
        await db.collection("users").doc(user.uid).set({
            cart: cart,
            cartUpdatedAt: firebase.firestore.FieldValue.serverTimestamp(),
            email: user.email,
            lastActive: firebase.firestore.FieldValue.serverTimestamp()
        }, { merge: true });
        console.log('Cart saved to Firebase');
    } catch (error) {
        console.error('Error saving cart to Firebase:', error);
    }
}

// ================= UPDATE CART AND SAVE =================
async function updateCartAndSave() {
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Save to sessionStorage for backup
    saveCartToSessionStorage();
    
    // Save to Firebase if logged in
    if (isUserLoggedIn) {
        await saveCartToFirebase();
    }
    
    // Update display
    await displayCartItems();
    updateCartCount();
    
    // Show login message if needed
    showLoginMessageIfNeeded();
}

// ================= SAVE CART TO SESSION STORAGE =================
function saveCartToSessionStorage() {
    try {
        sessionStorage.setItem('guest_cart_backup', JSON.stringify(cart));
        sessionStorage.setItem('guest_cart_timestamp', new Date().toISOString());
    } catch (error) {
        console.error('Error saving cart to sessionStorage:', error);
    }
}

// ================= CHECK URL FOR CART DATA =================
function checkUrlForCartData() {
    const urlParams = new URLSearchParams(window.location.search);
    const cartData = urlParams.get('cart_data');
    
    if (cartData) {
        try {
            const newCart = JSON.parse(decodeURIComponent(cartData));
            console.log('Received cart data from URL:', newCart.length, 'items');
            
            if (newCart.length > 0) {
                // Check restaurant compatibility
                if (cart.length > 0) {
                    const firstNewItem = newCart[0];
                    const currentRestaurantId = cart[0].vendorID || cart[0].restaurant_id;
                    
                    if (firstNewItem.vendorID !== currentRestaurantId) {
                        showRestaurantSwitchModal(firstNewItem);
                        return;
                    }
                }
                
                // Merge carts
                mergeCarts(newCart);
                displayCartItems();
                saveCartToFirebase();
                
                // Clear URL parameters
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        } catch (error) {
            console.error('Error parsing cart data from URL:', error);
        }
    }
}

// ================= MERGE CARTS =================
function mergeCarts(newCart) {
    const cartMap = new Map();
    
    // Add existing items to map
    cart.forEach(item => {
        const key = item.id + (item.selectedAddons ? JSON.stringify(item.selectedAddons) : '');
        cartMap.set(key, item);
    });
    
    // Add or update with new items
    newCart.forEach(item => {
        const key = item.id + (item.selectedAddons ? JSON.stringify(item.selectedAddons) : '');
        
        if (cartMap.has(key)) {
            // Update quantity if same item with same addons exists
            const existingItem = cartMap.get(key);
            existingItem.qty = (existingItem.qty || 1) + (item.qty || 1);
        } else {
            cartMap.set(key, item);
        }
    });
    
    // Convert back to array
    cart = Array.from(cartMap.values());
    
    // Update cart
    updateCartAndSave();
    showNotification('Item added to cart', 'success');
}

// ================= DISPLAY CART ITEMS =================
async function displayCartItems() {
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const emptyCart = document.getElementById('emptyCart');
    const restaurantHeader = document.getElementById('restaurantHeader');
    const cartActions = document.getElementById('cartActions');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (cart.length === 0) {
        cartItemsContainer.style.display = 'none';
        emptyCart.style.display = 'block';
        restaurantHeader.style.display = 'none';
        cartActions.style.display = 'none';
        updateOrderSummary(0);
        checkoutBtn.disabled = true;
        return;
    }
    
    // Show loading
    cartItemsContainer.innerHTML = `
        <div class="cart-loading">
            <i class="fas fa-spinner"></i>
            <h3>Loading cart items...</h3>
            <p>Please wait while we fetch your items</p>
        </div>
    `;
    cartItemsContainer.style.display = 'block';
    emptyCart.style.display = 'none';
    
    // Get restaurant details from first item
    const firstItem = cart[0];
    currentRestaurant = firstItem.vendorID || firstItem.restaurant_id;
    
    try {
        // Fetch restaurant details
        restaurantDetails = await fetchRestaurantDetails(currentRestaurant);
        
        // Display restaurant header
        displayRestaurantHeader(restaurantDetails);
        restaurantHeader.style.display = 'block';
        
        // Show cart actions
        cartActions.style.display = 'flex';
        
        // Fetch product details for all items
        const itemsWithDetails = [];
        let subtotal = 0;
        
        for (const item of cart) {
            const productDetails = await fetchProductDetails(item.id);
            if (productDetails) {
                const price = parseFloat(productDetails.price) || 0;
                const addonsPrice = item.selectedAddons ? 
                    item.selectedAddons.reduce((sum, addon) => sum + (parseFloat(addon.price) || 0), 0) : 0;
                const itemTotal = (price + addonsPrice) * (item.qty || 1);
                subtotal += itemTotal;
                
                itemsWithDetails.push({
                    ...item,
                    details: productDetails,
                    itemTotal: itemTotal,
                    addonsPrice: addonsPrice
                });
            }
        }
        
        // Display cart items
        displayCartItemsList(itemsWithDetails);
        
        // Update order summary
        updateOrderSummary(subtotal);
        
        // Enable checkout button only if logged in and has items
        checkoutBtn.disabled = !isUserLoggedIn || cart.length === 0;
        
        // Add tooltip for disabled checkout button
        if (!isUserLoggedIn && cart.length > 0) {
            checkoutBtn.title = 'Please login to proceed to checkout';
            checkoutBtn.setAttribute('data-tooltip', 'Login required for checkout');
        } else {
            checkoutBtn.removeAttribute('title');
            checkoutBtn.removeAttribute('data-tooltip');
        }
        
    } catch (error) {
        console.error('Error displaying cart items:', error);
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error loading cart</h3>
                <p>Unable to load your cart items. Please try again.</p>
                <button class="empty-cart-btn" onclick="displayCartItems()">
                    <i class="fas fa-redo"></i> Retry
                </button>
            </div>
        `;
    }
}

// ================= DISPLAY RESTAURANT HEADER =================
function displayRestaurantHeader(restaurant) {
    document.getElementById('restaurantLogo').src = restaurant.logo;
    document.getElementById('restaurantLogo').alt = restaurant.name;
    document.getElementById('restaurantName').textContent = restaurant.name;
    document.getElementById('restaurantCategory').textContent = restaurant.category || 'Restaurant';
    document.getElementById('restaurantRating').textContent = restaurant.rating || '0.0';
    document.getElementById('restaurantReviews').textContent = restaurant.reviewsCount ? `(${restaurant.reviewsCount})` : '';
}

// ================= DISPLAY CART ITEMS LIST =================
function displayCartItemsList(items) {
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    
    if (items.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Add delicious foods from our restaurants to get started</p>
                <a href="index.php" class="empty-cart-btn">
                    <i class="fas fa-utensils"></i> Start Shopping
                </a>
            </div>
        `;
        return;
    }
    
    let itemsHTML = '';
    
    items.forEach(item => {
        const product = item.details;
        const price = parseFloat(product.price) || 0;
        const quantity = item.qty || 1;
        const addonsPrice = item.addonsPrice || 0;
        const totalPrice = (price + addonsPrice) * quantity;
        
        itemsHTML += `
            <div class="cart-item" data-id="${item.id}">
                <div class="cart-item-main">
                    <div class="cart-item-image">
                        <img src="${product.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
                             alt="${product.name}"
                             onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
                    </div>
                    
                    <div class="cart-item-details">
                        <div class="cart-item-name">${product.name || 'Product'}</div>
                        <div class="cart-item-description">${product.description || 'Delicious food item'}</div>
                        ${item.selectedAddons && item.selectedAddons.length > 0 ? `
                            <div class="addons-section">
                                <div class="addons-title">Add-ons:</div>
                                <div class="addons-list">
                                    ${item.selectedAddons.map(addon => `
                                        <div class="addon-badge">
                                            <i class="fas fa-plus"></i>
                                            ${addon.title} (+₱${(parseFloat(addon.price) || 0).toFixed(2)})
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                        <div class="cart-item-price">₱${totalPrice.toFixed(2)}</div>
                    </div>
                </div>
                
                <div class="cart-item-controls">
                    <div class="quantity-controls">
                        <button class="quantity-btn minus" onclick="updateCartQuantity('${item.id}', ${JSON.stringify(item.selectedAddons || [])}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="quantity-display">${quantity}</span>
                        <button class="quantity-btn plus" onclick="updateCartQuantity('${item.id}', ${JSON.stringify(item.selectedAddons || [])}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button class="remove-item" onclick="removeFromCart('${item.id}', ${JSON.stringify(item.selectedAddons || [])})">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
    });
    
    cartItemsContainer.innerHTML = itemsHTML;
}

// ================= FETCH RESTAURANT DETAILS =================
async function fetchRestaurantDetails(vendorId) {
    try {
        const vendorDoc = await db.collection("vendors").doc(vendorId).get();
        if (vendorDoc.exists) {
            const vendorData = vendorDoc.data();
            return {
                id: vendorId,
                name: vendorData.title || vendorData.name || "Unknown Restaurant",
                logo: vendorData.photo || vendorData.authorProfilePic || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
                category: vendorData.categoryTitle || 'Restaurant',
                rating: vendorData.reviewsCount > 0 ? (vendorData.reviewsSum / vendorData.reviewsCount).toFixed(1) : "0.0",
                reviewsCount: vendorData.reviewsCount || 0,
                deliveryCharge: vendorData.DeliveryCharge?.minimum_delivery_charges || vendorData.minimum_delivery_charges || 0
            };
        }
    } catch (error) {
        console.error("Error fetching restaurant details:", error);
    }
    
    // Return default if not found
    return {
        id: vendorId,
        name: "Unknown Restaurant",
        logo: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
        category: 'Restaurant',
        rating: "0.0",
        reviewsCount: 0,
        deliveryCharge: 0
    };
}

// ================= FETCH PRODUCT DETAILS =================
async function fetchProductDetails(productId) {
    try {
        const productDoc = await db.collection("vendor_products").doc(productId).get();
        if (productDoc.exists) {
            const data = productDoc.data();
            return {
                id: productId,
                name: data.name || 'Product',
                description: data.description || '',
                price: data.price || 0,
                photo: data.photo || '',
                vendorID: data.vendorID || ''
            };
        }
    } catch (error) {
        console.error("Error fetching product details:", error);
    }
    return null;
}

// ================= UPDATE CART QUANTITY =================
async function updateCartQuantity(productId, addons, change) {
    // Find the exact item (including addons)
    const itemIndex = cart.findIndex(item => {
        if (item.id !== productId) return false;
        
        const itemAddons = item.selectedAddons || [];
        const compareAddons = addons || [];
        
        if (itemAddons.length !== compareAddons.length) return false;
        
        return itemAddons.every((addon, index) => 
            addon.title === compareAddons[index]?.title && 
            parseFloat(addon.price) === parseFloat(compareAddons[index]?.price)
        );
    });
    
    if (itemIndex > -1) {
        let newQuantity = (cart[itemIndex].qty || 1) + change;
        
        if (newQuantity < 1) {
            // Remove item if quantity becomes 0
            removeFromCart(productId, addons);
            return;
        }
        
        if (newQuantity > 99) {
            newQuantity = 99;
            showNotification('Maximum quantity reached (99)', 'warning');
        }
        
        cart[itemIndex].qty = newQuantity;
        
        // Update and save
        await updateCartAndSave();
        
        showNotification('Quantity updated', 'success');
    }
}

// ================= REMOVE FROM CART =================
async function removeFromCart(productId, addons) {
    cart = cart.filter(item => {
        if (item.id !== productId) return true;
        
        const itemAddons = item.selectedAddons || [];
        const compareAddons = addons || [];
        
        if (itemAddons.length !== compareAddons.length) return true;
        
        const sameAddons = itemAddons.every((addon, index) => 
            addon.title === compareAddons[index]?.title && 
            parseFloat(addon.price) === parseFloat(compareAddons[index]?.price)
        );
        
        return !sameAddons;
    });
    
    // Update and save
    await updateCartAndSave();
    
    showNotification('Item removed from cart', 'success');
}

// ================= REMOVE ALL FROM RESTAURANT =================
async function removeAllFromRestaurant() {
    if (!currentRestaurant || cart.length === 0) return;
    
    if (confirm('Are you sure you want to remove all items from this restaurant?')) {
        cart = cart.filter(item => (item.vendorID || item.restaurant_id) !== currentRestaurant);
        
        // Update and save
        await updateCartAndSave();
        
        showNotification('All items removed from restaurant', 'success');
    }
}

// ================= UPDATE ORDER SUMMARY =================
function updateOrderSummary(subtotal) {
    const deliveryFee = restaurantDetails ? parseFloat(restaurantDetails.deliveryCharge) || 0 : 0;
    const serviceFee = subtotal * 0.02; // 2% service fee
    const tax = subtotal * 0.12; // 12% VAT
    const total = subtotal + deliveryFee + serviceFee + tax;
    
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('deliveryFee').textContent = `₱${deliveryFee.toFixed(2)}`;
    document.getElementById('serviceFee').textContent = `₱${serviceFee.toFixed(2)}`;
    document.getElementById('tax').textContent = `₱${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `₱${total.toFixed(2)}`;
}

// ================= UPDATE CART COUNT =================
function updateCartCount() {
    const totalItems = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
        cartCountElement.style.display = totalItems > 0 ? 'flex' : 'none';
    }
}

// ================= RESTAURANT SWITCH MODAL =================
function showRestaurantSwitchModal(newItem) {
    pendingItemToAdd = newItem;
    
    // Fetch restaurant details for comparison
    fetchRestaurantDetails(cart[0].vendorID || cart[0].restaurant_id).then(currentRestaurant => {
        fetchRestaurantDetails(newItem.vendorID || newItem.restaurant_id).then(newRestaurant => {
            // Set current restaurant info
            document.getElementById('currentRestaurantLogo').src = currentRestaurant.logo;
            document.getElementById('currentRestaurantLogo').alt = currentRestaurant.name;
            document.getElementById('currentRestaurantName').textContent = currentRestaurant.name;
            
            // Set new restaurant info
            document.getElementById('newRestaurantLogo').src = newRestaurant.logo;
            document.getElementById('newRestaurantLogo').alt = newRestaurant.name;
            document.getElementById('newRestaurantName').textContent = newRestaurant.name;
            
            // Show modal
            document.getElementById('switchModal').classList.add('active');
        });
    }).catch(() => {
        // Fallback for new restaurant
        document.getElementById('newRestaurantLogo').src = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
        document.getElementById('newRestaurantName').textContent = 'New Restaurant';
        document.getElementById('switchModal').classList.add('active');
    });
}

// ================= CLOSE SWITCH MODAL =================
function closeSwitchModal() {
    document.getElementById('switchModal').classList.remove('active');
    pendingItemToAdd = null;
}

// ================= CONFIRM RESTAURANT SWITCH =================
async function confirmRestaurantSwitch() {
    if (pendingItemToAdd) {
        // Clear current cart and add new item
        cart = [pendingItemToAdd];
        
        // Update and save
        await updateCartAndSave();
        
        // Close modal
        closeSwitchModal();
        
        showNotification('Cart cleared and new item added', 'success');
    }
}

// ================= CLEAR CART =================
async function clearCart() {
    if (cart.length === 0) {
        showNotification('Your cart is already empty', 'warning');
        return;
    }
    
    if (confirm('Are you sure you want to clear your entire cart?')) {
        cart = [];
        
        // Update and save
        await updateCartAndSave();
        
        showNotification('Cart cleared', 'success');
    }
}

// ================= PROCEED TO CHECKOUT =================
function proceedToCheckout() {
    console.log('Proceed to checkout clicked');
    console.log('User logged in:', isUserLoggedIn);
    console.log('Cart items:', cart.length);
    
    if (cart.length === 0) {
        showNotification('Your cart is empty', 'warning');
        return;
    }
    
    // Check if user is logged in
    if (!isUserLoggedIn) {
        showNotification('Please login to proceed to checkout', 'warning');
        setTimeout(() => {
            // Redirect to login with return URL
            const currentUrl = encodeURIComponent(window.location.href);
            window.location.href = `login.php?redirect=${currentUrl}`;
        }, 1500);
        return;
    }
    
    // Check if user has location set (using PHP session)
    <?php if ($isLoggedIn && !$hasSavedLocation): ?>
    showNotification('Please set your delivery location first', 'warning');
    setTimeout(() => {
        // Redirect to profile to set location
        window.location.href = 'profile.php?tab=location&return=' + encodeURIComponent('cart.php');
    }, 1500);
    return;
    <?php endif; ?>
    
    // All checks passed, proceed to checkout
    showNotification('Proceeding to checkout...', 'success');
    
    // Save cart one more time
    if (isUserLoggedIn) {
        saveCartToFirebase().then(() => {
            setTimeout(() => {
                window.location.href = 'checkout.php';
            }, 1000);
        }).catch(error => {
            console.error('Error saving cart:', error);
            // Still proceed to checkout even if save fails
            setTimeout(() => {
                window.location.href = 'checkout.php';
            }, 1000);
        });
    }
}

// ================= NOTIFICATION SYSTEM =================
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'warning' ? 'exclamation-triangle' : 
                 type === 'error' ? 'exclamation-circle' : 'info-circle';
    
    notification.innerHTML = `
        <i class="fas fa-${icon}" style="color: ${type === 'success' ? '#27AE60' : type === 'warning' ? '#F39C12' : type === 'error' ? '#E74C3C' : '#3498DB'};"></i>
        <span style="font-weight: 500;">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideIn 0.3s ease reverse forwards';
            setTimeout(() => notification.remove(), 300);
        }
    }, 3000);
}

// ================= ADD TO CART FROM EXTERNAL SOURCE =================
// This function can be called from other pages
function addToCartFromExternal(productData) {
    const newItem = {
        id: productData.id,
        name: productData.name,
        price: productData.price,
        qty: productData.qty || 1,
        photo: productData.photo,
        vendorID: productData.vendorID,
        selectedAddons: productData.selectedAddons || [],
        timestamp: new Date().toISOString()
    };
    
    // Check if cart already has items
    if (cart.length > 0) {
        const currentVendorId = cart[0].vendorID;
        
        if (newItem.vendorID !== currentVendorId) {
            // Different restaurant - show switch modal
            showRestaurantSwitchModal(newItem);
            return;
        }
    }
    
    // Same restaurant or empty cart - add item
    cart.push(newItem);
    
    // Update and save
    updateCartAndSave();
    
    showNotification('Item added to cart', 'success');
}

// ================= LISTEN FOR BEFORE UNLOAD (SAVE CART) =================
window.addEventListener('beforeunload', function() {
    // Save cart to session for guest users
    if (!isUserLoggedIn && cart.length > 0) {
        saveCartToSessionStorage();
    }
});
</script>

</body>
</html>
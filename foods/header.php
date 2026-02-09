<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set base URL for consistent paths
$base_url = dirname(dirname($_SERVER['PHP_SELF']));
if ($base_url == '/') {
    $base_url = '';
}

// Debug output
echo "<!-- DEBUG: Session ID: " . session_id() . " -->\n";
echo "<!-- DEBUG: Current directory: " . __DIR__ . " -->\n";

// Default values
$isLoggedIn = false;
$userName = 'Guest';
$userInitial = 'G';
$profilePictureUrl = null;
$loginPage = $base_url . "/login.php";
$userEmail = '';
$userId = '';
$cartItemCount = 0;

// Check if user is logged in - Look for Firebase user data in session
if (isset($_SESSION['firebase_user']) || isset($_SESSION['user_data']) || isset($_SESSION['uid'])) {
    $isLoggedIn = true;
    
    // Get user data from session
    if (isset($_SESSION['firebase_user'])) {
        $userData = $_SESSION['firebase_user'];
    } elseif (isset($_SESSION['user_data'])) {
        $userData = $_SESSION['user_data'];
    } else {
        $userData = $_SESSION;
    }
    
    // Get user ID
    $userId = $userData['id'] ?? $userData['uid'] ?? $userData['user_id'] ?? '';
    
    // Get user name
    $firstName = $userData['firstName'] ?? $userData['first_name'] ?? '';
    $lastName = $userData['lastName'] ?? $userData['last_name'] ?? '';
    
    if (!empty($firstName) && !empty($lastName)) {
        $userName = trim($firstName . ' ' . $lastName);
    } elseif (!empty($firstName)) {
        $userName = trim($firstName);
    } elseif (!empty($lastName)) {
        $userName = trim($lastName);
    } else {
        $userName = $userData['email'] ?? $userData['display_name'] ?? 'User';
    }
    
    // Get user email
    $userEmail = $userData['email'] ?? '';
    
    // Get initial from name or email
    if (!empty($userName) && $userName !== 'User') {
        $userInitial = strtoupper(substr($userName, 0, 1));
    } elseif (!empty($userEmail)) {
        $userInitial = strtoupper(substr($userEmail, 0, 1));
    } else {
        $userInitial = 'U';
    }
    
    // Check for profile picture URL in session data
    $profilePictureUrl = $userData['profilePictureURL'] ?? $userData['profile_picture_url'] ?? $userData['photoURL'] ?? null;
}

// Get cart item count from session or initialize
if (isset($_SESSION['cart'])) {
    $cartItemCount = count($_SESSION['cart']);
} elseif (isset($_SESSION['cart_items'])) {
    $cartItemCount = count($_SESSION['cart_items']);
}

// Determine active page for navigation highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>LalaGO - Food Delivery</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ================== GLOBAL RESPONSIVE SETTINGS ================== */
        * {
            box-sizing: border-box;
        }
        
        /* ================== HEADER STYLES ================== */
        .main-header {
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5%;
            border-bottom: 1px solid #eaeaea;
            gap: 15px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 0 1 auto;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            color: #ff6600;
            text-decoration: none;
            font-family: 'Segoe UI', Arial, sans-serif;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
            line-height: 1.2;
        }

        .logo-text span {
            color: #333333;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: #666;
            cursor: pointer;
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn:hover {
            background-color: #f5f5f5;
            color: #ff6600;
        }

        .location-selector {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 150px;
            max-width: 220px;
            flex: 0 1 auto;
        }

        .location-selector:hover {
            background-color: #e9ecef;
        }

        .location-icon {
            color: #ff6600;
            flex-shrink: 0;
            font-size: 14px;
        }

        .location-text {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        .chevron-down {
            font-size: 11px;
            color: #666;
            flex-shrink: 0;
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .action-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #666;
            cursor: pointer;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            flex-shrink: 0;
        }

        .action-btn:hover {
            background-color: #f5f5f5;
            color: #ff6600;
        }

        /* Cart Badge Styles */
        .cart-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: #ff4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 68, 68, 0.7);
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 5px rgba(255, 68, 68, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 68, 68, 0);
            }
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            max-width: 200px;
            min-width: 0;
            flex: 0 1 auto;
        }

        .user-profile:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-initial {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #ff6600, #ff9500);
            color: white;
            font-weight: 700;
            font-size: 16px;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        /* Main Navigation */
        .main-nav {
            padding: 0 5%;
            background-color: white;
            border-bottom: 1px solid #eaeaea;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .main-nav::-webkit-scrollbar {
            display: none;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            padding: 0;
            margin: 0;
            min-width: min-content;
        }

        .nav-links li {
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            padding: 16px 0;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
        }

        .nav-links a:hover {
            color: #ff6600;
        }

        .nav-links a.active {
            color: #ff6600;
        }

        .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background-color: #ff6600;
            border-radius: 3px 3px 0 0;
        }

        /* Mobile Navigation */
        .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: white;
            z-index: 1001;
            display: none;
            flex-direction: column;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .mobile-nav.open {
            transform: translateX(0);
            display: flex;
        }

        .mobile-nav-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            border-bottom: 1px solid #eaeaea;
            background-color: white;
            position: sticky;
            top: 0;
            z-index: 1;
            flex-shrink: 0;
        }

        .mobile-user-status {
            padding: 20px 5%;
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            flex-shrink: 0;
        }

        .mobile-user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .mobile-user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .mobile-user-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .mobile-action-btn {
            padding: 12px;
            border: 2px solid #eaeaea;
            background-color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .mobile-action-btn:hover {
            background-color: #ff6600;
            color: white;
            border-color: #ff6600;
        }

        .mobile-nav-links {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
        }

        .mobile-nav-links li {
            border-bottom: 1px solid #eaeaea;
        }

        .mobile-nav-links li:last-child {
            border-bottom: none;
        }

        .mobile-nav-links a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 5%;
            text-decoration: none;
            color: #333;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .mobile-nav-links a:hover {
            background-color: #f8f9fa;
            color: #ff6600;
        }

        .mobile-nav-links a i {
            width: 24px;
            text-align: center;
            color: #ff6600;
        }

        .close-mobile-nav {
            background: none;
            border: none;
            font-size: 22px;
            color: #666;
            cursor: pointer;
            padding: 8px;
            border-radius: 5px;
            transition: all 0.3s ease;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-mobile-nav:hover {
            background-color: #f5f5f5;
            color: #ff6600;
        }

        /* Tablet Styles (1024px and below) */
        @media (max-width: 1024px) {
            .header-top {
                padding: 15px 3%;
            }
            
            .main-nav {
                padding: 0 3%;
            }
            
            .logo-text {
                font-size: 26px;
                max-width: 180px;
            }
            
            .nav-links {
                gap: 18px;
            }
            
            .nav-links a {
                font-size: 13.5px;
            }
        }

        /* Small Tablet (768px and below) */
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
            }
            
            .header-top {
                padding: 12px 15px;
                gap: 10px;
            }
            
            .logo-text {
                font-size: 24px;
                max-width: 160px;
            }
            
            .location-selector {
                min-width: 120px;
                padding: 8px 10px;
                margin: 0 auto;
            }
            
            .location-text {
                font-size: 13px;
                max-width: 80px;
            }
            
            .user-name {
                display: none;
            }
            
            .user-profile {
                padding: 6px;
            }
            
            .main-nav {
                display: none;
            }
            
            .user-actions {
                gap: 8px;
            }
            
            .action-btn {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .user-avatar {
                width: 36px;
                height: 36px;
            }
            
            .avatar-initial {
                font-size: 15px;
            }
            
            .mobile-user-avatar {
                width: 45px;
                height: 45px;
            }
            
            .cart-badge {
                width: 16px;
                height: 16px;
                font-size: 10px;
            }
        }

        /* Mobile Landscape (640px and below) */
        @media (max-width: 640px) {
            .logo-text {
                font-size: 22px;
                max-width: 140px;
            }
            
            .location-selector {
                min-width: 100px;
                padding: 7px 8px;
            }
            
            .location-text {
                font-size: 12px;
                max-width: 65px;
            }
            
            .location-icon, .chevron-down {
                font-size: 12px;
            }
            
            .user-profile {
                padding: 5px;
            }
            
            .user-avatar {
                width: 34px;
                height: 34px;
            }
            
            .mobile-menu-btn {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
            
            .cart-badge {
                width: 15px;
                height: 15px;
                font-size: 9px;
            }
        }

        /* Mobile Portrait (480px and below) */
        @media (max-width: 480px) {
            .header-top {
                padding: 10px 12px;
                gap: 8px;
            }
            
            .logo-text {
                font-size: 20px;
                max-width: 120px;
            }
            
            .location-selector {
                min-width: 90px;
                padding: 6px 7px;
            }
            
            .location-text {
                font-size: 11px;
                max-width: 55px;
            }
            
            .user-actions {
                gap: 6px;
            }
            
            .action-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
            
            .user-avatar {
                width: 32px;
                height: 32px;
            }
            
            .avatar-initial {
                font-size: 14px;
            }
            
            .mobile-menu-btn {
                width: 36px;
                height: 36px;
                font-size: 18px;
                padding: 6px;
            }
            
            .mobile-user-info {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            
            .mobile-user-actions {
                grid-template-columns: 1fr;
            }
            
            .cart-badge {
                width: 14px;
                height: 14px;
                font-size: 8px;
            }
        }

        /* Extra Small Mobile (375px and below) */
        @media (max-width: 375px) {
            .logo-text {
                font-size: 18px;
                max-width: 100px;
            }
            
            .location-selector {
                min-width: 80px;
                padding: 5px 6px;
                gap: 5px;
            }
            
            .location-text {
                font-size: 10px;
                max-width: 45px;
            }
            
            .action-btn {
                width: 34px;
                height: 34px;
            }
            
            .user-avatar {
                width: 30px;
                height: 30px;
            }
            
            .logo-container {
                gap: 8px;
            }
            
            .mobile-menu-btn {
                width: 34px;
                height: 34px;
            }
            
            .cart-badge {
                width: 13px;
                height: 13px;
                font-size: 7px;
            }
        }

        /* Search Overlay */
        .search-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.98);
            z-index: 1002;
            display: none;
            align-items: flex-start;
            justify-content: center;
            padding-top: 100px;
        }

        .search-overlay.active {
            display: flex;
        }
        
        /* Mobile-specific search overlay */
        @media (max-width: 768px) {
            .search-overlay {
                padding-top: 80px;
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .search-overlay input {
                font-size: 16px !important; /* Prevents zoom on iOS */
            }
        }
        
        /* Skip to content for accessibility */
        .skip-to-content {
            position: absolute;
            top: -40px;
            left: 0;
            background: #ff6600;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            z-index: 10000;
            border-radius: 0 0 4px 0;
            transition: top 0.3s;
        }
        
        .skip-to-content:focus {
            top: 0;
        }
        
        /* Main content spacing */
        .page-content {
            padding-top: 120px;
            min-height: calc(100vh - 120px);
        }
        
        @media (max-width: 768px) {
            .page-content {
                padding-top: 100px;
                min-height: calc(100vh - 100px);
            }
        }
        
        @media (max-width: 480px) {
            .page-content {
                padding-top: 90px;
                min-height: calc(100vh - 90px);
            }
        }
    </style>
</head>
<body>
    <!-- Skip to content link -->
    <a href="#main-content" class="skip-to-content">Skip to main content</a>
    
    <!-- Main Header -->
    <header class="main-header">
        <div class="header-top">
            <div class="logo-container">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="<?php echo $base_url; ?>/index.php" class="logo-text" title="LalaGO Home">
                    Lala<span>GO</span>
                </a>
            </div>
            
            <div class="location-selector" id="locationSelector">
                <i class="fas fa-map-marker-alt location-icon"></i>
                <span class="location-text" id="currentLocation">Select Location</span>
                <i class="fas fa-chevron-down chevron-down"></i>
            </div>
            
            <div class="user-actions">
                <button class="action-btn" id="searchBtn" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
                
                <button class="action-btn" id="cartBtn" title="Cart" aria-label="Shopping Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if($cartItemCount > 0): ?>
                        <span class="cart-badge" id="cartBadge"><?php echo $cartItemCount; ?></span>
                    <?php endif; ?>
                </button>
                
                <?php if($isLoggedIn): ?>
                    <a href="<?php echo $base_url; ?>/profile.php" class="user-profile" aria-label="Your profile">
                        <div class="user-avatar">
                            <?php if(!empty($profilePictureUrl) && $profilePictureUrl !== ''): ?>
                                <img src="<?php echo htmlspecialchars($profilePictureUrl); ?>" 
                                     alt="<?php echo htmlspecialchars($userName); ?>" 
                                     class="avatar-img"
                                     onerror="this.onerror=null; this.style.display='none';">
                            <?php endif; ?>
                            <?php if(empty($profilePictureUrl) || $profilePictureUrl === ''): ?>
                                <div class="avatar-initial">
                                    <?php echo htmlspecialchars($userInitial); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $loginPage; ?>" class="user-profile" aria-label="Login or register">
                        <div class="user-avatar">
                            <div class="avatar-initial">G</div>
                        </div>
                        <span class="user-name">Guest</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <nav class="main-nav" aria-label="Main Navigation">
            <ul class="nav-links">
                <li>
                    <a href="<?php echo $base_url; ?>/index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                        <i class="fas fa-home"></i> <span class="nav-text">Home</span>
                    </a>
                </li>
                <li>
                    <a href="categories.php" class="<?php echo ($currentPage == 'categories.php') ? 'active' : ''; ?>">
                        <i class="fas fa-utensils"></i> <span class="nav-text">Restaurants</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>/offers.php" class="<?php echo ($currentPage == 'offers.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tag"></i> <span class="nav-text">Offers</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>/contact.php" class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">
                        <i class="fas fa-phone"></i> <span class="nav-text">Contact</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>/about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
                        <i class="fas fa-info-circle"></i> <span class="nav-text">About</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-nav" id="mobileNav" role="dialog" aria-modal="true" aria-label="Mobile Menu">
        <div class="mobile-nav-header">
            <div class="logo-container">
                <a href="<?php echo $base_url; ?>/index.php" class="logo-text">Lala<span>GO</span></a>
            </div>
            <button class="close-mobile-nav" id="closeMobileNav" aria-label="Close menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-user-status">
            <div class="mobile-user-info">
                <div class="mobile-user-avatar">
                    <?php if($isLoggedIn && !empty($profilePictureUrl) && $profilePictureUrl !== ''): ?>
                        <img src="<?php echo htmlspecialchars($profilePictureUrl); ?>" 
                             alt="<?php echo htmlspecialchars($userName); ?>" 
                             class="avatar-img"
                             onerror="this.onerror=null; this.style.display='none';">
                    <?php endif; ?>
                    <?php if($isLoggedIn && (empty($profilePictureUrl) || $profilePictureUrl === '')): ?>
                        <div class="avatar-initial">
                            <?php echo htmlspecialchars($userInitial); ?>
                        </div>
                    <?php endif; ?>
                    <?php if(!$isLoggedIn): ?>
                        <div class="avatar-initial">G</div>
                    <?php endif; ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($userName); ?></strong>
                    <p style="font-size: 12px; color: #666; margin-top: 2px;">
                        <?php echo $isLoggedIn ? 'Logged In' : 'Guest User'; ?>
                        <?php if(!empty($userEmail)): ?>
                            <br><small><?php echo htmlspecialchars($userEmail); ?></small>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="mobile-user-actions">
                <?php if($isLoggedIn): ?>
                    <button class="mobile-action-btn" onclick="window.location.href='<?php echo $base_url; ?>/profile.php'">
                        <i class="fas fa-user"></i> Profile
                    </button>
                    <button class="mobile-action-btn" onclick="window.location.href='<?php echo $base_url; ?>/logout.php'">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                <?php else: ?>
                    <button class="mobile-action-btn" onclick="window.location.href='<?php echo $loginPage; ?>'">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                    <button class="mobile-action-btn" onclick="window.location.href='<?php echo $base_url; ?>/register.php'">
                        <i class="fas fa-user-plus"></i> Register
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="location-selector" style="margin: 20px 5%;" id="mobileLocationSelector">
            <i class="fas fa-map-marker-alt location-icon"></i>
            <span class="location-text">Select Location</span>
            <i class="fas fa-chevron-down chevron-down"></i>
        </div>
        
        <ul class="mobile-nav-links">
            <li><a href="<?php echo $base_url; ?>/index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="categories.php"><i class="fas fa-utensils"></i> Restaurants</a></li>
            <li><a href="<?php echo $base_url; ?>/offers.php"><i class="fas fa-tag"></i> Offers</a></li>
            <li><a href="<?php echo $base_url; ?>/contact.php"><i class="fas fa-phone"></i> Contact</a></li>
            <li><a href="<?php echo $base_url; ?>/about.php"><i class="fas fa-info-circle"></i> About</a></li>
            <?php if($isLoggedIn): ?>
                <li><a href="<?php echo $base_url; ?>/orders.php"><i class="fas fa-clipboard-list"></i> My Orders</a></li>
                <li><a href="<?php echo $base_url; ?>/favorites.php"><i class="fas fa-heart"></i> Favorites</a></li>
                <li><a href="<?php echo $base_url; ?>/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Search Overlay -->
    <div class="search-overlay" id="searchOverlay">
        <div style="width: 90%; max-width: 600px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #333;">Search Restaurants & Dishes</h3>
                <button id="closeSearch" style="background: none; border: none; font-size: 24px; color: #666; cursor: pointer; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;" aria-label="Close search">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <input type="text" 
                   id="headerSearchInput" 
                   placeholder="What are you craving today?" 
                   style="width: 100%; padding: 16px; font-size: 18px; border: 2px solid #ddd; border-radius: 8px; outline: none;"
                   aria-label="Search input">
        </div>
    </div>

    <!-- Location Modal -->
    <div id="locationModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1003; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; padding: 25px; border-radius: 12px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
            <h3 style="margin-bottom: 10px; color: #333;">Select Your Location</h3>
            <p style="color: #666; margin-bottom: 20px; font-size: 14px;">Choose your delivery location to see available restaurants</p>
            
            <div style="display: grid; gap: 10px; margin-bottom: 20px;">
                <div class="location-option" data-location="Downtown" style="padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-city" style="color: #ff6600; font-size: 20px;"></i>
                        <div>
                            <strong>Downtown</strong>
                            <p style="font-size: 12px; color: #666; margin-top: 2px;">Delivery: 20-30 min</p>
                        </div>
                    </div>
                </div>
                <div class="location-option" data-location="Midtown" style="padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-building" style="color: #ff6600; font-size: 20px;"></i>
                        <div>
                            <strong>Midtown</strong>
                            <p style="font-size: 12px; color: #666; margin-top: 2px;">Delivery: 25-35 min</p>
                        </div>
                    </div>
                </div>
                <div class="location-option" data-location="Uptown" style="padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-university" style="color: #ff6600; font-size: 20px;"></i>
                        <div>
                            <strong>Uptown</strong>
                            <p style="font-size: 12px; color: #666; margin-top: 2px;">Delivery: 30-40 min</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; justify-content: flex-end; flex-wrap: wrap;">
                <button id="cancelLocation" style="padding: 12px 20px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; font-weight: 600; min-width: 100px;">
                    Cancel
                </button>
                <button id="confirmLocation" style="padding: 12px 20px; background: #ff6600; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; min-width: 100px;">
                    Confirm
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="page-content" id="main-content">

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing header...');
            
            // ============ CART NOTIFICATION BADGE ============
            function updateCartBadge() {
                const cartBadge = document.getElementById('cartBadge');
                // Try to get cart from localStorage first, then from session
                let cartItems = [];
                
                // Check localStorage
                try {
                    const cartData = localStorage.getItem('cart');
                    if (cartData) {
                        cartItems = JSON.parse(cartData);
                    }
                } catch (e) {
                    console.log('Error reading cart from localStorage:', e);
                }
                
                // If no items in localStorage, check PHP session variable
                if (cartItems.length === 0 && <?php echo $cartItemCount; ?> > 0) {
                    cartItems = Array(<?php echo $cartItemCount; ?>).fill({}); // Create dummy array for count
                }
                
                // Calculate total items
                let totalItems = 0;
                if (Array.isArray(cartItems)) {
                    totalItems = cartItems.length;
                } else if (typeof cartItems === 'object' && cartItems !== null) {
                    // If cart is an object with items property
                    if (cartItems.items && Array.isArray(cartItems.items)) {
                        totalItems = cartItems.items.length;
                    } else {
                        // Count object properties
                        totalItems = Object.keys(cartItems).length;
                    }
                }
                
                // Update or create badge
                if (totalItems > 0) {
                    if (!cartBadge) {
                        // Create badge if it doesn't exist
                        const newBadge = document.createElement('span');
                        newBadge.className = 'cart-badge';
                        newBadge.id = 'cartBadge';
                        newBadge.textContent = totalItems;
                        document.getElementById('cartBtn').appendChild(newBadge);
                    } else {
                        // Update existing badge
                        cartBadge.textContent = totalItems;
                        cartBadge.style.display = 'flex';
                    }
                } else if (cartBadge) {
                    // Hide badge if no items
                    cartBadge.style.display = 'none';
                }
                
                console.log('Cart updated. Total items:', totalItems);
                return totalItems;
            }
            
            // Initial badge update
            updateCartBadge();
            
            // ============ MOBILE NAVIGATION ============
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const mobileNav = document.getElementById('mobileNav');
            const closeMobileNav = document.getElementById('closeMobileNav');
            
            function openMobileNav() {
                console.log('Opening mobile menu');
                mobileNav.classList.add('open');
                document.body.style.overflow = 'hidden';
                // Focus on close button for accessibility
                setTimeout(() => closeMobileNav.focus(), 100);
            }
            
            function closeMobileNavFunc() {
                console.log('Closing mobile menu');
                mobileNav.classList.remove('open');
                document.body.style.overflow = '';
                // Return focus to menu button
                mobileMenuBtn.focus();
            }
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', openMobileNav);
                // Add keyboard support
                mobileMenuBtn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openMobileNav();
                    }
                });
            }
            
            if (closeMobileNav) {
                closeMobileNav.addEventListener('click', closeMobileNavFunc);
                closeMobileNav.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeMobileNavFunc();
                    }
                });
            }
            
            // Close mobile nav when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
                    closeMobileNavFunc();
                }
            });
            
            // Close mobile nav when clicking outside (on backdrop)
            if (mobileNav) {
                mobileNav.addEventListener('click', function(e) {
                    if (e.target === mobileNav) {
                        closeMobileNavFunc();
                    }
                });
            }
            
            // ============ SEARCH FUNCTIONALITY ============
            const searchBtn = document.getElementById('searchBtn');
            const searchOverlay = document.getElementById('searchOverlay');
            const closeSearch = document.getElementById('closeSearch');
            const headerSearchInput = document.getElementById('headerSearchInput');
            
            function openSearch() {
                searchOverlay.classList.add('active');
                if (headerSearchInput) {
                    headerSearchInput.focus();
                }
                document.body.style.overflow = 'hidden';
            }
            
            function closeSearchFunc() {
                searchOverlay.classList.remove('active');
                document.body.style.overflow = '';
                // Return focus to search button
                searchBtn.focus();
            }
            
            if (searchBtn && searchOverlay) {
                searchBtn.addEventListener('click', openSearch);
                searchBtn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openSearch();
                    }
                });
            }
            
            if (closeSearch) {
                closeSearch.addEventListener('click', closeSearchFunc);
            }
            
            // Close search when pressing Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                    closeSearchFunc();
                }
            });
            
            // Close search when clicking outside
            if (searchOverlay) {
                searchOverlay.addEventListener('click', function(e) {
                    if (e.target === searchOverlay) {
                        closeSearchFunc();
                    }
                });
            }
            
            // Handle search input
            if (headerSearchInput) {
                headerSearchInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        const searchTerm = this.value.trim();
                        if (searchTerm) {
                            // Redirect to categories page with search term
                            window.location.href = 'categories.php?search=' + encodeURIComponent(searchTerm);
                        }
                    }
                });
            }
            
            // ============ LOCATION SELECTOR ============
            const locationSelector = document.getElementById('locationSelector');
            const mobileLocationSelector = document.getElementById('mobileLocationSelector');
            const locationModal = document.getElementById('locationModal');
            const cancelLocation = document.getElementById('cancelLocation');
            const confirmLocation = document.getElementById('confirmLocation');
            const currentLocation = document.getElementById('currentLocation');
            const locationOptions = document.querySelectorAll('.location-option');
            
            let selectedLocation = null;
            
            // Open location modal
            function openLocationModal() {
                if (locationModal) {
                    locationModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    // Focus first location option
                    if (locationOptions.length > 0) {
                        locationOptions[0].focus();
                    }
                }
            }
            
            if (locationSelector) {
                locationSelector.addEventListener('click', openLocationModal);
                locationSelector.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openLocationModal();
                    }
                });
            }
            
            if (mobileLocationSelector) {
                mobileLocationSelector.addEventListener('click', openLocationModal);
            }
            
            // Handle location selection
            if (locationOptions.length > 0) {
                locationOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        selectLocationOption(this);
                    });
                    
                    option.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            selectLocationOption(this);
                        }
                    });
                    
                    // Make option focusable for keyboard navigation
                    option.setAttribute('tabindex', '0');
                });
            }
            
            function selectLocationOption(option) {
                // Remove selection from all options
                locationOptions.forEach(opt => {
                    opt.style.borderColor = '#ddd';
                    opt.style.backgroundColor = '';
                    opt.removeAttribute('aria-selected');
                });
                
                // Highlight selected option
                option.style.borderColor = '#ff6600';
                option.style.backgroundColor = '#f8f9fa';
                option.setAttribute('aria-selected', 'true');
                selectedLocation = option.getAttribute('data-location');
                option.focus();
            }
            
            // Cancel location selection
            if (cancelLocation) {
                cancelLocation.addEventListener('click', closeLocationModal);
                cancelLocation.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeLocationModal();
                    }
                });
            }
            
            // Confirm location selection
            if (confirmLocation) {
                confirmLocation.addEventListener('click', confirmLocationSelection);
                confirmLocation.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        confirmLocationSelection();
                    }
                });
            }
            
            function closeLocationModal() {
                locationModal.style.display = 'none';
                document.body.style.overflow = '';
                selectedLocation = null;
                // Return focus to location selector
                if (window.innerWidth >= 768) {
                    locationSelector.focus();
                } else if (mobileNav.classList.contains('open')) {
                    mobileLocationSelector.focus();
                }
            }
            
            function confirmLocationSelection() {
                if (selectedLocation) {
                    // Update location text in both header and mobile
                    if (currentLocation) {
                        currentLocation.textContent = selectedLocation;
                    }
                    if (mobileLocationSelector) {
                        const mobileLocationText = mobileLocationSelector.querySelector('.location-text');
                        if (mobileLocationText) {
                            mobileLocationText.textContent = selectedLocation;
                        }
                    }
                    
                    // Save to localStorage for persistence
                    localStorage.setItem('selectedLocation', selectedLocation);
                    
                    // Show notification
                    showNotification('Location set to: ' + selectedLocation);
                }
                
                closeLocationModal();
            }
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && locationModal.style.display === 'flex') {
                    closeLocationModal();
                }
            });
            
            // Close modal when clicking outside
            locationModal.addEventListener('click', function(e) {
                if (e.target === locationModal) {
                    closeLocationModal();
                }
            });
            
            // Load saved location from localStorage
            function loadSavedLocation() {
                const savedLocation = localStorage.getItem('selectedLocation');
                if (savedLocation && currentLocation) {
                    currentLocation.textContent = savedLocation;
                    if (mobileLocationSelector) {
                        const mobileLocationText = mobileLocationSelector.querySelector('.location-text');
                        if (mobileLocationText) {
                            mobileLocationText.textContent = savedLocation;
                        }
                    }
                }
            }
            
            loadSavedLocation();
            
            // ============ CART BUTTON CLICK HANDLER ============
            const cartBtn = document.getElementById('cartBtn');
            
            if (cartBtn) {
                cartBtn.addEventListener('click', function() {
                    window.location.href = '<?php echo $base_url; ?>/cart.php';
                });
                
                cartBtn.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        window.location.href = '<?php echo $base_url; ?>/cart.php';
                    }
                });
            }
            
            // ============ HEADER SCROLL EFFECT ============
            const header = document.querySelector('.main-header');
            
            function updateHeaderOnScroll() {
                if (header) {
                    if (window.scrollY > 50) {
                        header.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                    } else {
                        header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
                    }
                }
            }
            
            window.addEventListener('scroll', updateHeaderOnScroll);
            updateHeaderOnScroll(); // Initial call
            
            // ============ TOUCH DEVICE DETECTION ============
            function isTouchDevice() {
                return 'ontouchstart' in window || navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0;
            }
            
            if (isTouchDevice()) {
                document.body.classList.add('touch-device');
            } else {
                document.body.classList.add('no-touch-device');
            }
            
            // ============ PROFILE PICTURE LOAD CHECK ============
            document.querySelectorAll('.avatar-img').forEach(img => {
                // Check if image is already loaded
                if (img.complete) {
                    handleImageLoad(img);
                } else {
                    // Set up load and error events
                    img.addEventListener('load', function() {
                        handleImageLoad(this);
                    });
                    img.addEventListener('error', function() {
                        handleImageError(this);
                    });
                }
            });
            
            function handleImageLoad(img) {
                if (img.naturalWidth === 0) {
                    handleImageError(img);
                } else {
                    // Image loaded successfully, hide initial
                    const parent = img.parentElement;
                    const initial = parent.querySelector('.avatar-initial');
                    if (initial) {
                        initial.style.display = 'none';
                    }
                }
            }
            
            function handleImageError(img) {
                console.log('Profile picture failed to load, showing initial');
                img.style.display = 'none';
                const parent = img.parentElement;
                const initial = parent.querySelector('.avatar-initial');
                if (initial) {
                    initial.style.display = 'flex';
                }
            }
            
            // ============ HELPER FUNCTIONS ============
            function showNotification(message) {
                // Create a simple notification toast
                const toast = document.createElement('div');
                toast.textContent = message;
                toast.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #333;
                    color: white;
                    padding: 12px 20px;
                    border-radius: 6px;
                    z-index: 10000;
                    animation: fadeInOut 3s ease;
                    font-size: 14px;
                `;
                
                // Add animation styles
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes fadeInOut {
                        0% { opacity: 0; transform: translateY(-20px); }
                        15% { opacity: 1; transform: translateY(0); }
                        85% { opacity: 1; transform: translateY(0); }
                        100% { opacity: 0; transform: translateY(-20px); }
                    }
                `;
                document.head.appendChild(style);
                
                document.body.appendChild(toast);
                
                // Remove after animation completes
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 3000);
            }
            
            // ============ WINDOW RESIZE HANDLER ============
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // Close mobile nav on resize to larger screens
                    if (window.innerWidth >= 768 && mobileNav.classList.contains('open')) {
                        closeMobileNavFunc();
                    }
                }, 250);
            });
            
            // ============ DEBUG INFO ============
            console.log('Header initialized successfully');
            console.log('User logged in: <?php echo $isLoggedIn ? "YES" : "NO"; ?>');
            console.log('User name: <?php echo $userName; ?>');
            console.log('User ID: <?php echo $userId; ?>');
            console.log('User Email: <?php echo $userEmail; ?>');
            console.log('Profile picture URL: <?php echo !empty($profilePictureUrl) ? htmlspecialchars($profilePictureUrl) : "NOT SET"; ?>');
            console.log('Cart items: <?php echo $cartItemCount; ?>');
            console.log('Base URL: <?php echo $base_url; ?>');
        });
        
        // Global function to update cart (can be called from other pages)
        window.updateCart = function(items) {
            localStorage.setItem('cart', JSON.stringify(items));
            // Update badge immediately
            updateCartBadge();
        };
        
        // Global function to update cart badge
        window.updateCartBadge = function() {
            updateCartBadge();
        };
        
        // Global function to show mobile nav from other pages
        window.showMobileNav = function() {
            const mobileNav = document.getElementById('mobileNav');
            if (mobileNav) {
                mobileNav.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        };
    </script>
<?php
// Include Firebase configuration
include '../inc/firebase.php';
include '../foods/header.php';

// Start session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get vendor ID from URL
$vendorID = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Details - Lalago</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        :root {
            --primary-color: #ff6600;
            --primary-light: #ff8c42;
            --secondary-color: #4CAF50;
            --secondary-light: rgba(76, 175, 80, 0.1);
            --dark-color: #1a1a1a;
            --dark-light: #333;
            --light-color: #f8f9fa;
            --gray-color: #6c757d;
            --gray-light: #e9ecef;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            --box-shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
            padding: 0;
            padding-bottom: 120px;
            padding-top: 80px;
        }
        
        /* Header Hide on Scroll */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }
        
        header.hide {
            transform: translateY(-100%);
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Loading State */
        .loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 400px;
            text-align: center;
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid var(--gray-light);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Cart Footer */
        .cart-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
            padding: 15px 20px;
            z-index: 1000;
            display: none;
            border-top: 3px solid var(--primary-color);
            transition: transform 0.3s ease;
        }
        
        .cart-footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        
        .cart-total {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .cart-items-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .cart-items-count {
            background: var(--primary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-right: 10px;
        }
        
        .cart-footer-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .view-cart-btn {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: var(--border-radius-sm);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .view-cart-btn:hover {
            background: #3d8b40;
            transform: translateY(-2px);
        }
        
        /* Footer Quantity Controls */
        .footer-quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--gray-light);
            padding: 5px 15px;
            border-radius: 30px;
        }
        
        .footer-qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: white;
            transition: var(--transition);
        }
        
        .footer-qty-btn.minus {
            background: #dc3545;
        }
        
        .footer-qty-btn.minus:hover {
            background: #c82333;
        }
        
        .footer-qty-btn:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }
        
        .footer-qty-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            color: var(--dark-color);
        }
        
        /* Category Tabs */
        .category-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
            padding: 10px;
            background: white;
            border-radius: var(--border-radius-sm);
            box-shadow: var(--box-shadow);
        }
        
        .category-tab {
            padding: 10px 20px;
            border: none;
            background: var(--gray-light);
            color: var(--dark-light);
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            white-space: nowrap;
        }
        
        .category-tab.active {
            background: var(--primary-color);
            color: white;
        }
        
        /* Foods Grid */
        .foods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .food-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--gray-light);
            cursor: pointer;
            position: relative;
        }
        
        .food-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-lg);
        }
        
        .food-image-container {
            height: 180px;
            overflow: hidden;
            position: relative;
        }
        
        .food-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .food-card:hover .food-image {
            transform: scale(1.05);
        }
        
        .food-details {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .food-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            line-height: 1.4;
        }
        
        .food-description {
            font-size: 14px;
            color: var(--gray-color);
            margin-bottom: 15px;
            line-height: 1.5;
            flex-grow: 1;
        }
        
        .food-price {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .food-card-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid var(--gray-light);
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--gray-light);
            padding: 5px;
            border-radius: 30px;
        }
        
        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            color: white;
            transition: var(--transition);
            z-index: 10;
            position: relative;
        }
        
        .qty-btn:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }
        
        .qty-btn.minus {
            background: #dc3545;
        }
        
        .qty-btn.minus:hover {
            background: #c82333;
        }
        
        .qty-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            z-index: 10;
            position: relative;
        }
        
        .add-to-cart-btn {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 10;
            position: relative;
        }
        
        .add-to-cart-btn:hover {
            background: var(--primary-light);
            transform: scale(1.1);
        }
        
        /* Product Card Overlay */
        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.02);
            z-index: 1;
            transition: var(--transition);
        }
        
        .food-card:hover .product-overlay {
            background: rgba(255,102,0,0.05);
        }
        
        /* Restaurant Switch Modal */
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
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUpModal 0.4s ease;
        }
        
        @keyframes slideUpModal {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .switch-modal-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .switch-modal-header i {
            font-size: 2.5rem;
            color: #F39C12;
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
            color: #2D3436;
            margin-bottom: 8px;
        }
        
        .switch-modal-header p {
            color: #6C757D;
            font-size: 0.9rem;
            line-height: 1.4;
        }
        
        .restaurant-comparison {
            background: #F8F9FA;
            border-radius: 16px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .restaurant-compare {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            margin-bottom: 10px;
            background: white;
            border-radius: 8px;
            border: 2px solid #E9ECEF;
        }
        
        .restaurant-compare.current {
            border-color: #FF6B35;
        }
        
        .restaurant-compare.new {
            border-color: #3498DB;
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
            color: #2D3436;
            font-size: 0.95rem;
        }
        
        .restaurant-compare-badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-current {
            background: #FF6B35;
            color: white;
        }
        
        .badge-new {
            background: #3498DB;
            color: white;
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
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 2px solid transparent;
            width: 100%;
        }
        
        .switch-modal-btn.cancel {
            background: transparent;
            color: #6C757D;
            border-color: #E9ECEF;
        }
        
        .switch-modal-btn.cancel:hover {
            background: #E9ECEF;
            color: #495057;
        }
        
        .switch-modal-btn.confirm {
            background: #3498DB;
            color: white;
            border-color: #3498DB;
        }
        
        .switch-modal-btn.confirm:hover {
            background: #FF6B35;
            border-color: #FF6B35;
            transform: translateY(-2px);
        }
        
        /* Toast Notification */
        .toast {
            position: fixed;
            bottom: 120px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 1000;
            display: none;
            max-width: 90vw;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .toast.show {
            display: block;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateX(-50%) translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        
        /* Animation for + button */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.3s ease;
        }
        
        .hidden {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
                padding-bottom: 140px;
            }
            
            .container {
                padding: 15px;
            }
            
            .cart-footer-content {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            .cart-footer-actions {
                width: 100%;
                justify-content: space-between;
            }
            
            .footer-quantity-controls {
                padding: 5px 10px;
            }
            
            .foods-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }
            
            .category-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container" id="vendorContainer">
        <div class="loading-container">
            <div class="loading-spinner"></div>
            <p class="loading-text">Loading restaurant details...</p>
        </div>
    </div>

    <!-- Cart Footer -->
    <div class="cart-footer" id="cartFooter">
        <div class="cart-footer-content">
            <div class="cart-items-info">
                <span class="cart-items-count" id="cartItemsCount">0 items</span>
                <span class="cart-total" id="cartTotal">Total: ₱0.00</span>
            </div>
            <div class="cart-footer-actions">
                <div class="footer-quantity-controls hidden" id="footerQuantityControls">
                    <button class="footer-qty-btn minus" id="footerMinusBtn">-</button>
                    <span class="footer-qty-display" id="footerQtyDisplay">1</span>
                    <button class="footer-qty-btn" id="footerPlusBtn">+</button>
                </div>
                <a href="../cart.php" class="view-cart-btn" id="viewCartBtn">
                    <i class="fas fa-shopping-cart"></i>
                    View Cart
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast"></div>

    <script>
        const vendorContainer = document.getElementById('vendorContainer');
        const cartFooter = document.getElementById('cartFooter');
        const cartItemsCount = document.getElementById('cartItemsCount');
        const cartTotal = document.getElementById('cartTotal');
        const toast = document.getElementById('toast');
        const footerQuantityControls = document.getElementById('footerQuantityControls');
        const footerQtyDisplay = document.getElementById('footerQtyDisplay');
        const footerMinusBtn = document.getElementById('footerMinusBtn');
        const footerPlusBtn = document.getElementById('footerPlusBtn');
        const vendorID = "<?php echo $vendorID; ?>";
        
        let allFoodItems = [];
        let currentCategory = 'all';
        let vendorData = null;
        let cartItemToAdd = null;
        let lastScrollTop = 0;
        let selectedProductId = null;
        
        // Initialize cart from localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Scroll detection for header
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let header = document.querySelector('header');
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down & past 100px
                if (header) header.classList.add('hide');
                // Hide cart footer when scrolling down
                cartFooter.style.transform = 'translateY(100%)';
            } else {
                // Scrolling up
                if (header) header.classList.remove('hide');
                // Show cart footer when scrolling up
                cartFooter.style.transform = 'translateY(0)';
            }
            
            // For cart footer, only show if we're near bottom or cart has items
            if (scrollTop < 100) {
                cartFooter.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Update cart display initially
        updateCartDisplay();
        
        // Show toast message
        function showToast(message, duration = 3000) {
            toast.textContent = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, duration);
        }
        
        // Check if vendor ID exists
        if(!vendorID || vendorID.trim() === ""){
            showErrorMessage("No Restaurant Selected", "Please select a restaurant from the main page.");
        } else {
            loadVendorDetails(vendorID);
        }
        
        async function loadVendorDetails(vendorID) {
            try {
                // Fetch vendor document
                const vendorDoc = await db.collection("vendors").doc(vendorID).get();
                
                if(!vendorDoc.exists){
                    showErrorMessage("Restaurant Not Found", "The requested restaurant could not be found.");
                    return;
                }
                
                vendorData = vendorDoc.data();
                vendorData.id = vendorDoc.id;
                
                // Render the main page
                renderPage(vendorData);
                
                // Load food items
                loadFoodItems(vendorID);
                
            } catch (error) {
                console.error("Error loading vendor details:", error);
                showErrorMessage("Connection Error", "Unable to load restaurant information.");
            }
        }
        
        function renderPage(vendor) {
            const ratingValue = vendor.reviewsCount > 0 ? (vendor.reviewsSum / vendor.reviewsCount).toFixed(1) : 'N/A';
            
            const pageHTML = `
                <div style="background: white; border-radius: var(--border-radius); padding: 30px; box-shadow: var(--box-shadow); margin-bottom: 30px;">
                    <div style="display: flex; gap: 30px; margin-bottom: 25px; flex-wrap: wrap;">
                        <div style="flex: 0 0 280px; border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--box-shadow-lg);">
                            <img src="${vendor.photo || 'https://via.placeholder.com/400x300/FF6600/FFFFFF?text=Restaurant'}" 
                                 alt="${vendor.title || 'Restaurant'}" 
                                 style="width: 100%; height: 220px; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/400x300/FF6600/FFFFFF?text=Restaurant'">
                        </div>
                        
                        <div style="flex: 1; min-width: 300px;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 15px; flex-wrap: wrap; gap: 15px;">
                                <h1 style="font-size: 32px; font-weight: 700; color: var(--dark-color); margin: 0; line-height: 1.2;">${vendor.title || 'Restaurant Name'}</h1>
                            </div>
                            
                            ${vendor.description ? `<p style="color: var(--gray-color); font-size: 16px; line-height: 1.7; margin-bottom: 20px;">${vendor.description}</p>` : ''}
                            
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-bottom: 25px;">
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background-color: rgba(255, 102, 0, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 18px;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 3px;">Rating</div>
                                        <div style="display: inline-flex; align-items: center; gap: 8px; background-color: #fff8e1; padding: 10px 18px; border-radius: 30px; font-weight: 600;">
                                            <span style="color: #ffc107; font-size: 16px;">${generateStarRating(ratingValue === 'N/A' ? 0 : parseFloat(ratingValue))}</span>
                                            <span style="color: #ff9800; font-size: 16px;">${ratingValue}</span>
                                            ${vendor.reviewsCount > 0 ? `<span style="color: var(--gray-color); font-size: 14px; font-weight: normal;">(${vendor.reviewsCount} review${vendor.reviewsCount !== 1 ? 's' : ''})</span>` : '<span style="color: var(--gray-color); font-size: 14px; font-weight: normal;">(No reviews)</span>'}
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: flex-start; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background-color: rgba(255, 102, 0, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 18px;">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 3px;">Location</div>
                                        <div style="font-size: 16px; font-weight: 600; color: var(--dark-color);">${vendor.location || 'Location not specified'}</div>
                                    </div>
                                </div>
                                
                                ${vendor.phonenumber ? `
                                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                                        <div style="width: 40px; height: 40px; background-color: rgba(255, 102, 0, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 18px;">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 3px;">Contact</div>
                                            <div style="font-size: 16px; font-weight: 600; color: var(--dark-color);">${vendor.phonenumber}</div>
                                        </div>
                                    </div>
                                ` : ''}
                                
                                ${vendor.categoryTitle ? `
                                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                                        <div style="width: 40px; height: 40px; background-color: rgba(255, 102, 0, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary-color); font-size: 18px;">
                                            <i class="fas fa-utensils"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 3px;">Category</div>
                                            <div style="font-size: 16px; font-weight: 600; color: var(--dark-color);">${vendor.categoryTitle}</div>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Tabs -->
                <div class="category-tabs" id="categoryTabs">
                    <!-- Categories will be added by JavaScript -->
                </div>
                
                <!-- Category Sections -->
                <div id="categorySections">
                    <!-- Category sections will be added by JavaScript -->
                </div>
            `;
            
            vendorContainer.innerHTML = pageHTML;
        }
        
        async function loadFoodItems(vendorID) {
            try {
                // Fetch food items
                const foodSnapshot = await db.collection("vendor_products")
                    .where("vendorID", "==", vendorID)
                    .where("publish", "==", true)
                    .get();
                
                if(foodSnapshot.empty) {
                    showNoFoods();
                    return;
                }
                
                // Process food items
                allFoodItems = [];
                const categories = new Set(['all']);
                
                foodSnapshot.forEach(foodDoc => {
                    const food = foodDoc.data();
                    food.id = foodDoc.id;
                    
                    // Determine category
                    let category = (food.category && food.category.toLowerCase()) || 'meals';
                    
                    // Map category to standard categories
                    const categoryMap = {
                        'popular': 'popular',
                        'snack': 'snacks',
                        'snacks': 'snacks',
                        'beverage': 'beverages',
                        'beverages': 'beverages',
                        'drink': 'beverages',
                        'drinks': 'beverages',
                        'meal': 'meals',
                        'meals': 'meals',
                        'main': 'meals',
                        'mains': 'meals'
                    };
                    
                    category = categoryMap[category] || 'meals';
                    food.category = category;
                    categories.add(category);
                    
                    allFoodItems.push(food);
                });
                
                // Create category tabs
                createCategoryTabs(Array.from(categories));
                
                // Group food items by category
                const foodByCategory = {};
                allFoodItems.forEach(food => {
                    if (!foodByCategory[food.category]) {
                        foodByCategory[food.category] = [];
                    }
                    foodByCategory[food.category].push(food);
                });
                
                // Create category sections
                createCategorySections(foodByCategory);
                
                // Show first category by default
                showCategory('all');
                
            } catch (error) {
                console.error("Error loading food items:", error);
                showNoFoods();
            }
        }
        
        function createCategoryTabs(categories) {
            const categoryTabs = document.getElementById('categoryTabs');
            
            // Define category display names and icons
            const categoryConfig = {
                'all': { name: 'All Items', icon: 'fas fa-list' },
                'popular': { name: 'Popular', icon: 'fas fa-fire' },
                'meals': { name: 'Meals', icon: 'fas fa-utensils' },
                'snacks': { name: 'Snacks', icon: 'fas fa-cookie-bite' },
                'beverages': { name: 'Beverages', icon: 'fas fa-glass-whiskey' }
            };
            
            categories.forEach(category => {
                const config = categoryConfig[category] || { 
                    name: category.charAt(0).toUpperCase() + category.slice(1),
                    icon: 'fas fa-utensils'
                };
                
                const button = document.createElement('button');
                button.className = 'category-tab';
                button.dataset.category = category;
                button.innerHTML = `<i class="${config.icon}"></i> ${config.name}`;
                
                button.addEventListener('click', () => {
                    showCategory(category);
                });
                
                categoryTabs.appendChild(button);
            });
        }
        
        function createCategorySections(foodByCategory) {
            const categorySections = document.getElementById('categorySections');
            const categoryConfig = {
                'popular': { name: 'Popular Items', icon: 'fas fa-fire' },
                'meals': { name: 'Meals', icon: 'fas fa-utensils' },
                'snacks': { name: 'Snacks', icon: 'fas fa-cookie-bite' },
                'beverages': { name: 'Beverages', icon: 'fas fa-glass-whiskey' }
            };
            
            // Create "All Items" section
            const allSection = document.createElement('div');
            allSection.className = 'category-section';
            allSection.id = 'category-all';
            allSection.innerHTML = `
                <h3 style="font-size: 22px; font-weight: 700; color: var(--dark-color); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--gray-light);">
                    <i class="fas fa-list" style="color: var(--primary-color);"></i>
                    All Menu Items
                </h3>
                <div class="foods-grid" id="foodsGridAll">
                    <!-- All items will be rendered here -->
                </div>
            `;
            categorySections.appendChild(allSection);
            
            // Render all items
            const allFoodsGrid = document.getElementById('foodsGridAll');
            allFoodItems.forEach(food => {
                const foodCard = createFoodCard(food);
                allFoodsGrid.appendChild(foodCard);
            });
            
            // Create individual category sections
            Object.keys(categoryConfig).forEach(category => {
                if (foodByCategory[category]) {
                    const section = document.createElement('div');
                    section.className = 'category-section hidden';
                    section.id = `category-${category}`;
                    
                    const config = categoryConfig[category];
                    section.innerHTML = `
                        <h3 style="font-size: 22px; font-weight: 700; color: var(--dark-color); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--gray-light);">
                            <i class="${config.icon}" style="color: var(--primary-color);"></i>
                            ${config.name}
                        </h3>
                        <div class="foods-grid" id="foodsGrid${category.charAt(0).toUpperCase() + category.slice(1)}">
                            <!-- Category items will be rendered here -->
                        </div>
                    `;
                    categorySections.appendChild(section);
                    
                    // Render category items
                    const categoryGrid = document.getElementById(`foodsGrid${category.charAt(0).toUpperCase() + category.slice(1)}`);
                    foodByCategory[category].forEach(food => {
                        const foodCard = createFoodCard(food);
                        categoryGrid.appendChild(foodCard);
                    });
                }
            });
        }
        
        function showCategory(category) {
            // Update active tab
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.category === category) {
                    tab.classList.add('active');
                }
            });
            
            // Show/hide sections
            document.querySelectorAll('.category-section').forEach(section => {
                section.classList.add('hidden');
            });
            
            const sectionToShow = document.getElementById(`category-${category}`);
            if (sectionToShow) {
                sectionToShow.classList.remove('hidden');
            }
            
            currentCategory = category;
        }
        
        function createFoodCard(food) {
            const card = document.createElement('div');
            card.className = 'food-card';
            card.dataset.id = food.id;
            
            // Check if item is in cart
            const cartItem = cart.find(item => item.id === food.id);
            const cartQuantity = cartItem ? cartItem.qty : 0;
            
            card.innerHTML = `
                <div class="product-overlay"></div>
                <div class="food-image-container">
                    <img src="${food.photo || 'https://via.placeholder.com/300x200/FF6600/FFFFFF?text=Food+Item'}" 
                         alt="${food.name || food.title || 'Food item'}" 
                         class="food-image"
                         onerror="this.src='https://via.placeholder.com/300x200/FF6600/FFFFFF?text=Food+Item'">
                </div>
                <div class="food-details">
                    <h3 class="food-name">${food.name || food.title || 'Unnamed Item'}</h3>
                    ${food.description ? `<p class="food-description">${truncateText(food.description, 80)}</p>` : '<p class="food-description" style="color: #999;">No description available</p>'}
                    <div class="food-price">₱${food.price ? parseFloat(food.price).toFixed(2) : '0.00'}</div>
                    
                    <div class="food-card-footer">
                        ${cartQuantity > 0 ? `
                            <div class="quantity-controls">
                                <button class="qty-btn minus" data-id="${food.id}">-</button>
                                <span class="qty-display">${cartQuantity}</span>
                                <button class="qty-btn" data-id="${food.id}">+</button>
                            </div>
                        ` : `
                            <button class="add-to-cart-btn" data-id="${food.id}">
                                <i class="fas fa-plus"></i>
                            </button>
                        `}
                    </div>
                </div>
            `;
            
            // Add event listeners to buttons
            const minusBtn = card.querySelector('.qty-btn.minus');
            const plusBtn = card.querySelector('.qty-btn:not(.minus)');
            const addBtn = card.querySelector('.add-to-cart-btn');
            
            if (minusBtn) {
                minusBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const productId = e.target.dataset.id;
                    const product = allFoodItems.find(f => f.id === productId);
                    if (product) {
                        updateCartItemQuantity(productId, -1, true);
                    }
                });
            }
            
            if (plusBtn) {
                plusBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const productId = e.target.dataset.id;
                    const product = allFoodItems.find(f => f.id === productId);
                    if (product) {
                        updateCartItemQuantity(productId, 1, true);
                    }
                });
            }
            
            if (addBtn) {
                addBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const productId = e.target.dataset.id;
                    const product = allFoodItems.find(f => f.id === productId);
                    if (product) {
                        addToCart(
                            productId, 
                            product.name || product.title || 'Food item', 
                            product.price || 0, 
                            product.photo || ''
                        );
                    }
                });
            }
            
            // Make card clickable to go to product details
            card.addEventListener('click', (e) => {
                // Don't trigger if clicking on buttons inside
                if (e.target.closest('button') || 
                    e.target.classList.contains('add-to-cart-btn') || 
                    e.target.classList.contains('qty-btn') || 
                    e.target.closest('.quantity-controls')) {
                    return;
                }
                
                window.location.href = `../foods/product.php?id=${food.id}`;
            });
            
            return card;
        }
        
        // MAIN ADD TO CART FUNCTION
        function addToCart(productId, productName, productPrice, productPhoto) {
            console.log('Adding to cart:', productId, productName, productPrice);
            
            // Get user from PHP session
            const user = <?php echo isset($_SESSION['uid']) ? json_encode($_SESSION['uid']) : 'null'; ?>;
            
            if (!user) {
                showToast("Please login to add items to cart!");
                setTimeout(() => {
                    window.location.href = "../login.php";
                }, 1500);
                return;
            }
            
            // Create cart item
            cartItemToAdd = {
                id: productId,
                name: productName,
                price: parseFloat(productPrice),
                qty: 1,
                photo: productPhoto,
                vendorID: vendorID,
                timestamp: new Date().toISOString()
            };
            
            // Check restaurant compatibility
            if (cart.length > 0) {
                const currentVendorId = cart[0].vendorID;
                const newVendorId = vendorID;
                
                if (newVendorId !== currentVendorId) {
                    // Show restaurant switch modal
                    showRestaurantSwitchModal();
                    return;
                }
            }
            
            // If same restaurant, add directly
            addCartItemDirectly();
        }
        
        // SHOW RESTAURANT SWITCH MODAL
        function showRestaurantSwitchModal() {
            // Create modal HTML
            const modalHTML = `
                <div class="switch-modal active" id="restaurantSwitchModal">
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
                                        <img id="newRestaurantLogo" src="${vendorData?.photo || ''}" alt="">
                                    </div>
                                    <div class="restaurant-compare-name">${vendorData?.title || vendorData?.name || 'New Restaurant'}</div>
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
            `;
            
            // Add modal to page
            const modalContainer = document.createElement('div');
            modalContainer.innerHTML = modalHTML;
            document.body.appendChild(modalContainer);
            
            // Fetch current restaurant details
            if (cart.length > 0) {
                fetchRestaurantDetails(cart[0].vendorID).then(restaurant => {
                    const currentLogo = document.getElementById('currentRestaurantLogo');
                    const currentName = document.getElementById('currentRestaurantName');
                    
                    if (currentLogo) currentLogo.src = restaurant.logo;
                    if (currentName) currentName.textContent = restaurant.name;
                });
            }
        }
        
        // CLOSE MODAL
        function closeSwitchModal() {
            const modal = document.getElementById('restaurantSwitchModal');
            if (modal) {
                modal.remove();
            }
            cartItemToAdd = null;
        }
        
        // CONFIRM RESTAURANT SWITCH
        function confirmRestaurantSwitch() {
            if (!cartItemToAdd) return;
            
            // Clear current cart
            cart = [];
            
            // Add new item
            cart.push(cartItemToAdd);
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update UI
            updateCartDisplay();
            updateProductCardUI(cartItemToAdd.id);
            
            // Show success message
            showToast("Cart cleared and new item added!");
            
            // Close modal
            closeSwitchModal();
            
            // Animate + button
            const btn = document.querySelector(`[data-id="${cartItemToAdd.id}"] .add-to-cart-btn`);
            if (btn) {
                btn.classList.add('pulse');
                setTimeout(() => {
                    btn.classList.remove('pulse');
                }, 300);
            }
        }
        
        // ADD ITEM DIRECTLY (SAME RESTAURANT)
        function addCartItemDirectly() {
            if (!cartItemToAdd) return;
            
            // Check if item already exists in cart
            const existingIndex = cart.findIndex(item => item.id === cartItemToAdd.id);
            
            if (existingIndex > -1) {
                // Update quantity of existing item
                cart[existingIndex].qty += 1;
                if (cart[existingIndex].qty > 99) {
                    cart[existingIndex].qty = 99;
                    showToast("Maximum quantity reached (99 per item)");
                } else {
                    showToast(`Quantity updated to ${cart[existingIndex].qty}`);
                }
            } else {
                // Add new item
                cart.push(cartItemToAdd);
                showToast("Added to cart!");
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update UI
            updateCartDisplay();
            updateProductCardUI(cartItemToAdd.id);
            
            // Animate + button
            const btn = document.querySelector(`[data-id="${cartItemToAdd.id}"] .add-to-cart-btn`);
            if (btn) {
                btn.classList.add('pulse');
                setTimeout(() => {
                    btn.classList.remove('pulse');
                }, 300);
            }
            
            cartItemToAdd = null;
        }
        
        // UPDATE CART ITEM QUANTITY
        function updateCartItemQuantity(productId, change, updateFooter = false) {
            const itemIndex = cart.findIndex(item => item.id === productId);
            
            if (itemIndex === -1 && change > 0) {
                // Item not in cart, adding new
                const product = allFoodItems.find(f => f.id === productId);
                if (product) {
                    const newItem = {
                        id: productId,
                        name: product.name || product.title || 'Food item',
                        price: parseFloat(product.price || 0),
                        qty: 1,
                        photo: product.photo || '',
                        vendorID: vendorID,
                        timestamp: new Date().toISOString()
                    };
                    cart.push(newItem);
                    showToast("Added to cart!");
                }
            } else if (itemIndex > -1) {
                let newQuantity = cart[itemIndex].qty + change;
                
                if (newQuantity <= 0) {
                    // Remove from cart
                    cart.splice(itemIndex, 1);
                    showToast("Removed from cart");
                } else {
                    if (newQuantity > 99) {
                        newQuantity = 99;
                        showToast("Maximum quantity reached (99 per item)");
                    }
                    cart[itemIndex].qty = newQuantity;
                    showToast(`Quantity updated to ${newQuantity}`);
                }
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update UI
            updateCartDisplay();
            updateProductCardUI(productId);
            
            // If this is the selected product, update footer
            if (updateFooter && selectedProductId === productId) {
                updateFooterQuantityControls(productId);
            }
        }
        
        // UPDATE PRODUCT CARD UI
        function updateProductCardUI(productId) {
            const cartItem = cart.find(item => item.id === productId);
            const cartQuantity = cartItem ? cartItem.qty : 0;
            
            // Find all cards with this product ID
            const cards = document.querySelectorAll(`[data-id="${productId}"]`);
            
            cards.forEach(card => {
                const footer = card.querySelector('.food-card-footer');
                if (footer) {
                    if (cartQuantity > 0) {
                        footer.innerHTML = `
                            <div class="quantity-controls">
                                <button class="qty-btn minus" data-id="${productId}">-</button>
                                <span class="qty-display">${cartQuantity}</span>
                                <button class="qty-btn" data-id="${productId}">+</button>
                            </div>
                        `;
                        
                        // Reattach event listeners
                        const minusBtn = footer.querySelector('.qty-btn.minus');
                        const plusBtn = footer.querySelector('.qty-btn:not(.minus)');
                        
                        if (minusBtn) {
                            minusBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                updateCartItemQuantity(productId, -1, true);
                            });
                        }
                        
                        if (plusBtn) {
                            plusBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                updateCartItemQuantity(productId, 1, true);
                            });
                        }
                    } else {
                        footer.innerHTML = `
                            <button class="add-to-cart-btn" data-id="${productId}">
                                <i class="fas fa-plus"></i>
                            </button>
                        `;
                        
                        // Reattach event listener
                        const addBtn = footer.querySelector('.add-to-cart-btn');
                        if (addBtn) {
                            addBtn.addEventListener('click', (e) => {
                                e.stopPropagation();
                                const product = allFoodItems.find(f => f.id === productId);
                                if (product) {
                                    addToCart(
                                        productId, 
                                        product.name || product.title || 'Food item', 
                                        product.price || 0, 
                                        product.photo || ''
                                    );
                                }
                            });
                        }
                    }
                }
            });
            
            // Update footer controls if this is the selected product
            if (selectedProductId === productId) {
                updateFooterQuantityControls(productId);
            }
        }
        
        // UPDATE CART DISPLAY
        function updateCartDisplay() {
            const itemCount = cart.reduce((sum, item) => sum + item.qty, 0);
            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            
            // Update cart footer
            if (itemCount > 0) {
                cartFooter.style.display = 'block';
                cartItemsCount.textContent = `${itemCount} item${itemCount !== 1 ? 's' : ''}`;
                cartTotal.textContent = `Total: ₱${total.toFixed(2)}`;
                
                // Auto-select a product for footer controls if none selected
                if (!selectedProductId && cart.length > 0) {
                    // Find first product with quantity >= 2
                    const itemWithQty2 = cart.find(item => item.qty >= 2);
                    if (itemWithQty2) {
                        selectedProductId = itemWithQty2.id;
                        updateFooterQuantityControls(selectedProductId);
                    }
                }
            } else {
                cartFooter.style.display = 'none';
                footerQuantityControls.classList.add('hidden');
                selectedProductId = null;
            }
        }
        
        // UPDATE FOOTER QUANTITY CONTROLS
        function updateFooterQuantityControls(productId) {
            const cartItem = cart.find(item => item.id === productId);
            if (cartItem && cartItem.qty >= 2) {
                selectedProductId = productId;
                footerQuantityControls.classList.remove('hidden');
                footerQtyDisplay.textContent = cartItem.qty;
                
                // Update button event listeners
                footerMinusBtn.onclick = () => updateCartItemQuantity(productId, -1, true);
                footerPlusBtn.onclick = () => updateCartItemQuantity(productId, 1, true);
            } else {
                footerQuantityControls.classList.add('hidden');
                selectedProductId = null;
            }
        }
        
        // FETCH RESTAURANT DETAILS FOR MODAL
        async function fetchRestaurantDetails(vendorId) {
            try {
                const vendorDoc = await db.collection("vendors").doc(vendorId).get();
                if (vendorDoc.exists) {
                    const vendorData = vendorDoc.data();
                    return {
                        id: vendorId,
                        name: vendorData.title || vendorData.name || "Unknown Restaurant",
                        logo: vendorData.photo || 'https://via.placeholder.com/35x35?text=R',
                        category: vendorData.categoryTitle || 'Restaurant'
                    };
                }
            } catch (error) {
                console.error("Error fetching restaurant details:", error);
            }
            
            return {
                id: vendorId,
                name: "Unknown Restaurant",
                logo: 'https://via.placeholder.com/35x35?text=R',
                category: 'Restaurant'
            };
        }
        
        // HELPER FUNCTIONS
        function generateStarRating(rating) {
            if (rating === 0 || rating === 'N/A' || isNaN(rating)) {
                return '<i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>';
            }
            
            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            
            let stars = '';
            for(let i = 0; i < fullStars; i++) stars += '<i class="fas fa-star"></i>';
            if(halfStar) stars += '<i class="fas fa-star-half-alt"></i>';
            for(let i = 0; i < emptyStars; i++) stars += '<i class="far fa-star"></i>';
            
            return stars;
        }
        
        function truncateText(text, maxLength) {
            if(!text) return '';
            if(text.length <= maxLength) return text;
            return text.substring(0, maxLength).trim() + '...';
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function showNoFoods() {
            const categorySections = document.getElementById('categorySections');
            
            if (categorySections) {
                categorySections.innerHTML = `
                    <div style="text-align: center; padding: 50px 20px; color: #666;">
                        <i class="fas fa-utensils" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <h3>No Menu Items Available</h3>
                        <p>This restaurant hasn't added any items to their menu yet.</p>
                    </div>
                `;
            }
        }
        
        function showErrorMessage(title, message) {
            vendorContainer.innerHTML = `
                <div style="text-align: center; padding: 80px 20px;">
                    <div style="font-size: 60px; color: #dc3545; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2 style="font-size: 24px; margin-bottom: 10px;">${title}</h2>
                    <p style="color: #666; margin-bottom: 30px;">${message}</p>
                    <a href="../users/vendor.php" style="display: inline-flex; align-items: center; gap: 8px; background-color: #ff6600; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left"></i>
                        Go Back to Restaurants
                    </a>
                </div>
            `;
        }
    </script>
</body>
</html>
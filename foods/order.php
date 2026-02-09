<?php
// Fix: Check if session is already started before starting it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Place Order - Food Delivery</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-storage-compat.js"></script>
    
    <style>
    :root {
        --primary-color: #27ae60;
        --primary-dark: #219955;
        --secondary-color: #ff6600;
        --secondary-dark: #e55a00;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --gray-color: #6c757d;
        --border-radius: 12px;
        --box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        --transition: all 0.3s ease;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        font-size: 16px;
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: transparent;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8fafc;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
    }

    /* Order Summary Styles */
    .order-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
        margin-bottom: 40px;
    }

    @media (max-width: 992px) {
        .order-container {
            grid-template-columns: 1fr;
        }
    }

    .order-section {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        margin-bottom: 25px;
        box-shadow: var(--box-shadow);
    }

    .section-title {
        font-size: 20px;
        color: var(--dark-color);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .section-title i {
        color: var(--primary-color);
    }

    /* Delivery Address */
    .address-section {
        position: relative;
    }

    .address-card {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 2px solid transparent;
        transition: var(--transition);
        cursor: pointer;
    }

    .address-card:hover {
        border-color: var(--primary-color);
        background: #f0f9f4;
    }

    .address-card.active {
        border-color: var(--primary-color);
        background: #f0f9f4;
    }

    .address-icon {
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        flex-shrink: 0;
    }

    .address-details {
        flex: 1;
    }

    .address-type {
        display: inline-block;
        padding: 4px 12px;
        background: var(--primary-color);
        color: white;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .address-text {
        font-size: 15px;
        color: var(--dark-color);
        margin-bottom: 5px;
        line-height: 1.5;
    }

    .address-landmark {
        font-size: 14px;
        color: var(--gray-color);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .change-address-btn {
        margin-top: 15px;
        padding: 10px 20px;
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .change-address-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    /* Order Items */
    .order-item {
        display: flex;
        gap: 15px;
        padding: 20px 0;
        border-bottom: 1px solid #eee;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .item-image {
        width: 80px;
        height: 80px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 8px;
    }

    .item-extras {
        font-size: 14px;
        color: var(--gray-color);
        margin-bottom: 8px;
    }

    .item-quantity {
        font-size: 14px;
        color: var(--dark-color);
        font-weight: 500;
    }

    .item-price {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        white-space: nowrap;
    }

    /* Payment Methods */
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    @media (max-width: 768px) {
        .payment-methods {
            grid-template-columns: 1fr;
        }
    }

    .payment-method {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        cursor: pointer;
        transition: var(--transition);
    }

    .payment-method:hover {
        border-color: var(--primary-color);
        background: #f0f9f4;
    }

    .payment-method.active {
        border-color: var(--primary-color);
        background: #f0f9f4;
    }

    .payment-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--primary-color);
    }

    .payment-details {
        flex: 1;
    }

    .payment-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 3px;
    }

    .payment-desc {
        font-size: 12px;
        color: var(--gray-color);
    }

    /* Special Instructions */
    .instructions-box {
        width: 100%;
        padding: 15px;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-family: inherit;
        font-size: 15px;
        resize: vertical;
        min-height: 120px;
        transition: var(--transition);
    }

    .instructions-box:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    /* Order Summary Sidebar */
    .order-summary-sidebar {
        position: sticky;
        top: 20px;
    }

    @media (max-width: 992px) {
        .order-summary-sidebar {
            position: static;
        }
    }

    .summary-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--box-shadow);
        margin-bottom: 20px;
    }

    .restaurant-info-summary {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }

    .restaurant-logo-sm {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f1f1f1;
    }

    .restaurant-name-sm {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 16px;
    }

    .price-breakdown {
        margin-bottom: 20px;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px dashed #eee;
    }

    .price-row:last-child {
        border-bottom: none;
    }

    .price-row.total {
        font-size: 18px;
        font-weight: 700;
        color: var(--primary-color);
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid #eee;
    }

    /* Tip Section */
    .tip-section {
        margin: 20px 0;
    }

    .tip-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    @media (max-width: 576px) {
        .tip-options {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .tip-option {
        padding: 12px;
        text-align: center;
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        transition: var(--transition);
        font-weight: 500;
    }

    .tip-option:hover {
        border-color: var(--primary-color);
    }

    .tip-option.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .tip-custom {
        margin-top: 10px;
    }

    .tip-input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 15px;
        text-align: center;
    }

    .tip-input:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    /* Place Order Button */
    .place-order-btn {
        width: 100%;
        padding: 18px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
    }

    .place-order-btn:hover:not(:disabled) {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(39, 174, 96, 0.3);
    }

    .place-order-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        display: none;
    }

    .loading-overlay.active {
        display: flex;
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 80px;
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

    /* Error States */
    .error-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        min-height: 50vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .error-icon {
        font-size: 48px;
        color: var(--danger-color);
        margin-bottom: 20px;
    }

    .error-title {
        font-size: 24px;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .error-message {
        color: var(--gray-color);
        margin-bottom: 25px;
        max-width: 400px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 30px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: var(--transition);
    }

    .back-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Loading State */
    .loading-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        min-height: 50vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .order-section {
            padding: 20px;
        }
        
        .section-title {
            font-size: 18px;
        }
        
        .address-card {
            flex-direction: column;
            gap: 10px;
        }
        
        .address-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }
        
        .order-item {
            flex-direction: column;
            gap: 10px;
        }
        
        .item-image {
            width: 100%;
            height: 150px;
        }
        
        .place-order-btn {
            padding: 16px;
            font-size: 16px;
            position: sticky;
            bottom: 20px;
            z-index: 100;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 12px;
        }
        
        .order-section {
            padding: 15px;
        }
        
        .tip-options {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['uid'])): ?>
        <div class="error-state">
            <div class="error-icon">⚠️</div>
            <h2 class="error-title">Login Required</h2>
            <p class="error-message">Please login to place an order.</p>
            <a href="../login.php" class="back-btn">
                <i class="fas fa-sign-in-alt"></i> Login Now
            </a>
        </div>
    <?php else: ?>
        <h1 style="font-size: 28px; margin-bottom: 30px; color: var(--dark-color);">
            <i class="fas fa-shopping-bag"></i> Complete Your Order
        </h1>
        
        <div id="orderContent">
            <div class="loading-state">
                <div class="loading-spinner"></div>
                <p>Loading order details...</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <p style="font-size: 18px; font-weight: 600; color: var(--dark-color);">Processing your order...</p>
    <p style="color: var(--gray-color); margin-top: 10px;" id="loadingMessage">Please wait</p>
</div>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<script>
// Initialize Firebase
const firebaseConfig = {
    apiKey: "AIzaSyAeIUnO8hDJ19YnruXWNZSW7iCsO9XggPg",
    authDomain: "lalago-1d721.firebaseapp.com",
    projectId: "lalago-1d721",
    storageBucket: "lalago-1d721.appspot.com",
    messagingSenderId: "687925021779",
    appId: "1:687925021779:web:3ab7482380d692f0790aa6",
    measurementId: "G-BGJEW8T98H"
};

// Initialize Firebase only if not already initialized
if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}

// Create global Firebase instances
const auth = firebase.auth();
const db = firebase.firestore();
const storage = firebase.storage();

// Global variables
const userId = "<?php echo isset($_SESSION['uid']) ? $_SESSION['uid'] : ''; ?>";
let userData = null;
let userAddress = null;
let orderData = null;
let selectedTip = 0;
let paymentMethod = 'cod';
let deliveryFee = 30;
let restaurantData = null;

// Check if Firebase is properly initialized
function checkFirebaseInitialization() {
    if (typeof firebase === 'undefined') {
        throw new Error("Firebase SDK failed to load. Check your internet connection.");
    }
    
    if (!db) {
        throw new Error("Firebase Firestore is not available. Please refresh the page.");
    }
    
    return true;
}

// Show toast message
function showToast(message, duration = 3000) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}

// Show error message
function showError(message) {
    const container = document.getElementById('orderContent');
    container.innerHTML = `
        <div class="error-state">
            <div class="error-icon">❌</div>
            <h2 class="error-title">Order Error</h2>
            <p class="error-message">${message}</p>
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <a href="index.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Browse Products
                </a>
                <a href="../users/profile.php" class="back-btn" style="background: var(--secondary-color);">
                    <i class="fas fa-map-marker-alt"></i> Add Address
                </a>
            </div>
        </div>
    `;
}

// Load order data from localStorage
function loadOrderData() {
    console.log("Loading order data from localStorage...");
    
    // Try to get order data from localStorage
    const savedOrder = localStorage.getItem('currentOrder');
    const singleProductOrder = localStorage.getItem('singleProductOrder');
    
    if (savedOrder) {
        try {
            orderData = JSON.parse(savedOrder);
            console.log("Loaded order from localStorage (currentOrder):", orderData);
            return orderData;
        } catch (e) {
            console.error("Error parsing saved order:", e);
        }
    }
    
    if (singleProductOrder) {
        try {
            // Convert single product order to array format
            const singleOrder = JSON.parse(singleProductOrder);
            console.log("Parsed single product order:", singleOrder);
            
            orderData = {
                products: [{
                    id: singleOrder.product_id,
                    name: singleOrder.name || "Product",
                    price: singleOrder.price || "0",
                    discountPrice: "0",
                    quantity: singleOrder.quantity || 1,
                    photo: singleOrder.photo || "",
                    vendorID: singleOrder.vendorID || "",
                    extras: singleOrder.addons ? singleOrder.addons.map(a => a.title) : [],
                    extras_price: singleOrder.addons ? singleOrder.addons.reduce((sum, a) => sum + (a.price || 0), 0) : 0,
                    variant_info: null
                }],
                vendorID: singleOrder.vendorID || "",
                restaurantName: singleOrder.restaurantName || "Restaurant",
                restaurantLogo: singleOrder.restaurantLogo || "",
                orderTotal: singleOrder.total || 0
            };
            
            console.log("Converted to order data:", orderData);
            return orderData;
            
        } catch (e) {
            console.error("Error parsing single product order:", e);
        }
    }
    
    console.error("No order data found in localStorage");
    return null;
}

// Load user data from Firebase
async function loadUserData() {
    try {
        console.log("Loading user data for ID:", userId);
        
        checkFirebaseInitialization();
        
        const userDoc = await db.collection("users").doc(userId).get();
        
        if (userDoc.exists) {
            userData = userDoc.data();
            console.log("User data loaded:", userData);
            
            // Get default shipping address
            if (userData.shippingAddress && Array.isArray(userData.shippingAddress) && userData.shippingAddress.length > 0) {
                // Find default address
                const defaultAddress = userData.shippingAddress.find(addr => addr.isDefault === true);
                userAddress = defaultAddress || userData.shippingAddress[0];
                console.log("User address found:", userAddress);
            } else {
                console.log("No shipping address found for user");
                userAddress = null;
            }
        } else {
            console.log("User document does not exist");
            throw new Error("User data not found in database");
        }
    } catch (error) {
        console.error("Error loading user data:", error);
        
        // Provide fallback address for testing
        userAddress = {
            addressAs: "Home",
            address: "No address found. Please update your profile.",
            landmark: "Please add your address",
            locality: "",
            isDefault: true
        };
    }
}

// Load restaurant data
async function loadRestaurantData(vendorID) {
    try {
        console.log("Loading restaurant data for vendor:", vendorID);
        
        if (!vendorID) {
            console.log("No vendorID provided");
            return;
        }
        
        const vendorDoc = await db.collection("vendors").doc(vendorID).get();
        if (vendorDoc.exists) {
            restaurantData = vendorDoc.data();
            console.log("Restaurant data loaded:", restaurantData);
            
            // Update delivery fee based on restaurant settings
            if (restaurantData.DeliveryCharge && restaurantData.DeliveryCharge.minimum_delivery_charges) {
                deliveryFee = parseFloat(restaurantData.DeliveryCharge.minimum_delivery_charges) || 30;
                console.log("Delivery fee set to:", deliveryFee);
            }
        } else {
            console.log("Restaurant document does not exist");
        }
    } catch (error) {
        console.error("Error loading restaurant data:", error);
    }
}

// Render order page
function renderOrderPage() {
    if (!orderData || !orderData.products) {
        showError("No order data found. Please select a product first.");
        return;
    }
    
    console.log("Rendering order page with data:", orderData);
    
    // Calculate subtotal
    let subtotal = 0;
    let extrasTotal = 0;
    
    orderData.products.forEach(product => {
        const productTotal = parseFloat(product.price || 0) * (product.quantity || 1);
        subtotal += productTotal;
        
        if (product.extras_price) {
            extrasTotal += parseFloat(product.extras_price || 0);
        }
    });
    
    subtotal += extrasTotal;
    const total = subtotal + deliveryFee + selectedTip;
    
    // Update order total in data
    orderData.orderTotal = total;
    
    // Render order items
    let orderItemsHTML = '';
    orderData.products.forEach((product, index) => {
        const extras = product.extras && product.extras.length > 0 ? 
            product.extras.join(', ') : 'No add-ons';
        
        const productPrice = parseFloat(product.price || 0);
        const productQuantity = product.quantity || 1;
        const productTotal = productPrice * productQuantity;
        
        orderItemsHTML += `
            <div class="order-item">
                <img src="${product.photo || 'https://via.placeholder.com/80x80?text=Product'}" 
                     alt="${product.name}" 
                     class="item-image"
                     loading="lazy"
                     onerror="this.src='https://via.placeholder.com/80x80?text=Product'">
                <div class="item-details">
                    <div class="item-name">${product.name || 'Unnamed Product'}</div>
                    <div class="item-extras">
                        <i class="fas fa-plus-circle" style="color: var(--primary-color);"></i>
                        ${extras}
                    </div>
                    <div class="item-quantity">
                        <i class="fas fa-times" style="color: var(--gray-color);"></i>
                        ${productQuantity}
                    </div>
                </div>
                <div class="item-price">
                    ₱${productTotal.toFixed(2)}
                </div>
            </div>
        `;
    });
    
    // Get address details
    let addressType = "Home";
    let addressText = "Loading address...";
    let landmarkText = "Please wait...";
    
    if (userAddress) {
        addressType = userAddress.addressAs || "Home";
        addressText = userAddress.address || "Address not specified";
        if (userAddress.landmark && userAddress.locality) {
            landmarkText = `${userAddress.landmark}, ${userAddress.locality}`;
        } else if (userAddress.landmark) {
            landmarkText = userAddress.landmark;
        } else if (userAddress.locality) {
            landmarkText = userAddress.locality;
        } else {
            landmarkText = "Location details not specified";
        }
    }
    
    // Render the complete page
    document.getElementById('orderContent').innerHTML = `
        <div class="order-container">
            <!-- Left Column: Order Details -->
            <div class="order-left">
                <!-- Delivery Address -->
                <div class="order-section address-section">
                    <h2 class="section-title">
                        <i class="fas fa-map-marker-alt"></i> Delivery Address
                    </h2>
                    
                    <div class="address-card active" id="currentAddress">
                        <div class="address-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="address-details">
                            <span class="address-type" id="addressType">${addressType}</span>
                            <p class="address-text" id="addressText">${addressText}</p>
                            <p class="address-landmark" id="addressLandmark">
                                <i class="fas fa-landmark"></i>
                                <span id="landmarkText">${landmarkText}</span>
                            </p>
                        </div>
                    </div>
                    
                    <button class="change-address-btn" onclick="changeAddress()">
                        <i class="fas fa-edit"></i> Change Address
                    </button>
                </div>

                <!-- Order Items -->
                <div class="order-section">
                    <h2 class="section-title">
                        <i class="fas fa-utensils"></i> Order Summary
                    </h2>
                    
                    <div id="orderItemsContainer">
                        ${orderItemsHTML}
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="order-section">
                    <h2 class="section-title">
                        <i class="fas fa-credit-card"></i> Payment Method
                    </h2>
                    
                    <div class="payment-methods">
                        <div class="payment-method active" onclick="selectPayment('cod')">
                            <div class="payment-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="payment-details">
                                <div class="payment-name">Cash on Delivery</div>
                                <div class="payment-desc">Pay when you receive your order</div>
                            </div>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment('gcash')">
                            <div class="payment-icon" style="color: #0056a6;">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="payment-details">
                                <div class="payment-name">GCash</div>
                                <div class="payment-desc">Pay with GCash mobile wallet</div>
                            </div>
                        </div>
                        
                        <div class="payment-method" onclick="selectPayment('card')">
                            <div class="payment-icon" style="color: #ff6600;">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="payment-details">
                                <div class="payment-name">Credit/Debit Card</div>
                                <div class="payment-desc">Secure online payment</div>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="paymentMethod" value="cod">
                </div>

                <!-- Special Instructions -->
                <div class="order-section">
                    <h2 class="section-title">
                        <i class="fas fa-sticky-note"></i> Special Instructions
                    </h2>
                    
                    <textarea 
                        class="instructions-box" 
                        id="specialInstructions" 
                        placeholder="Add cooking instructions, delivery notes, or any special requests..."
                        rows="4"></textarea>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="order-summary-sidebar">
                <!-- Restaurant Info -->
                <div class="summary-card">
                    <div class="restaurant-info-summary">
                        <img src="${orderData.restaurantLogo || 'https://via.placeholder.com/50x50?text=Restaurant'}" 
                             alt="Restaurant" 
                             class="restaurant-logo-sm"
                             id="restaurantLogo"
                             loading="lazy"
                             onerror="this.src='https://via.placeholder.com/50x50?text=Restaurant'">
                        <div>
                            <div class="restaurant-name-sm" id="restaurantName">${orderData.restaurantName || "Restaurant"}</div>
                            <div style="font-size: 12px; color: var(--gray-color);">
                                <i class="fas fa-clock"></i> <span id="deliveryTime">30-45 min</span> delivery
                            </div>
                        </div>
                    </div>
                    
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Subtotal</span>
                            <span>₱<span id="subtotalAmount">${subtotal.toFixed(2)}</span></span>
                        </div>
                        <div class="price-row">
                            <span>Delivery Fee</span>
                            <span>₱<span id="deliveryFee">${deliveryFee.toFixed(2)}</span></span>
                        </div>
                        <div class="price-row" id="tipRow" style="${selectedTip > 0 ? 'display: flex;' : 'display: none;'}">
                            <span>Tip</span>
                            <span>₱<span id="tipAmount">${selectedTip.toFixed(2)}</span></span>
                        </div>
                        <div class="price-row total">
                            <span>Total</span>
                            <span>₱<span id="totalAmount">${total.toFixed(2)}</span></span>
                        </div>
                    </div>
                </div>

                <!-- Tip Section -->
                <div class="summary-card">
                    <h3 style="font-size: 16px; margin-bottom: 15px; color: var(--dark-color);">
                        <i class="fas fa-hand-holding-heart"></i> Add a Tip
                    </h3>
                    
                    <div class="tip-options">
                        <div class="tip-option" onclick="selectTip(10)">₱10</div>
                        <div class="tip-option" onclick="selectTip(20)">₱20</div>
                        <div class="tip-option" onclick="selectTip(50)">₱50</div>
                        <div class="tip-option" onclick="selectTip('custom')">Custom</div>
                        <div class="tip-option active" onclick="selectTip(0)">No Tip</div>
                    </div>
                    
                    <div class="tip-custom" id="customTipSection" style="display: none;">
                        <input type="number" 
                               id="customTipAmount" 
                               class="tip-input" 
                               placeholder="Enter custom amount"
                               min="0"
                               step="10"
                               onchange="updateCustomTip()">
                    </div>
                </div>

                <!-- Place Order Button -->
                <button class="place-order-btn" id="placeOrderBtn" onclick="placeOrder()">
                    <i class="fas fa-check-circle"></i> Place Order
                </button>
                
                <p style="text-align: center; margin-top: 15px; font-size: 12px; color: var(--gray-color);">
                    By placing your order, you agree to our 
                    <a href="#" style="color: var(--primary-color);">Terms of Service</a>
                </p>
            </div>
        </div>
    `;
}

// Select payment method
function selectPayment(method) {
    paymentMethod = method;
    
    // Update UI
    document.querySelectorAll('.payment-method').forEach(el => {
        el.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    document.getElementById('paymentMethod').value = method;
    
    console.log("Payment method selected:", method);
}

// Select tip amount
function selectTip(amount) {
    // Update UI
    document.querySelectorAll('.tip-option').forEach(el => {
        el.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Show/hide custom tip input
    const customSection = document.getElementById('customTipSection');
    if (amount === 'custom') {
        customSection.style.display = 'block';
        document.getElementById('customTipAmount').focus();
        selectedTip = 0;
    } else {
        customSection.style.display = 'none';
        selectedTip = parseFloat(amount) || 0;
        updateTotal();
    }
    
    console.log("Tip selected:", selectedTip);
}

// Update custom tip
function updateCustomTip() {
    const customTipInput = document.getElementById('customTipAmount');
    const tipValue = parseFloat(customTipInput.value) || 0;
    
    if (tipValue >= 0) {
        selectedTip = tipValue;
        updateTotal();
        console.log("Custom tip updated:", selectedTip);
    }
}

// Update total amount
function updateTotal() {
    if (!orderData || !orderData.products) return;
    
    let subtotal = 0;
    let extrasTotal = 0;
    
    orderData.products.forEach(product => {
        const productTotal = parseFloat(product.price || 0) * (product.quantity || 1);
        subtotal += productTotal;
        
        if (product.extras_price) {
            extrasTotal += parseFloat(product.extras_price || 0);
        }
    });
    
    subtotal += extrasTotal;
    const total = subtotal + deliveryFee + selectedTip;
    
    // Update UI
    document.getElementById('subtotalAmount').textContent = subtotal.toFixed(2);
    document.getElementById('deliveryFee').textContent = deliveryFee.toFixed(2);
    document.getElementById('totalAmount').textContent = total.toFixed(2);
    
    // Update tip display
    const tipRow = document.getElementById('tipRow');
    const tipAmount = document.getElementById('tipAmount');
    
    if (selectedTip > 0) {
        tipRow.style.display = 'flex';
        tipAmount.textContent = selectedTip.toFixed(2);
    } else {
        tipRow.style.display = 'none';
    }
    
    // Update order total in data
    orderData.orderTotal = total;
}

// Change address
function changeAddress() {
    // Redirect to profile page to manage addresses
    window.location.href = '../users/profile.php?redirect=order.php';
}

// Place order function
async function placeOrder() {
    console.log("Starting place order process...");
    
    if (!userId) {
        showToast("Please login to place an order!");
        setTimeout(() => {
            window.location.href = "../login.php";
        }, 1500);
        return;
    }
    
    if (!orderData || !orderData.products) {
        showToast("No order data found. Please try again.");
        return;
    }
    
    if (!userAddress || !userAddress.address) {
        showToast("Please set a delivery address first.");
        setTimeout(() => {
            window.location.href = "../users/profile.php";
        }, 1500);
        return;
    }
    
    // Get special instructions
    const specialInstructions = document.getElementById('specialInstructions').value.trim();
    
    // Show loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    const placeOrderBtn = document.getElementById('placeOrderBtn');
    loadingOverlay.classList.add('active');
    document.getElementById('loadingMessage').textContent = "Processing your order...";
    placeOrderBtn.disabled = true;
    
    try {
        console.log("Preparing order data...");
        
        // Generate unique order ID
        const orderId = generateOrderId();
        console.log("Generated order ID:", orderId);
        
        // Get current timestamp
        const now = new Date();
        
        // Prepare the order document data
        const orderDocument = {
            // Order metadata
            id: orderId,
            createdAt: firebase.firestore.Timestamp.fromDate(now),
            acceptedAt: null,
            statusChangedAt: firebase.firestore.Timestamp.fromDate(now),
            
            // Order status
            status: "Order Placed",
            autoAccepted: false,
            
            // User information
            authorID: userId,
            author: {
                active: userData?.active || true,
                appIdentifier: "Web Food Delivery",
                createdAt: userData?.createdAt || firebase.firestore.Timestamp.fromDate(now),
                email: userData?.email || "",
                fcmToken: userData?.fcmToken || "",
                firstName: userData?.firstName || "",
                hasCompletedFirstOrder: userData?.hasCompletedFirstOrder || false,
                id: userId,
                isPromoDisabled: userData?.isPromoDisabled || false,
                isReferralPath: userData?.isReferralPath || false,
                lastName: userData?.lastName || "",
                lastOnlineTimestamp: firebase.firestore.Timestamp.fromDate(now),
                location: userData?.location || { latitude: 0, longitude: 0 },
                phoneNumber: userData?.phoneNumber || "",
                profilePictureURL: userData?.profilePictureURL || "",
                referralCode: userData?.referralCode || null,
                referralRewardAmount: userData?.referralRewardAmount || null,
                referredBy: userData?.referredBy || null,
                role: "customer",
                settings: userData?.settings || {
                    newArrivals: true,
                    orderUpdates: true,
                    promotions: true,
                    pushNewMessages: true
                },
                shippingAddress: userData?.shippingAddress || [],
                wallet_amount: userData?.wallet_amount || 0
            },
            
            // Delivery address
            address: {
                address: userAddress.address,
                addressAs: userAddress.addressAs || "Home",
                id: userAddress.id || generateUUID(),
                isDefault: userAddress.isDefault || true,
                landmark: userAddress.landmark || "",
                locality: userAddress.locality || "",
                location: userAddress.location || {
                    latitude: userAddress.latitude || 0,
                    longitude: userAddress.longitude || 0
                }
            },
            
            // Order details
            couponCode: "",
            couponId: "",
            deliveryCharge: deliveryFee.toString(),
            discount: 0,
            estimatedTimeToPrepare: "30 min",
            isReferralPath: false,
            notes: specialInstructions,
            payment_method: paymentMethod,
            
            // Products
            products: orderData.products.map(product => ({
                category_id: product.category_id || "",
                discountPrice: product.discountPrice || "0",
                extras: product.extras || [],
                extras_price: product.extras_price ? product.extras_price.toString() : "0.0",
                id: product.id,
                name: product.name,
                photo: product.photo || "",
                price: product.price ? product.price.toString() : "0",
                quantity: product.quantity || 1,
                variant_info: product.variant_info || null,
                vendorID: product.vendorID
            })),
            
            // Referral and special discounts
            referralAuditNote: null,
            scheduleTime: null,
            specialDiscount: {
                specialType: "amount",
                special_discount: 0,
                special_discount_label: 0
            },
            
            // Tax and tip
            taxSetting: [],
            tip_amount: selectedTip.toString(),
            
            // Vendor information
            vendorID: orderData.vendorID,
            vendor: restaurantData || {
                title: orderData.restaurantName || "Restaurant",
                location: "Unknown location",
                phonenumber: "",
                photo: orderData.restaurantLogo || ""
            },
            
            // Commission settings
            adminCommission: "20",
            adminCommissionType: "Fixed"
        };
        
        console.log("Order document prepared:", orderDocument);
        
        // Save order to Firebase
        console.log("Saving order to Firebase...");
        const orderRef = db.collection("restaurant_orders").doc(orderId);
        await orderRef.set(orderDocument);
        console.log("Order saved successfully!");
        
        // Clear local storage
        localStorage.removeItem('currentOrder');
        localStorage.removeItem('singleProductOrder');
        
        // Show success message
        showToast("Order placed successfully!");
        
        // Redirect to order confirmation page
        setTimeout(() => {
            window.location.href = `order-confirmation.php?id=${orderId}`;
        }, 2000);
        
    } catch (error) {
        console.error("Error placing order:", error);
        
        // Hide loading overlay
        loadingOverlay.classList.remove('active');
        placeOrderBtn.disabled = false;
        
        // Show error message
        showToast("Failed to place order: " + error.message);
    }
}

// Helper function to generate order ID
function generateOrderId() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < 20; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

// Helper function to generate UUID
function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', async function() {
    if (!userId) {
        showError("Please login to place an order.");
        return;
    }
    
    // Show initial loading
    const loadingOverlay = document.getElementById('loadingOverlay');
    const loadingMessage = document.getElementById('loadingMessage');
    loadingOverlay.classList.add('active');
    loadingMessage.textContent = "Initializing...";
    
    try {
        // Wait for Firebase to initialize
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Check if Firebase is properly initialized
        checkFirebaseInitialization();
        
        console.log("Firebase initialized successfully:", { 
            db: typeof db, 
            auth: typeof auth,
            storage: typeof storage 
        });
        
        // 1. Load user data and address
        loadingMessage.textContent = "Loading user information...";
        await loadUserData();
        
        // 2. Load order data from localStorage
        loadingMessage.textContent = "Loading order details...";
        const loadedOrderData = loadOrderData();
        
        if (!loadedOrderData) {
            throw new Error("No order data found. Please select a product first.");
        }
        
        // 3. Load restaurant data if we have vendorID
        if (loadedOrderData.vendorID) {
            loadingMessage.textContent = "Loading restaurant information...";
            await loadRestaurantData(loadedOrderData.vendorID);
        }
        
        // 4. Render everything
        loadingMessage.textContent = "Finalizing...";
        renderOrderPage();
        
        // Hide loading
        setTimeout(() => {
            loadingOverlay.classList.remove('active');
        }, 500);
        
    } catch (error) {
        console.error("Error loading order data:", error);
        loadingOverlay.classList.remove('active');
        showError("Error loading order details: " + error.message);
    }
});

// Handle beforeunload event
window.addEventListener('beforeunload', function(e) {
    if (document.getElementById('placeOrderBtn')?.disabled) {
        e.preventDefault();
        e.returnValue = 'You have an order in progress. Are you sure you want to leave?';
        return e.returnValue;
    }
});
</script>  

</body>
</html>
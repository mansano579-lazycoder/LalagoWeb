<?php
// Check if session is already started before starting it
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
    <title>My Orders - Food Delivery</title>
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
        --info-color: #3498db;
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

    /* Header Styles */
    .page-header {
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #eee;
    }

    .page-title {
        font-size: 32px;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .page-subtitle {
        color: var(--gray-color);
        font-size: 16px;
    }

    /* Tabs Navigation */
    .tabs-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
        flex-wrap: wrap;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
    }

    .tab-btn {
        padding: 12px 24px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-color);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        border-radius: 8px 8px 0 0;
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: var(--primary-color);
        background: #f0f9f4;
    }

    .tab-btn.active {
        color: var(--primary-color);
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -12px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary-color);
    }

    /* Orders Container */
    .orders-container {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    /* Order Card */
    .order-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        overflow: hidden;
        transition: var(--transition);
        border: 1px solid #eee;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    /* Order Header */
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-info {
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
        min-width: 0;
    }

    .order-id {
        font-size: 18px;
        font-weight: 700;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order-id i {
        color: var(--primary-color);
    }

    .order-date {
        font-size: 14px;
        color: var(--gray-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .restaurant-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .restaurant-logo-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f1f1f1;
        flex-shrink: 0;
    }

    .restaurant-name {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 16px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Order Status */
    .order-status {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .status-placed { background: #e3f2fd; color: #1976d2; }
    .status-accepted { background: #fff3e0; color: #ef6c00; }
    .status-preparing { background: #fff8e1; color: #ff8f00; }
    .status-ready { background: #e8f5e9; color: #2e7d32; }
    .status-picked { background: #f3e5f5; color: #7b1fa2; }
    .status-delivered { background: #e8f5e9; color: #1b5e20; }
    .status-cancelled { background: #ffebee; color: #c62828; }

    /* Order Body */
    .order-body {
        padding: 20px;
    }

    /* Order Items */
    .order-items {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
    }

    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        transition: var(--transition);
    }

    .order-item:hover {
        background: #e9ecef;
    }

    .item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .item-details {
        flex: 1;
        min-width: 0;
    }

    .item-name {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-extras {
        font-size: 13px;
        color: var(--gray-color);
        margin-bottom: 5px;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .item-quantity {
        font-size: 14px;
        color: var(--dark-color);
        font-weight: 500;
    }

    .item-price {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 16px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* Order Footer */
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-total {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        text-decoration: none;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .btn-secondary:hover {
        background: #f0f9f4;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: var(--danger-color);
        color: white;
    }

    .btn-danger:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin: 40px 0;
    }

    .empty-icon {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .empty-title {
        font-size: 24px;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .empty-message {
        color: var(--gray-color);
        margin-bottom: 30px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    .empty-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: var(--transition);
    }

    .empty-cta:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Loading State */
    .loading-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin: 40px 0;
    }

    .loading-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Order Details Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        padding: 20px;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: var(--border-radius);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        animation: modalSlideIn 0.3s ease;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
        background: #f8f9fa;
    }

    .modal-title {
        font-size: 20px;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: var(--gray-color);
        cursor: pointer;
        padding: 5px;
        border-radius: 4px;
        transition: var(--transition);
    }

    .modal-close:hover {
        color: var(--danger-color);
        background: #ffebee;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-section {
        margin-bottom: 25px;
    }

    .modal-section-title {
        font-size: 18px;
        color: var(--dark-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .detail-label {
        font-size: 13px;
        color: var(--gray-color);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-value {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 15px;
    }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e0e0e0;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #e0e0e0;
        border: 3px solid white;
    }

    .timeline-item.completed::before {
        background: var(--primary-color);
    }

    .timeline-item.active::before {
        background: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.2);
    }

    .timeline-time {
        font-size: 13px;
        color: var(--gray-color);
        margin-bottom: 5px;
    }

    .timeline-status {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .timeline-note {
        font-size: 14px;
        color: var(--gray-color);
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

    /* Filter Controls */
    .filter-controls {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-select {
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: var(--dark-color);
        background: white;
        cursor: pointer;
        transition: var(--transition);
        min-width: 150px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    .search-box {
        flex: 1;
        min-width: 200px;
        padding: 10px 15px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-family: inherit;
        font-size: 14px;
        color: var(--dark-color);
        transition: var(--transition);
    }

    .search-box:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }

    .refresh-btn {
        padding: 10px 15px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .refresh-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Responsive Styles */
    @media (max-width: 992px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .restaurant-info {
            width: 100%;
        }
        
        .order-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .order-actions {
            width: 100%;
            justify-content: flex-start;
        }
        
        .details-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .page-title {
            font-size: 28px;
        }
        
        .tabs-nav {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        
        .tab-btn {
            padding: 10px 15px;
            font-size: 14px;
            flex-shrink: 0;
        }
        
        .order-header, .order-body, .order-footer {
            padding: 15px;
        }
        
        .order-item {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
        
        .item-image {
            width: 100%;
            height: 120px;
        }
        
        .order-total {
            font-size: 18px;
        }
        
        .action-btn {
            padding: 8px 15px;
            font-size: 13px;
        }
        
        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-select, .search-box {
            width: 100%;
            min-width: auto;
        }
    }

    @media (max-width: 576px) {
        .container {
            padding: 12px;
        }
        
        .page-title {
            font-size: 24px;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }
        
        .order-status {
            align-self: flex-start;
        }
        
        .order-actions {
            flex-direction: column;
            width: 100%;
        }
        
        .action-btn {
            width: 100%;
            justify-content: center;
        }
        
        .modal-content {
            max-height: 80vh;
        }
        
        .modal-header, .modal-body {
            padding: 15px;
        }
    }
    /* Animation for order cards */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .order-card {
        animation: fadeIn 0.3s ease;
    }

    /* Scrollbar Styling */
    .modal-content::-webkit-scrollbar {
        width: 8px;
    }

    .modal-content::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .modal-content::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 4px;
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
        background: var(--primary-dark);
    }
    </style>
</head>
<body>

<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-shopping-bag"></i>
            <span>My Orders</span>
        </h1>
        <p class="page-subtitle">Track and manage all your food delivery orders</p>
    </div>

    <!-- Filter Controls -->
    <div class="filter-controls">
        <select class="filter-select" id="statusFilter" onchange="filterOrders()">
            <option value="all">All Status</option>
            <option value="Order Placed">Order Placed</option>
            <option value="Accepted">Accepted</option>
            <option value="Preparing">Preparing</option>
            <option value="Ready for Pickup">Ready for Pickup</option>
            <option value="Picked">Picked</option>
            <option value="Delivered">Delivered</option>
            <option value="Cancelled">Cancelled</option>
        </select>
        
        <select class="filter-select" id="timeFilter" onchange="filterOrders()">
            <option value="all">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
        </select>
        
        <input type="text" class="search-box" id="searchBox" placeholder="Search orders by ID or restaurant..." oninput="filterOrders()">
        
        <button class="refresh-btn" onclick="loadOrders()">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh</span>
        </button>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-nav">
        <button class="tab-btn active" onclick="switchTab('all')">
            <i class="fas fa-list"></i> All Orders
        </button>
        <button class="tab-btn" onclick="switchTab('active')">
            <i class="fas fa-clock"></i> Active Orders
        </button>
        <button class="tab-btn" onclick="switchTab('completed')">
            <i class="fas fa-check-circle"></i> Completed
        </button>
        <button class="tab-btn" onclick="switchTab('cancelled')">
            <i class="fas fa-times-circle"></i> Cancelled
        </button>
    </div>

    <!-- Orders Container -->
    <div id="ordersContainer">
        <div class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading your orders...</p>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal-overlay" id="orderModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-file-invoice"></i>
                <span>Order Details</span>
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Modal content will be loaded here -->
        </div>
    </div>
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
let allOrders = [];
let currentTab = 'all';
let currentFilter = 'all';
let currentTimeFilter = 'all';
let currentSearch = '';

// Check Firebase initialization
function checkFirebase() {
    if (typeof firebase === 'undefined') {
        throw new Error("Firebase SDK not loaded. Check your internet connection.");
    }
    
    if (!db) {
        // Try to reinitialize
        db = firebase.firestore();
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

// Format date
function formatDate(timestamp) {
    if (!timestamp) return 'N/A';
    
    try {
        const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return 'Invalid Date';
    }
}

// Format currency
function formatCurrency(amount) {
    return `₱${parseFloat(amount).toFixed(2)}`;
}

// Get status class
function getStatusClass(status) {
    const statusMap = {
        'Order Placed': 'status-placed',
        'Accepted': 'status-accepted',
        'Preparing': 'status-preparing',
        'Ready for Pickup': 'status-ready',
        'Picked': 'status-picked',
        'Delivered': 'status-delivered',
        'Cancelled': 'status-cancelled'
    };
    return statusMap[status] || 'status-placed';
}

// Get status icon
function getStatusIcon(status) {
    const iconMap = {
        'Order Placed': 'fa-shopping-bag',
        'Accepted': 'fa-check-circle',
        'Preparing': 'fa-utensils',
        'Ready for Pickup': 'fa-box',
        'Picked': 'fa-shipping-fast',
        'Delivered': 'fa-check-circle',
        'Cancelled': 'fa-times-circle'
    };
    return iconMap[status] || 'fa-shopping-bag';
}

// Switch tab
function switchTab(tab) {
    currentTab = tab;
    
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    // Filter and render orders
    filterOrders();
}

// Filter orders
function filterOrders() {
    currentFilter = document.getElementById('statusFilter').value;
    currentTimeFilter = document.getElementById('timeFilter').value;
    currentSearch = document.getElementById('searchBox').value.toLowerCase();
    
    let filteredOrders = allOrders;
    
    // Apply tab filter
    if (currentTab === 'active') {
        filteredOrders = filteredOrders.filter(order => 
            !['Delivered', 'Cancelled'].includes(order.status)
        );
    } else if (currentTab === 'completed') {
        filteredOrders = filteredOrders.filter(order => 
            order.status === 'Delivered'
        );
    } else if (currentTab === 'cancelled') {
        filteredOrders = filteredOrders.filter(order => 
            order.status === 'Cancelled'
        );
    }
    
    // Apply status filter
    if (currentFilter !== 'all') {
        filteredOrders = filteredOrders.filter(order => 
            order.status === currentFilter
        );
    }
    
    // Apply time filter
    if (currentTimeFilter !== 'all') {
        const now = new Date();
        filteredOrders = filteredOrders.filter(order => {
            if (!order.createdAt) return false;
            
            const orderDate = order.createdAt.toDate ? order.createdAt.toDate() : new Date(order.createdAt);
            
            switch (currentTimeFilter) {
                case 'today':
                    return orderDate.toDateString() === now.toDateString();
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    return orderDate >= weekAgo;
                case 'month':
                    const monthAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);
                    return orderDate >= monthAgo;
                case 'year':
                    const yearAgo = new Date(now.getTime() - 365 * 24 * 60 * 60 * 1000);
                    return orderDate >= yearAgo;
                default:
                    return true;
            }
        });
    }
    
    // Apply search filter
    if (currentSearch) {
        filteredOrders = filteredOrders.filter(order => 
            order.id.toLowerCase().includes(currentSearch) ||
            (order.vendor?.title || '').toLowerCase().includes(currentSearch) ||
            (order.vendor?.name || '').toLowerCase().includes(currentSearch)
        );
    }
    
    // Sort by date (newest first)
    filteredOrders.sort((a, b) => {
        const dateA = a.createdAt?.toDate ? a.createdAt.toDate() : new Date(a.createdAt || 0);
        const dateB = b.createdAt?.toDate ? b.createdAt.toDate() : new Date(b.createdAt || 0);
        return dateB - dateA;
    });
    
    renderOrders(filteredOrders);
}

// Render orders
function renderOrders(orders) {
    const container = document.getElementById('ordersContainer');
    
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2 class="empty-title">No Orders Found</h2>
                <p class="empty-message">
                    ${currentTab === 'active' ? 'You don\'t have any active orders right now.' : 
                      currentTab === 'completed' ? 'You haven\'t completed any orders yet.' :
                      currentTab === 'cancelled' ? 'You don\'t have any cancelled orders.' :
                      currentFilter !== 'all' ? `You don't have any orders with status "${currentFilter}".` :
                      currentSearch ? `No orders found matching "${currentSearch}".` :
                      'You haven\'t placed any orders yet.'}
                </p>
                <a href="index.php" class="empty-cta">
                    <i class="fas fa-utensils"></i>
                    <span>Browse Restaurants</span>
                </a>
            </div>
        `;
        return;
    }
    
    let ordersHTML = '<div class="orders-container">';
    
    orders.forEach(order => {
        const statusClass = getStatusClass(order.status);
        const statusIcon = getStatusIcon(order.status);
        const orderDate = formatDate(order.createdAt);
        const restaurantName = order.vendor?.title || order.vendor?.name || 'Unknown Restaurant';
        const restaurantLogo = order.vendor?.photo || order.vendor?.authorProfilePic || 'https://via.placeholder.com/40x40?text=Restaurant';
        
        // Calculate total
        let total = 0;
        if (order.products && Array.isArray(order.products)) {
            order.products.forEach(product => {
                const price = parseFloat(product.price) || 0;
                const quantity = product.quantity || 1;
                const extrasPrice = parseFloat(product.extras_price) || 0;
                total += (price * quantity) + extrasPrice;
            });
        }
        
        // Add delivery charge and tip
        total += parseFloat(order.deliveryCharge || 0);
        total += parseFloat(order.tip_amount || 0);
        
        ordersHTML += `
            <div class="order-card">
                <!-- Order Header -->
                <div class="order-header">
                    <div class="order-info">
                        <div class="order-id">
                            <i class="fas fa-hashtag"></i>
                            <span>Order #${order.id.substring(0, 8)}</span>
                        </div>
                        <div class="order-date">
                            <i class="far fa-calendar"></i>
                            <span>${orderDate}</span>
                        </div>
                    </div>
                    
                    <div class="restaurant-info">
                        <img src="${restaurantLogo}" 
                             alt="${restaurantName}" 
                             class="restaurant-logo-sm"
                             onerror="this.src='https://via.placeholder.com/40x40?text=Restaurant'">
                        <span class="restaurant-name">${restaurantName}</span>
                    </div>
                    
                    <div class="order-status ${statusClass}">
                        <i class="fas ${statusIcon}"></i>
                        <span>${order.status}</span>
                    </div>
                </div>
                
                <!-- Order Body -->
                <div class="order-body">
                    <div class="order-items">
        `;
        
        // Render order items
        if (order.products && Array.isArray(order.products)) {
            order.products.forEach((product, index) => {
                const productTotal = (parseFloat(product.price) || 0) * (product.quantity || 1);
                const extras = product.extras && product.extras.length > 0 ? 
                    product.extras.join(', ') : 'No add-ons';
                
                ordersHTML += `
                    <div class="order-item">
                        <img src="${product.photo || 'https://via.placeholder.com/60x60?text=Product'}" 
                             alt="${product.name}" 
                             class="item-image"
                             onerror="this.src='https://via.placeholder.com/60x60?text=Product'">
                        <div class="item-details">
                            <div class="item-name">${product.name || 'Unnamed Product'}</div>
                            <div class="item-extras">${extras}</div>
                            <div class="item-quantity">Quantity: ${product.quantity || 1}</div>
                        </div>
                        <div class="item-price">${formatCurrency(productTotal)}</div>
                    </div>
                `;
            });
        } else {
            ordersHTML += `
                <div class="order-item">
                    <div class="item-details">
                        <div class="item-name">No product information available</div>
                    </div>
                </div>
            `;
        }
        
        ordersHTML += `
                    </div>
                </div>
                
                <!-- Order Footer -->
                <div class="order-footer">
                    <div class="order-total">
                        <i class="fas fa-receipt"></i>
                        <span>Total: ${formatCurrency(total)}</span>
                    </div>
                    
                    <div class="order-actions">
                        <button class="action-btn btn-primary" onclick="viewOrderDetails('${order.id}')">
                            <i class="fas fa-eye"></i>
                            <span>View Details</span>
                        </button>
                        
                        ${order.status === 'Order Placed' || order.status === 'Accepted' ? `
                        <button class="action-btn btn-danger" onclick="cancelOrder('${order.id}')">
                            <i class="fas fa-times"></i>
                            <span>Cancel Order</span>
                        </button>
                        ` : ''}
                        
                        ${order.status === 'Delivered' ? `
                        <button class="action-btn btn-secondary" onclick="reorder('${order.id}')">
                            <i class="fas fa-redo"></i>
                            <span>Reorder</span>
                        </button>
                        ` : ''}
                        
                        ${order.status === 'Delivered' ? `
                        <button class="action-btn btn-secondary" onclick="rateOrder('${order.id}')">
                            <i class="fas fa-star"></i>
                            <span>Rate Order</span>
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    ordersHTML += '</div>';
    container.innerHTML = ordersHTML;
}

// View order details
function viewOrderDetails(orderId) {
    const order = allOrders.find(o => o.id === orderId);
    if (!order) {
        showToast("Order not found!");
        return;
    }
    
    const restaurantName = order.vendor?.title || order.vendor?.name || 'Unknown Restaurant';
    const restaurantLogo = order.vendor?.photo || order.vendor?.authorProfilePic || '';
    const orderDate = formatDate(order.createdAt);
    const deliveryAddress = order.address ? 
        `${order.address.address}, ${order.address.landmark || ''}, ${order.address.locality || ''}` : 
        'No address specified';
    
    // Calculate totals
    let subtotal = 0;
    let extrasTotal = 0;
    
    if (order.products && Array.isArray(order.products)) {
        order.products.forEach(product => {
            const price = parseFloat(product.price) || 0;
            const quantity = product.quantity || 1;
            const productTotal = price * quantity;
            subtotal += productTotal;
            extrasTotal += parseFloat(product.extras_price) || 0;
        });
    }
    
    const deliveryFee = parseFloat(order.deliveryCharge) || 0;
    const tip = parseFloat(order.tip_amount) || 0;
    const total = subtotal + extrasTotal + deliveryFee + tip;
    
    // Build status timeline
    let timelineHTML = '';
    const statuses = [
        { status: 'Order Placed', time: order.createdAt },
        { status: 'Accepted', time: order.acceptedAt },
        { status: 'Preparing', time: order.status === 'Preparing' || order.status === 'Ready for Pickup' || order.status === 'Picked' || order.status === 'Delivered' ? order.createdAt : null },
        { status: 'Ready for Pickup', time: order.status === 'Ready for Pickup' || order.status === 'Picked' || order.status === 'Delivered' ? order.createdAt : null },
        { status: 'Picked', time: order.status === 'Picked' || order.status === 'Delivered' ? order.createdAt : null },
        { status: 'Delivered', time: order.status === 'Delivered' ? order.createdAt : null }
    ];
    
    const currentStatusIndex = statuses.findIndex(s => s.status === order.status);
    
    statuses.forEach((item, index) => {
        let timelineClass = '';
        if (index <= currentStatusIndex) {
            timelineClass = index === currentStatusIndex ? 'active' : 'completed';
        }
        
        const time = item.time ? formatDate(item.time) : 'Pending';
        
        timelineHTML += `
            <div class="timeline-item ${timelineClass}">
                <div class="timeline-time">${time}</div>
                <div class="timeline-status">${item.status}</div>
                ${index === currentStatusIndex ? `<div class="timeline-note">Current status</div>` : ''}
            </div>
        `;
    });
    
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-info-circle"></i>
                Order Information
            </h4>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Order ID</div>
                    <div class="detail-value">${order.id}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Order Date</div>
                    <div class="detail-value">${orderDate}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="order-status ${getStatusClass(order.status)}" style="display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px;">
                            <i class="fas ${getStatusIcon(order.status)}"></i>
                            ${order.status}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">${order.payment_method || 'Cash on Delivery'}</div>
                </div>
            </div>
        </div>
        
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-store"></i>
                Restaurant Details
            </h4>
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <img src="${restaurantLogo || 'https://via.placeholder.com/50x50?text=Restaurant'}" 
                     alt="${restaurantName}" 
                     style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;"
                     onerror="this.src='https://via.placeholder.com/50x50?text=Restaurant'">
                <div>
                    <div style="font-weight: 600; color: var(--dark-color); margin-bottom: 5px;">${restaurantName}</div>
                    <div style="font-size: 14px; color: var(--gray-color);">
                        <i class="fas fa-phone"></i> ${order.vendor?.phonenumber || 'Not available'}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-map-marker-alt"></i>
                Delivery Information
            </h4>
            <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <div style="font-weight: 600; color: var(--dark-color); margin-bottom: 5px;">
                    <i class="fas fa-home"></i> ${order.address?.addressAs || 'Delivery Address'}
                </div>
                <div style="color: var(--gray-color); line-height: 1.5;">${deliveryAddress}</div>
                ${order.notes ? `
                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
                    <div style="font-weight: 600; color: var(--dark-color); margin-bottom: 5px;">
                        <i class="fas fa-sticky-note"></i> Special Instructions
                    </div>
                    <div style="color: var(--gray-color); font-style: italic;">"${order.notes}"</div>
                </div>
                ` : ''}
            </div>
        </div>
        
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-clipboard-list"></i>
                Order Items
            </h4>
            <div style="display: flex; flex-direction: column; gap: 10px;">
    `;
    
    // Render order items in modal
    if (order.products && Array.isArray(order.products)) {
        order.products.forEach(product => {
            const price = parseFloat(product.price) || 0;
            const quantity = product.quantity || 1;
            const extrasPrice = parseFloat(product.extras_price) || 0;
            const productTotal = (price * quantity) + extrasPrice;
            const extras = product.extras && product.extras.length > 0 ? 
                product.extras.join(', ') : 'No add-ons';
            
            modalBody.innerHTML += `
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 10px; background: #f8f9fa; border-radius: 8px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: var(--dark-color); margin-bottom: 5px;">${product.name}</div>
                        <div style="font-size: 13px; color: var(--gray-color); margin-bottom: 5px;">${extras}</div>
                        <div style="font-size: 14px; color: var(--dark-color);">Quantity: ${quantity}</div>
                    </div>
                    <div style="font-weight: 700; color: var(--primary-color);">${formatCurrency(productTotal)}</div>
                </div>
            `;
        });
    }
    
    modalBody.innerHTML += `
            </div>
        </div>
        
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-receipt"></i>
                Order Summary
            </h4>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Subtotal</span>
                    <span>${formatCurrency(subtotal)}</span>
                </div>
                ${extrasTotal > 0 ? `
                <div style="display: flex; justify-content: space-between;">
                    <span>Add-ons</span>
                    <span>${formatCurrency(extrasTotal)}</span>
                </div>
                ` : ''}
                <div style="display: flex; justify-content: space-between;">
                    <span>Delivery Fee</span>
                    <span>${formatCurrency(deliveryFee)}</span>
                </div>
                ${tip > 0 ? `
                <div style="display: flex; justify-content: space-between;">
                    <span>Tip</span>
                    <span>${formatCurrency(tip)}</span>
                </div>
                ` : ''}
                <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: var(--primary-color); padding-top: 10px; border-top: 2px solid #eee;">
                    <span>Total</span>
                    <span>${formatCurrency(total)}</span>
                </div>
            </div>
        </div>
        
        <div class="modal-section">
            <h4 class="modal-section-title">
                <i class="fas fa-shipping-fast"></i>
                Order Status Timeline
            </h4>
            <div class="timeline">
                ${timelineHTML}
            </div>
        </div>
    `;
    
    // Show modal
    document.getElementById('orderModal').classList.add('active');
}

// Close modal
function closeModal() {
    document.getElementById('orderModal').classList.remove('active');
}

// Cancel order
function cancelOrder(orderId) {
    if (!confirm('Are you sure you want to cancel this order?')) {
        return;
    }
    
    showToast('Cancelling order...');
    
    try {
        // Check Firebase
        checkFirebase();
        
        // Update order status in Firebase
        db.collection("restaurant_orders").doc(orderId).update({
            status: "Cancelled",
            statusChangedAt: firebase.firestore.FieldValue.serverTimestamp()
        })
        .then(() => {
            showToast('Order cancelled successfully!');
            
            // Update local data
            const orderIndex = allOrders.findIndex(o => o.id === orderId);
            if (orderIndex !== -1) {
                allOrders[orderIndex].status = 'Cancelled';
                allOrders[orderIndex].statusChangedAt = new Date();
                filterOrders();
            }
        })
        .catch(error => {
            console.error('Error cancelling order:', error);
            showToast('Failed to cancel order: ' + error.message);
        });
    } catch (error) {
        console.error('Firebase error:', error);
        showToast('Cannot cancel order: Firebase not connected');
    }
}

// Reorder
function reorder(orderId) {
    const order = allOrders.find(o => o.id === orderId);
    if (!order) {
        showToast('Order not found!');
        return;
    }
    
    // Save order data to localStorage for product.php to use
    const reorderData = {
        products: order.products || [],
        vendorID: order.vendorID,
        restaurantName: order.vendor?.title || order.vendor?.name || 'Restaurant',
        restaurantLogo: order.vendor?.photo || order.vendor?.authorProfilePic || '',
        orderTotal: 0
    };
    
    localStorage.setItem('currentOrder', JSON.stringify(reorderData));
    showToast('Order added to cart! Redirecting...');
    
    setTimeout(() => {
        window.location.href = 'order.php';
    }, 1500);
}

// Rate order
function rateOrder(orderId) {
    const rating = prompt('Please rate your order (1-5 stars):');
    if (!rating || isNaN(rating) || rating < 1 || rating > 5) {
        showToast('Please enter a valid rating between 1 and 5.');
        return;
    }
    
    const comment = prompt('Optional: Add a comment about your experience:');
    
    try {
        // Check Firebase
        checkFirebase();
        
        // Save rating to Firebase
        const reviewData = {
            order_id: orderId,
            user_id: userId,
            rating: parseFloat(rating),
            comment: comment || '',
            timestamp: firebase.firestore.FieldValue.serverTimestamp()
        };
        
        db.collection("foods_review").add(reviewData)
            .then(() => {
                showToast('Thank you for your feedback!');
            })
            .catch(error => {
                console.error('Error saving review:', error);
                showToast('Failed to save review: ' + error.message);
            });
    } catch (error) {
        console.error('Firebase error:', error);
        showToast('Cannot submit rating: Firebase not connected');
    }
}

// Load orders from Firebase
async function loadOrders() {
    const container = document.getElementById('ordersContainer');
    container.innerHTML = `
        <div class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading your orders...</p>
        </div>
    `;
    
    try {
        // Check Firebase initialization
        checkFirebase();
        
        console.log('Loading orders for user:', userId);
        
        // Get orders where authorID matches current user
        const snapshot = await db.collection("restaurant_orders")
            .where("authorID", "==", userId)
            .orderBy("createdAt", "desc")
            .get();
        
        allOrders = [];
        snapshot.forEach(doc => {
            const orderData = doc.data();
            orderData.id = doc.id;
            allOrders.push(orderData);
        });
        
        console.log('Loaded orders:', allOrders.length);
        
        // Apply current filters
        filterOrders();
        
    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="empty-title">Error Loading Orders</h2>
                <p class="empty-message">We couldn't load your orders. Please check your internet connection and try again.</p>
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button class="empty-cta" onclick="loadOrders()" style="border: none; cursor: pointer;">
                        <i class="fas fa-redo"></i>
                        <span>Try Again</span>
                    </button>
                    <a href="../login.php" class="empty-cta" style="background: var(--secondary-color);">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Re-login</span>
                    </a>
                </div>
            </div>
        `;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (!userId) {
        window.location.href = '../login.php';
        return;
    }
    
    // Load orders after a short delay to ensure Firebase is ready
    setTimeout(() => {
        loadOrders();
    }, 500);
    
    // Close modal when clicking outside
    document.getElementById('orderModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
});

// Handle window resize
window.addEventListener('resize', function() {
    // Adjust any responsive elements if needed
});

// Prevent pinch zoom on mobile
document.addEventListener('touchmove', function (event) {
    if (event.scale !== 1) { 
        event.preventDefault(); 
    }
}, { passive: false });
</script>

</body>
</html>
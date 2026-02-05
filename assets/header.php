<?php
// assets/header.php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loginPage = "login.php";

// Get cart item count from session or initialize
$cartItemCount = 0;
if (isset($_SESSION['cart'])) {
    $cartItemCount = count($_SESSION['cart']);
} elseif (isset($_SESSION['cart_items'])) {
    $cartItemCount = count($_SESSION['cart_items']);
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user']) || isset($_SESSION['firebase_user']) || isset($_SESSION['uid']);
$userName = 'Guest';
$userEmail = '';
$profilePictureUrl = null;

if ($isLoggedIn) {
    if (isset($_SESSION['firebase_user'])) {
        $userData = $_SESSION['firebase_user'];
    } elseif (isset($_SESSION['user'])) {
        $userData = $_SESSION['user'];
    } else {
        $userData = $_SESSION;
    }
    
    $userName = $userData['display_name'] ?? $userData['first_name'] ?? $userData['email'] ?? 'User';
    $userEmail = $userData['email'] ?? '';
    $profilePictureUrl = $userData['profilePictureURL'] ?? $userData['photoURL'] ?? null;
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Include Firebase Configuration -->
<?php include_once __DIR__ . '/../inc/firebase.php'; ?>

<style>
/* ================== FOOD DELIVERY THEME ================== */
:root {
    --primary-red: #FF3B30;
    --primary-orange: #FF9500;
    --accent-green: #34C759;
    --dark-gray: #1D1D1F;
    --light-gray: #F5F5F7;
    --medium-gray: #86868B;
    --white: #FFFFFF;
    --shadow-soft: 0 4px 24px rgba(0, 0, 0, 0.06);
    --shadow-medium: 0 8px 32px rgba(0, 0, 0, 0.1);
    --radius-large: 16px;
    --radius-medium: 12px;
    --radius-small: 8px;
    --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Inter', 'Segoe UI', -apple-system, sans-serif;
    background-color: #FAFAFA;
    color: var(--dark-gray);
    margin: 0;
    padding-top: 90px;
    line-height: 1.6;
}

/* ================== MODERN FOOD DELIVERY HEADER ================== */
.delivery-header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 90px;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 5%;
    z-index: 1000;
    box-shadow: var(--shadow-soft);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: var(--transition-smooth);
}

.delivery-header.scrolled {
    height: 75px;
    box-shadow: var(--shadow-medium);
}

/* ================== ANIMATED LOGO ================== */
.delivery-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    position: relative;
    flex-shrink: 0;
    z-index: 1001;
}

.logo-container {
    position: relative;
    width: 48px;
    height: 48px;
}

.logo-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 0.7rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.logo-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    border-radius: var(--radius-medium);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 20px rgba(255, 59, 48, 0.25);
    transition: var(--transition-smooth);
}

.logo-icon img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    transform: rotate(-15deg);
    transition: var(--transition-smooth);
}

.delivery-logo:hover .logo-icon {
    transform: rotate(5deg) scale(1.05);
}

.delivery-logo:hover .logo-icon img {
    transform: rotate(5deg);
}

.logo-text {
    display: flex;
    flex-direction: column;
}

.logo-main {
    font-family: 'Poppins', sans-serif;
    font-size: 1.8rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
}

.logo-tagline {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--medium-gray);
    letter-spacing: 0.3px;
}

/* ================== NAVIGATION MENU ================== */
.delivery-nav {
    display: flex;
    align-items: center;
    gap: 4px;
    background: var(--light-gray);
    padding: 6px;
    border-radius: var(--radius-large);
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}

.nav-link {
    text-decoration: none;
    color: var(--dark-gray);
    font-weight: 500;
    font-size: 0.95rem;
    padding: 10px 20px;
    border-radius: var(--radius-medium);
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition-smooth);
    position: relative;
    white-space: nowrap;
}

.nav-link i {
    font-size: 1.1rem;
    opacity: 0.7;
    transition: var(--transition-smooth);
}

.nav-link:hover {
    background: var(--white);
    color: var(--primary-red);
    transform: translateY(-1px);
}

.nav-link:hover i {
    opacity: 1;
    color: var(--primary-red);
}

.nav-link.active {
    background: var(--white);
    color: var(--primary-red);
    box-shadow: 0 4px 12px rgba(255, 59, 48, 0.15);
}

.nav-link.active i {
    color: var(--primary-red);
}

/* ================== CART INDICATOR - RED BADGE ================== */
.cart-indicator {
    position: relative;
}

.cart-count {
    position: absolute;
    top: -6px;
    right: -6px;
    min-width: 20px;
    height: 20px;
    background: var(--primary-red);
    color: var(--white);
    font-size: 0.7rem;
    font-weight: 600;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid var(--white);
    box-shadow: 0 2px 6px rgba(255, 59, 48, 0.3);
    opacity: 0;
    transform: scale(0.5);
    transition: var(--transition-smooth);
    animation: pulseRed 2s infinite;
}

.cart-count.has-items {
    opacity: 1;
    transform: scale(1);
}

@keyframes pulseRed {
    0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0.7); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 5px rgba(255, 59, 48, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(255, 59, 48, 0); }
}

/* ================== BUTTONS ================== */
.delivery-btn {
    padding: 10px 22px;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    color: var(--white) !important;
    border-radius: var(--radius-medium);
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: var(--transition-smooth);
    border: none;
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(255, 59, 48, 0.2);
    white-space: nowrap;
}

.delivery-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 59, 48, 0.3);
}

.delivery-btn:active {
    transform: translateY(0);
}

.delivery-btn i {
    font-size: 1rem;
}

.btn-outline {
    background: transparent;
    color: var(--dark-gray) !important;
    border: 2px solid var(--light-gray);
    box-shadow: none;
}

.btn-outline:hover {
    background: var(--light-gray);
    border-color: var(--medium-gray);
}

/* ================== USER MENU ================== */
.user-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    transition: var(--transition-smooth);
    box-shadow: 0 4px 12px rgba(255, 59, 48, 0.2);
    cursor: pointer;
    overflow: hidden;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    border: 2px solid var(--white);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-avatar .avatar-text {
    font-weight: 600;
    font-size: 1.1rem;
}

.user-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 18px rgba(255, 59, 48, 0.3);
}

/* ================== DROPDOWN MENU ================== */
.user-dropdown {
    position: relative;
}

.dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    margin-top: 10px;
    background: var(--white);
    border-radius: var(--radius-medium);
    box-shadow: var(--shadow-medium);
    min-width: 220px;
    z-index: 1001;
    display: none;
    overflow: hidden;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.dropdown-content.show {
    display: block;
    animation: slideInDown 0.2s ease-out;
}

.dropdown-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
    color: var(--white);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.dropdown-header .user-name {
    font-weight: 600;
    margin-bottom: 4px;
}

.dropdown-header .user-email {
    font-size: 0.85rem;
    opacity: 0.9;
}

.dropdown-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    color: var(--dark-gray);
    text-decoration: none;
    transition: var(--transition-smooth);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.dropdown-link:hover {
    background: var(--light-gray);
    color: var(--primary-red);
}

.dropdown-link:last-child {
    border-bottom: none;
    color: var(--primary-red);
}

.dropdown-link i {
    width: 20px;
    text-align: center;
    opacity: 0.7;
}

.dropdown-link:hover i {
    opacity: 1;
}

/* ================== LOADING STATE ================== */
.loading-avatar {
    width: 42px;
    height: 42px;
    background: var(--light-gray);
    border-radius: 50%;
    animation: pulse 1.5s infinite;
}

/* ================== BURGER ICON IMAGE ================== */
.burger-icon {
    width: 24px;
    height: 24px;
    object-fit: contain;
    transition: var(--transition-smooth);
}

.menu-toggle.active .burger-icon {
    transform: rotate(90deg);
}

.menu-toggle:hover .burger-icon {
    transform: scale(1.1);
}

/* ================== MOBILE MENU ================== */
.menu-toggle {
    display: none;
    width: 48px;
    height: 48px;
    background: var(--light-gray);
    border-radius: var(--radius-medium);
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-smooth);
    border: none;
    color: var(--primary-red);
    font-size: 1.5rem;
    z-index: 1001;
}

.menu-toggle:hover {
    background: var(--white);
    transform: scale(1.05);
}

.menu-toggle.active {
    background: var(--primary-red);
    color: var(--white);
}

/* ================== RESPONSIVE DESIGN - MOBILE FIRST ================== */

/* Extra Small Phones (up to 360px) */
@media (max-width: 360px) {
    body {
        padding-top: 60px;
    }
    
    .delivery-header {
        height: 60px;
        padding: 0 10px;
    }
    
    .logo-container {
        width: 36px;
        height: 36px;
    }
    
    .logo-icon {
        width: 36px;
        height: 36px;
    }
    
    .logo-icon img {
        width: 18px;
        height: 18px;
    }
    
    .logo-badge {
        width: 16px;
        height: 16px;
        font-size: 0.6rem;
    }
    
    .logo-main {
        font-size: 1.2rem;
    }
    
    .logo-tagline {
        font-size: 0.6rem;
    }
    
    .menu-toggle {
        width: 40px;
        height: 40px;
        font-size: 1.2rem;
    }
    
    .burger-icon {
        width: 20px;
        height: 20px;
    }
    
    .user-avatar {
        width: 36px;
        height: 36px;
        font-size: 0.9rem;
    }
    
    .cart-count {
        min-width: 16px;
        height: 16px;
        font-size: 0.6rem;
    }
}

/* Small Phones (361px to 480px) */
@media (max-width: 480px) {
    body {
        padding-top: 70px;
    }
    
    .delivery-header {
        height: 70px;
        padding: 0 15px;
    }
    
    .menu-toggle {
        display: flex;
        order: 2;
    }
    
    .delivery-nav {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--white);
        flex-direction: column;
        padding: 20px;
        border-radius: 0;
        box-shadow: var(--shadow-medium);
        display: none;
        transform: translateY(-20px);
        opacity: 0;
        transition: var(--transition-smooth);
        z-index: 999;
        overflow-y: auto;
        justify-content: flex-start;
        align-items: stretch;
        gap: 10px;
    }
    
    .delivery-nav.active {
        display: flex;
        transform: translateY(0);
        opacity: 1;
    }
    
    .nav-link {
        width: 100%;
        justify-content: center;
        padding: 15px;
        font-size: 1rem;
        border-radius: var(--radius-medium);
    }
    
    .nav-link span {
        display: inline-block !important;
        font-size: 1rem;
    }
    
    .nav-link i {
        font-size: 1.3rem;
        min-width: 24px;
    }
    
    .delivery-btn {
        width: 100%;
        justify-content: center;
        padding: 15px;
        font-size: 1rem;
        margin-top: 10px;
    }
    
    .delivery-btn span {
        display: inline-block !important;
    }
    
    .logo-text {
        display: flex !important;
        flex-direction: column;
    }
    
    .logo-main {
        font-size: 1.4rem;
    }
    
    .logo-tagline {
        font-size: 0.7rem;
    }
    
    .dropdown-content {
        position: fixed;
        top: 70px;
        left: 15px;
        right: 15px;
        margin-top: 0;
        max-width: calc(100% - 30px);
    }
    
    .cart-count {
        min-width: 18px;
        height: 18px;
        font-size: 0.7rem;
    }
}

/* Medium Phones (481px to 768px) */
@media (min-width: 481px) and (max-width: 768px) {
    body {
        padding-top: 80px;
    }
    
    .delivery-header {
        height: 80px;
        padding: 0 20px;
    }
    
    .menu-toggle {
        display: flex;
        order: 2;
    }
    
    .delivery-nav {
        position: fixed;
        top: 80px;
        left: 20px;
        right: 20px;
        background: var(--white);
        flex-direction: column;
        padding: 25px;
        border-radius: var(--radius-large);
        box-shadow: var(--shadow-medium);
        display: none;
        transform: translateY(-20px);
        opacity: 0;
        transition: var(--transition-smooth);
        z-index: 999;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }
    
    .delivery-nav.active {
        display: flex;
        transform: translateY(0);
        opacity: 1;
    }
    
    .nav-link {
        width: 100%;
        justify-content: flex-start;
        padding: 16px 20px;
        font-size: 1rem;
    }
    
    .nav-link span {
        display: inline-block !important;
    }
    
    .delivery-btn {
        width: 100%;
        justify-content: center;
        padding: 16px;
        margin-top: 10px;
    }
    
    .delivery-btn span {
        display: inline-block !important;
    }
    
    .logo-text {
        display: flex !important;
    }
    
    .logo-main {
        font-size: 1.6rem;
    }
    
    .dropdown-content {
        position: fixed;
        top: 80px;
        left: 20px;
        right: 20px;
        margin-top: 0;
        max-width: calc(100% - 40px);
    }
}

/* Tablets (769px to 1024px) */
@media (min-width: 769px) and (max-width: 1024px) {
    .delivery-header {
        padding: 0 3%;
    }
    
    .delivery-nav {
        gap: 2px;
    }
    
    .nav-link {
        padding: 10px 16px;
        font-size: 0.9rem;
    }
    
    .nav-link i {
        font-size: 1rem;
    }
    
    .delivery-btn {
        padding: 10px 18px;
        font-size: 0.9rem;
    }
    
    .logo-main {
        font-size: 1.6rem;
    }
    
    .logo-tagline {
        font-size: 0.7rem;
    }
    
    .user-avatar {
        width: 38px;
        height: 38px;
        font-size: 0.9rem;
    }
}

/* Small Desktops (1025px to 1280px) */
@media (min-width: 1025px) and (max-width: 1280px) {
    .delivery-header {
        padding: 0 4%;
    }
    
    .nav-link {
        padding: 10px 18px;
    }
    
    .delivery-btn {
        padding: 10px 20px;
    }
}

/* Large Desktops (1281px and above) */
@media (min-width: 1281px) {
    .delivery-header {
        padding: 0 6%;
    }
}

/* Landscape Mode */
@media (max-height: 500px) and (orientation: landscape) {
    .delivery-nav {
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .nav-link {
        padding: 12px 20px;
    }
    
    .delivery-btn {
        padding: 12px;
        margin-top: 5px;
    }
}


/* High DPI Screens */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .logo-icon {
        box-shadow: 0 8px 30px rgba(255, 59, 48, 0.3);
    }
    
    .user-avatar {
        box-shadow: 0 6px 20px rgba(255, 59, 48, 0.3);
    }
}

/* ================== ANIMATIONS ================== */
@keyframes slideInDown {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.delivery-nav.active .nav-link {
    animation: slideInDown 0.3s ease-out forwards;
}

.delivery-nav.active .nav-link:nth-child(1) { animation-delay: 0.1s; }
.delivery-nav.active .nav-link:nth-child(2) { animation-delay: 0.2s; }
.delivery-nav.active .nav-link:nth-child(3) { animation-delay: 0.3s; }
.delivery-nav.active .nav-link:nth-child(4) { animation-delay: 0.4s; }
.delivery-nav.active .nav-link:nth-child(5) { animation-delay: 0.5s; }
.delivery-nav.active .delivery-btn:nth-child(1) { animation-delay: 0.6s; }
.delivery-nav.active .delivery-btn:nth-child(2) { animation-delay: 0.7s; }

/* Smooth transitions for mobile menu */
.delivery-nav {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<header class="delivery-header">

    <a href="index.php" class="delivery-logo">
        <div class="logo-container">
            <div class="logo-badge">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <div class="logo-icon">
                <img src="assets/img/logo.png" alt="Burger" class="logo-burger-img">
            </div>
        </div>
        <div class="logo-text">
            <span class="logo-main">LalaGO</span>
            <span class="logo-tagline">Fast Food Delivery</span>
        </div>
    </a>

    <button class="menu-toggle" id="menuToggle" onclick="toggleMenu()">
        <img src="assets/img/logo.png" alt="Menu" class="burger-icon">
    </button>

    <nav class="delivery-nav" id="navMenu">
        <a href="index.php" class="nav-link">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        
        <a href="foods/categories.php" class="nav-link">
            <i class="fa-solid fa-utensils"></i>
            <span>Restaurants</span>
        </a>
        
        <a href="cart.php" class="nav-link cart-indicator">
            <i class="fa-solid fa-basket-shopping"></i>
            <span>Cart</span>
            <div class="cart-count" id="cartCount"><?php echo $cartItemCount > 0 ? $cartItemCount : '0'; ?></div>
        </a>

        <!-- DYNAMIC USER SECTION WILL BE POPULATED BY JAVASCRIPT -->
        <div id="userSection">
            <!-- Loading state initially -->
            <div class="loading-avatar" id="loadingAvatar"></div>
        </div>
    </nav>

</header>

<script>
// Global variables
let currentUser = null;
let authCheckCompleted = false;
const defaultProfilePic = 'storage/images/default-avatar.jpg';
const initialCartCount = <?php echo $cartItemCount; ?>;

// Toggle mobile menu
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    const menuToggle = document.getElementById('menuToggle');
    
    navMenu.classList.toggle('active');
    menuToggle.classList.toggle('active');
    
    // Change icon
    const burgerIcon = menuToggle.querySelector('.burger-icon');
    if (navMenu.classList.contains('active')) {
        // Rotate the burger icon when menu is active
        if (burgerIcon) {
            burgerIcon.style.transform = 'rotate(90deg)';
        }
        // Prevent body scroll when menu is open
        document.body.style.overflow = 'hidden';
    } else {
        // Reset rotation when menu is closed
        if (burgerIcon) {
            burgerIcon.style.transform = '';
        }
        // Restore body scroll
        document.body.style.overflow = '';
    }
}

// Close mobile menu when clicking outside or on links
function setupClickOutsideMenu() {
    document.addEventListener('click', function(event) {
        const navMenu = document.getElementById('navMenu');
        const menuToggle = document.getElementById('menuToggle');
        
        if (navMenu && menuToggle && 
            !navMenu.contains(event.target) && 
            !menuToggle.contains(event.target) && 
            navMenu.classList.contains('active')) {
            toggleMenu();
        }
    });
}

// Update cart count - RED BADGE VERSION
function updateCartCount(count) {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        cartCount.textContent = count;
        
        if (count > 0) {
            cartCount.classList.add('has-items');
        } else {
            cartCount.classList.remove('has-items');
        }
    }
}

// Refresh cart count from localStorage and session
function refreshCartCount() {
    const cartCountElement = document.getElementById('cartCount');
    if (!cartCountElement) return;
    
    // Try to get cart from localStorage first, then from PHP session
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
    
    // If no items in localStorage, use PHP session count
    let totalItems = 0;
    if (cartItems.length === 0 && initialCartCount > 0) {
        totalItems = initialCartCount;
    } else if (Array.isArray(cartItems)) {
        totalItems = cartItems.length;
    } else if (typeof cartItems === 'object' && cartItems !== null) {
        if (cartItems.items && Array.isArray(cartItems.items)) {
            totalItems = cartItems.items.length;
        } else {
            totalItems = Object.keys(cartItems).length;
        }
    }
    
    // Update the cart count
    updateCartCount(totalItems);
    console.log('Cart count updated:', totalItems);
    return totalItems;
}

// Toggle user dropdown
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
function setupDropdownClose() {
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const avatar = document.querySelector('.user-avatar');
        
        if (dropdown && avatar && 
            !dropdown.contains(event.target) && 
            !avatar.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
}

// Get user profile picture URL
function getProfilePictureUrl(user, userData) {
    // First priority: User's uploaded profile picture from Firestore
    if (userData && userData.profilePictureURL) {
        return userData.profilePictureURL;
    }
    
    // Second priority: Firebase photoURL (from Google/Facebook auth)
    if (user.photoURL) {
        return user.photoURL;
    }
    
    // Fallback: Default profile picture
    return defaultProfilePic;
}

// Create logged-in user UI
function createLoggedInUI(user, userData = null) {
    const firstName = userData?.firstName || 
                     user.displayName?.split(' ')[0] || 
                     'User';
    const userEmail = user.email || 'No email';
    const avatarLetter = firstName.charAt(0).toUpperCase();
    const profilePicUrl = getProfilePictureUrl(user, userData);
    
    // Check if it's the default profile picture
    const isDefaultPic = profilePicUrl === defaultProfilePic;
    
    let avatarContent = `<div class="avatar-text">${avatarLetter}</div>`;
    
    // If profile picture URL exists and is not the default, use img tag
    if (profilePicUrl !== defaultProfilePic) {
        avatarContent = `<img src="${profilePicUrl}" alt="${firstName}" onerror="this.parentElement.innerHTML='<div class=\\'avatar-text\\'>${avatarLetter}</div>'">`;
    }
    
    return `
        <div class="user-dropdown">
            <a href="javascript:void(0)" class="user-avatar" onclick="toggleUserDropdown()">
                ${avatarContent}
            </a>
            <div class="dropdown-content" id="userDropdown">
                <div class="dropdown-header">
                    <div class="user-name">${firstName}</div>
                    <div class="user-email">${userEmail}</div>
                </div>
                <a href="users/profile.php" class="dropdown-link" onclick="closeMobileMenu()">
                    <i class="fa-solid fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="foods/my-orders.php" class="dropdown-link" onclick="closeMobileMenu()">
                    <i class="fa-solid fa-receipt"></i>
                    <span>My Orders</span>
                </a>
                <a href="favorites.php" class="dropdown-link" onclick="closeMobileMenu()">
                    <i class="fa-solid fa-heart"></i>
                    <span>Favorites</span>
                </a>
                <a href="javascript:void(0)" class="dropdown-link" onclick="logout()">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    `;
}

// Create logged-out user UI
function createLoggedOutUI() {
    return `
        <a href="<?php echo $loginPage; ?>" class="delivery-btn" id="loginBtn" onclick="closeMobileMenu()">
            <i class="fa-solid fa-right-to-bracket"></i>
            <span>Login</span>
        </a>
        
        <a href="register.php" class="delivery-btn btn-outline" id="registerBtn" onclick="closeMobileMenu()">
            <i class="fa-solid fa-user-plus"></i>
            <span>Register</span>
        </a>
    `;
}

// Close mobile menu function
function closeMobileMenu() {
    const navMenu = document.getElementById('navMenu');
    const menuToggle = document.getElementById('menuToggle');
    
    if (navMenu && navMenu.classList.contains('active')) {
        navMenu.classList.remove('active');
        menuToggle.classList.remove('active');
        // Reset burger icon rotation
        const burgerIcon = menuToggle.querySelector('.burger-icon');
        if (burgerIcon) {
            burgerIcon.style.transform = '';
        }
        document.body.style.overflow = '';
    }
}

// Fetch user data from Firestore
async function fetchUserData(uid) {
    try {
        if (!db) {
            console.error('Firestore database not initialized');
            return null;
        }
        
        const doc = await db.collection('users').doc(uid).get();
        if (doc.exists) {
            return doc.data();
        }
        return null;
    } catch (error) {
        console.error('Error fetching user data:', error);
        return null;
    }
}

// Update UI based on auth state
async function updateUI(user) {
    const userSection = document.getElementById('userSection');
    if (!userSection) return;
    
    if (user) {
        // User is logged in
        currentUser = user;
        
        // Show loading initially
        userSection.innerHTML = '<div class="loading-avatar"></div>';
        
        try {
            // Fetch user data from Firestore
            const userData = await fetchUserData(user.uid);
            
            // Update UI with user data
            userSection.innerHTML = createLoggedInUI(user, userData);
            
        } catch (error) {
            console.error('Error in updateUI:', error);
            // Fallback to basic user info
            userSection.innerHTML = createLoggedInUI(user, null);
        }
        
        // Re-setup dropdown close listener
        setTimeout(setupDropdownClose, 100);
        
    } else {
        // User is not logged in
        currentUser = null;
        userSection.innerHTML = createLoggedOutUI();
    }
    
    // Refresh cart count when UI updates
    refreshCartCount();
}

// Logout function
function logout() {
    closeMobileMenu();
    
    if (auth) {
        auth.signOut().then(() => {
            currentUser = null;
            window.location.href = 'index.php';
        }).catch(error => {
            console.error('Logout error:', error);
            alert('Error logging out. Please try again.');
        });
    }
}

// Listen for auth state changes
function setupAuthListener() {
    if (auth) {
        // Set a timeout in case auth check takes too long
        const authTimeout = setTimeout(() => {
            if (!authCheckCompleted) {
                updateUI(null);
            }
        }, 5000);
        
        auth.onAuthStateChanged(async (user) => {
            authCheckCompleted = true;
            clearTimeout(authTimeout);
            await updateUI(user);
        });
        
        // Check current auth state immediately
        const currentAuthUser = auth.currentUser;
        if (currentAuthUser) {
            authCheckCompleted = true;
            clearTimeout(authTimeout);
            updateUI(currentAuthUser);
        }
    } else {
        updateUI(null);
    }
}

// Initialize responsive behavior
function initResponsiveBehavior() {
    // Handle resize events
    window.addEventListener('resize', function() {
        // Close mobile menu if resizing to desktop
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
    
    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        // Close mobile menu on orientation change
        closeMobileMenu();
    });
}

// Initialize cart count
refreshCartCount();

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('.delivery-header');
    if (header) {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }
});

// Initial setup when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup event listeners
    setupClickOutsideMenu();
    setupDropdownClose();
    initResponsiveBehavior();
    
    // Setup auth listener with a small delay to ensure Firebase is loaded
    setTimeout(() => {
        setupAuthListener();
    }, 100);
    
    // Add click handlers to all nav links to close mobile menu
    document.addEventListener('click', function(event) {
        if (event.target.closest('.nav-link') || event.target.closest('.delivery-btn')) {
            closeMobileMenu();
        }
    });
    
    // Listen for storage events to update cart count when changed in other tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'cart') {
            refreshCartCount();
        }
    });
});

// Handle back button on mobile
window.addEventListener('popstate', function() {
    closeMobileMenu();
});

// Prevent body scroll when menu is open
document.addEventListener('touchmove', function(event) {
    const navMenu = document.getElementById('navMenu');
    if (navMenu && navMenu.classList.contains('active')) {
        event.preventDefault();
    }
}, { passive: false });

// Global function to update cart (can be called from other pages)
window.updateCart = function(items) {
    localStorage.setItem('cart', JSON.stringify(items));
    // Update badge immediately
    refreshCartCount();
};

// Global function to update cart badge
window.refreshCartCount = function() {
    refreshCartCount();
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
<?php
session_start();

$isLoggedIn = isset($_SESSION['uid']);
$userEmail  = $_SESSION['email'] ?? '';
$searchQuery = $_GET['q'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LalaGO - Search</title>

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

/* ================= LAYOUT ENHANCEMENTS ================= */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* ================= SEARCH HEADER ================= */
.search-header {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border-radius: var(--radius);
  padding: 40px;
  margin-bottom: 40px;
  color: var(--white);
  position: relative;
  overflow: hidden;
}

.search-header::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 200%;
  background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
  transform: rotate(30deg);
  animation: shine 3s infinite linear;
}

@keyframes shine {
  0% { transform: translateX(-100%) rotate(30deg); }
  100% { transform: translateX(100%) rotate(30deg); }
}

.search-header h1 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 20px;
  position: relative;
  z-index: 1;
}

.search-header p {
  font-size: 1.1rem;
  opacity: 0.9;
  position: relative;
  z-index: 1;
  max-width: 600px;
}

/* ================= SEARCH BAR ================= */
.search-container {
  position: relative;
  z-index: 1;
  max-width: 800px;
  margin-top: 30px;
}

.search-bar {
  display: flex;
  gap: 15px;
  position: relative;
}

.search-bar input {
  flex: 1;
  padding: 18px 25px;
  border: none;
  border-radius: 50px;
  font-size: 1.1rem;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transition: var(--transition);
}

.search-bar input:focus {
  outline: none;
  background: var(--white);
  box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.search-bar button {
  background: var(--white);
  color: var(--primary);
  border: none;
  padding: 18px 35px;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 10px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  white-space: nowrap;
}

.search-bar button:hover {
  background: var(--secondary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
}

.search-bar button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* ================= SEARCH RESULTS ================= */
.search-results-container {
  margin-top: 40px;
}

.search-results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.search-results-header h2 {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-count {
  color: var(--primary);
  font-weight: 600;
}

.search-filters {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 30px;
}

.filter-btn {
  background: var(--white);
  border: 2px solid var(--gray-light);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  color: var(--gray-dark);
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-btn:hover {
  border-color: var(--primary);
  color: var(--primary);
}

.filter-btn.active {
  background: var(--primary);
  border-color: var(--primary);
  color: var(--white);
}

/* ================= SEARCH RESULTS GRID ================= */
.search-results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 25px;
  margin-bottom: 40px;
}

/* ================= SEARCH RESULT CARD ================= */
.search-result-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  display: block;
  height: 100%;
}

.search-result-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-hover);
}

.search-result-image {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.search-result-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.search-result-card:hover img {
  transform: scale(1.05);
}

/* ================= RESTAURANT BADGE ================= */
.restaurant-badge {
  position: absolute;
  bottom: 10px;
  left: 10px;
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.95);
  padding: 6px 12px;
  border-radius: 20px;
  z-index: 1;
  backdrop-filter: blur(5px);
}

.restaurant-image {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid var(--primary);
}

.restaurant-name {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--secondary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 120px;
}

/* ================= SEARCH RESULT INFO ================= */
.search-result-info {
  padding: 20px;
}

.search-result-name {
  font-size: 1.2rem;
  font-weight: 700;
  margin-bottom: 10px;
  color: var(--secondary);
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.search-result-description {
  color: var(--gray);
  font-size: 0.95rem;
  margin-bottom: 15px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.search-result-meta {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.search-result-rating {
  display: flex;
  align-items: center;
  gap: 5px;
}

.rating-stars {
  color: var(--warning);
  font-size: 0.9rem;
}

.rating-value {
  font-weight: 600;
  color: var(--secondary);
  font-size: 0.95rem;
}

.rating-count {
  color: var(--gray);
  font-size: 0.85rem;
}

.search-result-category {
  background: var(--light);
  color: var(--gray);
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
}

/* ================= PRICE & CART SECTION ================= */
.search-result-price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid var(--gray-light);
}

.search-result-price {
  font-size: 1.4rem;
  font-weight: 800;
  color: var(--primary);
  display: flex;
  align-items: center;
  gap: 8px;
}

.search-result-price .original-price {
  font-size: 1rem;
  color: var(--gray);
  text-decoration: line-through;
  font-weight: 400;
}

.search-cart-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--light);
  border-radius: 50px;
  padding: 5px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.quantity-btn {
  background: var(--white);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  font-size: 1rem;
  font-weight: bold;
  color: var(--primary);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.quantity-btn:hover {
  background: var(--primary);
  color: var(--white);
  transform: scale(1.1);
}

.quantity-input {
  width: 40px;
  text-align: center;
  border: none;
  background: transparent;
  font-size: 1rem;
  font-weight: 600;
  color: var(--secondary);
  outline: none;
}

.add-to-cart-btn {
  background: var(--primary);
  color: var(--white);
  border: none;
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
  white-space: nowrap;
}

.add-to-cart-btn:hover {
  background: var(--primary-dark);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.add-to-cart-btn.added {
  background: var(--success);
}

/* ================= RECENT SEARCHES ================= */
.recent-searches {
  background: var(--white);
  border-radius: var(--radius);
  padding: 30px;
  margin-top: 40px;
  box-shadow: var(--shadow);
}

.recent-searches h3 {
  font-size: 1.4rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 10px;
}

.recent-searches-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.recent-search-tag {
  background: var(--light);
  border: 1px solid var(--gray-light);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  color: var(--gray-dark);
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.recent-search-tag:hover {
  background: var(--primary);
  color: var(--white);
  border-color: var(--primary);
  transform: translateY(-2px);
}

/* ================= SEARCH ANALYTICS ================= */
.search-analytics {
  background: var(--white);
  border-radius: var(--radius);
  padding: 30px;
  margin-top: 30px;
  box-shadow: var(--shadow);
}

.search-analytics h3 {
  font-size: 1.4rem;
  font-weight: 700;
  margin-bottom: 20px;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 10px;
}

.analytics-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

/* Ensure proper column distribution based on number of items */
.analytics-stats:has(.stat-item:nth-child(3):last-child) {
  grid-template-columns: repeat(3, 1fr);
}

.analytics-stats:has(.stat-item:nth-child(2):last-child) {
  grid-template-columns: repeat(2, 1fr);
}

.stat-item {
  background: var(--light);
  padding: 20px;
  border-radius: var(--radius-sm);
  text-align: center;
}

.stat-value {
  font-size: 2rem;
  font-weight: 800;
  color: var(--primary);
  margin-bottom: 5px;
}

.stat-label {
  font-size: 0.9rem;
  color: var(--gray);
}

/* ================= SIGN IN PROMPT ================= */
.signin-prompt {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border-radius: var(--radius);
  padding: 40px;
  margin-top: 40px;
  color: var(--white);
  text-align: center;
  box-shadow: var(--shadow);
}

.signin-prompt i {
  font-size: 3rem;
  margin-bottom: 20px;
}

.signin-prompt h3 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 15px;
}

.signin-prompt p {
  font-size: 1.1rem;
  opacity: 0.9;
  margin-bottom: 25px;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

.signin-buttons {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
}

.signin-btn {
  background: var(--white);
  color: var(--primary);
  border: none;
  padding: 15px 30px;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.signin-btn:hover {
  background: var(--secondary);
  color: var(--white);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.signup-btn {
  background: transparent;
  color: var(--white);
  border: 2px solid var(--white);
  padding: 15px 30px;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
}

.signup-btn:hover {
  background: var(--white);
  color: var(--primary);
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

/* ================= NO RESULTS ================= */
.no-results {
  text-align: center;
  padding: 60px 20px;
  color: var(--gray);
}

.no-results i {
  font-size: 3rem;
  color: var(--gray-light);
  margin-bottom: 20px;
}

.no-results h3 {
  font-size: 1.5rem;
  margin-bottom: 10px;
  color: var(--secondary);
}

.no-results p {
  font-size: 1rem;
  color: var(--gray);
}

/* ================= LOADING ================= */
.loading {
  text-align: center;
  padding: 40px;
  color: var(--gray);
}

.loading i {
  font-size: 2rem;
  margin-bottom: 20px;
}

/* ================= RESPONSIVE DESIGN ================= */

/* Tablet */
@media (max-width: 1024px) {
  .search-results-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
  }
  
  .search-header {
    padding: 30px;
  }
  
  .search-header h1 {
    font-size: 2rem;
  }
  
  .analytics-stats {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Small Tablet */
@media (max-width: 768px) {
  .search-results-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
  
  .search-bar {
    flex-direction: column;
  }
  
  .search-bar button {
    justify-content: center;
    width: 100%;
  }
  
  .search-results-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .search-filters {
    justify-content: center;
  }
  
  .analytics-stats {
    grid-template-columns: 1fr;
  }
  
  .signin-prompt {
    padding: 30px 20px;
  }
  
  .signin-buttons {
    flex-direction: column;
  }
  
  .signin-btn,
  .signup-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Mobile */
@media (max-width: 480px) {
  .search-results-grid {
    grid-template-columns: 1fr;
  }
  
  .search-header {
    padding: 20px;
  }
  
  .search-header h1 {
    font-size: 1.6rem;
  }
  
  .search-header p {
    font-size: 1rem;
  }
  
  .search-result-image {
    height: 180px;
  }
  
  .search-cart-controls {
    flex-wrap: wrap;
    justify-content: center;
  }
}

/* ================= ANIMATIONS ================= */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.search-result-card {
  animation: fadeIn 0.5s ease forwards;
}

/* ================= NOTIFICATION STYLES ================= */
.custom-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: var(--white);
  color: var(--secondary);
  padding: 15px 20px;
  border-radius: var(--radius-sm);
  box-shadow: var(--shadow-hover);
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 9999;
  animation: slideIn 0.3s ease forwards;
  border-left: 4px solid var(--info);
}

.custom-notification.success {
  border-left-color: var(--success);
}

.custom-notification.warning {
  border-left-color: var(--warning);
}

.custom-notification.danger {
  border-left-color: var(--danger);
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes fadeOut {
  from {
    opacity: 1;
    transform: translateX(0);
  }
  to {
    opacity: 0;
    transform: translateX(100%);
  }
}

/* ================= GUEST CART WARNING ================= */
.guest-cart-warning {
  background: linear-gradient(135deg, var(--warning), #E67E22);
  border-radius: var(--radius);
  padding: 15px 20px;
  margin: 20px 0;
  color: var(--white);
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 0.95rem;
}

.guest-cart-warning a {
  color: var(--white);
  text-decoration: underline;
  font-weight: 600;
  margin-left: 5px;
}

.guest-cart-warning a:hover {
  text-decoration: none;
}
</style>

</head>
<body>

<?php include 'assets/header.php'; ?>

<main class="container">

<!-- ================= SEARCH HEADER ================= -->
<div class="search-header">
  <h1>Search Foods & Restaurants</h1>
  <p>Find your favorite meals from top-rated restaurants near you</p>
  
  <div class="search-container">
    <div class="search-bar">
      <input type="text" id="productSearch" placeholder="Search for burgers, pizza, sushi, desserts..." value="<?php echo htmlspecialchars($searchQuery); ?>">
      <button id="searchBtn">
        <i class="fa-solid fa-magnifying-glass"></i> 
        Search
      </button>
    </div>
  </div>
</div>

<?php if (!$isLoggedIn): ?>
<!-- ================= GUEST CART WARNING ================= -->
<div class="guest-cart-warning">
  <i class="fas fa-exclamation-triangle"></i>
  <span>You're browsing as a guest. Items added to cart will be saved locally. <a href="login.php">Sign in</a> to save cart across devices.</span>
</div>
<?php endif; ?>

<!-- ================= SEARCH RESULTS ================= -->
<div class="search-results-container">
  <div class="search-results-header">
    <h2 id="resultsTitle">
      <i class="fas fa-search"></i>
      Search Results
    </h2>
    <div class="search-count" id="resultsCount">0 results</div>
  </div>

  <div class="search-filters" id="searchFilters">
    <button class="filter-btn active" data-filter="all">All</button>
    <button class="filter-btn" data-filter="restaurant">Restaurants</button>
    <button class="filter-btn" data-filter="food">Foods</button>
    <button class="filter-btn" data-filter="popular">Popular</button>
    <button class="filter-btn" data-filter="discount">Discount</button>
  </div>

  <div id="searchResults" class="search-results-grid">
    <!-- Search results will be loaded here -->
  </div>

  <div id="loading" class="loading" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i>
    <p>Searching...</p>
  </div>

  <div id="noResults" class="no-results" style="display: none;">
    <i class="fas fa-search"></i>
    <h3>No results found</h3>
    <p>Try searching for something else</p>
  </div>
</div>

<!-- ================= RECENT SEARCHES ================= -->
<div class="recent-searches">
  <h3><i class="fas fa-history"></i> Recent Searches</h3>
  <div class="recent-searches-list" id="recentSearches">
    <!-- Recent searches will be loaded here -->
  </div>
</div>

<!-- ================= SEARCH ANALYTICS ================= -->
<div class="search-analytics">
  <h3><i class="fas fa-chart-line"></i> Search Analytics</h3>
  <div class="analytics-stats" id="analyticsStats">
    <!-- Stats will be loaded by JavaScript -->
  </div>
</div>

<?php if (!$isLoggedIn): ?>
<!-- ================= SIGN IN PROMPT ================= -->
<div class="signin-prompt">
  <i class="fas fa-user-plus"></i>
  <h3>Sign In to Explore More Foods!</h3>
  <p>Create an account or sign in to access exclusive features, save your favorite foods, get personalized recommendations, and enjoy faster checkout.</p>
  <div class="signin-buttons">
    <a href="login.php" class="signin-btn">
      <i class="fas fa-sign-in-alt"></i>
      Sign In
    </a>
    <a href="register.php" class="signup-btn">
      <i class="fas fa-user-plus"></i>
      Create Account
    </a>
  </div>
</div>
<?php endif; ?>

</main>

<!-- ================= FIREBASE ================= -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="js/firebase.js"></script>

<script>
// ================= GLOBAL VARIABLES =================
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let currentFilter = 'all';
let currentResults = [];
let recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];

// ================= INITIALIZE ON LOAD ================= 
document.addEventListener('DOMContentLoaded', async function() {
  console.log('Search Page Initializing...');
  
  // Initialize cart from localStorage
  cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  // Load recent searches from localStorage
  recentSearches = JSON.parse(localStorage.getItem('recentSearches')) || [];
  
  // Setup search functionality
  setupSearchFunctionality();
  
  // Load recent searches
  loadRecentSearches();
  
  // Load search analytics
  await loadSearchAnalytics();
  
  // If there's a query in URL, perform search
  const urlParams = new URLSearchParams(window.location.search);
  const query = urlParams.get('q');
  if (query && query.trim().length >= 2) {
    document.getElementById('productSearch').value = query;
    performSearch(query, true);
  }
  
  console.log('Search Page initialized');
});

// ================= SETUP SEARCH FUNCTIONALITY =================
function setupSearchFunctionality() {
  const searchInput = document.getElementById('productSearch');
  const searchBtn = document.getElementById('searchBtn');
  const searchResults = document.getElementById('searchResults');
  
  if (!searchInput || !searchBtn) return;
  
  let searchTimeout = null;
  
  // Real-time search with debounce
  searchInput.addEventListener('input', (e) => {
    const query = e.target.value.trim();
    
    // Clear previous timeout
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }
    
    // Set new timeout for debounce
    searchTimeout = setTimeout(() => {
      if (query.length >= 2) {
        // Update URL without reloading
        const url = new URL(window.location);
        url.searchParams.set('q', query);
        window.history.pushState({}, '', url);
        
        performSearch(query, false);
      } else if (query.length === 0) {
        // Clear results if search is empty
        searchResults.innerHTML = '';
        document.getElementById('noResults').style.display = 'none';
        document.getElementById('resultsCount').textContent = '0 results';
        document.getElementById('resultsTitle').innerHTML = '<i class="fas fa-search"></i> Search Results';
      }
    }, 500);
  });
  
  // Search button click
  searchBtn.addEventListener('click', () => {
    const query = searchInput.value.trim();
    if (query.length >= 2) {
      // Update URL without reloading
      const url = new URL(window.location);
      url.searchParams.set('q', query);
      window.history.pushState({}, '', url);
      
      performSearch(query, true);
    } else {
      showNotification('Please enter at least 2 characters to search', 'warning');
    }
  });
  
  // Enter key support
  searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      searchBtn.click();
    }
  });
  
  // Setup filter buttons
  setupFilterButtons();
}

// ================= SETUP FILTER BUTTONS =================
function setupFilterButtons() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(button => {
    button.addEventListener('click', () => {
      // Update active button
      filterButtons.forEach(btn => btn.classList.remove('active'));
      button.classList.add('active');
      
      // Update current filter
      currentFilter = button.dataset.filter;
      
      // Filter results
      filterResults(currentFilter);
    });
  });
}

// ================= PERFORM SEARCH =================
async function performSearch(query, fromButton = false) {
  const searchResults = document.getElementById('searchResults');
  const loading = document.getElementById('loading');
  const noResults = document.getElementById('noResults');
  const searchBtn = document.getElementById('searchBtn');
  const originalContent = searchBtn.innerHTML;
  
  // Show loading state
  searchResults.innerHTML = '';
  loading.style.display = 'block';
  noResults.style.display = 'none';
  searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';
  searchBtn.disabled = true;
  
  // Update title
  document.getElementById('resultsTitle').innerHTML = `<i class="fas fa-search"></i> Search Results for "${query}"`;
  
  try {
    // Get all products
    const snapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .get();
    
    // Get all vendors
    const vendorsSnapshot = await db.collection("vendors").get();
    const vendors = {};
    vendorsSnapshot.forEach(doc => {
      vendors[doc.id] = doc.data();
    });
    
    const results = [];
    const lowerQuery = query.toLowerCase();
    
    // Filter locally
    snapshot.forEach(doc => {
      const data = doc.data();
      const foodName = (data.name || '').toLowerCase();
      const description = (data.description || '').toLowerCase();
      const category = (data.category || '').toLowerCase();
      
      // Get vendor name
      let vendorName = '';
      if (data.vendorId && vendors[data.vendorId]) {
        vendorName = vendors[data.vendorId].restaurantName || '';
      }
      
      const vendorNameLower = vendorName.toLowerCase();
      
      // Check if matches search query
      if (foodName.includes(lowerQuery) || 
          description.includes(lowerQuery) || 
          category.includes(lowerQuery) ||
          vendorNameLower.includes(lowerQuery)) {
        results.push({
          id: doc.id,
          ...data,
          vendor: data.vendorId && vendors[data.vendorId] ? vendors[data.vendorId] : null,
          type: 'food'
        });
      }
    });
    
    // Also search in vendors
    vendorsSnapshot.forEach(doc => {
      const vendor = doc.data();
      const vendorName = (vendor.restaurantName || '').toLowerCase();
      const vendorDescription = (vendor.description || '').toLowerCase();
      
      if (vendorName.includes(lowerQuery) || 
          vendorDescription.includes(lowerQuery)) {
        results.push({
          id: doc.id,
          ...vendor,
          type: 'restaurant'
        });
      }
    });
    
    // Store current results
    currentResults = results;
    
    // Display results
    displaySearchResults(results);
    
    // Save to recent searches if from button
    if (fromButton) {
      saveToRecentSearches(query);
    }
    
    // Log search to analytics
    logSearchToAnalytics(query, results.length);
    
  } catch (error) {
    console.error("Error searching:", error);
    showNotification("Error searching", "warning");
  } finally {
    // Hide loading
    loading.style.display = 'none';
    
    // Restore button state
    searchBtn.innerHTML = originalContent;
    searchBtn.disabled = false;
  }
}

// ================= FILTER RESULTS =================
function filterResults(filter) {
  if (!currentResults || currentResults.length === 0) return;
  
  let filteredResults = [...currentResults];
  
  switch(filter) {
    case 'restaurant':
      filteredResults = currentResults.filter(item => item.type === 'restaurant');
      break;
    case 'food':
      filteredResults = currentResults.filter(item => item.type === 'food');
      break;
    case 'popular':
      filteredResults = currentResults.filter(item => {
        const rating = item.reviewAttributes?.reviewsCount > 0 ? 
          (item.reviewAttributes.reviewsSum / item.reviewAttributes.reviewsCount) : 0;
        return rating >= 4;
      });
      break;
    case 'discount':
      filteredResults = currentResults.filter(item => {
        const disPrice = parseFloat(item.disPrice || 0);
        const regPrice = parseFloat(item.price || 0);
        return disPrice > 0 && disPrice < regPrice;
      });
      break;
  }
  
  displaySearchResults(filteredResults);
}

// ================= DISPLAY SEARCH RESULTS =================
function displaySearchResults(results) {
  const searchResults = document.getElementById('searchResults');
  const noResults = document.getElementById('noResults');
  const resultsCount = document.getElementById('resultsCount');
  
  if (!searchResults) return;
  
  if (results.length === 0) {
    searchResults.innerHTML = '';
    noResults.style.display = 'block';
    resultsCount.textContent = '0 results';
    return;
  }
  
  // Update count
  resultsCount.textContent = `${results.length} result${results.length !== 1 ? 's' : ''}`;
  
  // Clear previous results
  searchResults.innerHTML = '';
  
  // Display results
  results.forEach(item => {
    if (item.type === 'food') {
      createFoodCard(item, searchResults);
    } else {
      createRestaurantCard(item, searchResults);
    }
  });
  
  noResults.style.display = 'none';
}

// ================= CREATE FOOD CARD =================
function createFoodCard(food, container) {
  const reviewsCount = food.reviewAttributes?.reviewsCount || 0;
  const reviewsSum = food.reviewAttributes?.reviewsSum || 0;
  const rating = reviewsCount > 0 ? (reviewsSum / reviewsCount).toFixed(1) : "0.0";
  
  // Handle price safely
  let price = 0;
  let originalPrice = null;
  
  try {
    const disPrice = parseFloat(food.disPrice);
    const regPrice = parseFloat(food.price);
    
    if (!isNaN(disPrice) && disPrice > 0 && disPrice < regPrice) {
      price = disPrice;
      originalPrice = regPrice;
    } else if (!isNaN(regPrice)) {
      price = regPrice;
    }
  } catch (e) {
    console.warn(`Error parsing price for ${food.id}`);
    price = 0;
  }
  
  // Get restaurant name and image
  const restaurantName = food.vendor?.restaurantName || 'Restaurant';
  const restaurantImage = food.vendor?.profileImage || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';
  
  // Check if item is already in cart
  const cartItem = cart.find(item => item.id === food.id);
  const quantity = cartItem ? cartItem.qty : 1;
  
  // Generate stars HTML
  const starsHTML = Array(5).fill(0).map((_, i) => {
    const starRating = parseFloat(rating);
    if (i < Math.floor(starRating)) {
      return '<i class="fas fa-star"></i>';
    } else if (i === Math.floor(starRating) && starRating % 1 >= 0.5) {
      return '<i class="fas fa-star-half-alt"></i>';
    } else {
      return '<i class="far fa-star"></i>';
    }
  }).join('');
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${food.id}`;
  card.className = "search-result-card";
  card.setAttribute('data-type', 'food');
  
  card.innerHTML = `
    <div class="search-result-image">
      <img src="${food.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           loading="lazy" alt="${food.name || 'Food item'}"
           onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      <div class="restaurant-badge">
        <img src="${restaurantImage}" alt="${restaurantName}" class="restaurant-image">
        <span class="restaurant-name">${restaurantName}</span>
      </div>
    </div>
    <div class="search-result-info">
      <div class="search-result-name">${food.name || 'Unnamed Item'}</div>
      <div class="search-result-description">${food.description || 'Delicious food prepared with fresh ingredients'}</div>
      <div class="search-result-meta">
        <div class="search-result-rating">
          <div class="rating-stars">${starsHTML}</div>
          <span class="rating-value">${rating}</span>
          <span class="rating-count">(${reviewsCount})</span>
        </div>
        <div class="search-result-category">${food.category || 'General'}</div>
      </div>
      <div class="search-result-price-section">
        <div class="search-result-price">
          ₱${price.toFixed(2)}
          ${originalPrice ? `<span class="original-price">₱${originalPrice.toFixed(2)}</span>` : ''}
        </div>
        <div class="search-cart-controls">
          <button class="quantity-btn minus" onclick="event.preventDefault(); event.stopPropagation(); updateQuantity('${food.id}', -1, this)">
            <i class="fas fa-minus"></i>
          </button>
          <input type="text" class="quantity-input" value="${quantity}" data-product="${food.id}" readonly>
          <button class="quantity-btn plus" onclick="event.preventDefault(); event.stopPropagation(); updateQuantity('${food.id}', 1, this)">
            <i class="fas fa-plus"></i>
          </button>
          <button class="add-to-cart-btn ${cartItem ? 'added' : ''}" 
                  onclick="event.preventDefault(); event.stopPropagation(); addToCartWithQuantity('${food.id}', '${food.name || 'Item'}', ${price}, this)">
            <i class="fas fa-shopping-cart"></i>
            ${cartItem ? 'Update' : 'Add'}
          </button>
        </div>
      </div>
    </div>
  `;

  container.appendChild(card);
}

// ================= CREATE RESTAURANT CARD =================
function createRestaurantCard(restaurant, container) {
  const rating = restaurant.averageRating || 0;
  const reviewsCount = restaurant.totalReviews || 0;
  
  const starsHTML = Array(5).fill(0).map((_, i) => {
    if (i < Math.floor(rating)) {
      return '<i class="fas fa-star"></i>';
    } else if (i === Math.floor(rating) && rating % 1 >= 0.5) {
      return '<i class="fas fa-star-half-alt"></i>';
    } else {
      return '<i class="far fa-star"></i>';
    }
  }).join('');
  
  const card = document.createElement("a");
  card.href = `vendor/vendor.php?id=${restaurant.id}`;
  card.className = "search-result-card";
  card.setAttribute('data-type', 'restaurant');
  
  card.innerHTML = `
    <div class="search-result-image">
      <img src="${restaurant.profileImage || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           loading="lazy" alt="${restaurant.restaurantName || 'Restaurant'}"
           onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
    </div>
    <div class="search-result-info">
      <div class="search-result-name">${restaurant.restaurantName || 'Restaurant'}</div>
      <div class="search-result-description">${restaurant.description || 'Delicious food from our restaurant'}</div>
      <div class="search-result-meta">
        <div class="search-result-rating">
          <div class="rating-stars">${starsHTML}</div>
          <span class="rating-value">${rating.toFixed(1)}</span>
          <span class="rating-count">(${reviewsCount})</span>
        </div>
        <div class="search-result-category">Restaurant</div>
      </div>
      <div class="search-result-price-section">
        <div class="search-result-price">
          ${restaurant.isOpen ? '<span style="color: var(--success);">Open</span>' : '<span style="color: var(--danger);">Closed</span>'}
        </div>
        <button class="add-to-cart-btn" onclick="event.preventDefault(); event.stopPropagation(); window.location.href='vendor/vendor.php?id=${restaurant.id}'">
          <i class="fas fa-store"></i>
          View Restaurant
        </button>
      </div>
    </div>
  `;

  container.appendChild(card);
}

// ================= SAVE TO RECENT SEARCHES =================
function saveToRecentSearches(query) {
  if (!query || query.trim().length < 2) return;
  
  const trimmedQuery = query.trim().toLowerCase();
  
  // Remove if already exists
  recentSearches = recentSearches.filter(item => item.query !== trimmedQuery);
  
  // Add to beginning
  recentSearches.unshift({
    query: trimmedQuery,
    timestamp: new Date().toISOString()
  });
  
  // Keep only last 10 searches
  recentSearches = recentSearches.slice(0, 10);
  
  // Save to localStorage
  localStorage.setItem('recentSearches', JSON.stringify(recentSearches));
  
  // Update display
  loadRecentSearches();
}

// ================= LOAD RECENT SEARCHES =================
function loadRecentSearches() {
  const recentSearchesContainer = document.getElementById('recentSearches');
  if (!recentSearchesContainer) return;
  
  if (recentSearches.length === 0) {
    recentSearchesContainer.innerHTML = '<p style="color: var(--gray);">No recent searches</p>';
    return;
  }
  
  const recentSearchesHTML = recentSearches.map(item => `
    <button class="recent-search-tag" onclick="performRecentSearch('${item.query}')">
      <i class="fas fa-search"></i>
      ${item.query}
    </button>
  `).join('');
  
  recentSearchesContainer.innerHTML = recentSearchesHTML;
}

// ================= PERFORM RECENT SEARCH =================
function performRecentSearch(query) {
  document.getElementById('productSearch').value = query;
  
  // Update URL
  const url = new URL(window.location);
  url.searchParams.set('q', query);
  window.history.pushState({}, '', url);
  
  // Perform search
  performSearch(query, true);
}

// ================= LOG SEARCH TO ANALYTICS =================
async function logSearchToAnalytics(query, resultCount) {
  try {
    const userId = '<?php echo $isLoggedIn ? $_SESSION["uid"] : "guest"; ?>';
    const userEmail = '<?php echo $userEmail; ?>';
    
    // Get device info
    const deviceInfo = navigator.userAgent;
    const isMobile = /Mobile|Android|iPhone|iPad|iPod/i.test(deviceInfo);
    
    // Try to get location (requires user permission)
    let location = null;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          location = `${position.coords.latitude},${position.coords.longitude}`;
          saveSearchLog(query, resultCount, userId, userEmail, deviceInfo, location);
        },
        () => {
          saveSearchLog(query, resultCount, userId, userEmail, deviceInfo, null);
        }
      );
    } else {
      saveSearchLog(query, resultCount, userId, userEmail, deviceInfo, null);
    }
  } catch (error) {
    console.error("Error logging search:", error);
  }
}

// ================= SAVE SEARCH LOG =================
async function saveSearchLog(query, resultCount, userId, userEmail, deviceInfo, location) {
  try {
    const searchLog = {
      searchQuery: query,
      resultCount: resultCount,
      searchType: 'mixed',
      userId: userId,
      userEmail: userEmail,
      deviceInfo: deviceInfo,
      location: location,
      timestamp: new Date().toISOString()
    };
    
    // Save to Firebase
    await db.collection("search_analytics").add(searchLog);
    console.log("Search logged to analytics");
  } catch (error) {
    console.error("Error saving search log:", error);
  }
}

// ================= LOAD SEARCH ANALYTICS =================
async function loadSearchAnalytics() {
  try {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const analyticsStats = document.getElementById('analyticsStats');
    
    <?php if (!$isLoggedIn): ?>
    // For guests: Show total searches and today's searches
    const totalSnapshot = await db.collection("search_analytics").get();
    const totalSearches = totalSnapshot.size;
    
    const todaySnapshot = await db.collection("search_analytics")
      .where("timestamp", ">=", today.toISOString())
      .get();
    const todaySearches = todaySnapshot.size;
    
    // Get popular searches
    const searchCounts = {};
    totalSnapshot.forEach(doc => {
      const data = doc.data();
      const query = data.searchQuery?.toLowerCase().trim();
      if (query && query.length >= 2) {
        searchCounts[query] = (searchCounts[query] || 0) + 1;
      }
    });
    
    // Find most popular search
    let popularSearch = '-';
    let maxCount = 0;
    Object.entries(searchCounts).forEach(([query, count]) => {
      if (count > maxCount) {
        maxCount = count;
        popularSearch = query;
      }
    });
    
    analyticsStats.innerHTML = `
      <div class="stat-item">
        <div class="stat-value" id="totalSearches">${totalSearches}</div>
        <div class="stat-label">Total Searches</div>
      </div>
      <div class="stat-item">
        <div class="stat-value" id="todaySearches">${todaySearches}</div>
        <div class="stat-label">Today's Searches</div>
      </div>
      <div class="stat-item">
        <div class="stat-value" id="popularSearch">${popularSearch}</div>
        <div class="stat-label">Most Popular Search</div>
      </div>
    `;
    
    <?php else: ?>
    // For logged-in users: Show user-specific data
    const userEmail = '<?php echo $userEmail; ?>';
    
    const userSearchesSnapshot = await db.collection("search_analytics")
      .where("userEmail", "==", userEmail)
      .get();
    const userSearches = userSearchesSnapshot.size;
    
    const todayUserSearchesSnapshot = await db.collection("search_analytics")
      .where("userEmail", "==", userEmail)
      .where("timestamp", ">=", today.toISOString())
      .get();
    const todayUserSearches = todayUserSearchesSnapshot.size;
    
    // Get user's popular searches
    const userSearchCounts = {};
    userSearchesSnapshot.forEach(doc => {
      const data = doc.data();
      const query = data.searchQuery?.toLowerCase().trim();
      if (query && query.length >= 2) {
        userSearchCounts[query] = (userSearchCounts[query] || 0) + 1;
      }
    });
    
    // Find user's most popular search
    let userPopularSearch = '-';
    let userMaxCount = 0;
    Object.entries(userSearchCounts).forEach(([query, count]) => {
      if (count > userMaxCount) {
        userMaxCount = count;
        userPopularSearch = query;
      }
    });
    
    analyticsStats.innerHTML = `
      <div class="stat-item">
        <div class="stat-value" id="userSearches">${userSearches}</div>
        <div class="stat-label">Your Searches</div>
      </div>
      <div class="stat-item">
        <div class="stat-value" id="todaySearches">${todayUserSearches}</div>
        <div class="stat-label">Today's Searches</div>
      </div>
      <div class="stat-item">
        <div class="stat-value" id="popularSearch">${userPopularSearch}</div>
        <div class="stat-label">Your Top Search</div>
      </div>
    `;
    <?php endif; ?>
    
  } catch (error) {
    console.error("Error loading search analytics:", error);
    showNotification("Error loading analytics", "warning");
  }
}

// ================= CART FUNCTIONS =================
function updateQuantity(productId, change, button) {
  const card = button.closest('.search-result-card');
  const quantityInput = card.querySelector(`.quantity-input[data-product="${productId}"]`);
  if (!quantityInput) return;
  
  let currentQuantity = parseInt(quantityInput.value) || 1;
  currentQuantity += change;
  
  if (currentQuantity < 1) currentQuantity = 1;
  if (currentQuantity > 99) currentQuantity = 99;
  
  quantityInput.value = currentQuantity;
  
  const addToCartBtn = card.querySelector('.add-to-cart-btn');
  if (addToCartBtn) {
    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Update';
    addToCartBtn.classList.add('added');
  }
}

function addToCartWithQuantity(productId, productName, price, button) {
  const card = button.closest('.search-result-card');
  const quantityInput = card.querySelector(`.quantity-input[data-product="${productId}"]`);
  const quantity = parseInt(quantityInput?.value) || 1;
  
  const existingItemIndex = cart.findIndex(item => item.id === productId);
  
  if (existingItemIndex > -1) {
    cart[existingItemIndex].qty = quantity;
  } else {
    cart.push({ 
      id: productId, 
      name: productName, 
      price: price, 
      qty: quantity,
      image: card.querySelector('img')?.src || ''
    });
  }
  
  localStorage.setItem('cart', JSON.stringify(cart));
  
  const originalHTML = button.innerHTML;
  button.innerHTML = '<i class="fas fa-check"></i> Added';
  button.classList.add('added');
  
  setTimeout(() => {
    button.innerHTML = '<i class="fas fa-shopping-cart"></i> Update';
  }, 2000);

  <?php if ($isLoggedIn): ?>
  fetch('add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      user: '<?= $userEmail ?>', 
      product_id: productId, 
      qty: quantity 
    })
  }).then(response => response.json())
    .then(data => {
      if (data.success) {
        console.log('Cart updated on server');
      }
    })
    .catch(error => {
      console.error('Error updating cart on server:', error);
    });
  <?php else: ?>
  // For guests, show a reminder to sign in
  showNotification(`${productName} added to cart! Sign in to save cart across devices.`, 'success');
  <?php endif; ?>

  updateCartCount();
}

// ================= UPDATE CART COUNT =================
function updateCartCount() {
  const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
  const cartCountElement = document.querySelector('.cart-count');
  if (cartCountElement) {
    cartCountElement.textContent = totalItems;
    cartCountElement.style.display = totalItems > 0 ? 'flex' : 'none';
  }
}

// ================= NOTIFICATION SYSTEM =================
function showNotification(message, type = 'info') {
  const existingNotification = document.querySelector('.custom-notification');
  if (existingNotification) {
    existingNotification.remove();
  }
  
  const notification = document.createElement('div');
  notification.className = `custom-notification ${type}`;
  notification.innerHTML = `
    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
    <span>${message}</span>
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    if (notification.parentNode) {
      notification.style.animation = 'fadeOut 0.3s ease forwards';
      setTimeout(() => notification.remove(), 300);
    }
  }, 3000);
}
</script>

</body>
</html>
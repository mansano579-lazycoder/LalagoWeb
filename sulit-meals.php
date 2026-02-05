<?php
session_start();

$isLoggedIn = isset($_SESSION['uid']);
$userEmail  = $_SESSION['email'] ?? '';

// Check if user has saved location
$hasSavedLocation = isset($_SESSION['user_location']);
$userLocationData = $_SESSION['user_location'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LalaGO - Sulit Meals (₱150 & below)</title>

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
  --warning: #FFD700; /* Yellow for stars */
  --danger: #E74C3C;
  --info: #3498DB;
  --white: #FFFFFF;
  --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  --shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.12);
  --radius: 12px; /* Reduced from 16px */
  --radius-sm: 6px; /* Reduced from 8px */
  --transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* ================= RESPONSIVE BREAKPOINTS ================= */
@media (max-width: 1400px) {
  .container {
    max-width: 1200px;
  }
}

@media (max-width: 1200px) {
  .container {
    max-width: 100%;
    padding: 15px;
  }
}

@media (max-width: 768px) {
  .container {
    padding: 12px;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 10px;
  }
}

/* ================= MAIN CONTAINER ================= */
.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* ================= HEADER SECTION ================= */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 15px 0 20px; /* Reduced margin */
  padding-bottom: 15px; /* Reduced padding */
  border-bottom: 2px solid var(--gray-light);
  flex-wrap: wrap;
  gap: 15px;
}

.page-title {
  font-size: 1.8rem; /* Reduced from 2rem */
  font-weight: 700;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 12px; /* Reduced gap */
}

.page-title i {
  color: var(--success);
  font-size: 1.5rem; /* Slightly smaller */
}

.back-button {
  background: var(--white);
  color: var(--primary);
  border: 2px solid var(--primary);
  padding: 8px 16px; /* Reduced padding */
  border-radius: 50px;
  font-size: 0.9rem; /* Reduced from 0.95rem */
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 6px; /* Reduced gap */
  text-decoration: none;
  white-space: nowrap;
}

.back-button:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}

/* Responsive header */
@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px; /* Reduced gap */
  }
  
  .page-title {
    font-size: 1.4rem; /* Reduced from 1.5rem */
  }
  
  .back-button {
    align-self: flex-start;
    padding: 6px 12px; /* Reduced padding */
    font-size: 0.85rem; /* Reduced from 0.9rem */
  }
}

@media (max-width: 480px) {
  .page-title {
    font-size: 1.2rem; /* Reduced from 1.3rem */
    gap: 8px; /* Reduced gap */
  }
  
  .back-button {
    width: 100%;
    justify-content: center;
  }
}

/* ================= FILTERS SECTION ================= */
.filters-section {
  background: var(--white);
  border-radius: var(--radius);
  padding: 15px; /* Reduced from 20px */
  margin-bottom: 20px; /* Reduced from 30px */
  box-shadow: var(--shadow);
}

.filters-title {
  font-size: 1rem; /* Reduced from 1.1rem */
  font-weight: 600;
  color: var(--secondary);
  margin-bottom: 12px; /* Reduced from 15px */
  display: flex;
  align-items: center;
  gap: 8px; /* Reduced from 10px */
}

.filter-buttons {
  display: flex;
  gap: 8px; /* Reduced from 10px */
  flex-wrap: wrap;
}

.filter-btn {
  background: var(--light);
  border: 2px solid var(--gray-light);
  padding: 6px 12px; /* Reduced from 8px 16px */
  border-radius: 50px;
  font-size: 0.85rem; /* Reduced from 0.9rem */
  color: var(--gray-dark);
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 5px; /* Reduced from 6px */
  white-space: nowrap;
}

.filter-btn:hover {
  background: var(--gray-light);
  border-color: var(--gray);
}

.filter-btn.active {
  background: var(--primary);
  color: var(--white);
  border-color: var(--primary);
}

/* Responsive filters */
@media (max-width: 768px) {
  .filter-buttons {
    gap: 6px; /* Reduced from 8px */
  }
  
  .filter-btn {
    padding: 5px 10px; /* Reduced from 6px 12px */
    font-size: 0.8rem; /* Reduced from 0.85rem */
  }
}

@media (max-width: 480px) {
  .filters-section {
    padding: 12px; /* Reduced from 15px */
  }
  
  .filter-buttons {
    gap: 5px; /* Reduced from 6px */
  }
  
  .filter-btn {
    padding: 4px 8px; /* Reduced from 5px 10px */
    font-size: 0.75rem; /* Reduced from 0.8rem */
  }
  
  .filter-btn i {
    font-size: 0.7rem; /* Reduced from 0.75rem */
  }
}

/* ================= GRID LAYOUT ================= */
.foods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); /* Reduced from 280px */
  gap: 20px; /* Reduced from 25px */
  margin-bottom: 30px; /* Reduced from 40px */
}

/* Responsive grid */
@media (max-width: 1200px) {
  .foods-grid {
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); /* Reduced from 250px */
    gap: 16px; /* Reduced from 20px */
  }
}

@media (max-width: 768px) {
  .foods-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px; /* Reduced from 15px */
  }
}

@media (max-width: 480px) {
  .foods-grid {
    grid-template-columns: 1fr;
    gap: 12px; /* Reduced from 15px */
  }
}

/* ================= FOOD CARD - UPDATED FIXED VERSION ================= */
.food-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  position: relative;
  animation: fadeIn 0.5s ease forwards;
  display: flex;
  flex-direction: column;
  height: 100%;
  opacity: 0;
  transform: translateY(20px);
}

.food-card.visible {
  opacity: 1;
  transform: translateY(0);
}

.food-card:hover {
  transform: translateY(-5px); /* Reduced from -8px */
  box-shadow: var(--shadow-hover);
}

/* FOOD IMAGE - COMPACT FIXED */
.food-image-container {
  position: relative;
  width: 100%;
  height: 160px; /* Reduced from 200px */
  overflow: hidden;
  background: linear-gradient(45deg, #f5f5f5, #e0e0e0);
  flex-shrink: 0; /* Prevent image from shrinking */
}

.food-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}

.food-card:hover img {
  transform: scale(1.05);
}

/* RESTAURANT AVATAR - BIGGER SIZE */
.restaurant-avatar {
  position: absolute;
  bottom: 15px;
  left: 15px;
  width: 48px; /* Increased from 36px */
  height: 48px; /* Increased from 36px */
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--white);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3); /* Enhanced shadow for better visibility */
  z-index: 10;
  background: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
}

.restaurant-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Responsive avatar - adjust for different screen sizes */
@media (max-width: 1200px) {
  .restaurant-avatar {
    width: 44px; /* Slightly smaller on medium screens */
    height: 44px;
  }
}

@media (max-width: 768px) {
  .food-image-container {
    height: 140px;
  }
  
  .restaurant-avatar {
    width: 40px; /* Keep good size on tablets */
    height: 40px;
    bottom: 12px;
    left: 12px;
    border-width: 3px; /* Keep thick border */
  }
}

@media (max-width: 480px) {
  .food-image-container {
    height: 160px;
  }
  
  .restaurant-avatar {
    width: 44px; /* Bigger on mobile for better visibility */
    height: 44px;
    bottom: 15px;
    left: 15px;
    border-width: 3px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3); /* Slightly smaller shadow on mobile */
  }
}

@media (max-width: 480px) {
  .food-image-container {
    height: 160px; /* Adjusted for mobile */
  }
  
  .restaurant-avatar {
    width: 36px; /* Keep visible on mobile */
    height: 36px;
    bottom: 15px; /* Keep at bottom */
    left: 15px; /* Keep at left */
    border-width: 3px; /* Keep border thickness */
  }
}

/* FOOD INFO - MORE COMPACT */
.food-info {
  padding: 15px 12px 12px; /* Reduced padding */
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.food-header {
  margin-bottom: 8px; /* Reduced from 10px */
}

.food-name {
  font-size: 1rem; /* Reduced from 1.1rem */
  font-weight: 700;
  margin-bottom: 6px; /* Reduced from 8px */
  color: var(--secondary);
  line-height: 1.3; /* Reduced from 1.4 */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.4em; /* Reduced from 2.8em */
}

/* Responsive food name */
@media (max-width: 768px) {
  .food-name {
    font-size: 0.95rem;
    min-height: 2.2em;
  }
}

@media (max-width: 480px) {
  .food-name {
    font-size: 1rem; /* Slightly larger on mobile for better readability */
    min-height: 2.4em;
  }
}

.restaurant-name {
  font-size: 0.8rem; /* Reduced from 0.85rem */
  color: var(--gray);
  display: flex;
  align-items: center;
  gap: 4px; /* Reduced from 5px */
  margin-bottom: 10px;
}

.restaurant-name i {
  color: var(--primary);
  font-size: 0.75rem; /* Reduced from 0.8rem */
}

/* Responsive restaurant name */
@media (max-width: 768px) {
  .restaurant-name {
    font-size: 0.75rem;
  }
}

/* FOOD RATING - COMPACT */
.food-rating {
  display: flex;
  align-items: center;
  gap: 5px; /* Reduced from 6px */
  margin-bottom: 12px; /* Reduced from 15px */
  padding: 4px 8px; /* Adjusted padding */
  background: rgba(255, 215, 0, 0.1);
  border-radius: var(--radius-sm);
  width: fit-content;
  flex-wrap: wrap;
}

.rating-stars {
  color: var(--warning);
  font-size: 0.8rem; /* Reduced from 0.9rem */
  display: flex;
  align-items: center;
  gap: 1px; /* Small gap between stars */
}

.rating-value {
  font-weight: 600;
  color: var(--secondary);
  font-size: 0.8rem; /* Reduced from 0.85rem */
  margin-left: 2px; /* Small margin from stars */
}

.rating-count {
  color: var(--gray);
  font-size: 0.7rem; /* Reduced from 0.75rem */
  margin-left: 3px; /* Small margin from rating value */
}

/* Responsive rating */
@media (max-width: 768px) {
  .food-rating {
    margin-bottom: 10px;
    padding: 3px 6px;
  }
  
  .rating-stars {
    font-size: 0.75rem;
  }
  
  .rating-value {
    font-size: 0.75rem;
  }
  
  .rating-count {
    font-size: 0.65rem;
  }
}

@media (max-width: 480px) {
  .food-rating {
    margin-bottom: 15px; /* More space on mobile */
    width: 100%;
    justify-content: flex-start;
  }
}

/* SINGLE PRICE SECTION - MORE COMPACT FIXED */
.food-price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 10px; /* Reduced from 12px */
  border-top: 1px solid var(--gray-light);
  margin-top: auto; /* Push to bottom */
  min-height: 45px; /* Ensure consistent height */
}

.food-price {
  font-size: 1.2rem; /* Reduced from 1.4rem */
  font-weight: 800;
  color: var(--primary);
  display: flex;
  align-items: baseline;
  gap: 2px; /* Reduced from 4px */
}

.food-price .currency {
  font-size: 0.9rem; /* Adjusted for better visibility */
  font-weight: 600;
  color: var(--primary);
}

.food-price .amount {
  font-size: 1.3rem; /* Slightly larger for emphasis */
  font-weight: 800;
}

/* Responsive price */
@media (max-width: 768px) {
  .food-price {
    font-size: 1.1rem;
  }
  
  .food-price .currency {
    font-size: 0.85rem;
  }
  
  .food-price .amount {
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .food-price {
    font-size: 1.2rem; /* Keep visible on mobile */
  }
  
  .food-price .currency {
    font-size: 0.9rem;
  }
  
  .food-price .amount {
    font-size: 1.3rem; /* Keep emphasis on mobile */
  }
}

/* ================= LOADING & NO RESULTS ================= */
.loading-container,
.no-results {
  grid-column: 1 / -1;
  text-align: center;
  padding: 40px 15px; /* Reduced from 60px 20px */
  color: var(--gray);
  width: 100%;
}

.loading-container i,
.no-results i {
  font-size: 2.5rem; /* Reduced from 3rem */
  color: var(--gray-light);
  margin-bottom: 15px; /* Reduced from 20px */
}

.loading-container h3,
.no-results h3 {
  font-size: 1.3rem; /* Reduced from 1.5rem */
  margin-bottom: 8px; /* Reduced from 10px */
  color: var(--secondary);
}

.loading-container p,
.no-results p {
  font-size: 0.9rem; /* Reduced from 1rem */
  color: var(--gray);
  margin-bottom: 15px; /* Reduced from 20px */
}

/* ================= LOADING SPINNER ================= */
.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 30px;
  grid-column: 1 / -1;
}

.loading-spinner i {
  font-size: 2rem;
  color: var(--primary);
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* ================= ANIMATIONS ================= */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(15px); /* Reduced from 20px */
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ================= FIX FOR FLEX LAYOUT ISSUES ================= */
.food-info > * {
  min-width: 0; /* Allow text truncation to work properly */
}
</style>

</head>
<body>

<?php include 'assets/header.php'; ?>

<main class="container">
  
  <!-- ================= PAGE HEADER ================= -->
  <div class="page-header">
    <h1 class="page-title">
      <i class="fas fa-tag"></i>
      Sulit Meals (₱150 & below)
    </h1>
    <a href="index.php" class="back-button">
      <i class="fas fa-arrow-left"></i>
      Back to Home
    </a>
  </div>
  
  <!-- ================= FILTERS ================= -->
  <div class="filters-section">
    <h3 class="filters-title">
      <i class="fas fa-filter"></i>
      Sort by:
    </h3>
    <div class="filter-buttons">
      <button class="filter-btn active" onclick="sortProducts('random')">
        <i class="fas fa-random"></i>
        Random
      </button>
      <button class="filter-btn" onclick="sortProducts('price_low')">
        <i class="fas fa-sort-amount-down"></i>
        Price: Low to High
      </button>
      <button class="filter-btn" onclick="sortProducts('price_high')">
        <i class="fas fa-sort-amount-up"></i>
        Price: High to Low
      </button>
      <button class="filter-btn" onclick="sortProducts('rating')">
        <i class="fas fa-star"></i>
        Highest Rating
      </button>
    </div>
  </div>
  
  <!-- ================= FOODS GRID ================= -->
  <div class="foods-grid" id="foodsGrid"></div>
  
  <!-- ================= LOADING SPINNER ================= -->
  <div class="loading-spinner" id="loadingSpinner" style="display: none;">
    <i class="fas fa-spinner fa-spin"></i>
  </div>
  
</main>

<!-- ================= FIREBASE ================= -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="js/firebase.js"></script>

<script>
// ================= GLOBAL VARIABLES =================
let allSulitProducts = [];
let displayedProducts = [];
let currentSort = 'random';
let isLoading = false;
let hasMoreProducts = true;
const ITEMS_PER_LOAD = 12; // Reduced for smoother infinite scroll
let currentOffset = 0;
const MAX_PRICE = 150;
let scrollTimeout = null;

// Cache for restaurant data
let restaurantCache = {};

// Intersection Observer for lazy loading
let intersectionObserver;

// ================= INITIALIZE =================
document.addEventListener('DOMContentLoaded', async function() {
  console.log('Sulit Meals Page Initializing...');
  
  // Load initial products
  await loadSulitProducts();
  
  // Setup infinite scroll
  setupInfiniteScroll();
  
  console.log('Sulit Meals Page initialized');
});

// ================= SETUP INFINITE SCROLL =================
function setupInfiniteScroll() {
  // Create Intersection Observer for lazy loading
  intersectionObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !isLoading && hasMoreProducts) {
        loadMoreProducts();
      }
    });
  }, {
    rootMargin: '100px', // Load 100px before reaching the bottom
    threshold: 0.1
  });

  // Start observing the loading spinner
  const loadingSpinner = document.getElementById('loadingSpinner');
  if (loadingSpinner) {
    intersectionObserver.observe(loadingSpinner);
  }

  // Also setup traditional scroll event as fallback
  window.addEventListener('scroll', handleScroll);
}

// ================= HANDLE SCROLL =================
function handleScroll() {
  // Use debouncing to prevent too many scroll events
  if (scrollTimeout) {
    clearTimeout(scrollTimeout);
  }
  
  scrollTimeout = setTimeout(() => {
    if (isLoading || !hasMoreProducts) return;
    
    // Calculate scroll position
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight;
    const clientHeight = document.documentElement.clientHeight;
    
    // Load more when user is 300px from bottom
    if (scrollTop + clientHeight >= scrollHeight - 300) {
      loadMoreProducts();
    }
  }, 200);
}

// ================= LOAD SULIT PRODUCTS =================
async function loadSulitProducts() {
  const foodsGrid = document.getElementById('foodsGrid');
  if (!foodsGrid) return;
  
  // Show loading state
  foodsGrid.innerHTML = `
    <div class="loading-container">
      <i class="fas fa-spinner fa-spin fa-2x"></i>
      <h3>Loading affordable meals...</h3>
      <p>Finding meals ₱${MAX_PRICE} and below</p>
    </div>
  `;
  
  try {
    // Get all published products
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(200) // Increased limit for more products
      .get();
    
    console.log(`Found ${productsSnapshot.size} total products`);
    
    if (productsSnapshot.empty) {
      showNoResults();
      return;
    }
    
    // Convert to array and filter by price
    allSulitProducts = [];
    
    productsSnapshot.forEach(doc => {
      const product = {
        id: doc.id,
        ...doc.data()
      };
      
      // Calculate actual price
      let actualPrice = 0;
      try {
        const disPrice = parseFloat(product.disPrice);
        const regPrice = parseFloat(product.price);
        
        // Use discounted price if available and valid, otherwise use regular price
        if (!isNaN(disPrice) && disPrice > 0 && (!isNaN(regPrice) ? disPrice <= regPrice : true)) {
          actualPrice = disPrice;
        } else if (!isNaN(regPrice)) {
          actualPrice = regPrice;
        }
      } catch (e) {
        actualPrice = 0;
      }
      
      // Filter by price (150 pesos or less)
      if (actualPrice > 0 && actualPrice <= MAX_PRICE) {
        allSulitProducts.push(product);
      }
    });
    
    console.log(`Found ${allSulitProducts.length} products within ₱${MAX_PRICE} budget`);
    
    if (allSulitProducts.length === 0) {
      showNoResults();
      return;
    }
    
    // Sort randomly by default
    shuffleArray(allSulitProducts);
    
    // Reset pagination
    currentOffset = 0;
    displayedProducts = [];
    hasMoreProducts = true;
    
    // Clear grid
    foodsGrid.innerHTML = '';
    
    // Show loading spinner
    const loadingSpinner = document.getElementById('loadingSpinner');
    if (loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }
    
    // Load initial batch
    await loadMoreProducts(true);
    
  } catch (error) {
    console.error("Error loading sulit meals:", error);
    showNoResults("Error loading affordable meals");
  }
}

// ================= LOAD MORE PRODUCTS =================
async function loadMoreProducts(isInitialLoad = false) {
  if (isLoading || !hasMoreProducts) return;
  
  isLoading = true;
  
  // Show loading spinner if not initial load
  if (!isInitialLoad) {
    const loadingSpinner = document.getElementById('loadingSpinner');
    if (loadingSpinner) {
      loadingSpinner.style.display = 'flex';
    }
  }
  
  try {
    // Calculate next batch
    const nextProducts = allSulitProducts.slice(currentOffset, currentOffset + ITEMS_PER_LOAD);
    
    if (nextProducts.length === 0) {
      hasMoreProducts = false;
      // Hide loading spinner
      const loadingSpinner = document.getElementById('loadingSpinner');
      if (loadingSpinner) {
        loadingSpinner.style.display = 'none';
      }
      return;
    }
    
    // Add to displayed products
    displayedProducts = [...displayedProducts, ...nextProducts];
    
    // Display new products
    await displayProductsBatch(nextProducts);
    
    // Update offset
    currentOffset += ITEMS_PER_LOAD;
    
    // Check if we have more products
    hasMoreProducts = currentOffset < allSulitProducts.length;
    
  } catch (error) {
    console.error("Error loading more products:", error);
  } finally {
    isLoading = false;
    
    // Hide loading spinner
    const loadingSpinner = document.getElementById('loadingSpinner');
    if (loadingSpinner) {
      loadingSpinner.style.display = 'none';
    }
  }
}

// ================= DISPLAY PRODUCTS BATCH =================
async function displayProductsBatch(products) {
  const foodsGrid = document.getElementById('foodsGrid');
  if (!foodsGrid) return;
  
  // Display products
  for (const product of products) {
    try {
      const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
      await createFoodCard(product, restaurantDetails, foodsGrid);
    } catch (error) {
      console.error("Error creating food card:", error);
    }
  }
  
  // Animate cards with staggered delay
  animateCards();
}

// ================= ANIMATE CARDS =================
function animateCards() {
  const cards = document.querySelectorAll('.food-card:not(.visible)');
  cards.forEach((card, index) => {
    setTimeout(() => {
      card.classList.add('visible');
    }, index * 50); // Staggered animation
  });
}

// ================= CREATE FOOD CARD - UPDATED =================
async function createFoodCard(food, restaurantDetails, container) {
  // Get product rating using the utility function
  const ratingData = getProductRating(food);
  const rating = ratingData.average;
  const reviewsCount = ratingData.count;
  
  // Handle price - ALWAYS show the lowest price available
  let price = 0;
  try {
    const disPrice = parseFloat(food.disPrice);
    const regPrice = parseFloat(food.price);
    
    // Use discounted price if available and valid
    if (!isNaN(disPrice) && disPrice > 0 && (!isNaN(regPrice) ? disPrice <= regPrice : true)) {
      price = disPrice;
    } else if (!isNaN(regPrice)) {
      price = regPrice;
    }
  } catch (e) {
    console.warn(`Error parsing price for ${food.id}`);
    price = 0;
  }
  
  // Generate star rating HTML based on actual rating
  const productStarsHTML = generateStarRatingHTML(rating);
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${food.id}`;
  card.className = "food-card";
  card.setAttribute('data-product', food.id);

  card.innerHTML = `
    <div class="food-image-container">
      <img src="${food.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           loading="lazy" alt="${food.name || 'Food item'}"
           onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      <div class="restaurant-avatar">
        <img src="${restaurantDetails.logo}" 
             alt="${restaurantDetails.name}"
             onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'">
      </div>
    </div>
    
    <div class="food-info">
      <div class="food-header">
        <div class="food-name">${food.name || 'Unnamed Item'}</div>
        <div class="restaurant-name">
          <i class="fas fa-store"></i>
          ${restaurantDetails.name}
        </div>
      </div>
      
      <div class="food-rating">
        <div class="rating-stars">${productStarsHTML}</div>
        <span class="rating-value">${rating}</span>
        <span class="rating-count">(${reviewsCount})</span>
      </div>
      
      <div class="food-price-section">
        <div class="food-price">
          <span class="currency">₱</span>
          <span class="amount">${price.toFixed(2)}</span>
        </div>
      </div>
    </div>
  `;

  container.appendChild(card);
}

// ================= GET PRODUCT RATING =================
function getProductRating(food) {
  // Try different possible locations for rating data
  const reviewsCount = 
    food.reviewAttributes?.reviewsCount || 
    food.reviewsCount || 
    food.reviewCount || 
    0;
  
  const reviewsSum = 
    food.reviewAttributes?.reviewsSum || 
    food.reviewsSum || 
    food.reviewSum || 
    0;
  
  // If no reviews, return default rating
  if (reviewsCount === 0 || reviewsSum === 0) {
    return {
      rating: 0,
      count: 0,
      average: "0.0"
    };
  }
  
  const average = (reviewsSum / reviewsCount).toFixed(1);
  const rating = parseFloat(average);
  
  return {
    rating: rating,
    count: reviewsCount,
    average: average
  };
}

// ================= GENERATE STAR RATING HTML =================
function generateStarRatingHTML(rating) {
  const numericRating = parseFloat(rating);
  if (isNaN(numericRating) || numericRating === 0) {
    // Show empty stars for 0 rating
    return `
      <i class="far fa-star"></i>
      <i class="far fa-star"></i>
      <i class="far fa-star"></i>
      <i class="far fa-star"></i>
      <i class="far fa-star"></i>
    `;
  }
  
  let starsHTML = '';
  const fullStars = Math.floor(numericRating);
  const hasHalfStar = (numericRating - fullStars) >= 0.5;
  
  // Full stars
  for (let i = 0; i < fullStars; i++) {
    starsHTML += '<i class="fas fa-star"></i>';
  }
  
  // Half star
  if (hasHalfStar) {
    starsHTML += '<i class="fas fa-star-half-alt"></i>';
  }
  
  // Empty stars
  const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
  for (let i = 0; i < emptyStars; i++) {
    starsHTML += '<i class="far fa-star"></i>';
  }
  
  return starsHTML;
}

// ================= SORT PRODUCTS =================
function sortProducts(sortType) {
  if (isLoading) return;
  
  // Update active filter button
  document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  
  currentSort = sortType;
  currentOffset = 0;
  displayedProducts = [];
  
  // Sort array based on type
  switch(sortType) {
    case 'price_low':
      allSulitProducts.sort((a, b) => {
        const priceA = getLowestPrice(a);
        const priceB = getLowestPrice(b);
        return priceA - priceB;
      });
      break;
      
    case 'price_high':
      allSulitProducts.sort((a, b) => {
        const priceA = getLowestPrice(a);
        const priceB = getLowestPrice(b);
        return priceB - priceA;
      });
      break;
      
    case 'rating':
      allSulitProducts.sort((a, b) => {
        const ratingA = getProductRating(a).rating;
        const ratingB = getProductRating(b).rating;
        return ratingB - ratingA;
      });
      break;
      
    case 'random':
    default:
      shuffleArray(allSulitProducts);
      break;
  }
  
  // Clear grid
  const foodsGrid = document.getElementById('foodsGrid');
  if (foodsGrid) {
    foodsGrid.innerHTML = '';
  }
  
  // Reset pagination
  hasMoreProducts = true;
  
  // Show loading spinner
  const loadingSpinner = document.getElementById('loadingSpinner');
  if (loadingSpinner) {
    loadingSpinner.style.display = 'flex';
  }
  
  // Load initial batch
  loadMoreProducts(true);
}

// Helper function to get lowest price
function getLowestPrice(product) {
  try {
    const disPrice = parseFloat(product.disPrice);
    const regPrice = parseFloat(product.price);
    
    if (!isNaN(disPrice) && disPrice > 0 && (!isNaN(regPrice) ? disPrice <= regPrice : true)) {
      return disPrice;
    } else if (!isNaN(regPrice)) {
      return regPrice;
    }
    return 0;
  } catch (e) {
    return 0;
  }
}

// ================= HELPER FUNCTIONS =================
function shuffleArray(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

async function fetchRestaurantDetails(vendorId) {
  // Check cache first
  if (restaurantCache[vendorId]) {
    return restaurantCache[vendorId];
  }
  
  try {
    const vendorDoc = await db.collection("vendors").doc(vendorId).get();
    if (vendorDoc.exists) {
      const vendorData = vendorDoc.data();
      const restaurantData = {
        name: vendorData.title || "Unknown Restaurant",
        logo: vendorData.photo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
        category: vendorData.categoryTitle || 'Restaurant',
        deliveryCharge: vendorData.minimum_delivery_charges ? `₱${vendorData.minimum_delivery_charges}` : 'Free',
        rating: vendorData.reviewsCount > 0 ? (vendorData.reviewsSum / vendorData.reviewsCount).toFixed(1) : "0.0"
      };
      
      // Cache the result
      restaurantCache[vendorId] = restaurantData;
      return restaurantData;
    }
  } catch (error) {
    console.error("Error fetching restaurant details:", error);
  }
  
  // Return default if not found
  const defaultData = {
    name: "Unknown Restaurant",
    logo: 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
    category: 'Restaurant',
    deliveryCharge: 'Free',
    rating: "0.0"
  };
  
  restaurantCache[vendorId] = defaultData;
  return defaultData;
}

function showNoResults(message = "No affordable meals found") {
  const foodsGrid = document.getElementById('foodsGrid');
  if (!foodsGrid) return;
  
  foodsGrid.innerHTML = `
    <div class="no-results">
      <i class="fas fa-tag"></i>
      <h3>${message}</h3>
      <p>Check back later for more budget-friendly options</p>
      <a href="index.php" class="back-button" style="margin-top: 15px;">
        <i class="fas fa-arrow-left"></i>
        Back to Home
      </a>
    </div>
  `;
  
  // Hide loading spinner
  const loadingSpinner = document.getElementById('loadingSpinner');
  if (loadingSpinner) {
    loadingSpinner.style.display = 'none';
  }
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  if (intersectionObserver) {
    intersectionObserver.disconnect();
  }
  window.removeEventListener('scroll', handleScroll);
});
</script>

</body>
</html>
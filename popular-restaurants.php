<?php
// Start session
session_start();

// Set base URL
$base_url = dirname(dirname($_SERVER['PHP_SELF']));
if ($base_url == '/') {
    $base_url = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LalaGO - Popular Restaurants</title>

<!-- CSS -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/carousel.css">
<link rel="stylesheet" href="css/random.css">
<link rel="stylesheet" href="css/categories.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Variables */
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

    /* Popular Restaurants Page Styles */
    .page-content {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        padding-top: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-subtitle {
        color: var(--gray);
        font-size: 16px;
        margin-top: 5px;
    }

    .sort-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sort-btn {
        background: var(--white);
        color: var(--secondary);
        border: 2px solid var(--gray-light);
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .sort-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .sort-btn.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    /* Popularity Badge */
    .popularity-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--warning);
        color: var(--white);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1;
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Restaurant Card - Enhanced for Popular */
    .restaurant-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        position: relative;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .restaurant-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
    }

    .restaurant-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .restaurant-info {
        padding: 20px;
    }

    .restaurant-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }

    .restaurant-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--secondary);
        margin: 0;
        line-height: 1.3;
    }

    .popularity-score {
        font-size: 14px;
        color: var(--warning);
        font-weight: 600;
        white-space: nowrap;
        margin-left: 10px;
    }

    .restaurant-category {
        font-size: 14px;
        color: var(--gray);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .restaurant-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .rating-stars {
        color: var(--warning);
        font-size: 14px;
    }

    .rating-value {
        font-weight: 600;
        color: var(--secondary);
    }

    .rating-count {
        color: var(--gray);
        font-size: 13px;
    }

    .reviews-stats {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 13px;
        color: var(--gray);
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .stat-item i {
        color: var(--info);
    }

    .restaurant-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid var(--gray-light);
    }

    .delivery-info {
        font-size: 14px;
        color: var(--gray);
    }

    .delivery-charge {
        font-weight: 600;
        color: var(--primary);
    }

    .view-btn {
        background: var(--primary);
        color: var(--white);
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .view-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* No Results */
    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: var(--gray);
        width: 100%;
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
        margin-bottom: 20px;
    }

    /* Loading State */
    .loading {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .loading-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        height: 300px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Restaurants Grid */
    .restaurants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .restaurants-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .sort-options {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .page-content {
            padding: 15px;
        }

        .restaurants-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .restaurant-card {
            max-width: 400px;
            margin: 0 auto;
        }
    }
</style>
</head>
<body>

<?php 
// Include the header
$headerPath = __DIR__ . '/assets/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    // Simple fallback header
    echo '<header style="background: var(--primary); padding: 15px; color: white;">
            <div style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
                <h1 style="margin: 0;">LalaGO</h1>
                <nav>
                    <a href="index.php" style="color: white; text-decoration: none; margin-left: 15px;">Home</a>
                    <a href="search.php" style="color: white; text-decoration: none; margin-left: 15px;">Search</a>
                </nav>
            </div>
          </header>';
}
?>

<div class="page-content">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-fire"></i>
                Popular Restaurants
            </h1>
            <p class="page-subtitle">Most rated and reviewed restaurants</p>
        </div>
        <div class="sort-options">
            <button class="sort-btn active" onclick="sortBy('popularity')">
                <i class="fas fa-fire"></i> Popularity
            </button>
            <button class="sort-btn" onclick="sortBy('rating')">
                <i class="fas fa-star"></i> Highest Rating
            </button>
            <button class="sort-btn" onclick="sortBy('reviews')">
                <i class="fas fa-comment"></i> Most Reviews
            </button>
        </div>
    </div>

    <!-- Restaurants Container -->
    <div id="restaurantsContainer">
        <!-- Loading State -->
        <div class="loading">
            <div class="loading-card"></div>
            <div class="loading-card"></div>
            <div class="loading-card"></div>
            <div class="loading-card"></div>
        </div>
    </div>
</div>

<!-- Firebase -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="js/firebase.js"></script>

<script>
    let allRestaurants = [];
    let sortedRestaurants = [];
    let currentSort = 'popularity';

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        loadPopularRestaurants();
    });

    // Load popular restaurants
    async function loadPopularRestaurants() {
        const container = document.getElementById('restaurantsContainer');
        
        try {
            // Get all active vendors
            const vendorsSnapshot = await db.collection("vendors")
                .where("publish", "==", true)
                .where("reststatus", "==", true)
                .limit(100)
                .get();
            
            if (vendorsSnapshot.empty) {
                showNoRestaurants();
                return;
            }
            
            // Process vendors
            allRestaurants = [];
            vendorsSnapshot.forEach(doc => {
                const vendor = {
                    id: doc.id,
                    ...doc.data()
                };
                
                // Calculate popularity score
                const rating = vendor.reviewsCount > 0 ? 
                    (vendor.reviewsSum / vendor.reviewsCount) : 0;
                
                const popularityScore = calculatePopularityScore(
                    rating,
                    vendor.reviewsCount || 0
                );
                
                allRestaurants.push({
                    ...vendor,
                    rating: rating,
                    popularityScore: popularityScore,
                    reviewsCount: vendor.reviewsCount || 0
                });
            });
            
            // Sort by current sort method
            sortRestaurants(currentSort);
            
        } catch (error) {
            console.error("Error loading restaurants:", error);
            showError("Error loading restaurants. Please try again.");
        }
    }

    // Calculate popularity score (rating × review count)
    function calculatePopularityScore(rating, reviewCount) {
        return rating * reviewCount;
    }

    // Sort restaurants
    function sortRestaurants(sortBy) {
        currentSort = sortBy;
        
        // Update active sort button
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Sort based on selected method
        switch(sortBy) {
            case 'popularity':
                sortedRestaurants = [...allRestaurants].sort((a, b) => 
                    b.popularityScore - a.popularityScore
                );
                break;
                
            case 'rating':
                sortedRestaurants = [...allRestaurants].sort((a, b) => 
                    b.rating - a.rating
                );
                break;
                
            case 'reviews':
                sortedRestaurants = [...allRestaurants].sort((a, b) => 
                    b.reviewsCount - a.reviewsCount
                );
                break;
        }
        
        renderRestaurants();
    }

    // Sort by specific method
    function sortBy(method) {
        if (method !== currentSort) {
            currentSort = method;
            sortRestaurants(method);
        }
    }

    // Render restaurants
    function renderRestaurants() {
        const container = document.getElementById('restaurantsContainer');
        
        if (sortedRestaurants.length === 0) {
            showNoRestaurants();
            return;
        }
        
        container.innerHTML = '';
        
        // Create grid container
        const grid = document.createElement('div');
        grid.className = 'restaurants-grid';
        
        sortedRestaurants.forEach((restaurant, index) => {
            const rating = restaurant.rating.toFixed(1);
            const starsHTML = generateStarRating(restaurant.rating);
            
            // Get popularity rank
            const rank = index + 1;
            let rankBadge = '';
            if (rank <= 3) {
                const rankColors = {
                    1: 'var(--warning)',
                    2: 'var(--gray)',
                    3: 'var(--secondary)'
                };
                rankBadge = `
                    <div class="popularity-badge" style="background: ${rankColors[rank]}">
                        <i class="fas fa-crown"></i>
                        #${rank} Popular
                    </div>
                `;
            }
            
            const card = document.createElement('a');
            card.href = `restaurant.php?id=${restaurant.id}`;
            card.className = 'restaurant-card';
            card.innerHTML = `
                ${rankBadge}
                <img src="${restaurant.photo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'}" 
                     alt="${restaurant.title || 'Restaurant'}" 
                     class="restaurant-image"
                     onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'">
                <div class="restaurant-info">
                    <div class="restaurant-header">
                        <h3 class="restaurant-name">${restaurant.title || 'Restaurant Name'}</h3>
                        <div class="popularity-score">
                            Score: ${restaurant.popularityScore.toFixed(1)}
                        </div>
                    </div>
                    <p class="restaurant-category">
                        <i class="fas fa-tag"></i>
                        ${restaurant.categoryTitle || 'Restaurant'}
                    </p>
                    <div class="restaurant-rating">
                        <div class="rating-stars">${starsHTML}</div>
                        <span class="rating-value">${rating}</span>
                        <span class="rating-count">(${restaurant.reviewsCount})</span>
                    </div>
                    <div class="reviews-stats">
                        <div class="stat-item">
                            <i class="fas fa-thumbs-up"></i>
                            <span>${Math.round(restaurant.rating * 20)}% positive</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-comment-dots"></i>
                            <span>${restaurant.reviewsCount} reviews</span>
                        </div>
                    </div>
                    <div class="restaurant-footer">
                        <div class="delivery-info">
                            <i class="fas fa-motorcycle"></i>
                            Delivery: <span class="delivery-charge">
                                ${restaurant.minimum_delivery_charges ? `₱${restaurant.minimum_delivery_charges}` : 'Free'}
                            </span>
                        </div>
                        <button class="view-btn" onclick="event.stopPropagation(); window.location.href='restaurant.php?id=${restaurant.id}'">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </div>
                </div>
            `;
            
            grid.appendChild(card);
        });
        
        container.appendChild(grid);
    }

    // Generate star rating HTML
    function generateStarRating(rating) {
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        
        let starsHTML = '';
        
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

    // Show no restaurants message
    function showNoRestaurants() {
        const container = document.getElementById('restaurantsContainer');
        container.innerHTML = `
            <div class="no-results">
                <i class="fas fa-store-slash"></i>
                <h3>No restaurants found</h3>
                <p>Try again later or check other categories</p>
            </div>
        `;
    }

    // Show error message
    function showError(message) {
        const container = document.getElementById('restaurantsContainer');
        container.innerHTML = `
            <div class="no-results">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Error</h3>
                <p>${message}</p>
                <button class="sort-btn active" onclick="loadPopularRestaurants()" style="margin-top: 15px;">
                    <i class="fas fa-redo"></i>
                    Try Again
                </button>
            </div>
        `;
    }
</script>

<?php 
// Include the footer
$footerPath = __DIR__ . '/assets/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo '</body></html>';
}
?>
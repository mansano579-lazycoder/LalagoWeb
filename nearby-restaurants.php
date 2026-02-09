<?php
// Start session and get user location
session_start();

$isLoggedIn = isset($_SESSION['uid']);
$userId = $_SESSION['uid'] ?? null;

// Get user location from session
$userLocation = $_SESSION['user_location'] ?? null;
$userLatitude = $userLocation['lat'] ?? null;
$userLongitude = $userLocation['lng'] ?? null;
$userAddress = $userLocation['address'] ?? 'Not set';

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
<title>LalaGO - Nearby Restaurants</title>

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

    /* Nearby Restaurants Page Styles */
    .page-content {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        padding-top: 20px;
    }

    .location-info-bar {
        background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
        border: 2px solid var(--info);
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }

    .location-text {
        flex: 1;
        min-width: 200px;
    }

    .location-label {
        font-size: 14px;
        color: var(--gray);
        margin-bottom: 5px;
    }

    .location-address {
        font-size: 16px;
        font-weight: 600;
        color: var(--secondary);
    }

    .location-distance {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--info);
        font-weight: 500;
    }

    .change-location-btn {
        background: var(--info);
        color: var(--white);
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .change-location-btn:hover {
        background: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
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

    .filter-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-btn {
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

    .filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .filter-btn.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
    }

    /* Distance Filter */
    .distance-filter {
        margin-bottom: 25px;
        padding: 20px;
        background: var(--white);
        border-radius: 12px;
        box-shadow: var(--shadow);
    }

    .distance-slider {
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: var(--gray-light);
        outline: none;
        -webkit-appearance: none;
    }

    .distance-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary);
        cursor: pointer;
        border: 3px solid var(--white);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .distance-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        color: var(--gray);
        font-size: 14px;
    }

    .distance-value {
        text-align: center;
        font-weight: 600;
        color: var(--primary);
        margin-top: 5px;
    }

    /* Restaurants Grid */
    .restaurants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    /* Restaurant Card - Enhanced for Nearby */
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

    .restaurant-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--info);
        color: var(--white);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        z-index: 1;
        backdrop-filter: blur(4px);
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

    .restaurant-distance {
        font-size: 14px;
        color: var(--info);
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

    /* Location Required */
    .location-required {
        text-align: center;
        padding: 60px 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .location-required i {
        font-size: 4rem;
        color: var(--info);
        margin-bottom: 20px;
    }

    .location-required h2 {
        color: var(--secondary);
        margin-bottom: 15px;
    }

    .location-required p {
        color: var(--gray);
        margin-bottom: 25px;
        font-size: 1.1rem;
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

        .filter-options {
            width: 100%;
            justify-content: center;
        }

        .location-info-bar {
            flex-direction: column;
            align-items: flex-start;
        }

        .change-location-btn {
            align-self: stretch;
            text-align: center;
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
    <!-- Location Info Bar -->
    <div class="location-info-bar">
        <div class="location-text">
            <div class="location-label">Your current location:</div>
            <div class="location-address"><?php echo htmlspecialchars($userAddress); ?></div>
        </div>
        <button class="change-location-btn" onclick="changeLocation()">
            <i class="fas fa-map-marker-alt"></i>
            Change Location
        </button>
    </div>

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="fas fa-store"></i>
                Nearby Restaurants
            </h1>
            <p class="page-subtitle">Restaurants near your current location</p>
        </div>
        <div class="filter-options">
            <button class="filter-btn active" onclick="filterByDistance('all')">
                <i class="fas fa-map-marker-alt"></i> All Distances
            </button>
            <button class="filter-btn" onclick="filterByDistance('5')">
                <i class="fas fa-walking"></i> Within 5km
            </button>
            <button class="filter-btn" onclick="filterByDistance('10')">
                <i class="fas fa-car"></i> Within 10km
            </button>
        </div>
    </div>

    <!-- Distance Slider -->
    <div class="distance-filter">
        <label for="distanceRange" style="display: block; margin-bottom: 15px; font-weight: 600; color: var(--secondary);">
            <i class="fas fa-ruler-combined"></i> Maximum Distance
        </label>
        <input type="range" id="distanceRange" class="distance-slider" min="1" max="20" value="10" step="1">
        <div class="distance-labels">
            <span>1km</span>
            <span>10km</span>
            <span>20km</span>
        </div>
        <div class="distance-value" id="distanceValue">10km</div>
    </div>

    <!-- Restaurants Container -->
    <div id="restaurantsContainer">
        <?php if (!$userLatitude || !$userLongitude): ?>
        <!-- Location Required Message -->
        <div class="location-required">
            <i class="fas fa-map-marker-alt"></i>
            <h2>Location Required</h2>
            <p>Please set your location to see nearby restaurants</p>
            <button class="change-location-btn" onclick="changeLocation()">
                <i class="fas fa-crosshairs"></i>
                Set Your Location
            </button>
        </div>
        <?php else: ?>
        <!-- Loading State -->
        <div class="loading">
            <div class="loading-card"></div>
            <div class="loading-card"></div>
            <div class="loading-card"></div>
            <div class="loading-card"></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Firebase -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="js/firebase.js"></script>

<script>
    // User location from PHP
    const userLocation = {
        latitude: <?php echo $userLatitude ? $userLatitude : 'null'; ?>,
        longitude: <?php echo $userLongitude ? $userLongitude : 'null'; ?>,
        address: '<?php echo addslashes($userAddress); ?>'
    };

    let allRestaurants = [];
    let filteredRestaurants = [];
    let maxDistance = 10; // Default 10km

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (userLocation.latitude && userLocation.longitude) {
            loadNearbyRestaurants();
            setupDistanceSlider();
        }
    });

    // Setup distance slider
    function setupDistanceSlider() {
        const slider = document.getElementById('distanceRange');
        const valueDisplay = document.getElementById('distanceValue');
        
        slider.addEventListener('input', function() {
            maxDistance = parseInt(this.value);
            valueDisplay.textContent = maxDistance + 'km';
            filterRestaurantsByDistance(maxDistance);
        });
    }

    // Load nearby restaurants
    async function loadNearbyRestaurants() {
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
            
            // Process vendors with distance calculation
            allRestaurants = [];
            vendorsSnapshot.forEach(doc => {
                const vendor = {
                    id: doc.id,
                    ...doc.data()
                };
                
                // Get vendor location
                let vendorLat = vendor.latitude;
                let vendorLng = vendor.longitude;
                
                // Check coordinates array
                if (vendor.coordinates && Array.isArray(vendor.coordinates) && vendor.coordinates.length >= 2) {
                    vendorLat = vendor.coordinates[0];
                    vendorLng = vendor.coordinates[1];
                }
                
                // Calculate distance if vendor has location
                if (vendorLat && vendorLng) {
                    const distance = calculateDistance(
                        userLocation.latitude,
                        userLocation.longitude,
                        vendorLat,
                        vendorLng
                    );
                    
                    allRestaurants.push({
                        ...vendor,
                        distance: distance,
                        latitude: vendorLat,
                        longitude: vendorLng
                    });
                }
            });
            
            // Sort by distance
            allRestaurants.sort((a, b) => a.distance - b.distance);
            
            // Filter by default distance
            filterRestaurantsByDistance(maxDistance);
            
        } catch (error) {
            console.error("Error loading restaurants:", error);
            showError("Error loading restaurants. Please try again.");
        }
    }

    // Filter restaurants by distance
    function filterRestaurantsByDistance(distance) {
        filteredRestaurants = allRestaurants.filter(restaurant => 
            restaurant.distance <= distance
        );
        
        renderRestaurants();
    }

    // Filter by specific distance
    function filterByDistance(distance) {
        // Update active filter button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Update slider
        if (distance !== 'all') {
            const slider = document.getElementById('distanceRange');
            slider.value = distance;
            document.getElementById('distanceValue').textContent = distance + 'km';
            maxDistance = parseInt(distance);
        } else {
            maxDistance = 100; // Show all
        }
        
        filterRestaurantsByDistance(maxDistance);
    }

    // Render restaurants
    function renderRestaurants() {
        const container = document.getElementById('restaurantsContainer');
        
        if (filteredRestaurants.length === 0) {
            showNoRestaurants();
            return;
        }
        
        container.innerHTML = '';
        
        // Create grid container
        const grid = document.createElement('div');
        grid.className = 'restaurants-grid';
        
        filteredRestaurants.forEach(restaurant => {
            const rating = restaurant.reviewsCount > 0 ? 
                (restaurant.reviewsSum / restaurant.reviewsCount).toFixed(1) : "0.0";
            
            const starsHTML = generateStarRating(parseFloat(rating));
            
            // Format distance
            let distanceText;
            if (restaurant.distance < 1) {
                distanceText = `${(restaurant.distance * 1000).toFixed(0)}m`;
            } else {
                distanceText = `${restaurant.distance.toFixed(1)}km`;
            }
            
            const card = document.createElement('a');
            card.href = `restaurant.php?id=${restaurant.id}`;
            card.className = 'restaurant-card';
            card.innerHTML = `
                <div class="restaurant-badge">${distanceText} away</div>
                <img src="${restaurant.photo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'}" 
                     alt="${restaurant.title || 'Restaurant'}" 
                     class="restaurant-image"
                     onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'">
                <div class="restaurant-info">
                    <div class="restaurant-header">
                        <h3 class="restaurant-name">${restaurant.title || 'Restaurant Name'}</h3>
                        <div class="restaurant-distance">${distanceText}</div>
                    </div>
                    <p class="restaurant-category">
                        <i class="fas fa-tag"></i>
                        ${restaurant.categoryTitle || 'Restaurant'}
                    </p>
                    <div class="restaurant-rating">
                        <div class="rating-stars">${starsHTML}</div>
                        <span class="rating-value">${rating}</span>
                        <span class="rating-count">(${restaurant.reviewsCount || 0})</span>
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

    // Calculate distance between two coordinates
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth's radius in km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = 
            Math.sin(dLat/2) * Math.sin(dLat/2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
            Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // Change location
    function changeLocation() {
        window.location.href = 'set-location.php?redirect=nearby-restaurants.php';
    }

    // Show no restaurants message
    function showNoRestaurants() {
        const container = document.getElementById('restaurantsContainer');
        container.innerHTML = `
            <div class="no-results">
                <i class="fas fa-store-slash"></i>
                <h3>No restaurants found nearby</h3>
                <p>Try increasing the distance or changing your location</p>
                <button class="change-location-btn" onclick="changeLocation()" style="margin-top: 15px;">
                    <i class="fas fa-map-marker-alt"></i>
                    Change Location
                </button>
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
                <button class="change-location-btn" onclick="loadNearbyRestaurants()" style="margin-top: 15px;">
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
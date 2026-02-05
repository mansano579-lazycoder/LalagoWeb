<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set base URL for consistent paths
$base_url = dirname(dirname($_SERVER['PHP_SELF']));
if ($base_url == '/') {
    $base_url = '';
}

// Include Firebase configuration
require_once dirname(__DIR__) . '/inc/firebase.php';
?>

<?php 
// Include the header with full path
$headerPath = __DIR__ . '/header.php';
if (file_exists($headerPath)) {
    include $headerPath;
} else {
    // Fallback if header not found
    echo "<!DOCTYPE html><html><head><title>LalaGO - Restaurants</title>";
    echo "<link rel='stylesheet' href='{$base_url}/css/style.css'>";
    echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>";
    echo "</head><body>";
}
?>

<style>
    /* Categories Page Specific Styles */
    .page-content {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
        padding-top: 20px; /* Reduced since header handles spacing */
    }

    /* Layout */
    .categories-container {
        display: flex;
        gap: 30px;
        margin-top: 20px;
    }

    /* Sidebar */
    .categories-sidebar {
        width: 280px;
        flex-shrink: 0;
        background: var(--white);
        border-radius: 12px;
        box-shadow: var(--shadow);
        padding: 20px;
        height: fit-content;
        position: sticky;
        top: 140px;
    }

    .sidebar-toggle {
        display: none;
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
        margin-bottom: 15px;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .search-container {
        margin-bottom: 25px;
    }

    .search-box {
        position: relative;
        margin-bottom: 10px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 15px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .search-btn:hover {
        background-color: #e55a00;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #eee;
    }

    /* Categories Grid in Sidebar */
    .categories-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .category-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }

    .category-item:hover {
        background-color: var(--light-gray);
    }

    .category-item.active {
        background-color: rgba(255, 102, 0, 0.1);
        border-color: var(--primary-color);
    }

    .category-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eee;
    }

    .category-item.active .category-icon {
        border-color: var(--primary-color);
    }

    .category-name {
        font-weight: 500;
        font-size: 14px;
        color: #333;
    }

    .category-item.active .category-name {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Main Content */
    .main-content {
        flex: 1;
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 25px;
    }

    .restaurants-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    /* Restaurant Card */
    .restaurant-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .restaurant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .restaurant-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .restaurant-info {
        padding: 20px;
    }

    .restaurant-name {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--secondary-color);
    }

    .restaurant-category {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .restaurant-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        color: #666;
    }

    .rating-star {
        color: #ffc107;
    }

    .delivery-time {
        color: var(--primary-color);
        font-weight: 500;
    }

    .no-results {
        grid-column: 1 / -1;
        text-align: center;
        padding: 50px 20px;
        color: #666;
    }

    .no-results i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }

    /* Loading State */
    .loading {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
    }

    .loading-card {
        background: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--shadow);
        height: 250px;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .categories-container {
            gap: 20px;
        }
        
        .categories-sidebar {
            width: 250px;
        }
    }

    @media (max-width: 768px) {
        .categories-container {
            flex-direction: column;
        }
        
        .categories-sidebar {
            width: 100%;
            position: static;
            top: auto;
            display: none;
        }
        
        .sidebar-toggle {
            display: flex;
        }
        
        .categories-sidebar.active {
            display: block;
        }
        
        .categories-list {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .category-item {
            flex-direction: column;
            text-align: center;
            min-width: 100px;
        }
        
        .restaurants-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 576px) {
        .page-content {
            padding: 15px;
        }
        
        .page-title {
            font-size: 24px;
        }
        
        .restaurants-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .restaurant-card {
            max-width: 400px;
            margin: 0 auto;
        }
        
        .search-box input {
            padding: 10px 40px 10px 12px;
        }
    }

    @media (max-width: 480px) {
        .categories-list {
            justify-content: space-between;
        }
        
        .category-item {
            min-width: 80px;
            padding: 8px;
        }
        
        .category-icon {
            width: 40px;
            height: 40px;
        }
        
        .category-name {
            font-size: 12px;
        }
    }
</style>

<div class="categories-container">
    <!-- Sidebar -->
    <aside class="categories-sidebar" id="categoriesSidebar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-filter"></i> Filter Categories
            <i class="fas fa-chevron-down" id="sidebarChevron"></i>
        </button>
        
        <div class="search-container">
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Search restaurants...">
                <button class="search-btn" onclick="searchVendors()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        
        <h3 class="section-title">Categories</h3>
        <div class="categories-list" id="categoriesList">
            <!-- Categories will be loaded here -->
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <h1 class="page-title">All Restaurants</h1>
        
        <div class="restaurants-grid" id="vendorsContainer">
            <!-- Loading skeleton -->
            <div class="loading">
                <div class="loading-card"></div>
                <div class="loading-card"></div>
                <div class="loading-card"></div>
                <div class="loading-card"></div>
            </div>
        </div>
    </main>
</div>

<script>
    // Initialize Firebase if needed
    <?php if (isset($firebase) && isset($db)): ?>
        const db = firebase.firestore();
    <?php endif; ?>

    // Categories page functionality
    const categoriesList = document.getElementById('categoriesList');
    const vendorsContainer = document.getElementById('vendorsContainer');
    const searchInput = document.getElementById('searchInput');
    
    let vendors = [];
    let categoriesMap = {};
    let selectedCategoryID = null;
    
    // Helper function to generate star rating HTML
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
    
    // Helper function to calculate rating
    function calculateRating(vendor) {
        if (vendor.reviewsCount > 0 && vendor.reviewsSum > 0) {
            const rating = vendor.reviewsSum / vendor.reviewsCount;
            return parseFloat(rating.toFixed(1));
        }
        return 0; // No ratings yet
    }
    
    // Helper function to calculate delivery time based on vendor data
    function getDeliveryTime(vendor) {
        // First, check if vendor has direct delivery time field
        if (vendor.deliveryTime && typeof vendor.deliveryTime === 'string') {
            return vendor.deliveryTime;
        }
        
        // Try different possible field names for delivery time
        if (vendor.estimatedDeliveryTime) return vendor.estimatedDeliveryTime;
        if (vendor.delivery_time) return vendor.delivery_time;
        if (vendor.deliveryTimeRange) return vendor.deliveryTimeRange;
        
        // Calculate based on delivery charge information
        // Vendors with higher minimum delivery charges often have longer delivery times
        let baseTime = 30; // Default base time
        let variation = 10; // Default variation
        
        // Adjust based on minimum delivery charges
        if (vendor.minimum_delivery_charges) {
            const minCharge = vendor.minimum_delivery_charges;
            if (minCharge >= 50) {
                // Higher charges might indicate premium service or longer distance
                baseTime = 35;
                variation = 15;
            } else if (minCharge <= 20) {
                // Lower charges might indicate faster service
                baseTime = 25;
                variation = 8;
            }
        }
        
        // Adjust based on delivery charges per km
        if (vendor.delivery_charges_per_km) {
            const chargePerKm = vendor.delivery_charges_per_km;
            if (chargePerKm >= 10) {
                // Higher per km charge might indicate longer distances
                baseTime = Math.max(baseTime, 35);
                variation = Math.max(variation, 15);
            } else if (chargePerKm <= 5) {
                // Lower per km charge might indicate local/short distance
                baseTime = Math.min(baseTime, 28);
                variation = Math.min(variation, 8);
            }
        }
        
        // Adjust based on category
        if (vendor.categoryID && categoriesMap[vendor.categoryID]) {
            const categoryName = categoriesMap[vendor.categoryID].title.toLowerCase();
            
            // Fast food categories are usually faster
            if (categoryName.includes('fast') || categoryName.includes('pizza') || 
                categoryName.includes('burger') || categoryName.includes('sandwich')) {
                baseTime = Math.min(baseTime, 25);
                variation = Math.min(variation, 10);
            }
            // Fine dining or specialty categories might take longer
            else if (categoryName.includes('fine') || categoryName.includes('steak') || 
                     categoryName.includes('seafood') || categoryName.includes('sushi') ||
                     categoryName.includes('italian') || categoryName.includes('chinese')) {
                baseTime = Math.max(baseTime, 35);
                variation = Math.max(variation, 15);
            }
            // Asian cuisine might be moderate
            else if (categoryName.includes('asian') || categoryName.includes('filipino') ||
                     categoryName.includes('japanese') || categoryName.includes('korean')) {
                baseTime = 30;
                variation = 12;
            }
        }
        
        // Generate a consistent but varied time based on vendor ID
        let hash = 0;
        for (let i = 0; i < vendor.id.length; i++) {
            hash = vendor.id.charCodeAt(i) + ((hash << 5) - hash);
        }
        hash = Math.abs(hash);
        
        // Calculate min and max times
        const minTime = baseTime + (hash % variation);
        const maxTime = minTime + variation + (hash % 5);
        
        return `${minTime}-${maxTime} min`;
    }
    
    // Load vendors and categories
    function loadData() {
        vendorsContainer.innerHTML = `
            <div class="loading">
                <div class="loading-card"></div>
                <div class="loading-card"></div>
                <div class="loading-card"></div>
                <div class="loading-card"></div>
            </div>
        `;
        
        // Fetch all vendors
        db.collection("vendors")
            .where("reststatus", "==", true)
            .get()
            .then(vendorSnap => {
                vendors = [];
                if (!vendorSnap.empty) {
                    vendorSnap.forEach(doc => {
                        const v = doc.data();
                        v.id = doc.id;
                        
                        // Ensure required fields exist
                        v.reviewsCount = v.reviewsCount || 0;
                        v.reviewsSum = v.reviewsSum || 0;
                        v.title = v.title || "Restaurant Name";
                        v.categoryID = v.categoryID || "";
                        v.categoryTitle = v.categoryTitle || "";
                        
                        // Ensure delivery charge fields exist
                        v.minimum_delivery_charges = v.minimum_delivery_charges || 30; // Default 30
                        v.delivery_charges_per_km = v.delivery_charges_per_km || 1; // Default 1
                        v.minimum_delivery_charges_within_km = v.minimum_delivery_charges_within_km || 3; // Default 3
                        
                        vendors.push(v);
                    });
                }
                
                // Now fetch categories
                return db.collection("vendor_categories")
                    .where("publish", "==", true)
                    .get();
            })
            .then(catSnap => {
                categoriesMap = {};
                if (!catSnap.empty) {
                    catSnap.forEach(doc => {
                        const c = doc.data();
                        const catID = c.id || doc.id;
                        categoriesMap[catID] = {
                            title: c.title?.trim() || "No Name",
                            photo: c.photo || "https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=60&h=60&fit=crop&crop=face"
                        };
                    });
                }
                
                // Build UI with all data
                buildCategories();
                loadVendors();
            })
            .catch(err => {
                console.error("Error loading data:", err);
                vendorsContainer.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-exclamation-circle"></i>
                        <h3>Error loading restaurants</h3>
                        <p>Please try again later</p>
                    </div>
                `;
            });
    }
    
    // Build categories in sidebar
    function buildCategories() {
        categoriesList.innerHTML = '';
        
        // All categories item
        const allItem = document.createElement('div');
        allItem.className = 'category-item active';
        allItem.innerHTML = `
            <img src="https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=60&h=60&fit=crop&crop=face" 
                 alt="All" class="category-icon">
            <span class="category-name">All</span>
        `;
        allItem.onclick = () => {
            selectedCategoryID = null;
            setActiveCategory(allItem);
            loadVendors();
        };
        categoriesList.appendChild(allItem);
        
        // Individual category items
        Object.keys(categoriesMap).forEach(catID => {
            const cat = categoriesMap[catID];
            const item = document.createElement('div');
            item.className = 'category-item';
            item.innerHTML = `
                <img src="${cat.photo}" 
                     alt="${cat.title}" 
                     class="category-icon"
                     onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=60&h=60&fit=crop&crop=face'">
                <span class="category-name">${cat.title}</span>
            `;
            item.onclick = () => {
                selectedCategoryID = catID;
                setActiveCategory(item);
                loadVendors(catID);
            };
            categoriesList.appendChild(item);
        });
    }
    
    // Set active category
    function setActiveCategory(activeItem) {
        const items = document.querySelectorAll('.category-item');
        items.forEach(item => item.classList.remove('active'));
        activeItem.classList.add('active');
    }
    
    // Load and display vendors
    function loadVendors(categoryID = null, searchTerm = "") {
        vendorsContainer.innerHTML = '';
        searchTerm = searchTerm.toLowerCase().trim();
        
        let filteredVendors = vendors.filter(v => {
            const matchesCategory = !categoryID || v.categoryID === categoryID;
            const matchesSearch = !searchTerm || 
                (v.title && v.title.toLowerCase().includes(searchTerm)) ||
                (v.description && v.description.toLowerCase().includes(searchTerm)) ||
                (v.categoryTitle && v.categoryTitle.toLowerCase().includes(searchTerm));
            return matchesCategory && matchesSearch;
        });
        
        if (filteredVendors.length === 0) {
            vendorsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-utensils"></i>
                    <h3>No restaurants found</h3>
                    <p>Try a different search or category</p>
                </div>
            `;
            return;
        }
        
        // Sort vendors by reviews count (popularity) or alphabetically
        filteredVendors.sort((a, b) => {
            // Sort by reviews count (descending), then by name
            if (b.reviewsCount !== a.reviewsCount) {
                return b.reviewsCount - a.reviewsCount;
            }
            return a.title.localeCompare(b.title);
        });
        
        filteredVendors.forEach(v => {
            const rating = calculateRating(v);
            const ratingDisplay = rating === 0 ? 'No ratings' : rating.toFixed(1);
            const deliveryTime = getDeliveryTime(v);
            
            const card = document.createElement('div');
            card.className = 'restaurant-card';
            card.innerHTML = `
                <img src="${v.photo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'}" 
                     alt="${v.title || 'Restaurant'}" 
                     class="restaurant-image"
                     onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400&h=200&fit=crop'">
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${v.title || 'Restaurant Name'}</h3>
                    <p class="restaurant-category">${v.categoryTitle || categoriesMap[v.categoryID]?.title || 'Restaurant'}</p>
                    <div class="restaurant-rating">
                        <span class="rating-star">${generateStarRating(rating)}</span>
                        <span>${ratingDisplay}</span>
                        ${v.reviewsCount > 0 ? `<span>(${v.reviewsCount})</span>` : ''}
                        <span>•</span>
                        <span class="delivery-time">${deliveryTime}</span>
                    </div>
                </div>
            `;
            card.onclick = () => {
                window.location.href = '<?php echo $base_url; ?>/users/vendor.php?id=' + encodeURIComponent(v.id);
            };
            vendorsContainer.appendChild(card);
        });
    }
    
    // Search function
    function searchVendors() {
        const term = searchInput.value.trim();
        loadVendors(selectedCategoryID, term);
    }
    
    // Event listeners
    searchInput.addEventListener('keyup', e => {
        if (e.key === 'Enter') {
            searchVendors();
        }
    });
    
    // Sidebar toggle for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('categoriesSidebar');
        const chevron = document.getElementById('sidebarChevron');
        sidebar.classList.toggle('active');
        chevron.classList.toggle('fa-chevron-down');
        chevron.classList.toggle('fa-chevron-up');
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('categoriesSidebar');
        if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
            if (!sidebar.contains(e.target) && !e.target.classList.contains('sidebar-toggle')) {
                sidebar.classList.remove('active');
                const chevron = document.getElementById('sidebarChevron');
                chevron.classList.add('fa-chevron-down');
                chevron.classList.remove('fa-chevron-up');
            }
        }
    });
    
    // Initialize when page loads
    window.addEventListener('DOMContentLoaded', function() {
        if (typeof db !== 'undefined') {
            loadData();
        } else {
            console.error('Firebase not initialized');
            vendorsContainer.innerHTML = `
                <div class="no-results">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Database connection error</h3>
                    <p>Please refresh the page</p>
                </div>
            `;
        }
    });
</script>

<?php 
// Include the footer
$footerPath = __DIR__ . '/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
} else {
    echo '</body></html>';
}
?>
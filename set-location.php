<?php
/**
 * Location Setter for Lalagoweb
 * Saves location in session and redirects back to index.php
 */

session_start();

// Google Maps API Key (you'll need to replace with your own)
define('GOOGLE_MAPS_API_KEY', 'AIzaSyBXNXXV60p-VYnIMD0mevMk8HeW9kSJnPs');

// If location is already set and we have a redirect flag, go to index.php
if (isset($_SESSION['user_location']) && isset($_GET['redirect'])) {
    header('Location: index.php');
    exit;
}

// Handle location updates via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['action']) && $_POST['action'] === 'save_location') {
        if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
            $latitude = floatval($_POST['latitude']);
            $longitude = floatval($_POST['longitude']);
            $address = isset($_POST['address']) ? htmlspecialchars($_POST['address']) : 'Current Location';
            
            // Store in session
            $_SESSION['user_location'] = [
                'lat' => $latitude,
                'lng' => $longitude,
                'address' => $address,
                'timestamp' => time()
            ];
            
            // Also store in separate session variables for easy access
            $_SESSION['user_latitude'] = $latitude;
            $_SESSION['user_longitude'] = $longitude;
            $_SESSION['user_address'] = $address;
            
            $response['success'] = true;
            $response['message'] = 'Location saved successfully';
            $response['redirect'] = 'set-location.php?redirect=true';
        } else {
            $response['message'] = 'Invalid location data';
        }
    }
    
    echo json_encode($response);
    exit;
}

// Get saved location from session
$savedLocation = isset($_SESSION['user_location']) ? $_SESSION['user_location'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Set Your Location - LalaGO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header {
            background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .logo {
            font-size: 3.5rem;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .content {
            padding: 30px;
        }
        
        .location-card {
            background: #FFF8E1;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 2px solid #FFE0B2;
            margin-bottom: 25px;
        }
        
        .location-icon {
            font-size: 4rem;
            color: #FF6B35;
            margin-bottom: 20px;
        }
        
        .location-btn {
            background: linear-gradient(135deg, #FF6B35 0%, #E55A2B 100%);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            width: 100%;
            box-shadow: 0 10px 25px rgba(255, 107, 53, 0.4);
            margin-bottom: 15px;
        }
        
        .location-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(255, 107, 53, 0.5);
        }
        
        .location-btn:active {
            transform: translateY(-1px);
        }
        
        .location-btn i {
            font-size: 1.2rem;
        }
        
        .info-text {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 10px;
        }
        
        .location-info {
            background: white;
            border-radius: 15px;
            padding: 20px;
            border: 2px solid #4CAF50;
            margin-top: 20px;
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .location-info.show {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .location-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .location-info p {
            color: #555;
            margin: 8px 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }
        
        .success-badge {
            background: #4CAF50;
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        
        .continue-btn {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 16px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.4);
            margin-top: 20px;
            display: none;
        }
        
        .continue-btn.show {
            display: block;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .continue-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        
        .skip-link {
            display: block;
            text-align: center;
            color: #666;
            text-decoration: none;
            font-size: 0.95rem;
            margin-top: 25px;
            padding: 10px;
            transition: color 0.3s;
        }
        
        .skip-link:hover {
            color: #FF6B35;
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }
        
        .loading-content {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            max-width: 90%;
            width: 300px;
            animation: popIn 0.3s ease;
        }
        
        @keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #FF6B35;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: #333;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }
        
        .step {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #E0E0E0;
            transition: all 0.3s;
        }
        
        .step.active {
            background: #FF6B35;
            transform: scale(1.2);
        }
        
        /* Mobile Responsive */
        @media (max-width: 480px) {
            .container {
                max-width: 100%;
                border-radius: 20px;
            }
            
            .header {
                padding: 25px 20px;
            }
            
            .logo {
                font-size: 3rem;
            }
            
            .header h1 {
                font-size: 1.5rem;
            }
            
            .content {
                padding: 25px 20px;
            }
            
            .location-card {
                padding: 20px;
            }
            
            .location-icon {
                font-size: 3.5rem;
            }
            
            .location-btn {
                padding: 16px 25px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 350px) {
            .header h1 {
                font-size: 1.3rem;
            }
            
            .location-btn {
                padding: 14px 20px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <h1>Welcome to LalaGO</h1>
            <p>Set your location to discover nearby restaurants</p>
        </div>
        
        <div class="content">
            <div class="step-indicator">
                <div class="step active"></div>
                <div class="step"></div>
                <div class="step"></div>
            </div>
            
            <div class="location-card">
                <div class="location-icon">
                    <i class="fas fa-location-arrow"></i>
                </div>
                
                <h2 style="color: #333; margin-bottom: 15px; font-size: 1.4rem;">Set Your Location</h2>
                <p class="info-text">We need your location to show restaurants and foods near you.</p>
                <p class="info-text">Your location is only used to find nearby services and is never shared with third parties.</p>
                
                <button class="location-btn" id="get-location-btn">
                    <i class="fas fa-crosshairs"></i>
                    Detect My Current Location
                </button>
                
                <div class="message" id="message"></div>
                
                <div class="location-info" id="location-info">
                    <h3><i class="fas fa-map-pin"></i> Location Set Successfully</h3>
                    <p id="location-address">Loading address...</p>
                    <p id="location-coords"></p>
                    <div class="success-badge">
                        <i class="fas fa-check-circle"></i> Ready to Go!
                    </div>
                </div>
                
                <button class="continue-btn" id="continue-btn">
                    <i class="fas fa-arrow-right"></i>
                    Continue to LalaGO
                </button>
                
                <a href="index.php" class="skip-link" id="skip-link">
                    <i class="fas fa-forward"></i> Skip for now
                </a>
            </div>
        </div>
    </div>
    
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text" id="loading-text">Detecting your location...</div>
        </div>
    </div>

    <script>
        // DOM Elements
        const getLocationBtn = document.getElementById('get-location-btn');
        const locationInfo = document.getElementById('location-info');
        const continueBtn = document.getElementById('continue-btn');
        const skipLink = document.getElementById('skip-link');
        const loadingOverlay = document.getElementById('loading-overlay');
        const loadingText = document.getElementById('loading-text');
        const messageDiv = document.getElementById('message');
        const locationAddress = document.getElementById('location-address');
        const locationCoords = document.getElementById('location-coords');
        const steps = document.querySelectorAll('.step');
        
        // Show message function
        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
        
        // Show loading
        function showLoading(text) {
            loadingText.textContent = text;
            loadingOverlay.style.display = 'flex';
        }
        
        // Hide loading
        function hideLoading() {
            loadingOverlay.style.display = 'none';
        }
        
        // Update steps
        function updateSteps(step) {
            steps.forEach((s, index) => {
                if (index <= step) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        }
        
        // Get current location
        function getCurrentLocation() {
            if (!navigator.geolocation) {
                showMessage('Geolocation is not supported by your browser', 'error');
                return;
            }
            
            // Update steps
            updateSteps(1);
            showLoading('Detecting your location...');
            
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    // Get address from coordinates
                    try {
                        showLoading('Getting your address...');
                        const address = await getAddressFromCoords(latitude, longitude);
                        
                        // Save location to server
                        showLoading('Saving location...');
                        await saveLocation(latitude, longitude, address);
                        
                        // Update UI
                        locationAddress.textContent = address;
                        locationCoords.textContent = `Coordinates: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;
                        locationInfo.classList.add('show');
                        continueBtn.classList.add('show');
                        
                        // Hide the get location button
                        getLocationBtn.style.display = 'none';
                        
                        // Update steps
                        updateSteps(2);
                        
                        // Show success message
                        showMessage('Location saved successfully! You can now continue.', 'success');
                        
                        hideLoading();
                        
                    } catch (error) {
                        hideLoading();
                        showMessage('Error getting address: ' + error.message, 'error');
                        updateSteps(0);
                    }
                },
                (error) => {
                    hideLoading();
                    updateSteps(0);
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            showMessage('Location access was denied. Please enable location permissions in your browser settings to continue.', 'error');
                            break;
                        case error.POSITION_UNAVAILABLE:
                            showMessage('Location information is unavailable. Please check your GPS or network connection.', 'error');
                            break;
                        case error.TIMEOUT:
                            showMessage('Location request timed out. Please try again.', 'error');
                            break;
                        default:
                            showMessage('Unable to get your location. Please try again.', 'error');
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }
        
        // Get address from coordinates using reverse geocoding
        async function getAddressFromCoords(lat, lng) {
            try {
                const response = await fetch(
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=<?php echo GOOGLE_MAPS_API_KEY; ?>`
                );
                
                const data = await response.json();
                
                if (data.status === 'OK' && data.results[0]) {
                    return data.results[0].formatted_address;
                } else {
                    return `Near ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                }
            } catch (error) {
                return `Location at ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
            }
        }
        
        // Save location to server
        async function saveLocation(lat, lng, address) {
            const formData = new FormData();
            formData.append('action', 'save_location');
            formData.append('latitude', lat);
            formData.append('longitude', lng);
            formData.append('address', address);
            
            const response = await fetch('<?php echo basename(__FILE__); ?>', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Failed to save location');
            }
            
            return data;
        }
        
        // Continue to index.php
        function continueToIndex() {
            showLoading('Redirecting to LalaGO...');
            setTimeout(() => {
                window.location.href = 'set-location.php?redirect=true';
            }, 1000);
        }
        
        // Event Listeners
        getLocationBtn.addEventListener('click', getCurrentLocation);
        continueBtn.addEventListener('click', continueToIndex);
        
        // Add touch feedback for mobile
        getLocationBtn.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.98)';
        });
        
        getLocationBtn.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
        
        // If location is already set in session, show it
        <?php if ($savedLocation): ?>
        document.addEventListener('DOMContentLoaded', function() {
            locationAddress.textContent = '<?php echo addslashes($savedLocation['address']); ?>';
            locationCoords.textContent = `Coordinates: <?php echo number_format($savedLocation['lat'], 6); ?>, <?php echo number_format($savedLocation['lng'], 6); ?>`;
            locationInfo.classList.add('show');
            continueBtn.classList.add('show');
            getLocationBtn.style.display = 'none';
            
            // Update steps
            updateSteps(2);
            
            // Show success message
            showMessage('Your location is already set! Click continue to proceed.', 'success');
        });
        <?php endif; ?>
    </script>
</body>
</html>
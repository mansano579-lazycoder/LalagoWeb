<?php
session_start();
include '../inc/firebase.php';

if (!isset($_SESSION['uid'])) {
    header("Location: /login.php");
    exit();
}

$uid = $_SESSION['uid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - LalaGO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBXNXXV60p-VYnIMD0mevMk8HeW9kSJnPs&libraries=places"></script>
    <style>
        /* ================== THEME VARIABLES ================== */
        :root {
            --primary-red: #FF3B30;
            --primary-orange: #FF9500;
            --accent-green: #34C759;
            --accent-blue: #007AFF;
            --dark-gray: #1D1D1F;
            --medium-gray: #86868B;
            --light-gray: #F5F5F7;
            --white: #FFFFFF;
            --shadow-soft: 0 4px 24px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 8px 32px rgba(0, 0, 0, 0.1);
            --shadow-large: 0 16px 48px rgba(0, 0, 0, 0.12);
            --radius-large: 20px;
            --radius-medium: 12px;
            --radius-small: 8px;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 100vh;
            color: var(--dark-gray);
            line-height: 1.6;
        }

        /* ================== HEADER ================== */
        .settings-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--white);
            box-shadow: var(--shadow-soft);
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light-gray);
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-medium);
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark-gray);
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
            margin-right: 20px;
        }

        .back-button:hover {
            background: var(--primary-red);
            color: var(--white);
            transform: translateX(-5px);
        }

        .header-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            flex: 1;
        }

        /* ================== MAIN CONTAINER ================== */
        .settings-main {
            padding-top: 90px;
            padding-bottom: 40px;
        }

        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ================== WELCOME BANNER ================== */
        .welcome-banner {
            background: linear-gradient(135deg, rgba(255, 59, 48, 0.1), rgba(255, 149, 0, 0.1));
            border-radius: var(--radius-large);
            padding: 30px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 2px solid rgba(255, 59, 48, 0.2);
            box-shadow: var(--shadow-soft);
        }

        .welcome-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .welcome-content {
            flex: 1;
        }

        .welcome-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 8px;
        }

        .welcome-text {
            color: var(--medium-gray);
            font-size: 1rem;
        }

        /* ================== SETTINGS FORM ================== */
        .settings-form {
            background: var(--white);
            border-radius: var(--radius-large);
            padding: 40px;
            box-shadow: var(--shadow-medium);
            margin-bottom: 30px;
        }

        /* ================== FORM SECTIONS ================== */
        .form-section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid var(--light-gray);
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.3rem;
        }

        .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-gray);
        }

        .section-subtitle {
            color: var(--medium-gray);
            font-size: 0.95rem;
            margin-top: 5px;
        }

        /* ================== FORM GRID ================== */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark-gray);
            font-size: 1rem;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 16px;
            border: 2px solid var(--light-gray);
            border-radius: var(--radius-medium);
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition-smooth);
            background: var(--white);
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.1);
        }

        .form-input::placeholder, .form-textarea::placeholder {
            color: var(--medium-gray);
            opacity: 0.7;
        }

        .form-helper {
            margin-top: 8px;
            font-size: 0.85rem;
            color: var(--medium-gray);
        }

        .form-helper.error {
            color: var(--primary-red);
        }

        /* ================== MAP SECTION ================== */
        .map-section {
            background: var(--light-gray);
            border-radius: var(--radius-large);
            padding: 30px;
            margin-bottom: 30px;
        }

        .map-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .map-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-gray);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .map-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .map-btn {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius-medium);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition-smooth);
        }

        .map-btn.primary {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            box-shadow: 0 4px 15px rgba(255, 59, 48, 0.2);
        }

        .map-btn.secondary {
            background: var(--white);
            color: var(--accent-blue);
            border: 2px solid var(--accent-blue);
        }

        .map-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .search-box-container {
            position: relative;
            margin: 15px 0;
        }

        .search-box {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid var(--light-gray);
            border-radius: var(--radius-medium);
            font-size: 1rem;
            transition: var(--transition-smooth);
            background: var(--white);
        }

        .search-box:focus {
            outline: none;
            border-color: var(--primary-red);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--medium-gray);
        }

        .map-container {
            height: 400px;
            border-radius: var(--radius-medium);
            overflow: hidden;
            margin: 15px 0;
            border: 2px solid var(--light-gray);
        }

        #map {
            width: 100%;
            height: 100%;
        }

        .coordinates-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        /* ================== IMAGE UPLOAD ================== */
        .image-upload-container {
            background: var(--light-gray);
            border-radius: var(--radius-medium);
            padding: 25px;
            margin: 20px 0;
        }

        .upload-area {
            border: 2px dashed var(--medium-gray);
            border-radius: var(--radius-medium);
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            background: var(--white);
        }

        .upload-area:hover {
            border-color: var(--primary-red);
            background: rgba(255, 59, 48, 0.05);
        }

        .upload-area.dragover {
            border-color: var(--primary-red);
            background: rgba(255, 59, 48, 0.1);
        }

        .upload-icon {
            font-size: 3rem;
            color: var(--medium-gray);
            margin-bottom: 15px;
        }

        .upload-text {
            color: var(--medium-gray);
            margin-bottom: 10px;
        }

        .upload-browse {
            color: var(--primary-red);
            font-weight: 600;
            cursor: pointer;
        }

        .upload-preview {
            margin-top: 20px;
            padding: 20px;
            background: var(--white);
            border-radius: var(--radius-medium);
            border: 1px solid var(--light-gray);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .preview-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: var(--shadow-soft);
        }

        .preview-info {
            flex: 1;
        }

        .preview-name {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 5px;
        }

        .preview-size {
            font-size: 0.9rem;
            color: var(--medium-gray);
            margin-bottom: 10px;
        }

        .preview-actions {
            display: flex;
            gap: 10px;
        }

        .preview-btn {
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius-small);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .preview-btn.remove {
            background: rgba(255, 59, 48, 0.1);
            color: var(--primary-red);
        }

        .preview-btn.remove:hover {
            background: var(--primary-red);
            color: var(--white);
        }

        .progress-container {
            margin-top: 15px;
        }

        .progress-bar {
            height: 8px;
            background: var(--light-gray);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            width: 0%;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.85rem;
            color: var(--medium-gray);
            text-align: center;
        }

        /* ================== NOTIFICATION SETTINGS ================== */
        .notification-settings {
            background: var(--light-gray);
            border-radius: var(--radius-medium);
            padding: 25px;
        }

        .notification-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notification-icon {
            width: 40px;
            height: 40px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-red);
            font-size: 1.1rem;
        }

        .notification-text {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 4px;
        }

        .notification-desc {
            font-size: 0.9rem;
            color: var(--medium-gray);
        }

        .notification-toggle {
            position: relative;
            width: 60px;
            height: 30px;
        }

        .notification-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: var(--transition-smooth);
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: var(--transition-smooth);
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
        }

        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }

        /* ================== FORM ACTIONS ================== */
        .form-actions {
            display: flex;
            gap: 20px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .action-btn {
            flex: 1;
            min-width: 200px;
            padding: 20px 30px;
            border: none;
            border-radius: var(--radius-medium);
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            transition: var(--transition-smooth);
            text-decoration: none;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            box-shadow: 0 4px 20px rgba(255, 59, 48, 0.2);
        }

        .action-btn.secondary {
            background: var(--white);
            color: var(--dark-gray);
            border: 2px solid var(--light-gray);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .action-btn.primary:hover {
            box-shadow: 0 8px 25px rgba(255, 59, 48, 0.3);
        }

        .action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ================== OFFLINE INDICATOR ================== */
        .offline-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary-red);
            color: var(--white);
            padding: 12px 24px;
            border-radius: var(--radius-medium);
            display: none;
            align-items: center;
            gap: 10px;
            z-index: 1001;
            box-shadow: var(--shadow-medium);
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* ================== RESPONSIVE DESIGN ================== */
        @media (max-width: 1024px) {
            .settings-form {
                padding: 30px 25px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .settings-header {
                height: 60px;
                padding: 0 15px;
            }
            
            .back-button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
            
            .settings-main {
                padding-top: 80px;
            }
            
            .welcome-banner {
                padding: 20px;
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .settings-form {
                padding: 25px 20px;
            }
            
            .section-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .map-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .map-actions {
                justify-content: center;
            }
            
            .action-btn {
                min-width: 100%;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .upload-preview {
                flex-direction: column;
                text-align: center;
            }
            
            .preview-actions {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .map-container {
                height: 300px;
            }
            
            .coordinates-grid {
                grid-template-columns: 1fr;
            }
            
            .notification-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .notification-toggle {
                align-self: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="settings-header">
        <button class="back-button" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <h1 class="header-title">Profile Settings</h1>
        <div style="width: 120px;"></div>
    </header>

    <!-- Offline Indicator -->
    <div id="offlineIndicator" class="offline-indicator">
        <i class="fas fa-wifi-slash"></i>
        <span>You are currently offline</span>
    </div>

    <!-- Main Content -->
    <main class="settings-main">
        <div class="settings-container">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <div class="welcome-content">
                    <h2 class="welcome-title">Complete Your Profile</h2>
                    <p class="welcome-text">Set up your delivery address, preferences, and personal information to enjoy seamless food ordering with LalaGO.</p>
                </div>
            </div>

            <!-- Settings Form -->
            <form id="settingsForm" class="settings-form">
                <!-- Personal Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Personal Information</h2>
                            <p class="section-subtitle">Update your name, contact details, and profile picture</p>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="firstName" class="form-label">First Name *</label>
                            <input type="text" id="firstName" class="form-input" placeholder="Enter your first name" required>
                            <div class="form-helper">Your given name</div>
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="form-label">Last Name *</label>
                            <input type="text" id="lastName" class="form-input" placeholder="Enter your last name" required>
                            <div class="form-helper">Your family name</div>
                        </div>
                    </div>

                    <div class="form-grid" style="margin-top: 25px;">
                        <div class="form-group">
                            <label for="phoneNumber" class="form-label">Phone Number *</label>
                            <input type="text" id="phoneNumber" class="form-input" placeholder="09xxxxxxxxx or +639xxxxxxxxx" required>
                            <div class="form-helper">Format: 09xxxxxxxxx or +639xxxxxxxxx (11 digits)</div>
                            <div id="phoneError" class="form-helper error" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Profile Picture Upload -->
                    <div class="image-upload-container">
                        <label class="form-label">Profile Picture</label>
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p class="upload-text">Drag & drop your photo here or</p>
                            <span class="upload-browse" id="browseLink">Browse files</span>
                            <p class="form-helper">Max size: 2MB • Formats: JPG, PNG, GIF</p>
                        </div>
                        <input type="file" id="profilePictureUpload" accept="image/*" style="display: none;">
                        
                        <div id="uploadPreview"></div>
                        
                        <div class="progress-container" id="uploadProgress" style="display: none;">
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>
                            <div class="progress-text" id="progressText">Uploading: 0%</div>
                        </div>
                        
                        <div class="form-grid" style="margin-top: 20px;">
                            <div class="form-group">
                                <label for="profilePictureURL" class="form-label">Profile Picture URL</label>
                                <input type="text" id="profilePictureURL" class="form-input" placeholder="Or enter image URL">
                                <div class="form-helper">Leave empty to use default avatar</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Delivery Location</h2>
                            <p class="section-subtitle">Set your location for accurate delivery estimates</p>
                        </div>
                    </div>

                    <div class="map-section">
                        <div class="map-header">
                            <div class="map-title">
                                <i class="fas fa-map"></i> Interactive Map
                            </div>
                            <div class="map-actions">
                                <button type="button" class="map-btn secondary" id="useCurrentLocation">
                                    <i class="fas fa-location-crosshairs"></i> Current Location
                                </button>
                                <button type="button" class="map-btn primary" id="resetLocation">
                                    <i class="fas fa-sync-alt"></i> Reset Map
                                </button>
                            </div>
                        </div>

                        <div class="search-box-container">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchBox" class="search-box" placeholder="Search for address or place...">
                        </div>

                        <div class="map-container">
                            <div id="map"></div>
                        </div>

                        <div class="coordinates-grid">
                            <div class="form-group">
                                <label for="lat" class="form-label">Latitude *</label>
                                <input type="number" step="any" id="lat" class="form-input" required readonly>
                            </div>
                            <div class="form-group">
                                <label for="lng" class="form-label">Longitude *</label>
                                <input type="number" step="any" id="lng" class="form-input" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="address" class="form-label">Full Address *</label>
                            <textarea id="address" class="form-textarea" rows="3" placeholder="Full address will be auto-filled from map" required></textarea>
                        </div>
                    </div>

                    <div class="form-grid" style="margin-top: 25px;">
                        <div class="form-group">
                            <label for="landmark" class="form-label">Landmark (Optional)</label>
                            <input type="text" id="landmark" class="form-input" placeholder="Near a known building or establishment">
                            <div class="form-helper">Helpful for delivery drivers</div>
                        </div>

                        <div class="form-group">
                            <label for="locality" class="form-label">Locality *</label>
                            <input type="text" id="locality" class="form-input" placeholder="City, Province, Country" required>
                            <div class="form-helper">Your city and province</div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 25px;">
                        <label for="addressType" class="form-label">Address Type</label>
                        <select id="addressType" class="form-select" style="max-width: 250px;">
                            <option value="Home">🏠 Home</option>
                            <option value="Work">🏢 Work</option>
                            <option value="Other">📍 Other</option>
                        </select>
                        <div class="form-helper">Select the type of this address</div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Notification Preferences</h2>
                            <p class="section-subtitle">Choose what notifications you want to receive</p>
                        </div>
                    </div>

                    <div class="notification-settings">
                        <div class="notification-item">
                            <div class="notification-info">
                                <div class="notification-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">New Restaurant Arrivals</div>
                                    <div class="notification-desc">Get notified when new restaurants join LalaGO</div>
                                </div>
                            </div>
                            <label class="notification-toggle">
                                <input type="checkbox" id="newArrivals" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <div class="notification-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">Order Status Updates</div>
                                    <div class="notification-desc">Real-time updates on your order status</div>
                                </div>
                            </div>
                            <label class="notification-toggle">
                                <input type="checkbox" id="orderUpdates" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <div class="notification-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">Promotions & Discounts</div>
                                    <div class="notification-desc">Special offers and discount notifications</div>
                                </div>
                            </div>
                            <label class="notification-toggle">
                                <input type="checkbox" id="promotions" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div class="notification-item">
                            <div class="notification-info">
                                <div class="notification-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="notification-text">
                                    <div class="notification-title">New Messages</div>
                                    <div class="notification-desc">Notifications about new messages from restaurants</div>
                                </div>
                            </div>
                            <label class="notification-toggle">
                                <input type="checkbox" id="pushNewMessages" checked>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="action-btn primary" id="saveBtn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="profile.php" class="action-btn secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
    // Initialize variables
    let map = null;
    let marker = null;
    let geocoder = null;
    let selectedFile = null;
    let uploadTask = null;
    let isFirebaseInitialized = false;
    const uid = "<?= $uid ?>";
    const defaultLocation = { lat: 12.8797, lng: 121.7740 }; // Philippines center

    // DOM Elements
    const saveBtn = document.getElementById('saveBtn');
    const uploadArea = document.getElementById('uploadArea');
    const browseLink = document.getElementById('browseLink');
    const fileInput = document.getElementById('profilePictureUpload');
    const uploadPreview = document.getElementById('uploadPreview');
    const progressContainer = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const offlineIndicator = document.getElementById('offlineIndicator');

    // Initialize Google Map
    function initMap() {
        console.log("Initializing Google Map...");
        
        // Check if Google Maps is loaded
        if (!window.google || !window.google.maps) {
            console.error("Google Maps API not loaded!");
            showError("Google Maps failed to load. Please check your API key.");
            return;
        }
        
        try {
            geocoder = new google.maps.Geocoder();
            
            // Create map
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 13,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: true,
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "off" }]
                    }
                ]
            });
            
            console.log("Map created successfully");
            
            // Add search box functionality
            const searchBox = new google.maps.places.SearchBox(document.getElementById('searchBox'));
            
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });
            
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;
                
                const place = places[0];
                if (!place.geometry) return;
                
                updateMarker(place.geometry.location.lat(), place.geometry.location.lng());
                reverseGeocode(place.geometry.location.lat(), place.geometry.location.lng());
            });
            
            // Add click listener to map
            map.addListener('click', function(event) {
                updateMarker(event.latLng.lat(), event.latLng.lng());
                reverseGeocode(event.latLng.lat(), event.latLng.lng());
            });
            
            // Load user data
            initializeFirebaseAndLoadData();
            
        } catch (error) {
            console.error("Error initializing map:", error);
            showError("Error initializing map: " + error.message);
        }
    }

    // Update marker position
    function updateMarker(lat, lng) {
        console.log("Updating marker to:", lat, lng);
        
        // Remove existing marker
        if (marker) {
            marker.setMap(null);
        }
        
        // Update coordinate fields
        document.getElementById('lat').value = lat.toFixed(6);
        document.getElementById('lng').value = lng.toFixed(6);
        
        // Create new marker with custom icon
        marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: "Drag to adjust location",
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: "#FF3B30",
                fillOpacity: 1,
                strokeColor: "#FFFFFF",
                strokeWeight: 2,
            }
        });
        
        // Center map on marker
        map.setCenter({ lat: lat, lng: lng });
        map.setZoom(16);
        
        // Add drag listener
        marker.addListener('dragend', function(event) {
            const position = marker.getPosition();
            document.getElementById('lat').value = position.lat().toFixed(6);
            document.getElementById('lng').value = position.lng().toFixed(6);
            reverseGeocode(position.lat(), position.lng());
        });
    }

    // Reverse geocode coordinates to address
    function reverseGeocode(lat, lng) {
        const latlng = { lat: lat, lng: lng };
        
        geocoder.geocode({ location: latlng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById('address').value = results[0].formatted_address;
                
                // Extract locality information
                let locality = '';
                const addressComponents = results[0].address_components;
                
                for (const component of addressComponents) {
                    if (component.types.includes('locality') || 
                        component.types.includes('administrative_area_level_1') || 
                        component.types.includes('country')) {
                        if (locality) locality += ', ';
                        locality += component.long_name;
                    }
                }
                
                document.getElementById('locality').value = locality || results[0].formatted_address;
            } else {
                console.warn("Geocoder failed due to: " + status);
            }
        });
    }

    // Use current location
    document.getElementById('useCurrentLocation').addEventListener('click', function() {
        if (!navigator.geolocation) {
            showError("Geolocation is not supported by your browser");
            return;
        }
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
        btn.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                updateMarker(position.coords.latitude, position.coords.longitude);
                reverseGeocode(position.coords.latitude, position.coords.longitude);
                btn.innerHTML = originalText;
                btn.disabled = false;
                showSuccess("Location detected successfully!");
            },
            function(error) {
                showError("Unable to retrieve your location: " + error.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // Reset location
    document.getElementById('resetLocation').addEventListener('click', function() {
        updateMarker(defaultLocation.lat, defaultLocation.lng);
        document.getElementById('address').value = '';
        document.getElementById('locality').value = '';
        showSuccess("Map reset to default location");
    });

    // ===== FILE UPLOAD HANDLING =====
    // Click browse link
    browseLink.addEventListener('click', () => fileInput.click());

    // Click upload area
    uploadArea.addEventListener('click', () => fileInput.click());

    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        selectedFile = file;
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(selectedFile.type)) {
            showError('Please select a valid image file (JPG, PNG, GIF)');
            fileInput.value = '';
            selectedFile = null;
            return;
        }
        
        // Validate file size (2MB max)
        if (selectedFile.size > 2 * 1024 * 1024) {
            showError('File size too large. Maximum size is 2MB.');
            fileInput.value = '';
            selectedFile = null;
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            uploadPreview.innerHTML = `
                <div class="upload-preview">
                    <img src="${e.target.result}" alt="Preview" class="preview-image">
                    <div class="preview-info">
                        <div class="preview-name">${selectedFile.name}</div>
                        <div class="preview-size">${(selectedFile.size / 1024).toFixed(1)} KB</div>
                        <div class="preview-actions">
                            <button type="button" class="preview-btn remove" onclick="removeUpload()">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
        };
        reader.readAsDataURL(selectedFile);
        
        // Hide progress bar if it was showing from previous upload
        progressContainer.style.display = 'none';
        progressFill.style.width = '0%';
        progressText.textContent = 'Uploading: 0%';
    }

    // Function to remove uploaded file
    function removeUpload() {
        fileInput.value = '';
        selectedFile = null;
        uploadPreview.innerHTML = '';
        progressContainer.style.display = 'none';
    }

    // Function to upload image to Firebase Storage
    function uploadProfilePicture() {
        return new Promise((resolve, reject) => {
            if (!selectedFile) {
                resolve(null);
                return;
            }
            
            // Show progress bar
            progressContainer.style.display = 'block';
            progressFill.style.width = '0%';
            progressText.textContent = 'Uploading: 0%';
            
            try {
                // Get Firebase storage instance
                const storage = firebase.storage();
                const storageRef = storage.ref();
                
                // Generate unique filename
                const fileExtension = selectedFile.name.split('.').pop();
                const fileName = `profile_${uid}_${Date.now()}.${fileExtension}`;
                const folderPath = 'profile-pictures/'; // Organized folder
                const fileRef = storageRef.child(folderPath + fileName);
                
                console.log("Uploading to:", folderPath + fileName);
                
                // Upload file
                uploadTask = fileRef.put(selectedFile);
                
                // Monitor upload progress
                uploadTask.on('state_changed',
                    (snapshot) => {
                        // Update progress bar
                        const progress = (snapshot.bytesTransferred / snapshot.totalBytes) * 100;
                        progressFill.style.width = progress + '%';
                        progressText.textContent = `Uploading: ${Math.round(progress)}%`;
                    },
                    (error) => {
                        console.error('Upload error:', error);
                        progressContainer.style.display = 'none';
                        reject(error);
                    },
                    () => {
                        // Upload completed successfully
                        uploadTask.snapshot.ref.getDownloadURL().then((downloadURL) => {
                            console.log('File uploaded to Firebase Storage:', downloadURL);
                            progressContainer.style.display = 'none';
                            
                            // Update preview with success message
                            uploadPreview.innerHTML = `
                                <div class="upload-preview">
                                    <img src="${downloadURL}" alt="Uploaded" class="preview-image">
                                    <div class="preview-info">
                                        <div class="preview-name">${selectedFile.name}</div>
                                        <div style="color: var(--accent-green); font-weight: 600; margin-bottom: 5px;">
                                            <i class="fas fa-check-circle"></i> Uploaded Successfully
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--medium-gray);">
                                            Image saved to Firebase Storage
                                        </div>
                                    </div>
                                </div>
                            `;
                            resolve(downloadURL);
                        });
                    }
                );
            } catch (error) {
                console.error("Storage error:", error);
                reject(error);
            }
        });
    }

    // ===== INITIALIZE FIREBASE AND LOAD DATA =====
    function initializeFirebaseAndLoadData() {
        console.log("Checking Firebase initialization...");
        
        // Wait for Firebase to be fully initialized
        const checkFirebase = setInterval(() => {
            if (typeof firebase !== 'undefined' && 
                firebase.apps.length > 0 && 
                typeof firebase.firestore !== 'undefined' &&
                typeof firebase.auth !== 'undefined') {
                
                clearInterval(checkFirebase);
                isFirebaseInitialized = true;
                console.log("Firebase is fully initialized");
                loadUserData();
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(() => {
            if (!isFirebaseInitialized) {
                clearInterval(checkFirebase);
                console.error("Firebase initialization timeout");
                showError("Firebase is taking too long to initialize. Please refresh the page.");
            }
        }, 5000);
    }

    // ===== LOAD USER DATA FROM FIRESTORE =====
    function loadUserData() {
        console.log("Loading user data for UID:", uid);
        
        // Get Firestore instance
        const db = firebase.firestore();
        const userDocRef = db.collection("users").doc(uid);
        
        userDocRef.get().then(doc => {
            if (doc.exists) {
                const u = doc.data();
                console.log("Found user document:", u);
                populateForm(u);
                showSuccess("Profile loaded successfully!");
            } else {
                console.log("No user document found, will create on save");
                showInfo("Welcome! Complete your profile to start ordering.");
            }
        }).catch(err => {
            console.error("Error loading user data:", err);
            
            if (err.code === 'unavailable') {
                showInfo("You appear to be offline. Some features may be limited.");
            }
        });
    }

    // Populate form with user data
    function populateForm(u) {
        // Personal info
        document.getElementById('firstName').value = u.firstName || "";
        document.getElementById('lastName').value = u.lastName || "";
        
        // Format phone number for display
        let phoneNumber = u.phoneNumber || u.phone || "";
        document.getElementById('phoneNumber').value = phoneNumber;
        
        // Handle profile picture
        const profilePicUrl = u.profilePictureURL || "";
        document.getElementById('profilePictureURL').value = profilePicUrl;
        
        // Show current profile picture if it exists
        if (profilePicUrl && profilePicUrl.startsWith('http')) {
            uploadPreview.innerHTML = `
                <div class="upload-preview">
                    <img src="${profilePicUrl}" alt="Current Profile" class="preview-image">
                    <div class="preview-info">
                        <div class="preview-name">Current Profile Picture</div>
                        <div style="color: var(--medium-gray); font-size: 0.9rem;">
                            Click "Browse files" above to change
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Location
        let userLat = defaultLocation.lat;
        let userLng = defaultLocation.lng;
        
        if (u.location) {
            if (u.location.latitude && u.location.longitude) {
                userLat = u.location.latitude;
                userLng = u.location.longitude;
            } else if (u.location.lat && u.location.lng) {
                userLat = u.location.lat;
                userLng = u.location.lng;
            }
        } else if (u.shippingAddress && u.shippingAddress.length > 0) {
            const addr = u.shippingAddress[0];
            if (addr.location) {
                userLat = addr.location.latitude || addr.location.lat || defaultLocation.lat;
                userLng = addr.location.longitude || addr.location.lng || defaultLocation.lng;
            }
        }
        
        updateMarker(userLat, userLng);
        
        // Address
        const addr = u.shippingAddress && u.shippingAddress.length > 0 ? u.shippingAddress[0] : {};
        document.getElementById('address').value = addr.address || u.address || "";
        document.getElementById('landmark').value = addr.landmark || "";
        document.getElementById('locality').value = addr.locality || "";
        document.getElementById('addressType').value = addr.addressAs || "Home";
        
        // Settings
        if (u.settings) {
            document.getElementById('newArrivals').checked = u.settings.newArrivals !== false;
            document.getElementById('orderUpdates').checked = u.settings.orderUpdates !== false;
            document.getElementById('promotions').checked = u.settings.promotions !== false;
            document.getElementById('pushNewMessages').checked = u.settings.pushNewMessages !== false;
        }
        
        // Reverse geocode if needed
        if ((userLat !== defaultLocation.lat || userLng !== defaultLocation.lng) && !addr.address) {
            setTimeout(() => reverseGeocode(userLat, userLng), 1000);
        }
    }

    // ===== VALIDATE PHONE NUMBER =====
    function validatePhoneNumber(phone) {
        const cleanedPhone = phone.replace(/\s+/g, '').replace(/[-()]/g, '');
        
        if (/^09\d{9}$/.test(cleanedPhone)) {
            return {
                isValid: true,
                formatted: cleanedPhone,
                international: '+63' + cleanedPhone.substring(1)
            };
        }
        
        if (/^\+63\d{10}$/.test(cleanedPhone)) {
            return {
                isValid: true,
                formatted: '0' + cleanedPhone.substring(3),
                international: cleanedPhone
            };
        }
        
        if (/^63\d{10}$/.test(cleanedPhone)) {
            return {
                isValid: true,
                formatted: '0' + cleanedPhone.substring(2),
                international: '+' + cleanedPhone
            };
        }
        
        return {
            isValid: false,
            message: "Please enter a valid Philippine phone number (09xxxxxxxxx or +639xxxxxxxxx)"
        };
    }

    // Real-time phone number validation
    document.getElementById('phoneNumber').addEventListener('input', function(e) {
        const phone = e.target.value.trim();
        const phoneError = document.getElementById('phoneError');
        
        if (!phone) {
            phoneError.style.display = 'none';
            return;
        }
        
        const validation = validatePhoneNumber(phone);
        if (!validation.isValid && phone.length >= 4) {
            phoneError.textContent = validation.message;
            phoneError.style.display = 'block';
            this.style.borderColor = 'var(--primary-red)';
        } else {
            phoneError.style.display = 'none';
            this.style.borderColor = '';
        }
    });

    // ===== SAVE PROFILE =====
    document.getElementById('settingsForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        console.log("Saving profile...");
        
        if (!isFirebaseInitialized) {
            showError("Firebase is not initialized. Please check your connection.");
            return;
        }
        
        // Validate location
        const latValue = parseFloat(document.getElementById('lat').value);
        const lngValue = parseFloat(document.getElementById('lng').value);
        
        if (isNaN(latValue) || isNaN(lngValue)) {
            showError("Please set your location on the map");
            return;
        }
        
        // Validate phone number
        const phoneNumber = document.getElementById('phoneNumber').value.trim();
        const phoneValidation = validatePhoneNumber(phoneNumber);
        
        if (!phoneValidation.isValid) {
            showError(phoneValidation.message);
            return;
        }
        
        // Get current user
        const currentUser = firebase.auth().currentUser;
        if (!currentUser) {
            showError("You must be logged in to save profile");
            return;
        }
        
        // Check online status
        const isOnline = navigator.onLine;
        if (!isOnline && selectedFile) {
            showError("Cannot upload image while offline. Please connect to the internet.");
            return;
        }
        
        // Disable submit button
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;
        
        try {
            let profilePictureURL = document.getElementById('profilePictureURL').value.trim();
            
            // Upload image if selected
            if (selectedFile) {
                try {
                    console.log("Uploading profile picture...");
                    const downloadURL = await uploadProfilePicture();
                    if (downloadURL) {
                        profilePictureURL = downloadURL;
                        document.getElementById('profilePictureURL').value = downloadURL;
                    }
                } catch (uploadError) {
                    console.error('Image upload failed:', uploadError);
                    showError('Image upload failed. Please try again.');
                    saveBtn.innerHTML = originalText;
                    saveBtn.disabled = false;
                    return;
                }
            }
            
            // Get Firestore instance
            const db = firebase.firestore();
            const userDocRef = db.collection("users").doc(uid);
            
            // Prepare user data
            const userData = {
                firstName: document.getElementById('firstName').value.trim(),
                lastName: document.getElementById('lastName').value.trim(),
                phoneNumber: phoneValidation.international,
                phoneNumberDisplay: phoneValidation.formatted,
                profilePictureURL: profilePictureURL,
                email: currentUser.email || "",
                location: {
                    latitude: latValue,
                    longitude: lngValue
                },
                shippingAddress: [{
                    address: document.getElementById('address').value.trim(),
                    landmark: document.getElementById('landmark').value.trim(),
                    locality: document.getElementById('locality').value.trim(),
                    addressAs: document.getElementById('addressType').value,
                    location: {
                        latitude: latValue,
                        longitude: lngValue
                    },
                    isDefault: true
                }],
                settings: {
                    newArrivals: document.getElementById('newArrivals').checked,
                    orderUpdates: document.getElementById('orderUpdates').checked,
                    promotions: document.getElementById('promotions').checked,
                    pushNewMessages: document.getElementById('pushNewMessages').checked
                },
                updatedAt: firebase.firestore.FieldValue.serverTimestamp(),
                lastUpdated: new Date().toISOString()
            };
            
            // Save to Firestore
            console.log("Saving to Firestore:", userData);
            await userDocRef.set(userData, { merge: true });
            
            showSuccess("Profile saved successfully!");
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = "profile.php";
            }, 2000);
            
        } catch (err) {
            console.error("Save error:", err);
            
            let errorMessage = "Failed to save profile: ";
            if (err.message.includes("permission")) {
                errorMessage = "You don't have permission to save this profile.";
            } else if (err.message.includes("network")) {
                errorMessage = "Network error. Please check your connection.";
            } else {
                errorMessage += err.message;
            }
            
            showError(errorMessage);
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });

    // ===== UTILITY FUNCTIONS =====
    function showError(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--primary-red);
            color: white;
            padding: 15px 20px;
            border-radius: var(--radius-medium);
            box-shadow: var(--shadow-medium);
            z-index: 1002;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease-out;
        `;
        notification.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    function showSuccess(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--accent-green);
            color: white;
            padding: 15px 20px;
            border-radius: var(--radius-medium);
            box-shadow: var(--shadow-medium);
            z-index: 1002;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease-out;
        `;
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    function showInfo(message) {
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--accent-blue);
            color: white;
            padding: 15px 20px;
            border-radius: var(--radius-medium);
            box-shadow: var(--shadow-medium);
            z-index: 1002;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease-out;
        `;
        notification.innerHTML = `
            <i class="fas fa-info-circle"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 4000);
    }

    // ===== INITIALIZE =====
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM loaded, initializing...");
        
        // Initialize the map
        initMap();
        
        // Online/offline detection
        window.addEventListener('online', function() {
            console.log("Browser is online");
            offlineIndicator.style.display = 'none';
            showSuccess("You are back online!");
        });
        
        window.addEventListener('offline', function() {
            console.log("Browser is offline");
            offlineIndicator.style.display = 'flex';
        });
        
        // Check initial online status
        if (!navigator.onLine) {
            offlineIndicator.style.display = 'flex';
        }
    });
    </script>
</body>
</html>
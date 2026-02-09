<?php
// users/profile.php
include_once '../inc/firebase.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - LalaGO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

        /* ================== HEADER WITH BACK BUTTON ================== */
        .profile-header-bar {
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

        .back-button i {
            font-size: 1.1rem;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            flex: 1;
        }

        /* ================== MAIN CONTENT ================== */
        .profile-content {
            padding-top: 90px;
            padding-bottom: 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* ================== PROFILE HERO ================== */
        .profile-hero {
            background: linear-gradient(135deg, rgba(255, 59, 48, 0.1), rgba(255, 149, 0, 0.1));
            border-radius: var(--radius-large);
            padding: 40px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 40px;
            box-shadow: var(--shadow-soft);
        }

        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            flex-shrink: 0;
        }

        .profile-avatar {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 6px solid var(--white);
            box-shadow: var(--shadow-medium);
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            display: block;
        }

        .avatar-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .avatar-edit {
            position: absolute;
            bottom: 10px;
            right: 10px;
            width: 45px;
            height: 45px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-red);
            cursor: pointer;
            transition: var(--transition-smooth);
            box-shadow: var(--shadow-soft);
            border: 2px solid var(--primary-red);
            z-index: 2;
        }

        .avatar-edit:hover {
            transform: scale(1.1);
            background: var(--primary-red);
            color: var(--white);
        }

        .user-details {
            flex: 1;
            min-width: 300px;
        }

        .user-name {
            font-family: 'Poppins', sans-serif;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .user-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .user-badge {
            padding: 8px 20px;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .user-badge.secondary {
            background: var(--light-gray);
            color: var(--dark-gray);
        }

        .user-badge i {
            font-size: 0.8rem;
        }

        .user-contact {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--medium-gray);
            font-size: 1rem;
        }

        .contact-item i {
            color: var(--primary-red);
            width: 20px;
        }

        /* ================== WALLET CARD ================== */
        .wallet-card {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            border-radius: var(--radius-large);
            padding: 30px;
            color: var(--white);
            box-shadow: var(--shadow-medium);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .wallet-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }

        .wallet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .wallet-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            font-weight: 600;
            opacity: 0.9;
        }

        .wallet-balance {
            font-family: 'Poppins', sans-serif;
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 30px;
            display: flex;
            align-items: baseline;
            gap: 10px;
        }

        .wallet-balance span {
            font-size: 1.5rem;
            opacity: 0.8;
        }

        .wallet-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .wallet-btn {
            padding: 14px 28px;
            border: none;
            border-radius: var(--radius-medium);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition-smooth);
            font-size: 1rem;
        }

        .wallet-btn.primary {
            background: linear-gradient(135deg, var(--accent-green), #2AA952);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(52, 199, 89, 0.3);
        }

        .wallet-btn.secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .wallet-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        /* ================== INFO SECTIONS ================== */
        .info-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-section {
            background: var(--white);
            border-radius: var(--radius-large);
            padding: 30px;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
        }

        .section-header i {
            font-size: 1.5rem;
            color: var(--primary-red);
            width: 40px;
            height: 40px;
            background: rgba(255, 59, 48, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-header h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-gray);
            flex: 1;
        }

        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .info-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 1rem;
        }

        .info-value {
            color: var(--medium-gray);
            text-align: right;
            max-width: 60%;
            font-size: 1rem;
        }

        .info-value.highlight {
            color: var(--primary-red);
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.active {
            background: rgba(52, 199, 89, 0.1);
            color: var(--accent-green);
        }

        .status-badge.inactive {
            background: rgba(255, 59, 48, 0.1);
            color: var(--primary-red);
        }

        /* ================== ADDRESS SECTION ================== */
        .address-section {
            background: var(--white);
            border-radius: var(--radius-large);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
        }

        .address-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .address-card {
            background: var(--light-gray);
            border: 2px solid transparent;
            border-radius: var(--radius-medium);
            padding: 25px;
            transition: var(--transition-smooth);
            position: relative;
        }

        .address-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .address-card.default {
            border-color: var(--accent-green);
            background: rgba(52, 199, 89, 0.05);
        }

        .address-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .address-title {
            font-weight: 700;
            color: var(--dark-gray);
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .address-title i {
            color: var(--primary-red);
        }

        .address-badge {
            padding: 6px 16px;
            background: var(--accent-green);
            color: var(--white);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .address-details {
            color: var(--medium-gray);
            line-height: 1.6;
        }

        .address-details p {
            margin-bottom: 8px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .address-details i {
            color: var(--primary-red);
            margin-top: 3px;
            flex-shrink: 0;
        }

        .no-address {
            text-align: center;
            padding: 60px 20px;
            color: var(--medium-gray);
        }

        .no-address i {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        .no-address h3 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark-gray);
        }

        /* ================== SETTINGS SECTION ================== */
        .settings-section {
            background: var(--white);
            border-radius: var(--radius-large);
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-soft);
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .setting-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 18px;
            background: var(--light-gray);
            border-radius: var(--radius-medium);
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .setting-item:hover {
            background: rgba(255, 59, 48, 0.05);
            transform: translateX(5px);
        }

        .setting-item input[type="checkbox"] {
            width: 24px;
            height: 24px;
            accent-color: var(--primary-red);
            cursor: pointer;
            flex-shrink: 0;
        }

        .setting-label {
            font-weight: 500;
            color: var(--dark-gray);
            cursor: pointer;
            flex: 1;
            font-size: 1rem;
        }

        /* ================== ACTION BUTTONS ================== */
        .action-buttons {
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
            color: var(--accent-blue);
            border: 2px solid var(--accent-blue);
            box-shadow: 0 4px 15px rgba(0, 122, 255, 0.1);
        }

        .action-btn.success {
            background: linear-gradient(135deg, var(--accent-green), #2AA952);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(52, 199, 89, 0.2);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .action-btn.primary:hover {
            box-shadow: 0 8px 25px rgba(255, 59, 48, 0.3);
        }

        /* ================== MODAL ================== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 20px;
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        .modal {
            background: var(--white);
            border-radius: var(--radius-large);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: var(--shadow-medium);
            animation: modalSlideIn 0.3s ease-out;
        }

        .modal-header {
            margin-bottom: 25px;
        }

        .modal-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 10px;
        }

        .modal-subtitle {
            color: var(--medium-gray);
            font-size: 1rem;
        }

        .modal-body {
            margin-bottom: 30px;
        }

        .modal-input {
            width: 100%;
            padding: 18px;
            border: 2px solid var(--light-gray);
            border-radius: var(--radius-medium);
            font-size: 1.1rem;
            transition: var(--transition-smooth);
            font-family: inherit;
        }

        .modal-input:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(255, 59, 48, 0.1);
        }

        .modal-footer {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .modal-btn {
            padding: 14px 30px;
            border: none;
            border-radius: var(--radius-medium);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition-smooth);
            font-size: 1rem;
        }

        .modal-btn.primary {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-orange));
            color: var(--white);
        }

        .modal-btn.secondary {
            background: var(--light-gray);
            color: var(--medium-gray);
        }

        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* ================== LOADING & ERROR STATES ================== */
        .loading-container {
            text-align: center;
            padding: 100px 20px;
        }

        .loading-spinner {
            width: 70px;
            height: 70px;
            border: 5px solid var(--light-gray);
            border-top-color: var(--primary-red);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }

        .loading-text {
            color: var(--medium-gray);
            font-size: 1.2rem;
            font-weight: 500;
        }

        .error-container {
            background: linear-gradient(135deg, #FFF5F5, #FFF);
            border: 2px solid #FED7D7;
            border-radius: var(--radius-large);
            padding: 60px 40px;
            text-align: center;
            margin: 40px auto;
            max-width: 600px;
        }

        .error-icon {
            font-size: 4rem;
            color: var(--primary-red);
            margin-bottom: 25px;
        }

        .error-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            color: var(--dark-gray);
            margin-bottom: 15px;
        }

        .error-message {
            color: var(--medium-gray);
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        /* ================== ANIMATIONS ================== */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-30px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .slide-in {
            animation: slideInUp 0.5s ease-out;
        }

        /* ================== RESPONSIVE DESIGN ================== */
        @media (max-width: 1024px) {
            .info-sections {
                grid-template-columns: 1fr;
            }
            
            .address-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .profile-header-bar {
                height: 60px;
                padding: 0 15px;
            }
            
            .back-button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .profile-content {
                padding-top: 80px;
            }
            
            .profile-hero {
                padding: 30px 20px;
                gap: 30px;
            }
            
            .avatar-container {
                width: 120px;
                height: 120px;
            }
            
            .avatar-text {
                font-size: 2.5rem;
            }
            
            .user-name {
                font-size: 1.8rem;
            }
            
            .wallet-balance {
                font-size: 2.5rem;
            }
            
            .info-section,
            .address-section,
            .settings-section {
                padding: 25px 20px;
            }
            
            .action-btn {
                min-width: 100%;
            }
            
            .action-buttons {
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .profile-hero {
                flex-direction: column;
                text-align: center;
            }
            
            .user-contact {
                align-items: center;
            }
            
            .wallet-actions {
                flex-direction: column;
            }
            
            .wallet-btn {
                width: 100%;
                justify-content: center;
            }
            
            .address-grid {
                grid-template-columns: 1fr;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .modal {
                padding: 30px 20px;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header with Back Button -->
    <header class="profile-header-bar">
        <button class="back-button" onclick="goBack()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <h1 class="page-title">My Profile</h1>
        <div style="width: 120px;"></div> <!-- Spacer for alignment -->
    </header>

    <!-- Main Content -->
    <main class="profile-content">
        <div class="container">
            <!-- Loading State -->
            <div id="loadingState" class="loading-container">
                <div class="loading-spinner"></div>
                <p class="loading-text">Loading your profile...</p>
            </div>

            <!-- Profile Content -->
            <div id="profileContent" style="display: none;"></div>
        </div>
    </main>

    <!-- Modal for Adding Funds -->
    <div id="fundsModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add Funds to Wallet</h3>
                <p class="modal-subtitle">Enter the amount you want to add</p>
            </div>
            <div class="modal-body">
                <div style="position: relative;">
                    <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 1.5rem; color: var(--primary-red);">₱</span>
                    <input type="number" 
                           id="fundsAmount" 
                           class="modal-input" 
                           placeholder="Enter amount"
                           min="10"
                           max="10000"
                           step="10"
                           value="100"
                           style="padding-left: 50px;">
                </div>
                <p style="margin-top: 10px; color: var(--medium-gray); font-size: 0.9rem;">
                    Minimum: ₱10 | Maximum: ₱10,000
                </p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn secondary" onclick="closeFundsModal()">Cancel</button>
                <button class="modal-btn primary" onclick="confirmAddFunds()">Add Funds</button>
            </div>
        </div>
    </div>

    <script>
    // Global variables
    let currentUser = null;
    let userProfileData = null;

    // Navigation
    function goBack() {
        if (document.referrer && document.referrer.includes(window.location.hostname)) {
            window.history.back();
        } else {
            window.location.href = '../index.php';
        }
    }

    // Format date
    function formatDate(timestamp) {
        if (!timestamp) return 'Not available';
        try {
            const date = timestamp.toDate ? timestamp.toDate() : new Date(timestamp);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return 'Invalid date';
        }
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2
        }).format(amount || 0);
    }

    // Show loading state
    function showLoading() {
        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('profileContent').style.display = 'none';
    }

    // Show content
    function showContent() {
        document.getElementById('loadingState').style.display = 'none';
        const profileContent = document.getElementById('profileContent');
        profileContent.style.display = 'block';
        profileContent.classList.add('slide-in');
    }

    // Get profile picture URL - SIMPLIFIED VERSION
    function getProfilePictureUrl(userData) {
        // Check for profilePictureURL field (your existing field name)
        if (userData.profilePictureURL) {
            console.log('Found profile picture:', userData.profilePictureURL);
            return userData.profilePictureURL;
        }
        
        // Check Firebase user photoURL as fallback
        if (currentUser && currentUser.photoURL) {
            console.log('Using Firebase photoURL:', currentUser.photoURL);
            return currentUser.photoURL;
        }
        
        console.log('No profile picture found');
        return null;
    }

    // Generate profile HTML - SIMPLIFIED VERSION THAT MATCHES YOUR ORIGINAL
    function generateProfileHTML(user, userData) {
        // Get profile picture URL
        const profilePicUrl = getProfilePictureUrl(userData);
        const fallbackLetter = (userData.firstName?.[0] || user.email?.[0] || 'U').toUpperCase();
        
        // Generate shipping addresses HTML
        let shippingAddressesHTML = '';
        if (userData.shippingAddress && userData.shippingAddress.length > 0) {
            userData.shippingAddress.forEach((address, index) => {
                shippingAddressesHTML += `
                    <div class="address-card ${address.isDefault ? 'default' : ''}">
                        <div class="address-header">
                            <div class="address-title">
                                <i class="fas fa-map-marker-alt"></i>
                                ${address.addressAs || 'Address ' + (index + 1)}
                            </div>
                            ${address.isDefault ? '<div class="address-badge">Default</div>' : ''}
                        </div>
                        <div class="address-details">
                            <p><i class="fas fa-road"></i> ${address.address || 'No address specified'}</p>
                            <p><i class="fas fa-city"></i> ${address.locality || 'No locality specified'}</p>
                            ${address.landmark ? `<p><i class="fas fa-landmark"></i> ${address.landmark}</p>` : ''}
                            ${address.location ? `
                                <p><i class="fas fa-location-dot"></i> 
                                    ${address.location.latitude}, ${address.location.longitude}
                                </p>
                            ` : ''}
                        </div>
                    </div>
                `;
            });
        } else {
            shippingAddressesHTML = `
                <div class="no-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>No Shipping Addresses</h3>
                    <p>Add a shipping address to get started with deliveries</p>
                </div>
            `;
        }

        // Generate settings HTML
        let settingsHTML = '';
        if (userData.settings) {
            settingsHTML = `
                <div class="settings-section">
                    <div class="section-header">
                        <i class="fas fa-bell"></i>
                        <h2>Notification Settings</h2>
                    </div>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <input type="checkbox" id="newArrivals" ${userData.settings.newArrivals ? 'checked' : ''} disabled>
                            <label for="newArrivals" class="setting-label">New Arrivals</label>
                        </div>
                        <div class="setting-item">
                            <input type="checkbox" id="orderUpdates" ${userData.settings.orderUpdates ? 'checked' : ''} disabled>
                            <label for="orderUpdates" class="setting-label">Order Updates</label>
                        </div>
                        <div class="setting-item">
                            <input type="checkbox" id="promotions" ${userData.settings.promotions ? 'checked' : ''} disabled>
                            <label for="promotions" class="setting-label">Promotions</label>
                        </div>
                        <div class="setting-item">
                            <input type="checkbox" id="pushNewMessages" ${userData.settings.pushNewMessages ? 'checked' : ''} disabled>
                            <label for="pushNewMessages" class="setting-label">New Messages</label>
                        </div>
                    </div>
                </div>
            `;
        }

        return `
            <div class="slide-in">
                <!-- Profile Hero -->
                <div class="profile-hero">
                    <div class="avatar-container">
                        ${profilePicUrl ? 
                            `<img src="${profilePicUrl}" alt="Profile" class="profile-avatar" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">` : 
                            ''
                        }
                        <div class="avatar-text" style="${profilePicUrl ? 'display: none;' : 'display: flex;'}">
                            ${fallbackLetter}
                        </div>
                        <div class="avatar-edit" onclick="window.location.href='settings.php'">
                            <i class="fas fa-pencil-alt"></i>
                        </div>
                    </div>
                    
                    <div class="user-details">
                        <h1 class="user-name">${userData.firstName || ''} ${userData.lastName || ''}</h1>
                        <div class="user-badges">
                            <div class="user-badge">
                                <i class="fas fa-user"></i> ${userData.role || 'customer'}
                            </div>
                            <div class="user-badge secondary">
                                <i class="fas fa-user-check"></i> 
                                <span class="${userData.active ? 'status-active' : 'status-inactive'}">
                                    ${userData.active ? 'Active' : 'Inactive'}
                                </span>
                            </div>
                        </div>
                        
                        <div class="user-contact">
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>${user.email}</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span>${userData.phoneNumber || userData.phone || 'Not provided'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wallet Card -->
                <div class="wallet-card">
                    <div class="wallet-header">
                        <div class="wallet-title">Your Wallet Balance</div>
                        <div class="wallet-title">LalaGO Wallet</div>
                    </div>
                    <div class="wallet-balance">
                        ${formatCurrency(userData.wallet_amount)}
                    </div>
                    <div class="wallet-actions">
                        <button class="wallet-btn primary" onclick="showFundsModal()">
                            <i class="fas fa-plus-circle"></i> Add Funds
                        </button>
                        <button class="wallet-btn secondary" onclick="viewTransactionHistory()">
                            <i class="fas fa-history"></i> View History
                        </button>
                    </div>
                </div>

                <!-- Info Sections -->
                <div class="info-sections">
                    <div class="info-section">
                        <div class="section-header">
                            <i class="fas fa-user-circle"></i>
                            <h2>Account Information</h2>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">User ID</span>
                                <span class="info-value">${user.uid.substring(0, 12)}...</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Account Created</span>
                                <span class="info-value">${formatDate(userData.createdAt)}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Online</span>
                                <span class="info-value">${formatDate(userData.lastOnlineTimestamp)}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Member Since</span>
                                <span class="info-value highlight">${userData.createdAt ? 
                                    new Date(userData.createdAt.toDate()).getFullYear() : 
                                    'N/A'
                                }</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-section">
                        <div class="section-header">
                            <i class="fas fa-location-dot"></i>
                            <h2>Location Details</h2>
                        </div>
                        <div class="info-grid">
                            ${userData.location ? `
                                <div class="info-item">
                                    <span class="info-label">Latitude</span>
                                    <span class="info-value">${userData.location.latitude || 'Not set'}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Longitude</span>
                                    <span class="info-value">${userData.location.longitude || 'Not set'}</span>
                                </div>
                                ${userData.location.accuracy ? `
                                    <div class="info-item">
                                        <span class="info-label">Accuracy</span>
                                        <span class="info-value">${userData.location.accuracy}m</span>
                                    </div>
                                ` : ''}
                            ` : `
                                <div class="info-item">
                                    <span class="info-label">Status</span>
                                    <span class="info-value">Location not set</span>
                                </div>
                            `}
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                ${userData.shippingAddress && userData.shippingAddress.length > 0 ? `
                    <div class="address-section">
                        <div class="section-header">
                            <i class="fas fa-shipping-fast"></i>
                            <h2>Shipping Addresses</h2>
                        </div>
                        <div class="address-grid">
                            ${shippingAddressesHTML}
                        </div>
                    </div>
                ` : ''}

                <!-- Settings Section -->
                ${settingsHTML}

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button class="action-btn primary" onclick="window.location.href='settings.php'">
                        <i class="fas fa-edit"></i> Edit Profile & Settings
                    </button>
                    <button class="action-btn secondary" onclick="viewOrderHistory()">
                        <i class="fas fa-receipt"></i> View Order History
                    </button>
                    <button class="action-btn success" onclick="showFundsModal()">
                        <i class="fas fa-wallet"></i> Add Wallet Funds
                    </button>
                </div>
            </div>
        `;
    }

    // Modal Functions
    function showFundsModal() {
        document.getElementById('fundsModal').classList.add('show');
        document.getElementById('fundsAmount').focus();
        document.getElementById('fundsAmount').select();
    }

    function closeFundsModal() {
        document.getElementById('fundsModal').classList.remove('show');
        document.getElementById('fundsAmount').value = '100';
    }

    function confirmAddFunds() {
        const amountInput = document.getElementById('fundsAmount');
        const amount = parseFloat(amountInput.value);
        
        if (!amount || isNaN(amount)) {
            alert('Please enter a valid amount');
            amountInput.focus();
            return;
        }
        
        if (amount < 10) {
            alert('Minimum amount is ₱10');
            amountInput.focus();
            return;
        }
        
        if (amount > 10000) {
            alert('Maximum amount is ₱10,000');
            amountInput.focus();
            return;
        }

        if (!currentUser) {
            alert('You must be logged in to add funds');
            closeFundsModal();
            return;
        }

        // Show loading in modal
        const addBtn = document.querySelector('#fundsModal .modal-btn.primary');
        const originalText = addBtn.innerHTML;
        addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        addBtn.disabled = true;

        db.collection('users').doc(currentUser.uid).update({
            wallet_amount: firebase.firestore.FieldValue.increment(amount),
            lastUpdated: firebase.firestore.FieldValue.serverTimestamp()
        }).then(() => {
            alert(`Successfully added ${formatCurrency(amount)} to your wallet!`);
            closeFundsModal();
            loadProfile(); // Reload profile to update balance
        }).catch(error => {
            console.error('Error adding funds:', error);
            alert('Error adding funds: ' + error.message);
        }).finally(() => {
            addBtn.innerHTML = originalText;
            addBtn.disabled = false;
        });
    }

    // Navigation Functions
    function viewOrderHistory() {
        window.location.href = '../foods/my-order.php';
    }

    function viewTransactionHistory() {
        alert('Transaction history feature coming soon!');
    }

    // Load profile data
    function loadProfile() {
        showLoading();
        
        auth.onAuthStateChanged(user => {
            if (!user) {
                window.location.href = '../login.php';
                return;
            }
            
            currentUser = user;
            
            db.collection('users').doc(user.uid).get().then(doc => {
                if (doc.exists) {
                    userProfileData = doc.data();
                    console.log('User data loaded:', userProfileData);
                    
                    // Generate and display HTML
                    document.getElementById('profileContent').innerHTML = generateProfileHTML(user, userProfileData);
                    showContent();
                    
                } else {
                    document.getElementById('profileContent').innerHTML = `
                        <div class="error-container">
                            <div class="error-icon">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <h3 class="error-title">Profile Not Found</h3>
                            <p class="error-message">Your profile data doesn't exist in our database yet.</p>
                            <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                                <button class="action-btn primary" onclick="window.location.href='settings.php'">
                                    <i class="fas fa-user-plus"></i> Create Profile
                                </button>
                                <button class="action-btn secondary" onclick="goBack()">
                                    <i class="fas fa-arrow-left"></i> Go Back
                                </button>
                            </div>
                        </div>
                    `;
                    showContent();
                }
            }).catch(error => {
                console.error("Error fetching profile:", error);
                document.getElementById('profileContent').innerHTML = `
                    <div class="error-container">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3 class="error-title">Error Loading Profile</h3>
                        <p class="error-message">${error.message || 'Could not load profile data. Please try again later.'}</p>
                        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                            <button class="action-btn primary" onclick="window.location.reload()">
                                <i class="fas fa-redo"></i> Retry
                            </button>
                            <button class="action-btn secondary" onclick="goBack()">
                                <i class="fas fa-arrow-left"></i> Go Back
                            </button>
                        </div>
                    </div>
                `;
                showContent();
            });
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        loadProfile();
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeFundsModal();
                }
            });
        });
        
        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeFundsModal();
            }
            
            // Submit with Enter key in modal
            if (e.key === 'Enter' && document.getElementById('fundsModal').classList.contains('show')) {
                confirmAddFunds();
            }
        });
        
        // Prevent modal closing when clicking inside modal
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
    </script>
</body>
</html>
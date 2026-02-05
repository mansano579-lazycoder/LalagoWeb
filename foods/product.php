<?php 
// Fix: Check if session is already started before starting it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../inc/firebase.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>Product Details - Food Delivery</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #ff6600;
        --primary-dark: #e55a00;
        --secondary-color: #27ae60;
        --secondary-dark: #219955;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
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

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8fafc;
        color: #333;
        line-height: 1.6;
        padding-bottom: 80px;
        transition: padding-top 0.3s ease;
    }

    /* Header Scroll Effect */
    .header-hide {
        transform: translateY(-100%);
        transition: transform 0.3s ease;
    }

    .header-show {
        transform: translateY(0);
        transition: transform 0.3s ease;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        padding-top: 20px;
    }

    /* Loading State */
    .loading-state {
        text-align: center;
        padding: 60px 20px;
        min-height: 50vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
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

    /* Restaurant Profile */
    .restaurant-profile {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: var(--box-shadow);
        display: flex;
        align-items: flex-start;
        gap: 20px;
        flex-wrap: wrap;
    }

    .restaurant-logo {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f1f1f1;
        flex-shrink: 0;
    }

    .restaurant-info {
        flex: 1;
        min-width: 0;
    }

    .restaurant-name {
        font-size: 22px;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .restaurant-rating {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .rating-stars {
        color: #ffc107;
        font-size: 16px;
    }

    .rating-value {
        font-weight: 600;
        color: var(--dark-color);
    }

    .rating-count {
        color: var(--gray-color);
        font-size: 14px;
    }

    .delivery-info {
        display: flex;
        gap: 20px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .delivery-charge, .delivery-time {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 14px;
        font-weight: 600;
    }

    .delivery-charge { color: var(--primary-color); }
    .delivery-time { color: var(--secondary-color); }

    .view-restaurant-btn {
        padding: 12px 20px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .view-restaurant-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Product Details Layout */
    .product-details-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }

    @media (max-width: 992px) {
        .product-details-container {
            grid-template-columns: 1fr;
        }
    }

    /* Product Gallery */
    .product-gallery {
        background: white;
        border-radius: var(--border-radius);
        padding: 20px;
        box-shadow: var(--box-shadow);
    }

    .main-image {
        width: 100%;
        height: 350px;
        object-fit: cover;
        border-radius: var(--border-radius);
        background-color: #f5f5f5;
        margin-bottom: 15px;
    }

    .image-thumbnails {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .thumbnail {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        opacity: 0.7;
        transition: var(--transition);
        border: 2px solid transparent;
        background-color: #f5f5f5;
    }

    .thumbnail.active, .thumbnail:hover {
        opacity: 1;
        border-color: var(--primary-color);
    }

    /* Product Info */
    .product-info {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--box-shadow);
    }

    .product-title {
        font-size: 28px;
        color: var(--dark-color);
        margin-bottom: 15px;
        line-height: 1.3;
    }

    .product-badges {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        text-transform: uppercase;
    }

    .veg-badge { background: #e8f5e9; color: #2e7d32; }
    .nonveg-badge { background: #ffebee; color: #c62828; }
    .spicy-badge { background: #fff3e0; color: #ef6c00; }
    .new-badge { background: #e3f2fd; color: #1565c0; }
    .bestseller-badge { background: #fff8e1; color: #ff8f00; }

    /* Pricing */
    .price-container {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }

    .current-price {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary-color);
    }

    .original-price {
        font-size: 20px;
        color: var(--gray-color);
        text-decoration: line-through;
    }

    .discount-percentage {
        background-color: #ffebee;
        color: var(--danger-color);
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
    }

    .rating-section {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    /* Stock Status */
    .stock-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 20px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .in-stock { background: #e8f5e9; color: #2e7d32; }
    .low-stock { background: #fff3e0; color: #ef6c00; }
    .out-of-stock { background: #ffebee; color: #c62828; }

    /* Sections */
    .description-section, .nutrition-section, .quantity-selector, .addons-section {
        margin-bottom: 25px;
    }

    .section-title {
        font-size: 18px;
        color: var(--dark-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .description-content {
        color: #555;
        line-height: 1.7;
    }

    /* Nutrition Grid */
    .nutrition-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    @media (max-width: 768px) {
        .nutrition-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .nutrition-grid {
            grid-template-columns: 1fr;
        }
    }

    .nutrition-item {
        text-align: center;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .nutrition-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .nutrition-label {
        font-size: 13px;
        color: var(--gray-color);
        text-transform: uppercase;
    }

    /* Quantity Selector */
    .quantity-controls {
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: white;
        max-width: 200px;
        width: 100%;
    }

    .quantity-btn {
        width: 45px;
        height: 45px;
        background: #f8f9fa;
        border: none;
        font-size: 18px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-btn:hover:not(:disabled) {
        background: #e9ecef;
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity-input {
        width: 70px;
        height: 45px;
        text-align: center;
        border: none;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        font-size: 18px;
        font-weight: 600;
        background: white;
    }

    /* Add-ons */
    .addons-list {
        display: grid;
        gap: 12px;
    }

    .addon-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        transition: var(--transition);
        cursor: pointer;
    }

    .addon-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.1);
    }

    .addon-item.selected {
        border-color: var(--primary-color);
        background-color: #f0f9f4;
    }

    .addon-info {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }

    .addon-checkbox {
        width: 22px;
        height: 22px;
        accent-color: var(--primary-color);
        cursor: pointer;
        flex-shrink: 0;
    }

    .addon-details {
        flex: 1;
    }

    .addon-title {
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 5px;
    }

    .addon-description {
        font-size: 14px;
        color: var(--gray-color);
    }

    .addon-price {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 18px;
        white-space: nowrap;
    }

    /* Price Summary */
    .price-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #ddd;
    }

    .summary-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .summary-total {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .btn {
        flex: 1;
        min-width: 120px;
        padding: 16px 20px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(39, 174, 96, 0.2);
    }

    .btn-secondary {
        background-color: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .btn-secondary:hover:not(:disabled) {
        background-color: #f0f9f4;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn-wishlist {
        width: 60px;
        flex: none;
        background: white;
        border: 2px solid #ddd;
        color: var(--gray-color);
        border-radius: 10px;
        padding: 16px;
    }

    .btn-wishlist:hover {
        border-color: var(--danger-color);
        color: var(--danger-color);
    }

    .btn-wishlist.active {
        border-color: var(--danger-color);
        background-color: #ffebee;
        color: var(--danger-color);
    }

    /* Tabs Section */
    .tabs-section {
        background: white;
        border-radius: var(--border-radius);
        padding: 25px;
        margin-bottom: 40px;
        box-shadow: var(--box-shadow);
    }

    .tabs-header {
        display: flex;
        border-bottom: 2px solid #eee;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 12px 20px;
        background: none;
        border: none;
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-color);
        cursor: pointer;
        transition: var(--transition);
        position: relative;
        white-space: nowrap;
    }

    .tab-btn:hover {
        color: var(--dark-color);
    }

    .tab-btn.active {
        color: var(--primary-color);
    }

    .tab-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--primary-color);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Reviews */
    .review-item {
        padding: 20px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .reviewer-name {
        font-weight: 600;
        color: var(--dark-color);
    }

    .review-date {
        color: var(--gray-color);
        font-size: 14px;
    }

    .review-rating {
        color: #ffc107;
        margin: 5px 0;
        font-size: 16px;
    }

    .review-comment {
        color: #555;
        line-height: 1.7;
    }

    /* Related Products */
    .related-products {
        margin-top: 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        }
    }

    @media (max-width: 576px) {
        .products-grid {
            grid-template-columns: 1fr;
        }
    }

    .product-card {
        background: white;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        cursor: pointer;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .product-card-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        background-color: #f5f5f5;
    }

    .product-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .product-card-price {
        font-size: 20px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }

    .product-card-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        color: var(--gray-color);
        font-size: 14px;
    }

    /* Error State */
    .error-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        min-height: 50vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .error-icon {
        font-size: 48px;
        color: var(--danger-color);
        margin-bottom: 20px;
    }

    .error-title {
        font-size: 24px;
        color: var(--dark-color);
        margin-bottom: 10px;
    }

    .error-message {
        color: var(--gray-color);
        margin-bottom: 25px;
        max-width: 400px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 30px;
        background: var(--primary-color);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: var(--transition);
    }

    .back-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
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

    /* ================= STICKY CART FOOTER ================= */
    .cart-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e0e0e0;
        padding: 15px 20px;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: none;
        animation: slideUpFooter 0.3s ease;
    }

    @keyframes slideUpFooter {
        from {
            transform: translateY(100%);
        }
        to {
            transform: translateY(0);
        }
    }

    .cart-footer-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 1200px;
        margin: 0 auto;
        gap: 15px;
    }

    .footer-price {
        flex: 1;
    }

    .footer-price-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
        display: block;
    }

    .footer-total-price {
        font-size: 22px;
        font-weight: 700;
        color: var(--primary-color);
    }

    .footer-quantity-controls {
        display: flex;
        align-items: center;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #ddd;
        overflow: hidden;
        min-width: 120px;
    }

    .footer-quantity-btn {
        width: 40px;
        height: 40px;
        background: #f8f9fa;
        border: none;
        font-size: 18px;
        color: #333;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .footer-quantity-btn:hover:not(:disabled) {
        background: #e9ecef;
    }

    .footer-quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .footer-quantity-input {
        width: 40px;
        height: 40px;
        border: none;
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        text-align: center;
        font-size: 18px;
        font-weight: 600;
        background: white;
        -moz-appearance: textfield;
    }

    .footer-quantity-input::-webkit-outer-spin-button,
    .footer-quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .footer-action-buttons {
        display: flex;
        gap: 10px;
        flex-shrink: 0;
    }

    .footer-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 120px;
    }

    .footer-btn-cart {
        background: white;
        color: var(--primary-color);
        border: 2px solid var(--primary-color);
    }

    .footer-btn-cart:hover:not(:disabled) {
        background: #f0f9f4;
    }

    .footer-btn-order {
        background: var(--primary-color);
        color: white;
    }

    .footer-btn-order:hover:not(:disabled) {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
    }

    .footer-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Restaurant Switch Modal */
    .switch-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
        padding: 15px;
    }

    .switch-modal.active {
        display: flex;
    }

    .switch-modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        padding: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: slideUpModal 0.4s ease;
    }

    @keyframes slideUpModal {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .switch-modal-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .switch-modal-header i {
        font-size: 2.5rem;
        color: #F39C12;
        margin-bottom: 10px;
        background: rgba(243, 156, 18, 0.1);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
    }

    .switch-modal-header h3 {
        font-size: 1.5rem;
        color: #2D3436;
        margin-bottom: 8px;
    }

    .switch-modal-header p {
        color: #6C757D;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .restaurant-comparison {
        background: #F8F9FA;
        border-radius: 16px;
        padding: 15px;
        margin: 15px 0;
    }

    .restaurant-compare {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px;
        margin-bottom: 10px;
        background: white;
        border-radius: 8px;
        border: 2px solid #E9ECEF;
    }

    .restaurant-compare.current {
        border-color: #FF6B35;
    }

    .restaurant-compare.new {
        border-color: #3498DB;
    }

    .restaurant-compare-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .restaurant-compare-logo {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .restaurant-compare-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .restaurant-compare-name {
        font-weight: 600;
        color: #2D3436;
        font-size: 0.95rem;
    }

    .restaurant-compare-badge {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-current {
        background: #FF6B35;
        color: white;
    }

    .badge-new {
        background: #3498DB;
        color: white;
    }

    .switch-modal-footer {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
    }

    .switch-modal-btn {
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 2px solid transparent;
        width: 100%;
    }

    .switch-modal-btn.cancel {
        background: transparent;
        color: #6C757D;
        border-color: #E9ECEF;
    }

    .switch-modal-btn.cancel:hover {
        background: #E9ECEF;
        color: #495057;
    }

    .switch-modal-btn.confirm {
        background: #3498DB;
        color: white;
        border-color: #3498DB;
    }

    .switch-modal-btn.confirm:hover {
        background: #FF6B35;
        border-color: #FF6B35;
        transform: translateY(-2px);
    }

    /* Mobile specific footer styles */
    @media (max-width: 768px) {
        body {
            padding-bottom: 90px;
        }
        
        .cart-footer {
            padding: 12px 15px;
        }
        
        .cart-footer-content {
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .footer-price {
            order: 1;
            flex: 1 0 100%;
            text-align: center;
            margin-bottom: 5px;
        }
        
        .footer-quantity-controls {
            order: 2;
            flex: 1;
        }
        
        .footer-action-buttons {
            order: 3;
            flex: 1;
        }
        
        .footer-btn {
            min-width: 0;
            padding: 12px 15px;
            font-size: 14px;
            flex: 1;
        }
        
        .footer-total-price {
            font-size: 20px;
        }
        
        .action-buttons {
            position: sticky;
            bottom: 20px;
            z-index: 100;
            background: white;
            padding: 15px;
            border-radius: var(--border-radius);
            box-shadow: 0 -2px 20px rgba(0,0,0,0.1);
            margin-bottom: -10px;
        }
    }

    @media (max-width: 576px) {
        body {
            padding-bottom: 85px;
        }
        
        .container {
            padding: 12px;
        }
        
        .cart-footer {
            padding: 10px 12px;
        }
        
        .main-image {
            height: 250px;
        }
        
        .thumbnail {
            width: 60px;
            height: 60px;
        }
        
        .product-title {
            font-size: 22px;
        }
        
        .current-price {
            font-size: 24px;
        }
        
        .btn {
            padding: 14px;
            font-size: 14px;
        }
        
        .btn-wishlist {
            width: 50px;
            padding: 14px;
        }
        
        .footer-btn {
            padding: 10px 12px;
            font-size: 13px;
        }
        
        .footer-quantity-btn {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
        
        .footer-quantity-input {
            width: 35px;
            height: 35px;
            font-size: 16px;
        }
        
        .footer-total-price {
            font-size: 18px;
        }
    }

    /* Show footer when product is loaded */
    .product-loaded .cart-footer {
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }
        
        .restaurant-profile {
            padding: 15px;
            gap: 15px;
        }
        
        .restaurant-logo {
            width: 60px;
            height: 60px;
        }
        
        .restaurant-name {
            font-size: 20px;
        }
        
        .main-image {
            height: 300px;
        }
        
        .product-title {
            font-size: 24px;
        }
        
        .current-price {
            font-size: 28px;
        }
    }

    /* Scroll to top button */
    .scroll-to-top {
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 50px;
        height: 50px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        z-index: 999;
        display: none;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .scroll-to-top:hover {
        background: var(--primary-dark);
        transform: translateY(-3px);
    }

    .scroll-to-top.show {
        display: flex;
        animation: fadeIn 0.3s ease;
    }
    </style>
</head>
<body>

<div class="container">
    <div id="productContainer">
        <div class="loading-state">
            <div class="loading-spinner"></div>
            <p>Loading product details...</p>
        </div>
    </div>
</div>

<!-- Scroll to top button -->
<button class="scroll-to-top" id="scrollToTopBtn">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Toast Notification -->
<div class="toast" id="toast"></div>

<!-- Sticky Cart Footer -->
<div class="cart-footer" id="cartFooter">
    <div class="cart-footer-content">
        <div class="footer-price">
            <span class="footer-price-label">Total</span>
            <span class="footer-total-price" id="footerTotalPrice">₱0.00</span>
        </div>
        
        <div class="footer-quantity-controls">
            <button class="footer-quantity-btn" id="footerMinusBtn" onclick="updateFooterQuantity(-1)">
                <i class="fas fa-minus"></i>
            </button>
            <input type="number" 
                   id="footerQuantityInput" 
                   class="footer-quantity-input" 
                   value="1" 
                   min="1" 
                   max="999"
                   onchange="updateFooterTotal()">
            <button class="footer-quantity-btn" id="footerPlusBtn" onclick="updateFooterQuantity(1)">
                <i class="fas fa-plus"></i>
            </button>
        </div>
        
        <div class="footer-action-buttons">
            <button class="footer-btn footer-btn-cart" id="footerCartBtn" onclick="addToCartFromFooter()">
                <i class="fas fa-shopping-cart"></i> Cart
            </button>
            <button class="footer-btn footer-btn-order" id="footerOrderBtn" onclick="orderNowFromFooter()">
                <i class="fas fa-bolt"></i> Order
            </button>
        </div>
    </div>
</div>

<script>
// Get login status from PHP session
const user = <?php echo isset($_SESSION['uid']) ? json_encode($_SESSION['uid']) : 'null'; ?>;
const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

const productContainer = document.getElementById('productContainer');
const toast = document.getElementById('toast');
const urlParams = new URLSearchParams(window.location.search);
const productID = urlParams.get('id');

// Global variables for product data
let productData = null;
let restaurantData = null;
let finalPrice = 0;
let basePrice = 0;
let disPrice = 0;

// Scroll variables
let lastScrollTop = 0;
const header = document.querySelector('header');
const scrollToTopBtn = document.getElementById('scrollToTopBtn');

// Show toast message
function showToast(message, duration = 3000) {
    toast.textContent = message;
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, duration);
}

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

// Scroll to top function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Handle scroll events for header
function handleScroll() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Show/hide scroll to top button
    if (scrollTop > 300) {
        scrollToTopBtn.classList.add('show');
    } else {
        scrollToTopBtn.classList.remove('show');
    }
    
    // Handle header show/hide on scroll
    if (header) {
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            header.classList.add('header-hide');
            header.classList.remove('header-show');
        } else {
            // Scrolling up
            header.classList.remove('header-hide');
            header.classList.add('header-show');
        }
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
}

// Initialize scroll event listener
function initScrollBehavior() {
    // Add scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Add click listener to scroll to top button
    scrollToTopBtn.addEventListener('click', scrollToTop);
    
    // Touch events for mobile
    let touchStartY = 0;
    let touchEndY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        touchEndY = e.touches[0].clientY;
        
        // Show header when user tries to scroll up by touching at the top
        if (touchEndY < touchStartY && window.scrollY === 0) {
            if (header) {
                header.classList.remove('header-hide');
                header.classList.add('header-show');
            }
        }
    }, { passive: true });
    
    // Initially show header
    if (header) {
        header.classList.add('header-show');
    }
}

if(!productID){
    productContainer.innerHTML = `
        <div class="error-state">
            <div class="error-icon">⚠️</div>
            <h2 class="error-title">No Product Selected</h2>
            <p class="error-message">Please select a product to view details.</p>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Browse Products
            </a>
        </div>
    `;
    
    // Initialize scroll behavior even for error state
    initScrollBehavior();
} else {
    // Initialize scroll behavior first
    initScrollBehavior();
    
    // First check Firebase
    try {
        checkFirebase();
        
        // Fetch product data first, then restaurant data
        db.collection("vendor_products").doc(productID).get()
            .then(productDoc => {
                if(!productDoc.exists){
                    throw new Error("Product not found");
                }

                productData = productDoc.data();
                
                // Calculate ratings
                const ratingCount = productData.reviewAttributes?.reviewsCount || 0;
                const ratingSum = productData.reviewAttributes?.reviewsSum || 0;
                const rating = ratingCount > 0 ? (ratingSum/ratingCount).toFixed(1) : "0.0";
                
                // Pricing
                basePrice = parseFloat(productData.price || 0);
                disPrice = parseFloat(productData.disPrice || 0);
                const showDiscount = disPrice > 0 && disPrice < basePrice;
                finalPrice = showDiscount ? disPrice : basePrice;
                const discountPercentage = showDiscount ? Math.round((1 - disPrice/basePrice) * 100) : 0;
                
                // Stock status
                const isOutOfStock = productData.quantity === 0;
                const isLowStock = productData.quantity > 0 && productData.quantity < 10;
                
                // Now fetch restaurant data from vendors table
                return db.collection("vendors").doc(productData.vendorID).get()
                    .then(vendorDoc => {
                        restaurantData = vendorDoc.exists ? vendorDoc.data() : null;
                        
                        // Render the page with both product and restaurant data
                        renderProductPage(productData, restaurantData, {
                            rating,
                            ratingCount,
                            showDiscount,
                            discountPercentage,
                            isOutOfStock,
                            isLowStock
                        });
                    });
            })
            .catch(err => {
                console.error("Error loading product:", err);
                productContainer.innerHTML = `
                    <div class="error-state">
                        <div class="error-icon">❌</div>
                        <h2 class="error-title">Product Not Found</h2>
                        <p class="error-message">The product you're looking for doesn't exist or has been removed.</p>
                        <a href="index.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Browse Products
                        </a>
                    </div>
                `;
            });
    } catch (error) {
        console.error("Firebase error:", error);
        productContainer.innerHTML = `
            <div class="error-state">
                <div class="error-icon">⚠️</div>
                <h2 class="error-title">Firebase Error</h2>
                <p class="error-message">Unable to connect to database. Please refresh the page.</p>
                <button onclick="window.location.reload()" class="back-btn">
                    <i class="fas fa-redo"></i> Refresh Page
                </button>
            </div>
        `;
    }
}

function renderProductPage(p, vendorData, stats) {
    // Restaurant profile HTML
    let restaurantHTML = '';
    if(vendorData) {
        const restaurantName = vendorData.title || vendorData.name || 'Coffee Cat Cafe';
        const vendorRating = vendorData.rating || (vendorData.reviewsSum && vendorData.reviewsCount ? vendorData.reviewsSum/vendorData.reviewsCount : 0) || 0;
        const vendorRatingCount = vendorData.reviewCount || vendorData.reviewsCount || 0;
        const vendorAddress = vendorData.location || vendorData.address || '';
        const vendorPhone = vendorData.phonenumber || vendorData.phone || '';
        const vendorCuisine = vendorData.categoryTitle || vendorData.cuisine || '';
        
        // Delivery information
        const deliveryCharge = vendorData.DeliveryCharge?.minimum_delivery_charges || 30;
        const deliveryChargePerKm = vendorData.DeliveryCharge?.delivery_charges_per_km || 1;
        const minDeliveryChargeWithinKm = vendorData.DeliveryCharge?.minimum_delivery_charges_within_km || 3;
        const deliveryTime = '30-45 min';
        
        restaurantHTML = `
            <div class="restaurant-profile">
                <img src="${vendorData.photo || vendorData.authorProfilePic || 'https://via.placeholder.com/80x80?text=Restaurant'}" 
                     alt="${restaurantName}" 
                     class="restaurant-logo"
                     loading="lazy"
                     onerror="this.src='https://via.placeholder.com/80x80?text=Restaurant'">
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${restaurantName}</h3>
                    <div class="restaurant-rating">
                        <div class="rating-stars">
                            ${'★'.repeat(Math.floor(vendorRating))}${'☆'.repeat(5 - Math.floor(vendorRating))}
                        </div>
                        <span class="rating-value">${vendorRating.toFixed(1)}</span>
                        <span class="rating-count">(${vendorRatingCount} reviews)</span>
                    </div>
                    <div class="delivery-info">
                        <div class="delivery-charge">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Delivery: ₱${deliveryCharge} (first ${minDeliveryChargeWithinKm}km) + ₱${deliveryChargePerKm}/km</span>
                        </div>
                        <div class="delivery-time">
                            <i class="fas fa-clock"></i>
                            <span>Delivery time: ${deliveryTime}</span>
                        </div>
                    </div>
                </div>
                <a href="vendor.php?id=${p.vendorID}" class="view-restaurant-btn">
                    <i class="fas fa-store"></i> View Restaurant
                </a>
            </div>
        `;
    } else {
        restaurantHTML = `
            <div class="restaurant-profile">
                <img src="https://via.placeholder.com/80x80?text=Restaurant" 
                     alt="Restaurant" 
                     class="restaurant-logo"
                     loading="lazy">
                <div class="restaurant-info">
                    <h3 class="restaurant-name">Restaurant Information</h3>
                    <div class="delivery-info">
                        <div class="delivery-charge">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Standard delivery charges apply</span>
                        </div>
                        <div class="delivery-time">
                            <i class="fas fa-clock"></i>
                            <span>Delivery time: 30-45 min</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Additional badges
    let additionalBadges = '';
    if (p.spicy) additionalBadges += '<span class="badge spicy-badge"><i class="fas fa-pepper-hot"></i> Spicy</span>';
    if (p.newArrival) additionalBadges += '<span class="badge new-badge"><i class="fas fa-star"></i> New</span>';
    if (p.popular) additionalBadges += '<span class="badge bestseller-badge"><i class="fas fa-fire"></i> Popular</span>';

    // Nutrition information
    const nutritionHTML = `
        <div class="nutrition-section">
            <h3 class="section-title"><i class="fas fa-apple-alt"></i> Nutrition Facts</h3>
            <div class="nutrition-grid">
                <div class="nutrition-item">
                    <div class="nutrition-value">${p.calories || 0}</div>
                    <div class="nutrition-label">Calories</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value">${p.fats || 0}g</div>
                    <div class="nutrition-label">Fats</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value">${p.proteins || 0}g</div>
                    <div class="nutrition-label">Proteins</div>
                </div>
                <div class="nutrition-item">
                    <div class="nutrition-value">${p.grams || 0}g</div>
                    <div class="nutrition-label">Serving Size</div>
                </div>
            </div>
        </div>
    `;

    // Add-ons section
    let addonsHTML = '';
    if(p.addOnsTitle && Array.isArray(p.addOnsTitle) && p.addOnsTitle.length > 0) {
        addonsHTML = `
            <div class="addons-section">
                <h3 class="section-title"><i class="fas fa-plus-circle"></i> Customize Your Order</h3>
                <div class="addons-list">
        `;
        
        p.addOnsTitle.forEach((title, index) => {
            const price = p.addOnsPrice && p.addOnsPrice[index] ? parseFloat(p.addOnsPrice[index]) : 0;
            const description = p.addOnsDescription && p.addOnsDescription[index] ? p.addOnsDescription[index] : '';
            
            addonsHTML += `
                <div class="addon-item" onclick="handleAddonClick(this, ${index})">
                    <div class="addon-info">
                        <input type="checkbox" 
                               class="addon-checkbox" 
                               id="addon-${index}"
                               data-index="${index}"
                               data-title="${title}" 
                               data-price="${price}"
                               data-description="${description}"
                               onchange="calculateTotal()">
                        <div class="addon-details">
                            <div class="addon-title">${title}</div>
                            ${description ? `<div class="addon-description">${description}</div>` : ''}
                        </div>
                    </div>
                    <div class="addon-price">+₱${price.toFixed(2)}</div>
                </div>
            `;
        });
        
        addonsHTML += `
                </div>
            </div>
        `;
    }

    // Main product HTML
    productContainer.innerHTML = `
        ${restaurantHTML}
        
        <div class="product-details-container">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <img src="${p.photo || 'https://via.placeholder.com/600x350?text=Product+Image'}" 
                     alt="${p.name}" 
                     class="main-image"
                     loading="lazy"
                     onerror="this.src='https://via.placeholder.com/600x350?text=Product+Image'">
                <div class="image-thumbnails">
                    <img src="${p.photo || 'https://via.placeholder.com/70x70?text=Thumb'}" 
                         alt="Thumbnail" 
                         class="thumbnail active"
                         loading="lazy">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-info">
                <div class="product-header">
                    <h1 class="product-title">${p.name}</h1>
                    <div class="product-badges">
                        ${p.veg ? '<span class="badge veg-badge"><i class="fas fa-leaf"></i> Vegetarian</span>' : ''}
                        ${p.nonveg ? '<span class="badge nonveg-badge"><i class="fas fa-drumstick-bite"></i> Non-Veg</span>' : ''}
                        ${additionalBadges}
                    </div>
                </div>
                
                <div class="pricing-section">
                    <div class="price-container">
                        <span class="current-price">₱${finalPrice.toFixed(2)}</span>
                        ${stats.showDiscount ? `
                            <span class="original-price">₱${basePrice.toFixed(2)}</span>
                            <span class="discount-percentage">${stats.discountPercentage}% OFF</span>
                        ` : ''}
                    </div>
                    
                    <div class="rating-section">
                        <div class="rating-stars">
                            ${'★'.repeat(Math.floor(stats.rating))}${'☆'.repeat(5 - Math.floor(stats.rating))}
                        </div>
                        <span class="rating-value">${stats.rating}</span>
                        <span class="rating-count">(${stats.ratingCount} reviews)</span>
                    </div>
                </div>
                
                <div class="stock-status ${stats.isOutOfStock ? 'out-of-stock' : stats.isLowStock ? 'low-stock' : 'in-stock'}">
                    <i class="fas ${stats.isOutOfStock ? 'fa-times-circle' : stats.isLowStock ? 'fa-exclamation-triangle' : 'fa-check-circle'}"></i>
                    ${stats.isOutOfStock ? 'Out of Stock' : stats.isLowStock ? `Only ${p.quantity} left in stock` : 'In Stock'}
                </div>
                
                ${p.description ? `
                    <div class="description-section">
                        <h3 class="section-title">
                            <i class="fas fa-align-left"></i> Description
                        </h3>
                        <p class="description-content">${p.description}</p>
                    </div>
                ` : ''}
                
                ${nutritionHTML}
                
                <div class="quantity-selector">
                    <h3 class="section-title">
                        <i class="fas fa-shopping-basket"></i> Quantity
                    </h3>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateMainQuantity(-1)" ${stats.isOutOfStock ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" 
                               id="quantityInput" 
                               class="quantity-input" 
                               value="1" 
                               min="1" 
                               max="${p.quantity > 0 ? p.quantity : ''}"
                               ${stats.isOutOfStock ? 'disabled' : ''}
                               onchange="updateMainQuantityFromInput()">
                        <button class="quantity-btn" onclick="updateMainQuantity(1)" ${stats.isOutOfStock ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                ${addonsHTML}
                
                <div class="price-summary">
                    <div class="summary-item">
                        <span>Base Price (x<span id="quantityDisplay">1</span>)</span>
                        <span>₱<span id="basePriceTotal">${finalPrice.toFixed(2)}</span></span>
                    </div>
                    <div class="summary-item" id="addonsSummary" style="display: none;">
                        <span>Add-ons</span>
                        <span>₱<span id="addonsTotal">0.00</span></span>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Total Price</span>
                        <span>₱<span id="totalPrice">${finalPrice.toFixed(2)}</span></span>
                    </div>
                </div>
                
                <div class="action-buttons" id="actionButtons">
                    <button class="btn btn-wishlist" id="wishlistBtn">
                        <i class="far fa-heart"></i>
                    </button>
                    <button class="btn btn-secondary" id="cartBtn" ${stats.isOutOfStock ? 'disabled' : ''}>
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button class="btn btn-primary" id="orderBtn" ${stats.isOutOfStock ? 'disabled' : ''}>
                        <i class="fas fa-bolt"></i> Order Now
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabs Section -->
        <div class="tabs-section">
            <div class="tabs-header">
                <button class="tab-btn active" onclick="switchTab('reviews')">
                    <i class="fas fa-star"></i> Reviews (${stats.ratingCount})
                </button>
                <button class="tab-btn" onclick="switchTab('details')">
                    <i class="fas fa-info-circle"></i> Details
                </button>
                <button class="tab-btn" onclick="switchTab('faq')">
                    <i class="fas fa-question-circle"></i> FAQ
                </button>
            </div>
            
            <div class="tab-content active" id="reviewsTab">
                <div id="reviewsContent">
                    <div class="loading-state" style="padding: 30px 20px;">
                        <div class="loading-spinner" style="width: 30px; height: 30px;"></div>
                        <p>Loading reviews...</p>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="detailsTab">
                <div class="description-content">
                    ${p.fullDescription || p.description || '<p style="text-align: center; color: #666; padding: 40px 20px;">No additional details available.</p>'}
                </div>
            </div>
            
            <div class="tab-content" id="faqTab">
                <div style="padding: 20px;">
                    <p style="text-align: center; color: #666;">Frequently asked questions about this product will appear here.</p>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="related-products">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-thumbs-up"></i> You Might Also Like
                </h2>
                <a href="index.php?category=${p.categoryID}" class="view-all-btn">
                    <i class="fas fa-eye"></i> View All
                </a>
            </div>
            <div class="products-grid" id="relatedProductsGrid">
                <div class="loading-state" style="grid-column: 1 / -1; padding: 30px;">
                    <div class="loading-spinner" style="width: 30px; height: 30px;"></div>
                    <p>Loading related products...</p>
                </div>
            </div>
        </div>
    `;
    
    // Show cart footer
    document.body.classList.add('product-loaded');
    
    // Initialize footer
    setTimeout(() => {
        initCartFooter();
    }, 100);
    
    // Load reviews
    loadReviews();
    
    // Load related products
    if (p.categoryID) {
        loadRelatedProducts(p.categoryID);
    }
    
    // Add event listeners
    setupEventListeners();
}

// ================= FOOTER CART FUNCTIONS =================

// Initialize footer when product is loaded
function initCartFooter() {
    // Sync footer with main quantity
    const mainQuantity = parseInt(document.getElementById('quantityInput')?.value || 1);
    const footerQuantityInput = document.getElementById('footerQuantityInput');
    if (footerQuantityInput) {
        footerQuantityInput.value = mainQuantity;
    }
    
    // Update footer total
    updateFooterTotal();
    
    // Add event listeners for footer
    setupFooterEventListeners();
    
    // Disable buttons if out of stock
    if (productData?.quantity === 0) {
        const footerCartBtn = document.getElementById('footerCartBtn');
        const footerOrderBtn = document.getElementById('footerOrderBtn');
        const footerMinusBtn = document.getElementById('footerMinusBtn');
        const footerPlusBtn = document.getElementById('footerPlusBtn');
        const footerQuantityInput = document.getElementById('footerQuantityInput');
        
        if (footerCartBtn) footerCartBtn.disabled = true;
        if (footerOrderBtn) footerOrderBtn.disabled = true;
        if (footerMinusBtn) footerMinusBtn.disabled = true;
        if (footerPlusBtn) footerPlusBtn.disabled = true;
        if (footerQuantityInput) footerQuantityInput.disabled = true;
    }
    
    // Set max quantity based on stock
    if (productData?.quantity > 0) {
        const maxQuantity = Math.min(productData.quantity, 999);
        const footerQuantityInput = document.getElementById('footerQuantityInput');
        const mainQuantityInput = document.getElementById('quantityInput');
        
        if (footerQuantityInput) {
            footerQuantityInput.max = maxQuantity;
        }
        if (mainQuantityInput) {
            mainQuantityInput.max = maxQuantity;
        }
    }
}

// Update footer quantity
function updateFooterQuantity(change) {
    const input = document.getElementById('footerQuantityInput');
    const mainInput = document.getElementById('quantityInput');
    let value = parseInt(input.value) || 1;
    const max = input.max ? parseInt(input.max) : 999;
    
    value += change;
    if (value < 1) value = 1;
    if (value > max) value = max;
    
    input.value = value;
    
    // Sync with main quantity input
    if (mainInput) {
        mainInput.value = value;
        updateMainTotal();
    }
    
    updateFooterTotal();
}

// Update main quantity
function updateMainQuantity(change) {
    const input = document.getElementById('quantityInput');
    const footerInput = document.getElementById('footerQuantityInput');
    let value = parseInt(input.value) || 1;
    const max = input.max ? parseInt(input.max) : 999;
    
    value += change;
    if (value < 1) value = 1;
    if (value > max) value = max;
    
    input.value = value;
    
    // Sync with footer quantity input
    if (footerInput) {
        footerInput.value = value;
    }
    
    updateMainTotal();
}

function updateMainQuantityFromInput() {
    const input = document.getElementById('quantityInput');
    const footerInput = document.getElementById('footerQuantityInput');
    let value = parseInt(input.value) || 1;
    const max = input.max ? parseInt(input.max) : 999;
    
    if (value < 1) value = 1;
    if (value > max) value = max;
    
    input.value = value;
    
    // Sync with footer quantity input
    if (footerInput) {
        footerInput.value = value;
    }
    
    updateMainTotal();
}

// Update footer total price
function updateFooterTotal() {
    const quantity = parseInt(document.getElementById('footerQuantityInput').value) || 1;
    const basePrice = parseFloat(finalPrice);
    
    // Calculate add-ons total
    let addonsTotal = 0;
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        const price = parseFloat(cb.dataset.price) || 0;
        addonsTotal += price * quantity;
    });
    
    // Calculate total
    const total = (basePrice * quantity) + addonsTotal;
    
    // Update footer display
    document.getElementById('footerTotalPrice').textContent = `₱${total.toFixed(2)}`;
    
    // Update main page total display
    const mainTotalElement = document.getElementById('totalPrice');
    if (mainTotalElement) {
        mainTotalElement.textContent = total.toFixed(2);
    }
}

// Update main total
function updateMainTotal() {
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    const basePrice = parseFloat(finalPrice);
    
    // Calculate add-ons total
    let addonsTotal = 0;
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        const price = parseFloat(cb.dataset.price) || 0;
        addonsTotal += price * quantity;
    });
    
    // Calculate total
    const total = (basePrice * quantity) + addonsTotal;
    
    // Update main page displays
    const quantityDisplay = document.getElementById('quantityDisplay');
    const basePriceTotal = document.getElementById('basePriceTotal');
    const addonsTotalElement = document.getElementById('addonsTotal');
    const addonsSummary = document.getElementById('addonsSummary');
    const totalPriceElement = document.getElementById('totalPrice');
    
    if (quantityDisplay) quantityDisplay.textContent = quantity;
    if (basePriceTotal) basePriceTotal.textContent = (basePrice * quantity).toFixed(2);
    if (addonsTotalElement) addonsTotalElement.textContent = addonsTotal.toFixed(2);
    if (addonsSummary) addonsSummary.style.display = addonsTotal > 0 ? 'flex' : 'none';
    if (totalPriceElement) totalPriceElement.textContent = total.toFixed(2);
    
    // Update footer
    const footerTotalPrice = document.getElementById('footerTotalPrice');
    const footerQuantityInput = document.getElementById('footerQuantityInput');
    
    if (footerTotalPrice) footerTotalPrice.textContent = `₱${total.toFixed(2)}`;
    if (footerQuantityInput) footerQuantityInput.value = quantity;
}

// Setup footer event listeners
function setupFooterEventListeners() {
    const footerQuantityInput = document.getElementById('footerQuantityInput');
    const mainQuantityInput = document.getElementById('quantityInput');
    
    // Sync footer quantity with main quantity
    if (footerQuantityInput && mainQuantityInput) {
        footerQuantityInput.addEventListener('input', function(e) {
            const value = parseInt(this.value) || 1;
            const max = this.max ? parseInt(this.max) : 999;
            
            if (value < 1) this.value = 1;
            if (value > max) this.value = max;
            
            // Update main input
            mainQuantityInput.value = this.value;
            updateMainTotal();
        });
        
        // Prevent invalid input
        footerQuantityInput.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') {
                e.preventDefault();
            }
        });
    }
    
    // Listen for addon changes to update totals
    document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Add/remove selected class from parent
            const parent = this.closest('.addon-item');
            if (parent) {
                parent.classList.toggle('selected', this.checked);
            }
            
            updateMainTotal();
            updateFooterTotal();
        });
    });
}

// Handle addon click
function handleAddonClick(element, index) {
    const checkbox = element.querySelector('.addon-checkbox');
    checkbox.checked = !checkbox.checked;
    checkbox.dispatchEvent(new Event('change'));
    
    if (checkbox.checked) {
        element.classList.add('selected');
    } else {
        element.classList.remove('selected');
    }
}

// Add to cart from footer
function addToCartFromFooter() {
    if (!user) {
        showToast("Please login to add items to cart!");
        setTimeout(() => {
            window.location.href = "../login.php";
        }, 1500);
        return;
    }
    
    const quantity = parseInt(document.getElementById('footerQuantityInput').value) || 1;
    const selectedAddons = [];
    
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        selectedAddons.push({
            title: cb.dataset.title,
            price: parseFloat(cb.dataset.price) || 0,
            description: cb.dataset.description || ''
        });
    });
    
    // Create cart item
    const cartItem = {
        id: productID,
        name: productData.name,
        price: finalPrice,
        qty: quantity,
        photo: productData.photo,
        vendorID: productData.vendorID,
        selectedAddons: selectedAddons,
        timestamp: new Date().toISOString()
    };
    
    // Check if cart exists in localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Check restaurant compatibility
    if (cart.length > 0) {
        const currentVendorId = cart[0].vendorID;
        if (productData.vendorID !== currentVendorId) {
            // Show restaurant switch modal
            showRestaurantSwitchModal(cartItem);
            return;
        }
    }
    
    // Check if same item with same addons exists
    const itemKey = cartItem.id + (cartItem.selectedAddons ? JSON.stringify(cartItem.selectedAddons) : '');
    const existingIndex = cart.findIndex(item => {
        const existingKey = item.id + (item.selectedAddons ? JSON.stringify(item.selectedAddons) : '');
        return existingKey === itemKey;
    });
    
    if (existingIndex > -1) {
        // Update quantity of existing item
        cart[existingIndex].qty += quantity;
        
        // Limit to 99
        if (cart[existingIndex].qty > 99) {
            cart[existingIndex].qty = 99;
            showToast("Maximum quantity reached (99 per item)", "warning");
        } else {
            showToast(`Quantity updated to ${cart[existingIndex].qty}`, "success");
        }
    } else {
        // Add new item
        cart.push(cartItem);
        showToast("Added to cart!", "success");
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Save to Firebase if logged in
    if (user) {
        saveCartToFirebase(cart);
    }
    
    // Animate cart button
    const cartBtn = document.getElementById('footerCartBtn');
    const originalHTML = cartBtn.innerHTML;
    cartBtn.innerHTML = '<i class="fas fa-check"></i> Added!';
    cartBtn.style.backgroundColor = '#28a745';
    cartBtn.style.color = 'white';
    cartBtn.style.borderColor = '#28a745';
    
    setTimeout(() => {
        cartBtn.innerHTML = originalHTML;
        cartBtn.style.backgroundColor = '';
        cartBtn.style.color = '';
        cartBtn.style.borderColor = '';
    }, 1500);
    
    // Update cart count in header
    updateCartCount();
}

// Order now from footer
function orderNowFromFooter() {
    if (!user) {
        showToast("Please login or signup first!");
        setTimeout(() => {
            window.location.href = "../login.php";
        }, 1500);
        return;
    }
    
    const quantity = parseInt(document.getElementById('footerQuantityInput').value) || 1;
    const selectedAddons = [];
    
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        selectedAddons.push({
            title: cb.dataset.title,
            price: parseFloat(cb.dataset.price) || 0,
            description: cb.dataset.description || ''
        });
    });
    
    // Get restaurant info
    const restaurantName = restaurantData ? (restaurantData.title || restaurantData.name || "Unknown Restaurant") : "Unknown Restaurant";
    const restaurantLogo = restaurantData ? (restaurantData.photo || restaurantData.authorProfilePic || "") : "";
    
    // Create single item order
    const orderData = {
        products: [{
            id: productID,
            name: productData.name,
            price: finalPrice.toString(),
            discountPrice: disPrice > 0 ? disPrice.toString() : "0",
            quantity: quantity,
            photo: productData.photo || '',
            vendorID: productData.vendorID,
            category_id: productData.categoryID || '',
            extras: selectedAddons.map(addon => addon.title),
            extras_price: selectedAddons.reduce((sum, addon) => sum + addon.price, 0).toFixed(2),
            variant_info: null
        }],
        vendorID: productData.vendorID,
        restaurantName: restaurantName,
        restaurantLogo: restaurantLogo,
        orderTotal: (finalPrice * quantity) + selectedAddons.reduce((sum, addon) => sum + addon.price, 0),
        orderDate: new Date().toISOString()
    };
    
    // Save order to localStorage
    try {
        localStorage.setItem("currentOrder", JSON.stringify(orderData));
        localStorage.setItem("singleProductOrder", JSON.stringify({
            product_id: productID,
            name: productData.name,
            price: finalPrice,
            quantity: quantity,
            photo: productData.photo,
            vendorID: productData.vendorID,
            restaurantName: restaurantName,
            restaurantLogo: restaurantLogo,
            total: orderData.orderTotal,
            addons: selectedAddons
        }));
        
        showToast("Order saved! Redirecting...");
        
        // Redirect to checkout
        setTimeout(() => {
            window.location.href = "../foods/order.php";
        }, 1000);
        
    } catch (error) {
        console.error("Error saving order:", error);
        showToast("Error saving order. Please try again.");
    }
}

// Restaurant switch modal
function showRestaurantSwitchModal(newItem) {
    // Create modal HTML
    const modalHTML = `
        <div class="switch-modal" style="display: flex;">
            <div class="switch-modal-content">
                <div class="switch-modal-header">
                    <i class="fas fa-exchange-alt"></i>
                    <h3>Switch Restaurant?</h3>
                    <p>Your cart contains items from a different restaurant. Adding this item will remove all current items from your cart.</p>
                </div>
                
                <div class="restaurant-comparison">
                    <div class="restaurant-compare current">
                        <div class="restaurant-compare-content">
                            <div class="restaurant-compare-logo">
                                <img id="currentRestaurantLogo" src="" alt="">
                            </div>
                            <div class="restaurant-compare-name" id="currentRestaurantName"></div>
                        </div>
                        <span class="restaurant-compare-badge badge-current">Current</span>
                    </div>
                    
                    <div class="restaurant-compare new">
                        <div class="restaurant-compare-content">
                            <div class="restaurant-compare-logo">
                                <img id="newRestaurantLogo" src="${restaurantData?.photo || ''}" alt="">
                            </div>
                            <div class="restaurant-compare-name">${restaurantData?.title || restaurantData?.name || 'New Restaurant'}</div>
                        </div>
                        <span class="restaurant-compare-badge badge-new">New</span>
                    </div>
                </div>
                
                <div class="switch-modal-footer">
                    <button class="switch-modal-btn cancel" onclick="closeSwitchModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button class="switch-modal-btn confirm" onclick="confirmRestaurantSwitch()">
                        <i class="fas fa-check"></i> Switch Restaurant
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    const modalContainer = document.createElement('div');
    modalContainer.id = 'restaurantSwitchModal';
    modalContainer.innerHTML = modalHTML;
    document.body.appendChild(modalContainer);
    
    // Set current restaurant info
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart.length > 0) {
        // Fetch current restaurant details
        fetchRestaurantDetails(cart[0].vendorID).then(restaurant => {
            const currentLogo = document.getElementById('currentRestaurantLogo');
            const currentName = document.getElementById('currentRestaurantName');
            
            if (currentLogo) currentLogo.src = restaurant.logo;
            if (currentName) currentName.textContent = restaurant.name;
        });
    }
}

function closeSwitchModal() {
    const modal = document.getElementById('restaurantSwitchModal');
    if (modal) {
        modal.remove();
    }
}

function confirmRestaurantSwitch() {
    const quantity = parseInt(document.getElementById('footerQuantityInput').value) || 1;
    const selectedAddons = [];
    
    document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
        selectedAddons.push({
            title: cb.dataset.title,
            price: parseFloat(cb.dataset.price) || 0,
            description: cb.dataset.description || ''
        });
    });
    
    // Create new cart with only this item
    const cartItem = {
        id: productID,
        name: productData.name,
        price: finalPrice,
        qty: quantity,
        photo: productData.photo,
        vendorID: productData.vendorID,
        selectedAddons: selectedAddons,
        timestamp: new Date().toISOString()
    };
    
    const cart = [cartItem];
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Save to Firebase if logged in
    if (user) {
        saveCartToFirebase(cart);
    }
    
    // Close modal
    closeSwitchModal();
    
    // Show success message
    showToast("Cart cleared and new item added!", "success");
    
    // Update cart count
    updateCartCount();
}

// Save cart to Firebase
async function saveCartToFirebase(cart) {
    try {
        const user = auth.currentUser;
        if (!user) return;
        
        await db.collection("users").doc(user.uid).set({
            cart: cart,
            cartUpdatedAt: firebase.firestore.FieldValue.serverTimestamp()
        }, { merge: true });
    } catch (error) {
        console.error("Error saving cart to Firebase:", error);
    }
}

// Update cart count in header
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
    
    // Update header cart count
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
        cartCountElement.style.display = totalItems > 0 ? 'flex' : 'none';
    }
}

// Fetch restaurant details (helper function)
async function fetchRestaurantDetails(vendorId) {
    try {
        const vendorDoc = await db.collection("vendors").doc(vendorId).get();
        if (vendorDoc.exists) {
            const vendorData = vendorDoc.data();
            return {
                id: vendorId,
                name: vendorData.title || vendorData.name || "Unknown Restaurant",
                logo: vendorData.photo || vendorData.authorProfilePic || 'https://via.placeholder.com/35x35?text=R',
                category: vendorData.categoryTitle || 'Restaurant',
                rating: vendorData.reviewsCount > 0 ? (vendorData.reviewsSum / vendorData.reviewsCount).toFixed(1) : "0.0",
                reviewsCount: vendorData.reviewsCount || 0
            };
        }
    } catch (error) {
        console.error("Error fetching restaurant details:", error);
    }
    
    return {
        id: vendorId,
        name: "Unknown Restaurant",
        logo: 'https://via.placeholder.com/35x35?text=R',
        category: 'Restaurant',
        rating: "0.0",
        reviewsCount: 0
    };
}

// Tab switching
function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    event.currentTarget.classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}Tab`).classList.add('active');
}

// Load reviews
function loadReviews() {
    const reviewsContent = document.getElementById('reviewsContent');
    
    db.collection("foods_review")
        .where("product_id", "==", productID)
        .orderBy("timestamp", "desc")
        .limit(10)
        .get()
        .then(snapshot => {
            if (snapshot.empty) {
                reviewsContent.innerHTML = `
                    <div style="text-align: center; padding: 40px 20px;">
                        <i class="fas fa-comments" style="font-size: 48px; color: #ddd; margin-bottom: 15px;"></i>
                        <h3 style="color: #666; margin-bottom: 10px;">No Reviews Yet</h3>
                        <p style="color: #999;">Be the first to review this product!</p>
                    </div>
                `;
                return;
            }
            
            let reviewsHTML = '';
            snapshot.forEach(rdoc => {
                const r = rdoc.data();
                const date = r.timestamp ? 
                    new Date(r.timestamp.seconds * 1000).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    }) : 'Recently';
                
                const stars = '★'.repeat(Math.round(r.rating || 0)) + '☆'.repeat(5 - Math.round(r.rating || 0));
                
                reviewsHTML += `
                    <div class="review-item">
                        <div class="review-header">
                            <div>
                                <div class="reviewer-name">${r.name || 'Anonymous Customer'}</div>
                                <div class="review-rating">${stars}</div>
                            </div>
                            <div class="review-date">${date}</div>
                        </div>
                        <div class="review-comment">${r.comment || 'No comment provided.'}</div>
                    </div>
                `;
            });
            
            reviewsContent.innerHTML = reviewsHTML;
        })
        .catch(err => {
            console.error("Error loading reviews:", err);
            reviewsContent.innerHTML = `
                <div style="text-align: center; padding: 40px 20px; color: #666;">
                    <i class="fas fa-exclamation-triangle" style="margin-bottom: 15px;"></i>
                    <p>Unable to load reviews at this time.</p>
                </div>
            `;
        });
}

// Load related products
function loadRelatedProducts(categoryID) {
    const relatedGrid = document.getElementById('relatedProductsGrid');
    
    db.collection("vendor_products")
        .where("categoryID", "==", categoryID)
        .where("publish", "==", true)
        .limit(6)
        .get()
        .then(snapshot => {
            let products = [];
            snapshot.forEach(doc => {
                if (doc.id !== productID) {
                    products.push({ id: doc.id, ...doc.data() });
                }
            });
            
            if (products.length === 0) {
                relatedGrid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #666;">
                        <i class="fas fa-utensils" style="font-size: 48px; margin-bottom: 15px; opacity: 0.3;"></i>
                        <p>No related products found.</p>
                    </div>
                `;
                return;
            }
            
            // Shuffle and take max 4 products
            products = products.sort(() => 0.5 - Math.random()).slice(0, 4);
            
            let productsHTML = '';
            products.forEach(product => {
                const ratingCount = product.reviewAttributes?.reviewsCount || 0;
                const ratingSum = product.reviewAttributes?.reviewsSum || 0;
                const rating = ratingCount > 0 ? (ratingSum/ratingCount).toFixed(1) : "0.0";
                const price = product.disPrice && product.disPrice !== "0" ? product.disPrice : product.price;
                const finalProductPrice = parseFloat(price || 0);
                
                productsHTML += `
                    <div class="product-card" onclick="window.location.href='product.php?id=${product.id}'">
                        <img src="${product.photo || 'https://via.placeholder.com/300x180?text=Product'}" 
                             alt="${product.name}" 
                             class="product-card-img"
                             loading="lazy"
                             onerror="this.src='https://via.placeholder.com/300x180?text=Product'">
                        <div class="product-card-content">
                            <h3 class="product-card-title">${product.name}</h3>
                            <div class="product-card-price">₱${finalProductPrice.toFixed(2)}</div>
                            <div class="product-card-rating">
                                <span class="rating-stars">${'★'.repeat(Math.floor(rating))}${'☆'.repeat(5 - Math.floor(rating))}</span>
                                <span>${rating} (${ratingCount})</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            relatedGrid.innerHTML = productsHTML;
        })
        .catch(err => {
            console.error("Error loading related products:", err);
            relatedGrid.innerHTML = `
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: #666;">
                    <p>Unable to load related products.</p>
                </div>
            `;
        });
}

// Setup event listeners
function setupEventListeners() {
    // Order button
    const orderBtn = document.getElementById('orderBtn');
    if (orderBtn) {
        orderBtn.addEventListener('click', async () => {
            if (!user) {
                showToast("Please login or signup first!");
                setTimeout(() => {
                    window.location.href = "../login.php";
                }, 1500);
                return;
            }
            
            const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
            const selectedAddons = [];
            
            document.querySelectorAll('.addon-checkbox:checked').forEach(cb => {
                selectedAddons.push({
                    title: cb.dataset.title,
                    price: parseFloat(cb.dataset.price) || 0,
                    description: cb.dataset.description || ''
                });
            });
            
            // Get restaurant name properly
            const restaurantName = restaurantData ? (restaurantData.title || restaurantData.name || "Coffee Cat Cafe") : "Unknown Restaurant";
            const restaurantLogo = restaurantData ? (restaurantData.photo || restaurantData.authorProfilePic || "") : "";
            
            // Create the order data with proper structure
            const orderData = {
                products: [{
                    id: productID,
                    name: productData.name,
                    price: finalPrice.toString(),
                    discountPrice: disPrice > 0 ? disPrice.toString() : "0",
                    quantity: quantity,
                    photo: productData.photo || '',
                    vendorID: productData.vendorID,
                    category_id: productData.categoryID || '',
                    extras: selectedAddons.map(addon => addon.title),
                    extras_price: selectedAddons.reduce((sum, addon) => sum + addon.price, 0).toFixed(2),
                    variant_info: null
                }],
                vendorID: productData.vendorID,
                restaurantName: restaurantName,
                restaurantLogo: restaurantLogo,
                orderTotal: (finalPrice * quantity) + selectedAddons.reduce((sum, addon) => sum + addon.price, 0),
                orderDate: new Date().toISOString()
            };
            
            // Save to localStorage with error handling
            try {
                localStorage.setItem("currentOrder", JSON.stringify(orderData));
                localStorage.setItem("singleProductOrder", JSON.stringify({
                    product_id: productID,
                    name: productData.name,
                    price: finalPrice,
                    quantity: quantity,
                    photo: productData.photo,
                    vendorID: productData.vendorID,
                    restaurantName: restaurantName,
                    restaurantLogo: restaurantLogo,
                    total: orderData.orderTotal,
                    addons: selectedAddons
                }));
                
                showToast("Order saved! Redirecting...");
                
                // Redirect to order page
                setTimeout(() => {
                    window.location.href = "../foods/order.php";
                }, 1000);
                
            } catch (error) {
                console.error("Error saving order:", error);
                showToast("Error saving order. Please try again.");
            }
        });
    }
    
    // Cart button
    const cartBtn = document.getElementById('cartBtn');
    if (cartBtn) {
        cartBtn.addEventListener('click', () => {
            addToCartFromFooter(); // Reuse the same function
        });
    }
    
    // Wishlist button
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            if (!user) {
                showToast("Please login to add to wishlist!");
                setTimeout(() => {
                    window.location.href = "../login.php";
                }, 1500);
                return;
            }
            
            const isActive = !this.classList.contains('active');
            this.classList.toggle('active');
            
            this.innerHTML = isActive ? 
                '<i class="fas fa-heart"></i>' : 
                '<i class="far fa-heart"></i>';
            
            // Add animation
            if (isActive) {
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 300);
            }
            
            // Wishlist logic here
            const wishlistItem = {
                product_id: productID,
                name: productData.name,
                price: finalPrice,
                photo: productData.photo,
                added_date: new Date().toISOString()
            };
            
            let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
            
            if (isActive) {
                // Add to wishlist
                wishlist = wishlist.filter(item => item.product_id !== productID);
                wishlist.push(wishlistItem);
                showToast("Added to wishlist!");
            } else {
                // Remove from wishlist
                wishlist = wishlist.filter(item => item.product_id !== productID);
                showToast("Removed from wishlist!");
            }
            
            localStorage.setItem('wishlist', JSON.stringify(wishlist));
        });
    }
    
    // Quantity input validation
    const quantityInput = document.getElementById('quantityInput');
    if (quantityInput) {
        quantityInput.addEventListener('input', function(e) {
            const max = this.max ? parseInt(this.max) : Infinity;
            const min = parseInt(this.getAttribute('min')) || 1;
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < min) value = min;
            if (value > max) value = max;
            
            this.value = value;
            updateMainTotal();
        });
        
        // Prevent invalid input
        quantityInput.addEventListener('keydown', function(e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') {
                e.preventDefault();
            }
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have a product ID in URL
    if (productID) {
        console.log("Product page loaded for ID:", productID);
    }
    
    // Initialize scroll behavior
    setTimeout(() => {
        handleScroll(); // Check initial scroll position
    }, 100);
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

// Show header when interacting with page (click, tap)
document.addEventListener('click', function() {
    if (header && header.classList.contains('header-hide')) {
        header.classList.remove('header-hide');
        header.classList.add('header-show');
        
        // Auto hide after 3 seconds if not at top
        setTimeout(() => {
            if (window.scrollY > 100) {
                header.classList.add('header-hide');
                header.classList.remove('header-show');
            }
        }, 3000);
    }
});

// Also show header when user hovers near top (desktop)
if (!isMobile) {
    document.addEventListener('mousemove', function(e) {
        if (e.clientY < 50 && header && header.classList.contains('header-hide')) {
            header.classList.remove('header-hide');
            header.classList.add('header-show');
        }
    });
}
</script>

</body>
</html>
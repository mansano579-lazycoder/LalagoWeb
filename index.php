<?php
session_start();

$isLoggedIn = isset($_SESSION['uid']);
$userEmail  = $_SESSION['email'] ?? '';
$userId     = $_SESSION['uid'] ?? null;

// Initialize location variables
$hasSavedLocation = false;
$userLocationData = null;
$userLatitude = null;
$userLongitude = null;
$userAddress = null;
$locationSource = 'session'; // session, firebase, or none

// Check if user has saved location in session first
if (isset($_SESSION['user_location'])) {
    $userLocationData = $_SESSION['user_location'];
    $userLatitude = $userLocationData['lat'] ?? null;
    $userLongitude = $userLocationData['lng'] ?? null;
    $userAddress = $userLocationData['address'] ?? null;
    $hasSavedLocation = ($userLatitude && $userLongitude);
    $locationSource = 'session';
}

// The JavaScript will handle fetching location from Firebase
// when the page loads (in the checkLocationStatus() function)
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LalaGO - Home</title>

<!-- CSS -->
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/carousel.css">
<link rel="stylesheet" href="css/random.css">
<link rel="stylesheet" href="css/categories.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<!-- Swiper CSS for swipeable random picks -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

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

/* ================= LOCATION POPUP MODAL ================= */
.location-modal {
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
}

.location-modal.active {
  display: flex;
}

.location-modal-content {
  background: var(--white);
  border-radius: var(--radius);
  width: 90%;
  max-width: 500px;
  padding: 30px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.4s ease;
  position: relative;
  max-height: 90vh;
  overflow-y: auto;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(50px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  text-align: center;
  margin-bottom: 25px;
  padding-bottom: 20px;
  border-bottom: 2px solid var(--gray-light);
}

.modal-header i {
  font-size: 3.5rem;
  color: var(--info);
  margin-bottom: 15px;
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
}

.modal-header h2 {
  font-size: 1.8rem;
  color: var(--secondary);
  margin-bottom: 10px;
}

.modal-header p {
  color: var(--gray);
  font-size: 1rem;
  line-height: 1.5;
}

.modal-body {
  margin-bottom: 25px;
}

.location-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
  margin-bottom: 25px;
}

.location-option {
  background: var(--light);
  border: 2px solid var(--gray-light);
  border-radius: var(--radius);
  padding: 25px 20px;
  text-align: center;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.location-option:hover {
  border-color: var(--info);
  transform: translateY(-5px);
  box-shadow: var(--shadow);
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(255, 107, 53, 0.05));
}

.location-option.active {
  border-color: var(--info);
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
}

.location-option i {
  font-size: 2.5rem;
  color: var(--info);
  background: var(--white);
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.location-option h3 {
  font-size: 1.2rem;
  color: var(--secondary);
  margin: 0;
}

.location-option p {
  font-size: 0.9rem;
  color: var(--gray);
  margin: 0;
  line-height: 1.4;
}

.manual-location-input {
  margin-top: 20px;
  padding: 20px;
  background: var(--light);
  border-radius: var(--radius);
  border: 2px dashed var(--gray-light);
}

.manual-location-input h4 {
  font-size: 1.1rem;
  color: var(--secondary);
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.manual-location-input h4 i {
  color: var(--primary);
}

.address-input {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid var(--gray-light);
  border-radius: var(--radius-sm);
  font-size: 0.95rem;
  margin-bottom: 15px;
  transition: var(--transition);
}

.address-input:focus {
  outline: none;
  border-color: var(--info);
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.coordinates-inputs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 15px;
}

.coordinate-input {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid var(--gray-light);
  border-radius: var(--radius-sm);
  font-size: 0.9rem;
  transition: var(--transition);
}

.coordinate-input:focus {
  outline: none;
  border-color: var(--info);
}

.coordinate-label {
  font-size: 0.8rem;
  color: var(--gray);
  margin-bottom: 5px;
  display: block;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 15px;
  padding-top: 20px;
  border-top: 2px solid var(--gray-light);
}

.modal-btn {
  padding: 12px 30px;
  border-radius: 50px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: 2px solid transparent;
}

.modal-btn.cancel {
  background: transparent;
  color: var(--gray);
  border-color: var(--gray-light);
}

.modal-btn.cancel:hover {
  background: var(--gray-light);
  color: var(--gray-dark);
}

.modal-btn.confirm {
  background: var(--info);
  color: var(--white);
  border-color: var(--info);
}

.modal-btn.confirm:hover {
  background: var(--primary);
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.modal-btn.confirm:disabled {
  background: var(--gray);
  border-color: var(--gray);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.location-loading {
  text-align: center;
  padding: 30px;
}

.location-loading i {
  font-size: 2.5rem;
  color: var(--info);
  margin-bottom: 15px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.location-loading h3 {
  color: var(--secondary);
  margin-bottom: 10px;
}

.location-loading p {
  color: var(--gray);
}

.location-success {
  text-align: center;
  padding: 30px;
}

.location-success i {
  font-size: 3rem;
  color: var(--success);
  margin-bottom: 15px;
  background: rgba(39, 174, 96, 0.1);
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
}

.location-success h3 {
  color: var(--secondary);
  margin-bottom: 10px;
}

.location-success p {
  color: var(--gray);
  margin-bottom: 20px;
}

.shipping-addresses {
  margin-top: 20px;
  max-height: 300px;
  overflow-y: auto;
}

.shipping-address-item {
  background: var(--light);
  border: 2px solid var(--gray-light);
  border-radius: var(--radius);
  padding: 15px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 12px;
}

.shipping-address-item:hover {
  border-color: var(--info);
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.05), rgba(255, 107, 53, 0.05));
}

.shipping-address-item.active {
  border-color: var(--info);
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
}

.shipping-address-item i {
  color: var(--info);
  font-size: 1.2rem;
  flex-shrink: 0;
}

.shipping-address-content {
  flex: 1;
}

.shipping-address-name {
  font-weight: 600;
  color: var(--secondary);
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.default-badge {
  background: var(--success);
  color: var(--white);
  font-size: 0.7rem;
  padding: 2px 8px;
  border-radius: 12px;
}

.shipping-address-text {
  font-size: 0.85rem;
  color: var(--gray);
  line-height: 1.4;
}

/* ================= LAYOUT ENHANCEMENTS ================= */
.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* ================= MAIN LAYOUT ================= */
.main-wrapper {
  display: flex;
  gap: 30px;
  margin-top: 30px;
  position: relative;
}

/* ================= CATEGORIES SIDEBAR ================= */
.categories-sidebar {
  flex: 0 0 280px;
  position: sticky;
  top: 100px;
  align-self: flex-start;
  max-height: calc(100vh - 140px);
  overflow-y: auto;
}

.categories-sidebar::-webkit-scrollbar {
  width: 6px;
}

.categories-sidebar::-webkit-scrollbar-track {
  background: var(--gray-light);
  border-radius: 10px;
}

.categories-sidebar::-webkit-scrollbar-thumb {
  background: var(--primary);
  border-radius: 10px;
}

/* ================= ENHANCED CATEGORIES STYLING ================= */
.categories-container {
  background: var(--white);
  border-radius: var(--radius);
  padding: 25px;
  box-shadow: var(--shadow);
}

.category-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.category-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--secondary);
  display: flex;
  align-items: center;
  gap: 10px;
}

.category-title i {
  color: var(--primary);
}

.category-filter {
  background: var(--light);
  border: none;
  padding: 8px 15px;
  border-radius: var(--radius-sm);
  color: var(--gray-dark);
  font-size: 0.9rem;
  cursor: pointer;
  transition: var(--transition);
}

.category-filter:hover {
  background: var(--gray-light);
}

.category-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 18px 20px;
  margin-bottom: 10px;
  border-radius: var(--radius);
  background: var(--white);
  border: 2px solid transparent;
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.category-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: var(--primary);
  transform: scaleY(0);
  transition: transform 0.3s ease;
}

.category-item:hover {
  background: var(--light);
  transform: translateX(5px);
  border-color: var(--gray-light);
}

.category-item:hover::before {
  transform: scaleY(1);
}

.category-item.active {
  background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(229, 90, 43, 0.05));
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.1);
}

.category-item.active::before {
  transform: scaleY(1);
}

.category-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
  font-size: 1.2rem;
  flex-shrink: 0;
}

.category-content {
  flex: 1;
}

.category-name {
  font-weight: 600;
  color: var(--secondary);
  margin-bottom: 4px;
  font-size: 1rem;
}

.category-count {
  font-size: 0.85rem;
  color: var(--gray);
  background: var(--light);
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}

/* ================= MAIN CONTENT AREA ================= */
.main-content {
  flex: 1;
  min-width: 0;
}

/* ================= ADVERTISEMENTS CAROUSEL - UPDATED ================= */
.advertisements-carousel {
  margin: 30px 0;
  padding: 20px;
  background: linear-gradient(135deg, rgba(255, 107, 53, 0.05), rgba(52, 152, 219, 0.05));
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

.advertisements-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.advertisements-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.advertisements-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--info));
  border-radius: 2px;
}

.advertisements-container {
  position: relative;
  border-radius: var(--radius);
  background: var(--white);
  padding: 15px;
  min-height: 300px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.advertisements-loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 50px;
  text-align: center;
  color: var(--gray);
}

.advertisements-loading i {
  font-size: 2rem;
  margin-bottom: 15px;
  color: var(--primary);
}

/* Desktop Carousel Styles */
.desktop-carousel {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius);
  width: 100%;
  height: 300px;
}

.advertisement-slide {
  width: 100%;
  height: 300px;
  border-radius: var(--radius);
  overflow: hidden;
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.5s ease, visibility 0.5s ease;
}

.advertisement-slide.active {
  opacity: 1;
  visibility: visible;
  position: relative;
}

.advertisement-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.advertisement-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  padding: 20px;
  color: var(--white);
}

.advertisement-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 5px;
  color: var(--white);
}

.advertisement-description {
  font-size: 1rem;
  margin-bottom: 10px;
  opacity: 0.9;
}

.advertisement-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.9);
  color: var(--primary);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
}

.advertisement-nav:hover {
  background: var(--primary);
  color: var(--white);
}

.advertisement-nav.prev {
  left: 20px;
}

.advertisement-nav.next {
  right: 20px;
}

.advertisement-dots {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 15px;
}

.advertisement-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--gray-light);
  cursor: pointer;
  transition: var(--transition);
}

.advertisement-dot.active {
  background: var(--primary);
}

.advertisement-dot:hover {
  background: var(--primary-dark);
}

/* Mobile Carousel Styles */
.mobile-carousel {
  display: none;
  width: 100%;
  overflow: hidden;
  position: relative;
}

.mobile-ad-track {
  display: flex;
  transition: transform 0.5s ease;
  gap: 15px;
  padding: 5px;
}

.mobile-ad-slide {
  flex: 0 0 calc((100% - 30px) / 3); /* Show 3 slides with gaps */
  height: 180px;
  border-radius: var(--radius);
  overflow: hidden;
  position: relative;
  box-shadow: var(--shadow);
}

.mobile-ad-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.mobile-ad-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
  padding: 12px;
  color: var(--white);
}

.mobile-ad-title {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 3px;
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.mobile-ad-description {
  font-size: 0.8rem;
  opacity: 0.9;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.mobile-ad-indicators {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 15px;
}

.mobile-ad-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--gray-light);
  transition: var(--transition);
  cursor: pointer;
}

.mobile-ad-indicator.active {
  background: var(--primary);
  transform: scale(1.2);
}
/* ================= UPDATED NEARBY FOODS COMPACT LAYOUT ================= */
.nearby-food-compact {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  position: relative;
  width: 100%;
  display: flex;
  align-items: stretch;
  min-height: 120px;
  max-height: 130px;
  border: 1px solid var(--gray-light);
}

.nearby-food-compact:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-hover);
  border-color: var(--primary);
}

/* Image on left - fixed width */
.nearby-food-image {
  width: 130px;
  min-width: 130px;
  height: 100%;
  overflow: hidden;
  position: relative;
  background-color: var(--light);
}

.nearby-food-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}

.nearby-food-compact:hover .nearby-food-image img {
  transform: scale(1.05);
}

/* Content on right */
.nearby-food-content {
  flex: 1;
  padding: 12px 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-width: 0;
  overflow: hidden;
}

.nearby-food-header {
  margin-bottom: 8px;
}

.nearby-food-name {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--secondary);
  margin-bottom: 5px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 2.6rem;
}

.nearby-food-restaurant {
  font-size: 0.85rem;
  color: var(--gray);
  display: flex;
  align-items: center;
  gap: 5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nearby-food-restaurant i {
  font-size: 0.8rem;
  color: var(--primary);
  flex-shrink: 0;
}

.nearby-food-description {
  color: var(--gray);
  font-size: 0.8rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 5px;
  flex: 1;
  min-height: 2.4rem;
}

/* Truncate to ~5 words */
.truncate-description {
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  max-height: 2.4rem;
}

/* Compact price section */
.nearby-food-price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  padding-top: 8px;
  border-top: 1px solid var(--gray-light);
}

.nearby-food-price {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--primary);
  display: flex;
  align-items: center;
  gap: 5px;
}

.nearby-food-price .original-price {
  font-size: 0.85rem;
  color: var(--gray);
  text-decoration: line-through;
  font-weight: 400;
}

/* Distance badge for nearby foods */
.nearby-distance-badge {
  position: absolute;
  top: 8px;
  left: 8px;
  background: var(--info);
  color: var(--white);
  padding: 4px 8px;
  border-radius: 12px;
  font-size: 0.75rem;
  font-weight: 600;
  z-index: 2;
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Restaurant logo on image */
.nearby-restaurant-avatar {
  position: absolute;
  bottom: 8px;
  left: 8px;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--white);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  z-index: 2;
  background-color: var(--white);
}

.nearby-restaurant-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* Adjust Swiper for compact layout */
.swiper-nearby-container {
  padding: 10px 5px 30px;
}

.swiper-nearby-slide {
  width: 380px !important;
  height: auto;
}

/* Rating in compact view */
.nearby-food-rating {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 0.8rem;
  color: var(--warning);
  margin-bottom: 5px;
}

.nearby-food-rating .rating-value {
  font-weight: 600;
  color: var(--secondary);
  margin-left: 3px;
}

.nearby-food-rating .rating-count {
  color: var(--gray);
  font-size: 0.75rem;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .swiper-nearby-slide {
    width: 350px !important;
  }
  
  .nearby-food-image {
    width: 120px;
    min-width: 120px;
  }
  
  .nearby-food-name {
    font-size: 1rem;
  }
}

@media (max-width: 768px) {
  .nearby-food-compact {
    min-height: 110px;
    max-height: 120px;
  }
  
  .nearby-food-image {
    width: 110px;
    min-width: 110px;
  }
  
  .nearby-food-content {
    padding: 10px 12px;
  }
  
  .nearby-food-name {
    font-size: 1rem;
    min-height: 2.4rem;
  }
  
  .nearby-food-description {
    font-size: 0.75rem;
    min-height: 2.2rem;
  }
  
  .swiper-nearby-slide {
    width: 320px !important;
  }
  
  .nearby-restaurant-avatar {
    width: 32px;
    height: 32px;
  }
}

@media (max-width: 480px) {
  .nearby-food-compact {
    min-height: 100px;
    max-height: 110px;
  }
  
  .nearby-food-image {
    width: 100px;
    min-width: 100px;
  }
  
  .swiper-nearby-slide {
    width: 300px !important;
  }
  
  .nearby-food-name {
    font-size: 0.95rem;
    min-height: 2.2rem;
  }
  
  .nearby-food-description {
    font-size: 0.7rem;
    min-height: 2rem;
  }
  
  .nearby-food-price {
    font-size: 1.1rem;
  }
  
  .nearby-restaurant-avatar {
    width: 28px;
    height: 28px;
    border-width: 1.5px;
  }
}


/* ================= TOP RESTAURANTS SECTION ================= */
.top-restaurants-section {
  margin: 40px 0;
}
/* Add to your existing CSS */
.top-restaurants-section .no-results,
.nearby-restaurants-section .no-results {
  background: var(--light);
  border-radius: var(--radius);
  padding: 40px;
  text-align: center;
  margin: 20px 0;
}

.top-restaurants-section .no-results i,
.nearby-restaurants-section .no-results i {
  font-size: 3rem;
  color: var(--gray-light);
  margin-bottom: 20px;
}

.top-restaurants-section .no-results h3,
.nearby-restaurants-section .no-results h3 {
  color: var(--secondary);
  font-size: 1.5rem;
  margin-bottom: 10px;
}

.top-restaurants-section .no-results p,
.nearby-restaurants-section .no-results p {
  color: var(--gray);
  font-size: 1rem;
  margin-bottom: 20px;
}
.top-restaurants-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.top-restaurants-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.top-restaurants-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--warning), var(--primary));
  border-radius: 2px;
}

/* ================= NEARBY RESTAURANTS SECTION ================= */
.nearby-restaurants-section {
  margin: 40px 0;
}

.nearby-restaurants-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.nearby-restaurants-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.nearby-restaurants-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--info), var(--primary));
  border-radius: 2px;
}

/* ================= RESTAURANT CARD WITH HEART BUTTON ================= */
.restaurant-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  position: relative;
  width: 280px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  height: 320px;
}

.restaurant-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

.restaurant-image-container {
  position: relative;
  width: 100%;
  height: 160px;
  overflow: hidden;
  flex-shrink: 0;
}

.restaurant-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.restaurant-card:hover .restaurant-image-container img {
  transform: scale(1.05);
}

/* HEART BUTTON STYLES */
.heart-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  transition: var(--transition);
  color: var(--gray);
  font-size: 1.2rem;
}

.heart-btn:hover {
  background: var(--white);
  color: var(--danger);
  transform: scale(1.1);
}

.heart-btn.active {
  color: var(--danger);
  background: rgba(231, 76, 60, 0.1);
}

.heart-btn.active i {
  animation: pulse 0.3s ease;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

.restaurant-info {
  padding: 20px 15px 15px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.restaurant-header {
  margin-bottom: 10px;
}

.restaurant-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--secondary);
  margin-bottom: 8px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.restaurant-distance {
  display: flex;
  align-items: center;
  gap: 8px;
  color: var(--gray);
  font-size: 0.9rem;
  margin-bottom: 12px;
}

.restaurant-distance i {
  color: var(--info);
}

.restaurant-rating {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 15px;
}

.rating-stars {
  color: var(--warning);
  font-size: 0.85rem;
}

.rating-value {
  font-weight: 600;
  color: var(--secondary);
  font-size: 0.9rem;
  margin-left: 3px;
}

.rating-count {
  color: var(--gray);
  font-size: 0.8rem;
}

/* Distance/time badge */
.restaurant-distance-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--info);
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 2;
  backdrop-filter: blur(4px);
}

/* Top-rated badge */
.top-rated-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: linear-gradient(135deg, var(--warning), var(--primary));
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 2;
  backdrop-filter: blur(4px);
}

/* ================= PRODUCT CARD STYLES (for foods) ================= */
.product-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  position: relative;
  width: 250px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  height: 280px;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 140px;
  overflow: hidden;
  flex-shrink: 0;
}

.product-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.product-card:hover .product-image-container img {
  transform: scale(1.05);
}

.product-info {
  padding: 15px 12px 12px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--secondary);
  margin-bottom: 8px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  font-size: 1.2rem;
  font-weight: 800;
  color: var(--primary);
  margin-top: auto;
}

.product-price .original-price {
  font-size: 0.9rem;
  color: var(--gray);
  text-decoration: line-through;
  font-weight: 400;
  margin-left: 8px;
}

.sulit-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  background: var(--success);
  color: var(--white);
  padding: 4px 10px;
  border-radius: 15px;
  font-size: 0.75rem;
  font-weight: 600;
  z-index: 2;
}

/* ================= RESTAURANTS & PRODUCTS GRID LAYOUT ================= */
.restaurants-products-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
  margin-top: 20px;
}

.restaurants-section,
.products-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-title {
  font-size: 1.3rem;
  font-weight: 600;
  color: var(--secondary);
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-title i {
  color: var(--primary);
}

.restaurants-container,
.products-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
  .restaurants-products-grid {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  
  .restaurant-card {
    width: 100%;
    max-width: 300px;
  }
  
  .product-card {
    width: 100%;
    max-width: 250px;
  }
}

@media (max-width: 768px) {
  .restaurants-container,
  .products-container {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .restaurants-container,
  .products-container {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  
  .restaurant-card,
  .product-card {
    width: 100%;
    max-width: none;
  }
}



/* ================= SAVED LOCATION BADGE ================= */
.saved-location-badge {
  background: linear-gradient(135deg, var(--success), #27ae60);
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3); }
  50% { box-shadow: 0 2px 15px rgba(39, 174, 96, 0.6); }
  100% { box-shadow: 0 2px 8px rgba(39, 174, 96, 0.3); }
}

/* ================= LOCATION INFO BOX ================= */
.location-info {
  background: var(--light);
  border: 2px solid var(--gray-light);
  border-radius: var(--radius);
  padding: 10px 15px;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: var(--transition);
  min-width: 250px;
  max-width: 400px;
  overflow: hidden;
}

.location-info:hover {
  border-color: var(--info);
  background: rgba(52, 152, 219, 0.05);
}

.location-info i {
  color: var(--info);
  font-size: 1rem;
  flex-shrink: 0;
}

.location-info span {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--secondary);
  font-weight: 500;
}

/* ================= REFRESH BUTTON STYLES ================= */
.refresh-nearby-btn {
  background: var(--info);
  color: var(--white);
  border: 2px solid var(--info);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.refresh-nearby-btn:hover {
  background: var(--white);
  color: var(--info);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.refresh-nearby-btn:active {
  transform: translateY(0);
}

.refresh-nearby-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* ================= SAVE LOCATION BUTTON ================= */
.save-location-btn {
  background: var(--success);
  color: var(--white);
  border: 2px solid var(--success);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
}

.save-location-btn:hover {
  background: var(--white);
  color: var(--success);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
}

.save-location-btn:active {
  transform: translateY(0);
}

.save-location-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* ================= CHANGE LOCATION BUTTON ================= */
.change-location-btn {
  background: transparent;
  border: none;
  color: var(--info);
  font-size: 0.8rem;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.change-location-btn:hover {
  background: rgba(52, 152, 219, 0.1);
}


/* ================= ENHANCED RANDOM FOOD SECTION - SWIPER STYLE ================= */
.random-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 40px 0 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.random-section-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.random-section-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--primary-dark));
  border-radius: 2px;
}

.refresh-random-btn {
  background: var(--white);
  color: var(--primary);
  border: 2px solid var(--primary);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
}

.refresh-random-btn:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}
/* ================= RECOMMENDED FOR YOU SECTION ================= */
.recommended-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 40px 0 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.recommended-section-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.recommended-section-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--warning), var(--primary));
  border-radius: 2px;
}

.refresh-recommended-btn {
  background: var(--white);
  color: var(--warning);
  border: 2px solid var(--warning);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
}

.refresh-recommended-btn:hover {
  background: var(--warning);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

/* ================= SWIPER CONTAINER FOR RECOMMENDED ================= */
.swiper-recommended-container {
  width: 100%;
  padding: 20px 10px 40px;
  margin: 20px 0;
  position: relative;
}

.swiper-recommended-wrapper {
  padding: 10px 5px;
}

.swiper-recommended-slide {
  height: auto;
  display: flex;
  justify-content: center;
}

/* Highlight badge for recommended items */
.recommended-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background: linear-gradient(135deg, var(--warning), var(--primary));
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 1;
  backdrop-filter: blur(4px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .recommended-section-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .refresh-recommended-btn {
    align-self: flex-start;
  }
}
/* ================= SULIT MEALS SECTION ================= */
.sulit-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 40px 0 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
}

.sulit-section-title {
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--secondary);
  position: relative;
  display: flex;
  align-items: center;
  gap: 10px;
}

.sulit-section-title::after {
  content: '';
  position: absolute;
  bottom: -17px;
  left: 0;
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, var(--success), var(--primary));
  border-radius: 2px;
}

.view-all-btn {
  background: var(--white);
  color: var(--primary);
  border: 2px solid var(--primary);
  padding: 10px 20px;
  border-radius: 50px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.view-all-btn:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}

/* ================= SWIPER CONTAINERS ================= */
.swiper-random-container,
.swiper-nearby-container,
.swiper-sulit-container {
  width: 100%;
  padding: 20px 10px 40px;
  margin: 20px 0;
  position: relative;
}

.swiper-random-wrapper,
.swiper-nearby-wrapper,
.swiper-sulit-wrapper {
  padding: 10px 5px;
}

.swiper-random-slide,
.swiper-nearby-slide,
.swiper-sulit-slide {
  height: auto;
  display: flex;
  justify-content: center;
}

/* ================= DISTANCE BADGE ================= */
.distance-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: var(--info);
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 1;
  backdrop-filter: blur(4px);
}

/* ================= UPDATED FOOD CARD DESIGN - VERTICAL LAYOUT ================= */
.food-card {
  background: var(--white);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  transition: var(--transition);
  text-decoration: none;
  color: var(--secondary);
  position: relative;
  width: 280px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  height: 480px; /* Fixed height for consistent cards */
}

.food-card:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-hover);
}

/* ================= FIXED FOOD IMAGE CONTAINER - FULL IMAGE ================= */
.food-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden; /* Change back to hidden to prevent overflow */
  flex-shrink: 0;
  background-color: var(--light);
  padding: 0;
  display: block;
}

.food-image-wrapper {
  width: 100%;
  height: 180px; /* Leave space for logo at bottom */
  overflow: hidden;
  position: relative;
}

.food-card .food-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Change from 'contain' to 'cover' */
  display: block;
  transition: transform 0.5s ease;
  background-color: var(--light);
}

.food-card:hover .food-image-wrapper img {
  transform: scale(1.05);
}

/* ================= RESTAURANT AVATAR - FIXED VERSION ================= */
.restaurant-avatar {
  position: absolute;
  bottom: 10px; /* Position it within the food-image-container */
  left: 15px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--white);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  z-index: 3; /* Increase z-index to be above the image */
  background-color: var(--white);
}

.restaurant-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

/* ================= ADJUST FOOD INFO PADDING ================= */
.food-info {
  padding: 20px 15px 15px; /* Reduce top padding since logo is inside image container */
  flex: 1;
  display: flex;
  flex-direction: column;
}

/* ================= ADJUST FOOD INFO PADDING ================= */
.food-info {
  padding: 30px 15px 15px; /* Keep extra top padding for overlap */
  flex: 1;
  display: flex;
  flex-direction: column;
}

.food-header {
  margin-bottom: 12px;
}

.food-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--secondary);
  margin-bottom: 5px;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.restaurant-name {
  font-size: 0.9rem;
  color: var(--gray);
  display: flex;
  align-items: center;
  gap: 6px;
}

.restaurant-name i {
  font-size: 0.8rem;
  color: var(--primary);
}

/* ================= FOOD DESCRIPTION ================= */
.food-description {
  color: var(--gray);
  font-size: 0.85rem;
  margin-bottom: 12px;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  flex: 1;
}

/* ================= FOOD RATING ================= */
.food-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.rating-stars {
  color: var(--warning);
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 2px;
}

.rating-value {
  font-weight: 600;
  color: var(--secondary);
  font-size: 0.9rem;
  margin-left: 3px;
}

.rating-count {
  color: var(--gray);
  font-size: 0.8rem;
}

/* ================= UPDATED PRICE SECTION ================= */
.food-price-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid var(--gray-light);
  margin-top: auto;
}

.food-price {
  font-size: 1.3rem;
  font-weight: 800;
  color: var(--primary);
  display: flex;
  align-items: center;
  gap: 8px;
}

.food-price .original-price {
  font-size: 0.9rem;
  color: var(--gray);
  text-decoration: line-through;
  font-weight: 400;
}

/* ================= FOOD CATEGORY ICONS ROW ================= */
.food-category-icons {
  display: flex;
  gap: 15px;
  margin: 20px 0;
  padding: 15px;
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  overflow-x: auto;
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE and Edge */
}

.food-category-icons::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}

.category-icon-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 12px 15px;
  background: var(--light);
  border-radius: var(--radius);
  border: 2px solid transparent;
  cursor: pointer;
  transition: var(--transition);
  flex: 0 0 auto;
  min-width: 80px;
  text-decoration: none;
  color: var(--secondary);
}

.category-icon-item:hover {
  background: var(--gray-light);
  transform: translateY(-3px);
  border-color: var(--primary);
}

.category-icon-item.active {
  background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(229, 90, 43, 0.05));
  border-color: var(--primary);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.1);
}

.category-icon-circle {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--white);
  font-size: 1.2rem;
  transition: var(--transition);
}

.category-icon-item:hover .category-icon-circle {
  transform: scale(1.1);
}

.category-icon-name {
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .food-category-icons {
    gap: 10px;
    padding: 12px;
  }
  
  .category-icon-item {
    padding: 10px 12px;
    min-width: 70px;
  }
  
  .category-icon-circle {
    width: 45px;
    height: 45px;
    font-size: 1.1rem;
  }
  
  .category-icon-name {
    font-size: 0.75rem;
  }
}

@media (max-width: 480px) {
  .food-category-icons {
    gap: 8px;
    padding: 10px;
  }
  
  .category-icon-item {
    padding: 8px 10px;
    min-width: 65px;
  }
  
  .category-icon-circle {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
}

/* ================= AUTH PROMPT ================= */
.auth-prompt {
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
  border: 2px solid var(--info);
  border-radius: var(--radius);
  padding: 30px;
  margin: 20px 0;
  text-align: center;
}

.auth-prompt-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.auth-prompt i {
  font-size: 2.5rem;
  color: var(--info);
}

.auth-prompt h3 {
  color: var(--secondary);
  font-size: 1.3rem;
  margin-bottom: 5px;
}

.auth-prompt p {
  color: var(--gray);
  font-size: 1rem;
  margin-bottom: 20px;
}

.auth-prompt-buttons {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  justify-content: center;
}

.auth-prompt-btn {
  padding: 10px 25px;
  border-radius: 50px;
  font-size: 0.95rem;
  font-weight: 600;
  text-decoration: none;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.auth-prompt-btn.login {
  background: var(--info);
  color: white;
  border: 2px solid var(--info);
}

.auth-prompt-btn.login:hover {
  background: var(--primary);
  border-color: var(--primary);
  transform: translateY(-2px);
}

.auth-prompt-btn.register {
  background: transparent;
  color: var(--info);
  border: 2px solid var(--info);
}

.auth-prompt-btn.register:hover {
  background: var(--info);
  color: white;
  transform: translateY(-2px);
}

/* ================= NO RESULTS MESSAGE ================= */
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

/* ================= SWIPER NAVIGATION BUTTONS ================= */
.swiper-button-next,
.swiper-button-prev {
  background: var(--white);
  color: var(--primary);
  width: 50px;
  height: 50px;
  border-radius: 50%;
  box-shadow: var(--shadow);
  transition: var(--transition);
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-50%) scale(1.1);
}

.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 20px;
  font-weight: bold;
}

.swiper-button-prev {
  left: -25px;
}

.swiper-button-next {
  right: -25px;
}

/* ================= SEARCH ENTRY POINT ================= */
.search-entry-container {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border-radius: var(--radius);
  padding: 30px;
  margin-bottom: 30px;
  box-shadow: var(--shadow);
  text-align: center;
  position: relative;
  overflow: hidden;
}

.search-entry-container::before {
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

.search-entry-title {
  color: var(--white);
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 20px;
  position: relative;
  z-index: 1;
}

.search-entry-button {
  background: var(--white);
  color: var(--primary);
  border: none;
  padding: 18px 35px;
  border-radius: 50px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 12px;
  position: relative;
  z-index: 1;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.search-entry-button:hover {
  background: var(--secondary);
  color: var(--white);
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

@keyframes shine {
  0% { transform: translateX(-100%) rotate(30deg); }
  100% { transform: translateX(100%) rotate(30deg); }
}

/* ================= CHANGE LOCATION BUTTON ================= */
.change-location-btn {
  background: transparent;
  border: none;
  color: var(--info);
  font-size: 0.8rem;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: var(--transition);
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-left: 8px;
}

.change-location-btn:hover {
  background: rgba(52, 152, 219, 0.1);
}

/* ================= LOCATION NOTICE ================= */
.location-notice {
  background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(255, 107, 53, 0.1));
  border: 2px solid var(--info);
  border-radius: var(--radius);
  padding: 20px;
  margin: 20px 0;
  text-align: center;
}

.location-notice-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
}

.location-notice i {
  font-size: 2rem;
  color: var(--info);
}

.location-notice-text {
  flex: 1;
  min-width: 200px;
}

.location-notice h3 {
  color: var(--secondary);
  margin-bottom: 5px;
  font-size: 1.1rem;
}

.location-notice p {
  color: var(--gray);
  font-size: 0.9rem;
  margin-bottom: 0;
}

.set-location-btn {
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

.set-location-btn:hover {
  background: var(--primary);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

/* ================= RESPONSIVE DESIGN ================= */

/* RESPONSIVE ADVERTISEMENTS */
@media (max-width: 1024px) {
  .advertisement-slide {
    height: 280px;
  }
  
  .desktop-carousel {
    height: 280px;
  }
}

@media (max-width: 768px) {
  .advertisements-carousel {
    padding: 15px;
  }
  
  .advertisements-container {
    padding: 10px;
    min-height: 220px;
  }
  
  .desktop-carousel {
    display: none;
  }
  
  .mobile-carousel {
    display: block;
  }
  
  .mobile-ad-slide {
    height: 160px;
  }
  
  .advertisement-nav {
    display: none;
  }
  
  .advertisement-dots {
    display: none;
  }
}

@media (max-width: 480px) {
  .mobile-ad-slide {
    flex: 0 0 calc((100% - 20px) / 3); /* Adjust for smaller gaps */
    height: 150px;
  }
  
  .mobile-ad-track {
    gap: 10px;
  }
  
  .mobile-ad-overlay {
    padding: 10px;
  }
  
  .mobile-ad-title {
    font-size: 0.9rem;
  }
  
  .mobile-ad-description {
    font-size: 0.75rem;
  }
}

@media (max-width: 360px) {
  .mobile-ad-slide {
    height: 140px;
  }
}

/* Desktop and Tablet */
@media (max-width: 1024px) {
  .categories-sidebar {
    flex: 0 0 240px;
  }
  
  .food-card {
    width: 260px;
    height: 460px;
  }
  
  .food-image-container {
    height: 180px;
  }
}

/* Tablet Portrait and Mobile */
@media (max-width: 768px) {
  .main-wrapper {
    flex-direction: column;
    gap: 20px;
  }
  
  .categories-sidebar {
    position: static;
    flex: none;
    width: 100%;
    max-height: none;
  }
  
  .categories-container {
    padding: 20px;
  }
  
  .swiper-button-next,
  .swiper-button-prev {
    display: none;
  }
  
  .food-card {
    width: 100%;
    max-width: 320px;
    height: 450px;
  }
  
  .food-image-container {
    height: 160px;
  }
  
  .search-entry-container {
    padding: 20px;
  }
  
  .search-entry-title {
    font-size: 1.5rem;
  }
  
  .nearby-section-header,
  .random-section-header,
  .sulit-section-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .refresh-nearby-btn,
  .refresh-random-btn,
  .view-all-btn {
    align-self: flex-start;
  }
  
  .location-options {
    grid-template-columns: 1fr;
  }
  
  .modal-footer {
    flex-direction: column;
  }
  
  .modal-btn {
    width: 100%;
    justify-content: center;
  }
}

/* Mobile - Food Card Specific Responsive Styles */
@media (max-width: 480px) {
  .container {
    padding: 15px;
  }
  
  .food-card {
    width: 100%;
    max-width: 300px;
    height: 430px;
    margin: 0 auto;
  }
  
  .food-image-container {
    height: 150px;
  }
  
  .restaurant-avatar {
    width: 50px;
    height: 50px;
    bottom: -15px;
  }
  
  .food-info {
    padding: 25px 12px 12px;
  }
  
  .food-name {
    font-size: 1rem;
    line-height: 1.3;
  }
  
  .restaurant-name {
    font-size: 0.8rem;
  }
  
  .food-description {
    font-size: 0.8rem;
    -webkit-line-clamp: 2;
  }
  
  .food-rating {
    flex-direction: row;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
  }
  
  .rating-stars {
    font-size: 0.75rem;
  }
  
  .rating-value {
    font-size: 0.85rem;
    margin-left: 3px;
  }
  
  .rating-count {
    font-size: 0.75rem;
    color: var(--gray);
  }
  
  .food-price {
    font-size: 1.2rem;
  }
  
  .search-entry-button {
    padding: 15px 25px;
    font-size: 1rem;
  }
  
  .distance-badge {
    top: 10px;
    left: 10px;
    padding: 4px 8px;
    font-size: 0.75rem;
  }
}

/* Extra Small Mobile Devices */
@media (max-width: 360px) {
  .food-card {
    max-width: 280px;
    height: 420px;
  }
  
  .food-image-container {
    height: 140px;
  }
  
  .restaurant-avatar {
    width: 45px;
    height: 45px;
    bottom: -12px;
  }
  
  .food-info {
    padding: 22px 10px 10px;
  }
  
  .food-rating {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  
  .rating-stars {
    font-size: 0.7rem;
  }
}

/* Small Tablet Landscape */
@media (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
  .food-card {
    width: 240px;
    height: 440px;
  }
  
  .food-image-container {
    height: 150px;
  }
}

/* ================= CUSTOM SCROLLBAR ================= */
::-webkit-scrollbar {
  width: 10px;
  height: 10px;
}

::-webkit-scrollbar-track {
  background: var(--gray-light);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
}

/* ================= NOTIFICATION STYLES ================= */
.custom-notification {
  position: fixed;
  top: 20px;
  right: 20px;
  background: white;
  padding: 15px 20px;
  border-radius: var(--radius);
  box-shadow: var(--shadow-hover);
  display: flex;
  align-items: center;
  gap: 10px;
  z-index: 9999;
  animation: slideIn 0.3s ease;
  border-left: 4px solid var(--primary);
}

.custom-notification.success {
  border-left-color: var(--success);
}

.custom-notification.warning {
  border-left-color: var(--warning);
}

.custom-notification.error {
  border-left-color: var(--danger);
}

.custom-notification.info {
  border-left-color: var(--info);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
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

/* ================= SWIPER SLIDE STYLING ================= */
.swiper-slide {
  width: auto !important;
}

/* ================= FIX FOR SWIPER CONTAINER HEIGHT ================= */
.swiper {
  height: auto;
}

/* ================= FIX FOR RECOMMENDED SECTION SWIPER ================= */
/* Add recommended swiper to the existing container styles */
.swiper-random-container,
.swiper-nearby-container,
.swiper-sulit-container,
.swiper-recommended-container {  /* Add recommended here */
  width: 100%;
  padding: 20px 10px 40px;
  margin: 20px 0;
  position: relative;
}

/* Add recommended to the wrapper styles */
.swiper-random-wrapper,
.swiper-nearby-wrapper,
.swiper-sulit-wrapper,
.swiper-recommended-wrapper {  /* Add recommended here */
  padding: 10px 5px;
}

/* Add recommended to the slide styles */
.swiper-random-slide,
.swiper-nearby-slide,
.swiper-sulit-slide,
.swiper-recommended-slide {  /* Add recommended here */
  height: auto;
  display: flex;
  justify-content: center;
  width: auto !important;
}

/* Fix the width of recommended slides to match others */
.swiper-recommended-slide {
  width: 280px !important; /* Same as food-card width */
}

/* Ensure food cards in recommended section are consistent */
.swiper-recommended-slide .food-card {
  width: 280px;
  height: 480px; /* Same as other food cards */
  margin: 0;
}

/* Fix the recommended badge positioning */
.recommended-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background: linear-gradient(135deg, var(--warning), var(--primary));
  color: var(--white);
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  z-index: 3; /* Increased to be above image */
  backdrop-filter: blur(4px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Fix spacing in recommended section header */
.recommended-section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 40px 0 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid var(--gray-light);
  width: 100%;
}

/* Make sure the swiper container is properly sized */
.mySwiperRecommended {
  width: 100%;
  height: auto;
}

/* Fix for swiper buttons in recommended section */
.mySwiperRecommended .swiper-button-next,
.mySwiperRecommended .swiper-button-prev {
  background: var(--white);
  color: var(--primary);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  box-shadow: var(--shadow);
  transition: var(--transition);
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.mySwiperRecommended .swiper-button-next:hover,
.mySwiperRecommended .swiper-button-prev:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-50%) scale(1.1);
}

.mySwiperRecommended .swiper-button-prev {
  left: -15px;
}

.mySwiperRecommended .swiper-button-next {
  right: -15px;
}

/* Ensure proper spacing between slides */
.swiper-recommended-wrapper .swiper-slide {
  margin-right: 20px; /* Space between slides */
}

/* Fix for responsive layout */
@media (max-width: 1024px) {
  .swiper-recommended-slide {
    width: 260px !important;
  }
  
  .swiper-recommended-slide .food-card {
    width: 260px;
    height: 460px;
  }
}

@media (max-width: 768px) {
  .recommended-section-header {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .refresh-recommended-btn {
    align-self: flex-start;
  }
  
  .swiper-recommended-slide {
    width: 250px !important;
  }
  
  .swiper-recommended-slide .food-card {
    width: 250px;
    height: 450px;
  }
  
  /* Hide swiper buttons on mobile */
  .mySwiperRecommended .swiper-button-next,
  .mySwiperRecommended .swiper-button-prev {
    display: none;
  }
}

@media (max-width: 480px) {
  .swiper-recommended-slide {
    width: 220px !important;
  }
  
  .swiper-recommended-slide .food-card {
    width: 220px;
    height: 420px;
  }
  
  .recommended-badge {
    top: 10px;
    right: 10px;
    padding: 4px 8px;
    font-size: 0.75rem;
  }
}

/* Ensure recommended foods have consistent star rating */
.swiper-recommended-slide .food-rating {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 15px;
  flex-wrap: wrap;
}

.swiper-recommended-slide .rating-stars {
  color: var(--warning);
  font-size: 0.85rem;
  display: flex;
  align-items: center;
  gap: 2px;
}

/* Fix for food image container in recommended section */
.swiper-recommended-slide .food-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  flex-shrink: 0;
  background-color: var(--light);
  padding: 0;
  display: block;
}

.swiper-recommended-slide .food-image-wrapper {
  width: 100%;
  height: 180px;
  overflow: hidden;
  position: relative;
}

.swiper-recommended-slide .food-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
  background-color: var(--light);
}

/* Fix for restaurant avatar in recommended section */
.swiper-recommended-slide .restaurant-avatar {
  position: absolute;
  bottom: 10px;
  left: 15px;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  overflow: hidden;
  border: 3px solid var(--white);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  z-index: 3;
  background-color: var(--white);
}

/* Add this to your existing swiper button styles */
.swiper-button-next,
.swiper-button-prev {
  background: var(--white);
  color: var(--primary);
  width: 40px;
  height: 40px;
  border-radius: 50%;
  box-shadow: var(--shadow);
  transition: var(--transition);
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-50%) scale(1.1);
}

.swiper-button-prev {
  left: -15px;
}

.swiper-button-next {
  right: -15px;
}

/* Ensure recommended food cards match other food cards */
.swiper-recommended-slide .food-card {
  width: 280px !important;
  height: 480px !important;
  margin: 0 auto;
}

/* Make sure the swiper container recognizes the slide width */
.swiper-recommended-slide {
  width: 280px !important;
  height: auto;
  margin-right: 20px;
}

/* Adjust responsive breakpoints */
@media (max-width: 1024px) {
  .swiper-recommended-slide .food-card {
    width: 260px !important;
    height: 460px !important;
  }
  
  .swiper-recommended-slide {
    width: 260px !important;
  }
}

@media (max-width: 768px) {
  .swiper-recommended-slide .food-card {
    width: 240px !important;
    height: 440px !important;
  }
  
  .swiper-recommended-slide {
    width: 240px !important;
  }
}

@media (max-width: 480px) {
  .swiper-recommended-slide .food-card {
    width: 220px !important;
    height: 420px !important;
  }
  
  .swiper-recommended-slide {
    width: 220px !important;
  }
}



/* Remove the conflicting duplicate styles at the bottom of your CSS */
/* Remove or comment out these duplicate styles: */
/*
.swiper-button-next,
.swiper-button-prev {
  background: var(--white);
  color: var(--primary);
  width: 50px;
  height: 50px;
  border-radius: 50%;
  box-shadow: var(--shadow);
  transition: var(--transition);
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
  background: var(--primary);
  color: var(--white);
  transform: translateY(-50%) scale(1.1);
}

.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 20px;
  font-weight: bold;
}

.swiper-button-prev {
  left: -25px;
}

.swiper-button-next {
  right: -25px;
}
*/


</style>

</head>
<body>

<?php include 'assets/header.php'; ?>

<!-- ================= LOCATION POPUP MODAL ================= -->
<div class="location-modal" id="locationModal">
  <div class="location-modal-content">
    <div class="modal-header">
      <i class="fas fa-map-marker-alt"></i>
      <h2 id="modalTitle">Set Your Location</h2>
      <p id="modalDescription">Set your location to discover nearby restaurants and foods</p>
    </div>
    
    <div class="modal-body" id="modalBody">
      <!-- Content will be loaded dynamically -->
    </div>
    
    <div class="modal-footer" id="modalFooter">
      <!-- Buttons will be loaded dynamically -->
    </div>
  </div>
</div>

<main class="container">

<!-- ================= ADVERTISEMENTS BANNER CAROUSEL ================= -->
<div class="advertisements-carousel">
  <div class="advertisements-header">
  </div>
  
  <div class="advertisements-container" id="advertisementsCarousel">
    <div class="advertisements-loading">
      <i class="fas fa-spinner fa-spin"></i>
      <p>Loading advertisements...</p>
    </div>
  </div>
</div>

<!-- ================= SEARCH ENTRY POINT (GOES TO SEARCH PAGE) ================= -->
<div class="search-entry-container">
  <h2 class="search-entry-title">What are you craving today?</h2>
  <a href="search.php" class="search-entry-button">
    <i class="fa-solid fa-magnifying-glass"></i> 
    Search Foods, Restaurants & More
  </a>
</div>

<!-- ================= MAIN LAYOUT ================= -->
<div class="main-wrapper">
  
  <!-- ================= CATEGORIES SIDEBAR ================= -->
  <aside class="categories-sidebar">
    <div class="categories-container" id="categoriesContainer">
      <div class="category-header">
        <h3 class="category-title">
          <i class="fas fa-list"></i>
          Categories
        </h3>
        <button class="category-filter">
          <i class="fas fa-filter"></i>
        </button>
      </div>
      <!-- Categories will be populated by JavaScript -->
    </div>
  </aside>

  <!-- ================= MAIN CONTENT ================= -->
  <div class="main-content">
    
    <?php if ($isLoggedIn && !$hasSavedLocation): ?>
    <!-- ================= LOCATION NOTICE ================= -->
    <div class="location-notice">
      <div class="location-notice-content">
        <i class="fas fa-map-marker-alt"></i>
        <div class="location-notice-text">
          <h3>Set Your Delivery Location</h3>
          <p>Set your location to discover nearby restaurants and get accurate delivery estimates</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
          <button class="set-location-btn" onclick="showLocationModal()">
            <i class="fas fa-crosshairs"></i>
            Set Current Location
          </button>
          <button class="set-location-btn" style="background: var(--success);" onclick="goToProfileLocationSettings()">
            <i class="fas fa-user"></i>
            Use Profile Address
          </button>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
    <!-- ================= NEARBY FOODS SECTION ================= -->
    <div class="nearby-section-header">
      <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
        <h2 class="nearby-section-title">
          <i class="fas fa-map-marker-alt"></i>
          Nearby Foods
        </h2>
        
        <?php if ($hasSavedLocation && $isLoggedIn): ?>
        <div class="saved-location-badge">
          <i class="fas fa-check-circle"></i>
          <span>Your Location</span>
        </div>
        <?php endif; ?>
      </div>
      
      <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap; justify-content: space-between; width: 100%; margin-top: 10px;">
        <div class="location-info" onclick="showLocationModal()">
          <i class="fas fa-location-dot"></i>
          <span id="locationText">
            <?php 
            if ($hasSavedLocation && !empty($userAddress)) {
                // Show address name if available
                echo htmlspecialchars($userAddress);
            } elseif ($hasSavedLocation) {
                // Show generic text instead of coordinates
                echo 'Your Saved Location';
            } else {
                echo 'Set your location';
            }
            ?>
          </span>
          <button class="change-location-btn" onclick="event.stopPropagation(); showLocationModal();">
            <i class="fas fa-pencil-alt"></i> Change
          </button>
        </div>
        
        <div style="display: flex; gap: 10px;">
          <button class="refresh-nearby-btn" id="refreshNearby">
            <i class="fas fa-redo"></i>
            Refresh Nearby
          </button>
          
          <?php if ($isLoggedIn && $hasSavedLocation): ?>
          <button class="save-location-btn" onclick="saveLocationToFirebase()" id="saveLocationBtn">
            <i class="fas fa-save"></i>
            Save to Profile
          </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <!-- Food Category Icons Row -->
    <div id="foodCategoryIcons" class="food-category-icons" style="margin-top: 20px;">
      <!-- Category icons will be loaded by JavaScript -->
    </div>
    
    <!-- Swiper Container for Nearby Foods -->
    <div class="swiper-nearby-container">
      <div class="swiper mySwiperNearby">
        <div class="swiper-wrapper" id="nearbySwiperWrapper"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>

    <!-- ================= RECOMMENDED FOR YOU SECTION ================= -->
    <div class="recommended-section-header">
      <h2 class="recommended-section-title">
        <i class="fas fa-star"></i>
        Recommended For You
      </h2>
      <button class="refresh-recommended-btn" id="refreshRecommended">
        <i class="fas fa-redo"></i>
        Refresh
      </button>
    </div>
    
    <!-- Swiper Container for Recommended Foods -->
    <div class="swiper-recommended-container">
      <div class="swiper mySwiperRecommended">
        <div class="swiper-wrapper" id="recommendedSwiperWrapper"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>
    
    <!-- ================= 10 RANDOM PICK FOR YOU (SWIPER) ================= -->
    <div class="random-section-header">
      <h2 class="random-section-title">
        <i class="fas fa-dice"></i>
        10 Random Pick For You
      </h2>
      <button class="refresh-random-btn" id="refreshRandom">
        <i class="fas fa-redo"></i>
        Refresh Picks
      </button>
    </div>
    
    <!-- Swiper Container for Random Foods -->
    <div class="swiper-random-container">
      <div class="swiper mySwiperRandom">
        <div class="swiper-wrapper" id="randomSwiperWrapper"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>
    
    <!-- ================= SULIT MEALS SECTION (₱150 & BELOW) ================= -->
    <div class="sulit-section-header">
      <h2 class="sulit-section-title">
        <i class="fas fa-tag"></i>
        Sulit Meals (₱150 & below)
      </h2>
      <div style="display: flex; gap: 10px; align-items: center;">
        <button class="refresh-nearby-btn" id="refreshSulit">
          <i class="fas fa-redo"></i> Refresh
        </button>
        <a href="sulit-meals.php" class="view-all-btn">
          <i class="fas fa-eye"></i> View All
        </a>
      </div>
    </div>
    
    <!-- Swiper Container for Sulit Meals -->
    <div class="swiper-sulit-container">
      <div class="swiper mySwiperSulit">
        <div class="swiper-wrapper" id="sulitSwiperWrapper"></div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
      </div>
    </div>
    </div>

  </div>
</div>

</main>

<!-- ================= FIREBASE ================= -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-auth-compat.js"></script>
<script src="js/firebase.js"></script>

<!-- ================= SWIPER JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- ================= JS FILES ================= -->
<script src="js/vendorProductsCarousel.js"></script>
<script src="js/vendorCategories.js"></script>

<script>
// ================= GLOBAL VARIABLES =================
let allFoodItems = [];
let randomProducts = [];
let nearbyProducts = [];
let sulitProducts = [];
let randomSwiper;
let nearbySwiper;
let sulitSwiper;
let recommendedProducts = [];
let recommendedSwiper;
const RECOMMENDED_LIMIT = 10;
const RANDOM_LIMIT = 10;
const NEARBY_LIMIT = 10;
const SULIT_LIMIT = 10;
const MAX_DISTANCE_KM = 10;
const SULIT_PRICE_LIMIT = 150;

// Advertisement variables
let advertisements = [];
let currentAdIndex = 0;
let adInterval;

// Cache for restaurant data
let restaurantCache = {};

// User location from PHP session
let userLocation = {
  latitude: <?php echo $hasSavedLocation && $userLatitude ? $userLatitude : 'null'; ?>,
  longitude: <?php echo $hasSavedLocation && $userLongitude ? $userLongitude : 'null'; ?>,
  address: '<?php echo $hasSavedLocation && $userAddress ? addslashes($userAddress) : ''; ?>',
  savedInDB: <?php echo $hasSavedLocation ? 'true' : 'false'; ?>
};

// User shipping addresses
let userShippingAddresses = [];

// Modal elements
const locationModal = document.getElementById('locationModal');
const modalBody = document.getElementById('modalBody');
const modalFooter = document.getElementById('modalFooter');
const modalTitle = document.getElementById('modalTitle');
const modalDescription = document.getElementById('modalDescription');

// ================= INITIALIZE ON LOAD ================= 
document.addEventListener('DOMContentLoaded', async function() {
  console.log('LalaGO Initializing...');
  console.log('User location from session:', userLocation);
  
  // Setup refresh buttons
  setupRefreshButtons();
  
  // Check if user needs to set location
  await checkLocationStatus();
  
  // Load advertisements first
  await loadAdvertisements();
  
  // Load all other sections
  await loadAllSections();
  
  console.log('LalaGO initialized');
});

// ================= ADVERTISEMENTS FUNCTIONS - UPDATED =================
async function loadAdvertisements() {
  console.log('Loading advertisements...');
  const container = document.getElementById('advertisementsCarousel');
  
  if (!container) {
    console.error('Advertisements container not found');
    return;
  }
  
  try {
    // Get current date
    const now = new Date();
    
    // Fetch active advertisements
    const adsSnapshot = await db.collection("advertisements")
      .where("is_enabled", "==", true)
      .where("is_deleted", "==", false)
      .get();
    
    if (adsSnapshot.empty) {
      showNoAdvertisements("No active advertisements found");
      return;
    }
    
    advertisements = [];
    adsSnapshot.forEach(doc => {
      const ad = doc.data();
      const adId = doc.id;
      
      // Simple date check - only check if dates exist
      let isActive = true;
      
      // If start_date exists, check if ad has started
      if (ad.start_date) {
        const startDate = ad.start_date.toDate ? ad.start_date.toDate() : new Date(ad.start_date);
        if (now < startDate) {
          isActive = false;
        }
      }
      
      // If end_date exists, check if ad has expired
      if (ad.end_date && isActive) {
        const endDate = ad.end_date.toDate ? ad.end_date.toDate() : new Date(ad.end_date);
        if (now > endDate) {
          isActive = false;
        }
      }
      
      if (isActive) {
        advertisements.push({
          id: adId,
          ...ad
        });
      }
    });
    
    console.log(`Found ${advertisements.length} active advertisements`);
    
    if (advertisements.length === 0) {
      showNoAdvertisements("No active advertisements at the moment");
      return;
    }
    
    // Sort by priority (higher priority first)
    advertisements.sort((a, b) => (b.priority || 0) - (a.priority || 0));
    
    // Render advertisements
    renderAdvertisements();
    
  } catch (error) {
    console.error("Error loading advertisements:", error);
    showNoAdvertisements("Error loading advertisements. Please try again later.");
  }
}

function renderAdvertisements() {
  const container = document.getElementById('advertisementsCarousel');
  if (!container || advertisements.length === 0) return;
  
  // Check if mobile device
  const isMobile = window.innerWidth <= 768;
  
  if (isMobile && advertisements.length >= 3) {
    // Mobile view: Show 3 slides
    renderMobileAdvertisements();
  } else {
    // Desktop view: Original carousel
    renderDesktopAdvertisements();
  }
}

function renderDesktopAdvertisements() {
  const container = document.getElementById('advertisementsCarousel');
  
  let slidesHTML = '';
  let dotsHTML = '';
  
  advertisements.forEach((ad, index) => {
    const imageUrl = ad.image_urls?.[0] || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    const isActive = index === 0 ? 'active' : '';
    
    slidesHTML += `
      <div class="advertisement-slide ${isActive}" data-index="${index}" data-ad-id="${ad.id}">
        <img src="${imageUrl}" alt="${ad.title || 'Advertisement'}" class="advertisement-image"
             onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
        <div class="advertisement-overlay">
          <h3 class="advertisement-title">${ad.title || 'Special Offer'}</h3>
          <p class="advertisement-description">${ad.description || 'Check out this amazing offer!'}</p>
        </div>
      </div>
    `;
    
    dotsHTML += `<div class="advertisement-dot ${isActive}" onclick="goToAd(${index})"></div>`;
  });
  
  const navHTML = advertisements.length > 1 ? `
    <button class="advertisement-nav prev" onclick="prevAd()">‹</button>
    <button class="advertisement-nav next" onclick="nextAd()">›</button>
  ` : '';
  
  container.innerHTML = `
    <div class="desktop-carousel">
      ${navHTML}
      ${slidesHTML}
      ${advertisements.length > 1 ? `<div class="advertisement-dots">${dotsHTML}</div>` : ''}
    </div>
    <div class="mobile-carousel" style="display: none;"></div>
  `;
  
  // Start auto-slide if multiple ads
  if (advertisements.length > 1) {
    startAutoSlide();
  }
  
  // Setup click tracking
  setupAdClicks();
  
  // Track impression for first ad
  if (advertisements.length > 0) {
    trackAdImpression(advertisements[0].id);
  }
}

function renderMobileAdvertisements() {
  const container = document.getElementById('advertisementsCarousel');
  
  // Take first 3 advertisements for mobile
  const mobileAds = advertisements.slice(0, 3);
  
  let slidesHTML = '';
  let indicatorsHTML = '';
  
  mobileAds.forEach((ad, index) => {
    const imageUrl = ad.image_urls?.[0] || 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    const isActive = index === 0 ? 'active' : '';
    
    slidesHTML += `
      <div class="mobile-ad-slide" data-index="${index}" data-ad-id="${ad.id}">
        <img src="${imageUrl}" alt="${ad.title || 'Advertisement'}" class="advertisement-image"
             onerror="this.src='https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
        <div class="mobile-ad-overlay">
          <h4 class="mobile-ad-title">${ad.title || 'Special Offer'}</h4>
          <p class="mobile-ad-description">${ad.description || 'Check out this amazing offer!'}</p>
        </div>
      </div>
    `;
    
    indicatorsHTML += `<div class="mobile-ad-indicator ${isActive}" onclick="goToMobileAd(${index})"></div>`;
  });
  
  container.innerHTML = `
    <div class="desktop-carousel" style="display: none;"></div>
    <div class="mobile-carousel">
      <div class="mobile-ad-track" id="mobileAdTrack">
        ${slidesHTML}
      </div>
      <div class="mobile-ad-indicators">
        ${indicatorsHTML}
      </div>
    </div>
  `;
  
  // Setup click tracking
  setupMobileAdClicks();
  
  // Track impression for first ad
  if (mobileAds.length > 0) {
    trackAdImpression(mobileAds[0].id);
  }
  
  // Start auto-scroll for mobile
  if (mobileAds.length > 1) {
    startMobileAutoScroll();
  }
}

function showNoAdvertisements(message) {
  const container = document.getElementById('advertisementsCarousel');
  if (!container) return;
  
  container.innerHTML = `
    <div style="text-align: center; padding: 50px; color: var(--gray);">
      <i class="fas fa-bullhorn fa-3x" style="color: var(--gray-light); margin-bottom: 15px;"></i>
      <h3 style="color: var(--gray); margin-bottom: 10px;">${message}</h3>
      <p style="color: var(--gray); font-size: 0.9rem;">Check back later for special offers</p>
    </div>
  `;
}

function nextAd() {
  if (advertisements.length <= 1) return;
  
  currentAdIndex = (currentAdIndex + 1) % advertisements.length;
  showAd(currentAdIndex);
  resetAutoSlide();
}

function prevAd() {
  if (advertisements.length <= 1) return;
  
  currentAdIndex = (currentAdIndex - 1 + advertisements.length) % advertisements.length;
  showAd(currentAdIndex);
  resetAutoSlide();
}

function goToAd(index) {
  if (index < 0 || index >= advertisements.length) return;
  
  currentAdIndex = index;
  showAd(currentAdIndex);
  resetAutoSlide();
}

function goToMobileAd(index) {
  if (index < 0 || index >= Math.min(3, advertisements.length)) return;
  
  const track = document.getElementById('mobileAdTrack');
  if (!track) return;
  
  const slideWidth = track.children[0].offsetWidth + 15; // width + gap
  track.style.transform = `translateX(-${index * slideWidth}px)`;
  
  // Update indicators
  document.querySelectorAll('.mobile-ad-indicator').forEach((indicator, i) => {
    indicator.classList.toggle('active', i === index);
  });
  
  // Track impression
  if (index < advertisements.length) {
    trackAdImpression(advertisements[index].id);
  }
}

function showAd(index) {
  // Hide all slides
  document.querySelectorAll('.advertisement-slide').forEach(slide => {
    slide.classList.remove('active');
  });
  
  // Deactivate all dots
  document.querySelectorAll('.advertisement-dot').forEach(dot => {
    dot.classList.remove('active');
  });
  
  // Show selected slide
  const slide = document.querySelector(`.advertisement-slide[data-index="${index}"]`);
  if (slide) {
    slide.classList.add('active');
  }
  
  // Activate selected dot
  const dot = document.querySelectorAll('.advertisement-dot')[index];
  if (dot) {
    dot.classList.add('active');
  }
  
  // Track impression
  if (advertisements[index]) {
    trackAdImpression(advertisements[index].id);
  }
}

function startAutoSlide() {
  // Check if mobile
  if (window.innerWidth <= 768 && advertisements.length >= 3) {
    startMobileAutoScroll();
    return;
  }
  
  // Clear existing interval
  if (adInterval) {
    clearInterval(adInterval);
  }
  
  // Don't auto-slide if only 1 ad
  if (advertisements.length <= 1) return;
  
  // Start new interval (change every 5 seconds)
  adInterval = setInterval(() => {
    nextAd();
  }, 5000);
}

function startMobileAutoScroll() {
  const mobileAdsCount = Math.min(3, advertisements.length);
  if (mobileAdsCount <= 1) return;
  
  // Clear existing interval
  if (adInterval) {
    clearInterval(adInterval);
  }
  
  let currentIndex = 0;
  
  adInterval = setInterval(() => {
    currentIndex = (currentIndex + 1) % mobileAdsCount;
    goToMobileAd(currentIndex);
  }, 4000); // Change every 4 seconds
}

function resetAutoSlide() {
  if (adInterval) {
    clearInterval(adInterval);
    startAutoSlide();
  }
}

function setupAdClicks() {
  document.addEventListener('click', function(event) {
    const adSlide = event.target.closest('.advertisement-slide');
    if (adSlide && advertisements.length > 0) {
      const index = parseInt(adSlide.getAttribute('data-index'));
      const adId = adSlide.getAttribute('data-ad-id');
      const ad = advertisements[index];
      
      if (ad) {
        // Track click
        trackAdClick(adId);
        
        // Handle click based on ad type
        handleAdClick(ad);
      }
    }
  });
}

function setupMobileAdClicks() {
  document.addEventListener('click', function(event) {
    const adSlide = event.target.closest('.mobile-ad-slide');
    if (adSlide && advertisements.length > 0) {
      const index = parseInt(adSlide.getAttribute('data-index'));
      const adId = adSlide.getAttribute('data-ad-id');
      const ad = advertisements[index];
      
      if (ad) {
        // Track click
        trackAdClick(adId);
        
        // Handle click based on ad type
        handleAdClick(ad);
      }
    }
  });
}

function handleAdClick(ad) {
  if (ad.restaurant_id && ad.restaurant_id !== "555") {
    window.location.href = `restaurant.php?id=${ad.restaurant_id}`;
  } else if (ad.external_url) {
    window.open(ad.external_url, '_blank');
  } else {
    // Default action - show alert
    alert(`Advertisement: ${ad.title || 'Special Offer'}\n\n${ad.description || 'Check out this amazing offer!'}`);
  }
}

async function trackAdClick(adId) {
  try {
    const adRef = db.collection("advertisements").doc(adId);
    await adRef.update({
      clicks: firebase.firestore.FieldValue.increment(1),
      updated_at: firebase.firestore.FieldValue.serverTimestamp()
    });
    console.log(`Tracked click for ad: ${adId}`);
  } catch (error) {
    console.error("Error tracking ad click:", error);
  }
}

async function trackAdImpression(adId) {
  try {
    // Only track once per session per ad
    const trackedImpressions = JSON.parse(sessionStorage.getItem('ad_impressions') || '{}');
    
    if (!trackedImpressions[adId]) {
      const adRef = db.collection("advertisements").doc(adId);
      await adRef.update({
        impressions: firebase.firestore.FieldValue.increment(1),
        updated_at: firebase.firestore.FieldValue.serverTimestamp()
      });
      
      trackedImpressions[adId] = true;
      sessionStorage.setItem('ad_impressions', JSON.stringify(trackedImpressions));
      console.log(`Tracked impression for ad: ${adId}`);
    }
  } catch (error) {
    console.error("Error tracking ad impression:", error);
  }
}

function viewAllAdvertisements() {
  alert('View all advertisements feature coming soon!');
  // You can implement this later:
  // window.location.href = 'advertisements.php';
}

// ================= WINDOW RESIZE HANDLER =================
window.addEventListener('resize', function() {
  if (advertisements.length > 0) {
    // Clear existing interval
    if (adInterval) {
      clearInterval(adInterval);
      adInterval = null;
    }
    
    // Re-render advertisements based on screen size
    renderAdvertisements();
  }
});


// ================= DEBUG FIREBASE DATA =================
async function checkFirebaseData() {
  console.log('Checking Firebase data...');
  
  try {
    // Check vendors
    const vendorsSnapshot = await db.collection("vendors")
      .where("publish", "==", true)
      .limit(5)
      .get();
    
    console.log(`Found ${vendorsSnapshot.size} published vendors`);
    
    if (vendorsSnapshot.empty) {
      console.warn('No published vendors found in Firebase!');
      console.log('Make sure you have vendors with "publish": true');
    } else {
      vendorsSnapshot.forEach(doc => {
        console.log(`Vendor ${doc.id}:`, doc.data());
      });
    }
    
    // Check products
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(5)
      .get();
    
    console.log(`Found ${productsSnapshot.size} published products`);
    
  } catch (error) {
    console.error('Firebase connection error:', error);
  }
}

// Call this function in your initialization:
document.addEventListener('DOMContentLoaded', async function() {
  console.log('LalaGO Initializing...');
  
  // Debug Firebase data
  await checkFirebaseData();
  
  // ... rest of your initialization code
});

// ================= CHECK LOCATION STATUS =================
async function checkLocationStatus() {
  console.log('Checking location status...');
  
  // Check if user is logged in
  <?php if ($isLoggedIn): ?>
    console.log('User is logged in, checking Firebase for location...');
    
    try {
      // First check session storage (fastest)
      if (userLocation.latitude && userLocation.longitude) {
        console.log('Location found in session:', userLocation);
        return;
      }
      
      // If no session location, check Firebase
      console.log('No location in session, checking Firebase...');
      const hasLocation = await getUserLocationFromFirebase();
      
      if (hasLocation) {
        console.log('Location found in Firebase:', userLocation);
        // Location found, update session
        await saveLocationToSession(userLocation.latitude, userLocation.longitude, userLocation.address);
      } else {
        console.log('No location found in Firebase');
        
        // Check if user has shipping addresses
        if (userShippingAddresses.length > 0) {
          console.log('User has shipping addresses, suggesting to use one');
          // Don't auto-show modal for shipping addresses, let user decide
        } else {
          console.log('No location data found anywhere, showing location modal');
          // No location anywhere, show popup after a short delay
          setTimeout(() => {
            showLocationModal();
          }, 1500);
        }
      }
    } catch (error) {
      console.error('Error checking location status:', error);
    }
    
  <?php else: ?>
    // For non-logged-in users, check local storage
    console.log('User is not logged in, checking local storage...');
    const savedLocation = localStorage.getItem('guest_location');
    if (!savedLocation) {
      console.log('No location in local storage, showing modal');
      // Show popup after a short delay
      setTimeout(() => {
        showLocationModal();
      }, 1500);
    } else {
      // Load location from local storage
      try {
        const locationData = JSON.parse(savedLocation);
        userLocation = {
          ...userLocation,
          ...locationData,
          savedInDB: false
        };
        console.log('Location loaded from local storage:', userLocation);
        updateLocationDisplay();
      } catch (e) {
        console.error('Error parsing saved location:', e);
      }
    }
  <?php endif; ?>
}

// ================= CHECK LOCATION STATUS =================
async function checkLocationStatus() {
  console.log('Checking location status...');
  
  <?php if ($isLoggedIn): ?>
    console.log('User is logged in, checking Firebase for location...');
    
    try {
      // First check session storage (fastest)
      if (userLocation.latitude && userLocation.longitude) {
        console.log('Location found in session:', userLocation);
        updateSaveButtonState();
        return;
      }
      
      // If no session location, check Firebase
      console.log('No location in session, checking Firebase...');
      const hasLocation = await getUserLocationFromFirebase();
      
      if (hasLocation) {
        console.log('Location found in Firebase:', userLocation);
        // Save to session for immediate use
        await saveLocationToSession(userLocation.latitude, userLocation.longitude, userLocation.address);
        updateSaveButtonState();
      } else {
        console.log('No location found in Firebase');
        
        // Check if user has shipping addresses
        if (userShippingAddresses.length > 0) {
          console.log('User has shipping addresses, suggesting to use one');
          // Don't auto-show modal, just suggest
        } else {
          console.log('No location data found anywhere');
          // Don't auto-show modal, let user decide
        }
      }
    } catch (error) {
      console.error('Error checking location status:', error);
    }
    
  <?php else: ?>
    // For non-logged-in users, check local storage
    console.log('User is not logged in, checking local storage...');
    const savedLocation = localStorage.getItem('guest_location');
    if (savedLocation) {
      try {
        const locationData = JSON.parse(savedLocation);
        userLocation = {
          ...userLocation,
          ...locationData,
          savedInDB: false
        };
        console.log('Location loaded from local storage:', userLocation);
        updateLocationDisplay();
      } catch (e) {
        console.error('Error parsing saved location:', e);
      }
    }
  <?php endif; ?>
}

// ================= ENHANCED GEOCODE FUNCTION =================
async function geocodeAddress(address) {
  if (!address || address.trim() === '') {
    console.log('No address provided for geocoding');
    return null;
  }
  
  try {
    console.log('Geocoding address:', address);
    
    // Use Nominatim OpenStreetMap (free, no API key needed)
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
    
    if (!response.ok) {
      throw new Error('Geocoding service failed');
    }
    
    const data = await response.json();
    
    if (data && data.length > 0) {
      const result = {
        lat: parseFloat(data[0].lat),
        lng: parseFloat(data[0].lon),
        display_name: data[0].display_name
      };
      
      console.log('Geocoding successful:', result);
      return result;
    } else {
      console.log('No results found for address:', address);
      return null;
    }
  } catch (error) {
    console.error('Geocoding error:', error);
    return null;
  }
}

// ================= SHOW LOCATION MODAL =================
function showLocationModal() {
  modalTitle.textContent = 'Set Your Location';
  
  <?php if ($isLoggedIn): ?>
  if (userShippingAddresses.length > 0) {
    showShippingAddressSelection();
  } else {
    showLocationOptions();
  }
  <?php else: ?>
  showLocationOptions();
  <?php endif; ?>
  
  locationModal.classList.add('active');
}

// ================= SHOW SHIPPING ADDRESS SELECTION =================
function showShippingAddressSelection() {
  modalDescription.textContent = 'Select one of your saved shipping addresses or add a new location';
  
  let addressesHTML = '';
  userShippingAddresses.forEach((address, index) => {
    addressesHTML += `
      <div class="shipping-address-item" onclick="selectShippingAddress(${index})">
        <i class="fas fa-map-marker-alt"></i>
        <div class="shipping-address-content">
          <div class="shipping-address-name">
            ${address.addressAs || 'Address'}
            ${address.isDefault ? '<span class="default-badge">Default</span>' : ''}
          </div>
          <div class="shipping-address-text">
            ${address.address || address.locality || ''}
            ${address.landmark ? `<br><small>Landmark: ${address.landmark}</small>` : ''}
          </div>
        </div>
      </div>
    `;
  });
  
  modalBody.innerHTML = `
    <div class="shipping-addresses">
      ${addressesHTML}
    </div>
    <div style="text-align: center; margin-top: 20px;">
      <button class="set-location-btn" onclick="showLocationOptions()" style="background: var(--primary);">
        <i class="fas fa-plus"></i> Use Different Location
      </button>
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn cancel" onclick="closeLocationModal()">
      <i class="fas fa-times"></i> Cancel
    </button>
    <button class="modal-btn confirm" id="confirmAddress" disabled onclick="useSelectedShippingAddress()">
      <i class="fas fa-check"></i> Use Selected Address
    </button>
  `;
}

// ================= SELECT SHIPPING ADDRESS =================
let selectedShippingAddressIndex = null;
function selectShippingAddress(index) {
  // Remove active class from all addresses
  document.querySelectorAll('.shipping-address-item').forEach(el => {
    el.classList.remove('active');
  });
  
  // Add active class to selected address
  const selectedItem = document.querySelectorAll('.shipping-address-item')[index];
  selectedItem.classList.add('active');
  
  selectedShippingAddressIndex = index;
  
  // Enable confirm button
  document.getElementById('confirmAddress').disabled = false;
}

// ================= USE SELECTED SHIPPING ADDRESS =================
async function useSelectedShippingAddress() {
  if (selectedShippingAddressIndex === null) return;
  
  const address = userShippingAddresses[selectedShippingAddressIndex];
  
  modalBody.innerHTML = `
    <div class="location-loading">
      <i class="fas fa-spinner"></i>
      <h3>Setting location...</h3>
      <p>Please wait while we set your location</p>
    </div>
  `;
  
  modalFooter.innerHTML = '';
  
  // Update user location
  userLocation = {
    latitude: address.location.latitude,
    longitude: address.location.longitude,
    address: address.address || address.locality || '',
    savedInDB: true
  };
  
  // Save to session
  await saveLocationToSession(userLocation.latitude, userLocation.longitude, userLocation.address);
  
  // Show success
  modalBody.innerHTML = `
    <div class="location-success">
      <i class="fas fa-check-circle"></i>
      <h3>Location Set!</h3>
      <p>Your location has been set to:</p>
      <p><strong>${address.address || address.locality}</strong></p>
      ${address.landmark ? `<p><small>Landmark: ${address.landmark}</small></p>` : ''}
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn confirm" onclick="closeLocationModalAndRefresh()">
      <i class="fas fa-check"></i> Continue
    </button>
  `;
}

// ================= SHOW LOCATION OPTIONS =================
function showLocationOptions() {
  modalDescription.textContent = 'Choose how you want to set your location';
  
  // For logged-in users
  <?php if ($isLoggedIn): ?>
  modalBody.innerHTML = `
    <div class="location-options">
      <div class="location-option" onclick="selectLocationOption('device')">
        <i class="fas fa-crosshairs"></i>
        <div>
          <h3>Use Current Location</h3>
          <p>Get your location automatically using GPS</p>
        </div>
      </div>
      <div class="location-option" onclick="selectLocationOption('manual')">
        <i class="fas fa-map-pin"></i>
        <div>
          <h3>Enter Address Manually</h3>
          <p>Type in your address or location</p>
        </div>
      </div>
      <div class="location-option" onclick="selectLocationOption('browse')">
        <i class="fas fa-search-location"></i>
        <div>
          <h3>Browse on Map</h3>
          <p>Select your location on a map</p>
        </div>
      </div>
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn cancel" onclick="closeLocationModal()">
      <i class="fas fa-times"></i> Cancel
    </button>
    <button class="modal-btn confirm" id="confirmLocation" disabled onclick="processLocationSelection()">
      <i class="fas fa-check"></i> Confirm
    </button>
  `;
  <?php else: ?>
  // For non-logged-in users
  modalBody.innerHTML = `
    <div class="location-options">
      <div class="location-option" onclick="selectLocationOption('device')">
        <i class="fas fa-crosshairs"></i>
        <div>
          <h3>Use Current Location</h3>
          <p>Get your location automatically using GPS</p>
        </div>
      </div>
      <div class="location-option" onclick="selectLocationOption('manual')">
        <i class="fas fa-map-pin"></i>
        <div>
          <h3>Enter Address Manually</h3>
          <p>Type in your address or location</p>
        </div>
      </div>
    </div>
    <div class="auth-prompt" style="margin-top: 20px; padding: 15px;">
      <p style="margin: 0; font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i> 
        <strong>Tip:</strong> Register or login to save your location permanently
      </p>
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn cancel" onclick="closeLocationModal()">
      <i class="fas fa-times"></i> Skip for now
    </button>
    <button class="modal-btn confirm" id="confirmLocation" disabled onclick="processLocationSelection()">
      <i class="fas fa-check"></i> Set Location
    </button>
  `;
  <?php endif; ?>
  
  // Reset selection
  selectedLocationOption = null;
}

// ================= SELECT LOCATION OPTION =================
let selectedLocationOption = null;
function selectLocationOption(option) {
  // Remove active class from all options
  document.querySelectorAll('.location-option').forEach(el => {
    el.classList.remove('active');
  });
  
  // Add active class to selected option
  event.currentTarget.classList.add('active');
  selectedLocationOption = option;
  
  // Enable confirm button
  document.getElementById('confirmLocation').disabled = false;
  
  // If manual option selected, show address input
  if (option === 'manual') {
    showManualAddressInput();
  }
}

// ================= SHOW MANUAL ADDRESS INPUT =================
function showManualAddressInput() {
  const existingInput = document.querySelector('.manual-location-input');
  if (!existingInput) {
    const manualInput = document.createElement('div');
    manualInput.className = 'manual-location-input';
    manualInput.innerHTML = `
      <h4><i class="fas fa-keyboard"></i> Enter Your Address</h4>
      <input type="text" class="address-input" placeholder="Enter your full address (e.g., 123 Main St, City, Country)" id="manualAddress">
      <div class="coordinates-inputs">
        <div>
          <label class="coordinate-label">Latitude</label>
          <input type="number" step="any" class="coordinate-input" placeholder="e.g., 14.5995" id="manualLatitude">
        </div>
        <div>
          <label class="coordinate-label">Longitude</label>
          <input type="number" step="any" class="coordinate-input" placeholder="e.g., 120.9842" id="manualLongitude">
        </div>
      </div>
      <p style="font-size: 0.8rem; color: var(--gray); margin: 0;">
        <i class="fas fa-lightbulb"></i> 
        Coordinates are optional. You can find them on Google Maps.
      </p>
    `;
    
    // Insert after location options
    const locationOptions = document.querySelector('.location-options');
    locationOptions.insertAdjacentElement('afterend', manualInput);
  }
}

// ================= PROCESS LOCATION SELECTION =================
async function processLocationSelection() {
  if (!selectedLocationOption) return;
  
  switch (selectedLocationOption) {
    case 'device':
      await getLocationFromDevice();
      break;
    case 'manual':
      await getManualLocation();
      break;
    case 'browse':
      // Redirect to map page
      window.location.href = 'set-location.php?redirect=index.php';
      break;
  }
}

// ================= GET USER LOCATION FROM FIREBASE =================
async function getUserLocationFromFirebase() {
  <?php if ($isLoggedIn): ?>
  try {
    const userDoc = await db.collection("users").doc("<?php echo $userId; ?>").get();
    if (userDoc.exists) {
      const userData = userDoc.data();
      
      // Check for location data in user document
      if (userData.location && userData.location.latitude && userData.location.longitude) {
        userLocation = {
          latitude: userData.location.latitude,
          longitude: userData.location.longitude,
          address: userData.location.address || userData.address || '',
          savedInDB: true
        };
        
        // Also fetch shipping addresses
        const addressesSnapshot = await db.collection("shipping_addresses")
          .where("userId", "==", "<?php echo $userId; ?>")
          .orderBy("createdAt", "desc")
          .get();
        
        userShippingAddresses = [];
        addressesSnapshot.forEach(doc => {
          userShippingAddresses.push({
            id: doc.id,
            ...doc.data()
          });
        });
        
        return true;
      }
    }
    return false;
  } catch (error) {
    console.error("Error fetching user location:", error);
    return false;
  }
  <?php else: ?>
  return false;
  <?php endif; ?>
}

// ================= SAVE LOCATION TO FIREBASE =================
async function saveLocationToFirebase() {
  <?php if ($isLoggedIn): ?>
  if (!userLocation.latitude || !userLocation.longitude) {
    showNotification('No location to save', 'warning');
    return;
  }
  
  try {
    const saveBtn = document.getElementById('saveLocationBtn');
    if (saveBtn) {
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
      saveBtn.disabled = true;
    }
    
    // Update user document with location
    await db.collection("users").doc("<?php echo $userId; ?>").set({
      location: {
        latitude: userLocation.latitude,
        longitude: userLocation.longitude,
        address: userLocation.address,
        updatedAt: firebase.firestore.FieldValue.serverTimestamp()
      }
    }, { merge: true });
    
    userLocation.savedInDB = true;
    updateSaveButtonState();
    
    showNotification('Location saved to profile!', 'success');
    
  } catch (error) {
    console.error("Error saving location to Firebase:", error);
    showNotification('Failed to save location', 'error');
  }
  <?php else: ?>
  showNotification('Please login to save location', 'warning');
  <?php endif; ?>
}

// ================= GET ADDRESS FROM COORDINATES =================
async function getAddressFromCoordinates(latitude, longitude) {
  try {
    console.log('Getting address for coordinates:', latitude, longitude);
    
    // Use Nominatim OpenStreetMap reverse geocoding
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
    );
    
    if (!response.ok) {
      throw new Error('Reverse geocoding failed');
    }
    
    const data = await response.json();
    
    if (data && data.display_name) {
      // Extract a shorter, more readable address
      let address = data.display_name;
      
      // Try to get a shorter address
      if (data.address) {
        // Build a nicer address format
        const parts = [];
        if (data.address.road) parts.push(data.address.road);
        if (data.address.suburb) parts.push(data.address.suburb);
        if (data.address.city || data.address.town || data.address.village) {
          parts.push(data.address.city || data.address.town || data.address.village);
        }
        
        if (parts.length > 0) {
          address = parts.join(', ');
        }
      }
      
      console.log('Address found:', address);
      return address;
    }
    
    // Fallback: Return coordinates as address
    return `Near ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
  } catch (error) {
    console.error('Reverse geocoding error:', error);
    // Fallback address
    return `Location: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
  }
}

// ================= UPDATE LOCATION DISPLAY =================
async function updateLocationDisplay() {
  const locationText = document.getElementById('locationText');
  if (!locationText) return;
  
  if (userLocation.address && userLocation.address.trim() !== '') {
    // Use existing address
    locationText.textContent = userLocation.address;
  } else if (userLocation.latitude && userLocation.longitude) {
    // If no address but has coordinates, try to get address
    locationText.textContent = 'Getting address...';
    
    try {
      const address = await getAddressFromCoordinates(
        userLocation.latitude, 
        userLocation.longitude
      );
      
      userLocation.address = address;
      locationText.textContent = address;
      
      // Save the address to session
      await saveLocationToSession(
        userLocation.latitude, 
        userLocation.longitude, 
        address
      );
    } catch (error) {
      console.error('Error getting address:', error);
      locationText.textContent = 'Your Saved Location';
    }
  } else {
    locationText.textContent = 'Set your location';
  }
  
  // Update save button state
  updateSaveButtonState();
}
// ================= GET LOCATION FROM DEVICE =================
async function getLocationFromDevice() {
  modalBody.innerHTML = `
    <div class="location-loading">
      <i class="fas fa-spinner"></i>
      <h3>Getting your location...</h3>
      <p>Please allow location access in your browser</p>
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn cancel" onclick="showLocationOptions()">
      <i class="fas fa-arrow-left"></i> Back
    </button>
  `;
  
  if (!navigator.geolocation) {
    showLocationError('Geolocation is not supported by your browser');
    return;
  }
  
  navigator.geolocation.getCurrentPosition(
    async (position) => {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;
      
      // Get address from coordinates using reverse geocoding
      const address = await reverseGeocode(latitude, longitude);
      
      // Save location
      await saveUserLocation(latitude, longitude, address);
    },
    (error) => {
      let errorMessage = 'Unable to retrieve your location';
      switch(error.code) {
        case error.PERMISSION_DENIED:
          errorMessage = 'Location access was denied. Please enable location services.';
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = 'Location information is unavailable.';
          break;
        case error.TIMEOUT:
          errorMessage = 'Location request timed out.';
          break;
      }
      showLocationError(errorMessage);
    },
    {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    }
  );
}

// ================= GET MANUAL LOCATION =================
async function getManualLocation() {
  const addressInput = document.getElementById('manualAddress');
  const latInput = document.getElementById('manualLatitude');
  const lngInput = document.getElementById('manualLongitude');
  
  let latitude = parseFloat(latInput?.value);
  let longitude = parseFloat(lngInput?.value);
  let address = addressInput?.value.trim();
  
  if (!address) {
    showNotification('Please enter an address', 'warning');
    return;
  }
  
  // If coordinates not provided, try to geocode the address
  if (!latitude || !longitude || isNaN(latitude) || isNaN(longitude)) {
    modalBody.innerHTML = `
      <div class="location-loading">
        <i class="fas fa-spinner"></i>
        <h3>Finding coordinates for your address...</h3>
        <p>Please wait while we process your address</p>
      </div>
    `;
    
    try {
      const coordinates = await geocodeAddress(address);
      if (coordinates) {
        latitude = coordinates.lat;
        longitude = coordinates.lng;
      } else {
        showLocationError('Could not find coordinates for this address. Please enter coordinates manually.');
        return;
      }
    } catch (error) {
      showLocationError('Error geocoding address. Please enter coordinates manually.');
      return;
    }
  }
  
  // Save location
  await saveUserLocation(latitude, longitude, address);
}

// ================= REVERSE GEOCODE (COORDINATES TO ADDRESS) =================
async function reverseGeocode(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
    const data = await response.json();
    
    if (data && data.display_name) {
      return data.display_name;
    }
  } catch (error) {
    console.error('Reverse geocoding error:', error);
  }
  
  // Fallback address
  return `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
}

// ================= GEOCODE ADDRESS (ADDRESS TO COORDINATES) =================
async function geocodeAddress(address) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
    const data = await response.json();
    
    if (data && data.length > 0) {
      return {
        lat: parseFloat(data[0].lat),
        lng: parseFloat(data[0].lon)
      };
    }
  } catch (error) {
    console.error('Geocoding error:', error);
  }
  return null;
}

// ================= SAVE USER LOCATION =================
async function saveUserLocation(latitude, longitude, address = null) {
  // Get address from coordinates if not provided
  let finalAddress = address;
  
  if (!finalAddress || finalAddress.trim() === '') {
    try {
      finalAddress = await getAddressFromCoordinates(latitude, longitude);
    } catch (error) {
      console.error('Error getting address:', error);
      finalAddress = `Near ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
    }
  }
  
  // Update user location object
  userLocation = {
    latitude: latitude,
    longitude: longitude,
    address: finalAddress,
    savedInDB: false
  };
  
  // Show success screen
  modalBody.innerHTML = `
    <div class="location-success">
      <i class="fas fa-check-circle"></i>
      <h3>Location Set Successfully!</h3>
      <p>Your location has been set to:</p>
      <p><strong>${finalAddress}</strong></p>
      <p><small>Coordinates: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}</small></p>
    </div>
  `;
  
  // Check if user is logged in (from PHP variable passed to JS)
  const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  
  if (isLoggedIn) {
    modalFooter.innerHTML = `
      <button class="modal-btn confirm" onclick="saveLocationToFirebaseAndClose()">
        <i class="fas fa-save"></i> Save to Account
      </button>
      <button class="modal-btn cancel" onclick="closeLocationModalAndRefresh()">
        <i class="fas fa-check"></i> Use Without Saving
      </button>
    `;
  } else {
    modalFooter.innerHTML = `
      <button class="modal-btn confirm" onclick="saveLocationToLocalStorage(${latitude}, ${longitude}, '${finalAddress.replace(/'/g, "\\'")}')">
        <i class="fas fa-check"></i> Continue
      </button>
    `;
  }
  
  // Save to session for immediate use
  await saveLocationToSession(latitude, longitude, finalAddress);
  
  // Update display
  updateLocationDisplay();
}

// ================= SAVE LOCATION TO FIREBASE AND CLOSE =================
async function saveLocationToFirebaseAndClose() {
  await saveLocationToFirebase();
  closeLocationModalAndRefresh();
}

// ================= GET ADDRESS FROM COORDINATES =================
async function getAddressFromCoordinates(latitude, longitude) {
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`
    );
    
    if (!response.ok) {
      throw new Error('Reverse geocoding failed');
    }
    
    const data = await response.json();
    
    if (data && data.display_name) {
      // Extract a shorter, more readable address
      let address = data.display_name;
      
      // Try to get a shorter address
      if (data.address) {
        const parts = [];
        if (data.address.road) parts.push(data.address.road);
        if (data.address.suburb) parts.push(data.address.suburb);
        if (data.address.city || data.address.town || data.address.village) {
          parts.push(data.address.city || data.address.town || data.address.village);
        }
        
        if (parts.length > 0) {
          address = parts.join(', ');
        }
      }
      
      return address;
    }
    
    // Fallback
    return `Near ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
  } catch (error) {
    console.error('Reverse geocoding error:', error);
    return `Location: ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
  }
}

// ================= UPDATE SAVE BUTTON STATE =================
function updateSaveButtonState() {
  const saveBtn = document.getElementById('saveLocationBtn');
  if (saveBtn) {
    if (userLocation.savedInDB) {
      saveBtn.innerHTML = '<i class="fas fa-check"></i> Saved';
      saveBtn.disabled = true;
      saveBtn.style.background = 'var(--success)';
    } else if (userLocation.latitude && userLocation.longitude) {
      saveBtn.innerHTML = '<i class="fas fa-save"></i> Save to Profile';
      saveBtn.disabled = false;
      saveBtn.style.background = '';
      saveBtn.onclick = saveLocationToFirebase;
    } else {
      saveBtn.style.display = 'none';
    }
  }
}

// ================= SAVE LOCATION TO LOCAL STORAGE =================
function saveLocationToLocalStorage(latitude, longitude, address) {
  try {
    const locationData = {
      latitude: latitude,
      longitude: longitude,
      address: address,
      timestamp: new Date().toISOString()
    };
    
    localStorage.setItem('guest_location', JSON.stringify(locationData));
    
    // Update user location object
    userLocation = {
      ...userLocation,
      savedInDB: false
    };
    
    closeLocationModalAndRefresh();
  } catch (error) {
    console.error("Error saving to local storage:", error);
    showNotification('Failed to save location', 'error');
  }
}

// ================= SAVE LOCATION TO SESSION =================
async function saveLocationToSession(latitude, longitude, address) {
  try {
    const response = await fetch('save-location.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        lat: latitude,
        lng: longitude,
        address: address || ''
      })
    });
    
    const data = await response.json();
    if (data.success) {
      console.log('Location saved to session');
      
      // Update the userLocation object
      userLocation.latitude = latitude;
      userLocation.longitude = longitude;
      userLocation.address = address || '';
      
      // Update the displayed location
      updateLocationDisplay();
      
      return true;
    } else {
      console.error('Error saving to session:', data.error);
      return false;
    }
  } catch (error) {
    console.error('Error saving to session:', error);
    return false;
  }
}

// ================= SHOW LOCATION ERROR =================
function showLocationError(message) {
  modalBody.innerHTML = `
    <div class="location-loading">
      <i class="fas fa-exclamation-triangle" style="color: var(--danger); animation: none;"></i>
      <h3>Location Error</h3>
      <p>${message}</p>
    </div>
  `;
  
  modalFooter.innerHTML = `
    <button class="modal-btn cancel" onclick="showLocationOptions()">
      <i class="fas fa-arrow-left"></i> Try Another Method
    </button>
  `;
}

// ================= CLOSE LOCATION MODAL =================
function closeLocationModal() {
  locationModal.classList.remove('active');
  selectedLocationOption = null;
  selectedShippingAddressIndex = null;
}

// ================= CLOSE MODAL AND REFRESH =================
function closeLocationModalAndRefresh() {
  closeLocationModal();
  updateLocationDisplay();
  loadNearbyFoods(); // Reload nearby foods with new location
  
  // Trigger location update event for restaurant sections
  const event = new Event('locationUpdated');
  document.dispatchEvent(event);
  
  showNotification('Location updated successfully!', 'success');
}

// ================= UPDATE LOCATION DISPLAY =================
function updateLocationDisplay() {
  const locationText = document.getElementById('locationText');
  if (locationText) {
    if (userLocation.address) {
      locationText.textContent = userLocation.address;
    } else if (userLocation.latitude && userLocation.longitude) {
      locationText.textContent = `Location: ${userLocation.latitude.toFixed(4)}, ${userLocation.longitude.toFixed(4)}`;
    } else {
      locationText.textContent = 'Set your location';
    }
  }
}

// ================= NAVIGATE TO PROFILE LOCATION SETTINGS =================
function goToProfileLocationSettings() {
  <?php if ($isLoggedIn): ?>
  window.location.href = 'users/profile.php#location';
  <?php else: ?>
  window.location.href = 'login.php?redirect=users/profile.php';
  <?php endif; ?>
}

// ================= SET LOCATION (FOR BUTTONS) =================
function setLocation() {
  showLocationModal();
}

// ================= LOAD ALL SECTIONS =================
async function loadAllSections() {
  // Load random foods
  await loadRandomFoods();
  
  // Load categories from Firebase
  await loadCategories();
  
  // Load nearby foods
  await loadNearbyFoods();
  
  // Load sulit meals
  await loadSulitMeals();
  
  // Load recommended foods
  await loadRecommendedFoods(); // Add this line
}



// ================= LOAD RECOMMENDED FOODS =================
async function loadRecommendedFoods() {
  console.log('Loading recommended foods...');
  const recommendedSwiperWrapper = document.getElementById('recommendedSwiperWrapper');
  
  if (!recommendedSwiperWrapper) {
    console.error('Recommended swiper wrapper not found!');
    return;
  }
  
  // Show loading state
  recommendedSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <h3>Loading recommendations...</h3>
        <p>Finding personalized picks for you</p>
      </div>
    </div>
  `;
  
  try {
    // Get all published products
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(100)
      .get();
    
    console.log(`Found ${productsSnapshot.size} total products for recommendations`);
    
    if (productsSnapshot.empty) {
      showNoRecommendedResults();
      return;
    }
    
    // Convert to array
    const allProducts = [];
    productsSnapshot.forEach(doc => {
      const product = {
        id: doc.id,
        ...doc.data()
      };
      allProducts.push(product);
    });
    
    // Get user preferences (you can expand this based on actual user data)
    const userPreferences = getUserPreferences();
    
    // Score and sort products based on recommendations
    const scoredProducts = await scoreProductsForRecommendation(allProducts, userPreferences);
    
    // Take top RECOMMENDED_LIMIT items
    recommendedProducts = scoredProducts.slice(0, Math.min(RECOMMENDED_LIMIT, scoredProducts.length));
    
    // Clear container
    recommendedSwiperWrapper.innerHTML = '';
    
    if (recommendedProducts.length === 0) {
      showNoRecommendedResults();
      return;
    }
    
    // Display recommended products
    for (const product of recommendedProducts) {
      const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
      await createRecommendedSlide(product, restaurantDetails, recommendedSwiperWrapper);
    }
    
    // Initialize or update Swiper
    if (!recommendedSwiper) {
      initializeRecommendedSwiper();
    } else {
      recommendedSwiper.update();
    }
    
    console.log(`Displayed ${recommendedProducts.length} recommended products`);
    
  } catch (error) {
    console.error("Error loading recommended foods:", error);
    showNoRecommendedResults("Error loading recommendations");
  }
}

// ================= GET USER PREFERENCES =================
function getUserPreferences() {
  // This function should get user preferences from:
  // 1. User's past orders
  // 2. User's liked/rated items
  // 3. User's browsing history
  // 4. User's saved restaurants
  
  // For now, return a default set of preferences
  // You should implement this based on your actual user data
  return {
    preferredCategories: [], // Add user's preferred categories
    preferredPriceRange: { min: 0, max: 500 }, // User's typical spending
    ratingThreshold: 4.0, // Minimum rating user prefers
    locationPreference: userLocation.latitude && userLocation.longitude ? {
      latitude: userLocation.latitude,
      longitude: userLocation.longitude,
      maxDistance: 15 // km
    } : null
  };
}

// ================= SCORE PRODUCTS FOR RECOMMENDATION =================
async function scoreProductsForRecommendation(products, preferences) {
  const scoredProducts = [];
  
  for (const product of products) {
    let score = 0;
    
    // 1. Rating-based scoring (40% weight)
    const ratingData = getProductRating(product);
    const rating = parseFloat(ratingData.average);
    score += (rating / 5) * 40;
    
    // 2. Popularity scoring (30% weight)
    const reviewsCount = ratingData.count;
    score += Math.min((reviewsCount / 100) * 30, 30); // Cap at 30
    
    // 3. Price scoring (15% weight)
    const price = parseFloat(product.disPrice) || parseFloat(product.price) || 0;
    if (price > 0) {
      // Lower price gets higher score for value
      if (price <= 100) score += 15;
      else if (price <= 200) score += 12;
      else if (price <= 300) score += 8;
      else score += 5;
    }
    
    // 4. Location-based scoring (15% weight, if location is available)
    if (preferences.locationPreference && userLocation.latitude && userLocation.longitude) {
      try {
        const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
        if (restaurantDetails.latitude && restaurantDetails.longitude) {
          const distance = calculateDistance(
            userLocation.latitude,
            userLocation.longitude,
            restaurantDetails.latitude,
            restaurantDetails.longitude
          );
          
          // Closer restaurants get higher scores
          if (distance <= 5) score += 15;
          else if (distance <= 10) score += 10;
          else if (distance <= 15) score += 5;
        }
      } catch (error) {
        console.error("Error calculating distance for recommendation:", error);
      }
    }
    
    // 5. Category preference (to be implemented based on user data)
    // if (product.category && preferences.preferredCategories.includes(product.category)) {
    //   score += 10;
    // }
    
    scoredProducts.push({
      ...product,
      recommendationScore: score
    });
  }
  
  // Sort by recommendation score (descending)
  scoredProducts.sort((a, b) => b.recommendationScore - a.recommendationScore);
  
  return scoredProducts;
}

// ================= CREATE RECOMMENDED SLIDE =================
async function createRecommendedSlide(food, restaurantDetails, container) {
  // Get product rating
  const ratingData = getProductRating(food);
  const rating = ratingData.average;
  const reviewsCount = ratingData.count;
  
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
  
  // Generate single yellow star (ONE STAR ONLY)
  const productStarsHTML = generateSingleStarHTML();
  
  // Get food description and truncate if needed
  let description = food.description || 'Delicious food item. Try it now!';
  const words = description.split(' ');
  if (words.length > 12) {
    description = words.slice(0, 12).join(' ') + '...';
  }
  
  const slide = document.createElement("div");
  slide.className = "swiper-slide swiper-recommended-slide";
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${food.id}`;
  card.className = "food-card";
  card.setAttribute('data-product', food.id);
  card.style.animation = 'fadeIn 0.5s ease forwards';
  card.style.position = 'relative';
  card.style.width = '280px'; // Fixed width
  card.style.height = '480px'; // Fixed height to match other cards

  card.innerHTML = `
    <div class="food-image-container">
      <div class="food-image-wrapper">
        <img src="${food.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
             loading="lazy" alt="${food.name || 'Food item'}"
             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';">
      </div>
      <div class="recommended-badge">
        <i class="fas fa-star"></i> Recommended
      </div>
      <div class="restaurant-avatar">
        <img src="${restaurantDetails.logo}" 
             alt="${restaurantDetails.name}"
             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80';">
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
      
      <div class="food-description">
        ${description}
      </div>
      
      <div class="food-price-section">
        <div class="food-price">
          ₱${price.toFixed(2)}
          ${originalPrice ? `<span class="original-price">₱${originalPrice.toFixed(2)}</span>` : ''}
        </div>
      </div>
    </div>
  `;

  slide.appendChild(card);
  container.appendChild(slide);
}

// ================= INITIALIZE RECOMMENDED SWIPER =================
// ================= INITIALIZE RECOMMENDED SWIPER =================
function initializeRecommendedSwiper() {
  recommendedSwiper = new Swiper('.mySwiperRecommended', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    centeredSlides: false,
    loop: false,
    navigation: {
      nextEl: '.mySwiperRecommended .swiper-button-next',
      prevEl: '.mySwiperRecommended .swiper-button-prev',
    },
    breakpoints: {
      320: { 
        slidesPerView: 1.1, 
        spaceBetween: 10 
      },
      480: { 
        slidesPerView: 1.3, 
        spaceBetween: 15 
      },
      640: { 
        slidesPerView: 1.8, 
        spaceBetween: 15 
      },
      768: { 
        slidesPerView: 2.2, 
        spaceBetween: 20 
      },
      1024: { 
        slidesPerView: 2.8, 
        spaceBetween: 20 
      },
      1200: { 
        slidesPerView: 3.5, 
        spaceBetween: 25 
      }
    }
  });
}

// ================= SHOW NO RECOMMENDED RESULTS =================
function showNoRecommendedResults(message = null) {
  const recommendedSwiperWrapper = document.getElementById('recommendedSwiperWrapper');
  if (!recommendedSwiperWrapper) return;
  
  <?php if ($isLoggedIn): ?>
  let specificMessage = message || "No recommendations available";
  let specificDetail = "Try exploring more foods to get personalized recommendations";
  
  recommendedSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-star"></i>
        <h3>${specificMessage}</h3>
        <p>${specificDetail}</p>
      </div>
    </div>
  `;
  <?php else: ?>
  recommendedSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="auth-prompt">
        <div class="auth-prompt-content">
          <i class="fas fa-star fa-3x"></i>
          <h3>Get Personalized Recommendations</h3>
          <p>Login or register to get personalized food recommendations based on your preferences</p>
          <div class="auth-prompt-buttons">
            <a href="login.php" class="auth-prompt-btn login">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="register.php" class="auth-prompt-btn register">
              <i class="fas fa-user-plus"></i> Register
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
  <?php endif; ?>
  
  if (recommendedSwiper) {
    recommendedSwiper.update();
  }
}
// ================= LOAD SULIT MEALS =================
async function loadSulitMeals() {
  console.log('Loading sulit meals...');
  const sulitSwiperWrapper = document.getElementById('sulitSwiperWrapper');
  
  if (!sulitSwiperWrapper) {
    console.error('Sulit swiper wrapper not found!');
    return;
  }
  
  // Show loading state
  sulitSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <h3>Loading affordable meals...</h3>
        <p>Finding meals ₱${SULIT_PRICE_LIMIT} and below</p>
      </div>
    </div>
  `;
  
  try {
    // Get all published products
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(100)
      .get();
    
    console.log(`Found ${productsSnapshot.size} total products for sulit filter`);
    
    if (productsSnapshot.empty) {
      showNoSulitResults();
      return;
    }
    
    // Convert to array and filter by price
    const allProducts = [];
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
        
        if (!isNaN(disPrice) && disPrice > 0) {
          actualPrice = disPrice;
        } else if (!isNaN(regPrice)) {
          actualPrice = regPrice;
        }
      } catch (e) {
        actualPrice = 0;
      }
      
      // Filter by price (150 pesos or less)
      if (actualPrice > 0 && actualPrice <= SULIT_PRICE_LIMIT) {
        allProducts.push(product);
      }
    });
    
    console.log(`Found ${allProducts.length} products within ₱${SULIT_PRICE_LIMIT} budget`);
    
    // Shuffle array to get random items
    const shuffled = [...allProducts].sort(() => 0.5 - Math.random());
    
    // Take first SULIT_LIMIT items
    sulitProducts = shuffled.slice(0, Math.min(SULIT_LIMIT, shuffled.length));
    
    // Clear container
    sulitSwiperWrapper.innerHTML = '';
    
    if (sulitProducts.length === 0) {
      showNoSulitResults();
      return;
    }
    
    // Display sulit products
    for (const product of sulitProducts) {
      const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
      await createFoodSlide(product, restaurantDetails, null, sulitSwiperWrapper, 'sulit');
    }
    
    // Initialize or update Swiper
    if (!sulitSwiper) {
      initializeSulitSwiper();
    } else {
      sulitSwiper.update();
    }
    
    console.log(`Displayed ${sulitProducts.length} sulit meals`);
    
  } catch (error) {
    console.error("Error loading sulit meals:", error);
    showNoSulitResults("Error loading affordable meals");
  }
}


// ================= FAVORITE RESTAURANTS FUNCTIONS =================
let userFavorites = [];
let favoriteRestaurantsMap = {};

// Check if user is logged in (from PHP)
const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
const userId = "<?php echo $userId ?? ''; ?>";

// Load user favorites
async function loadUserFavorites() {
  if (!isLoggedIn || !userId) {
    console.log('User not logged in, skipping favorites load');
    return;
  }
  
  try {
    console.log('Loading user favorites...');
    
    const favoritesSnapshot = await db.collection("favorite_restaurants")
      .where("userId", "==", userId)
      .get();
    
    userFavorites = [];
    favoriteRestaurantsMap = {};
    
    favoritesSnapshot.forEach(doc => {
      const favorite = doc.data();
      userFavorites.push(favorite);
      favoriteRestaurantsMap[favorite.restaurantId] = true;
    });
    
    console.log(`Loaded ${userFavorites.length} favorite restaurants`);
    
  } catch (error) {
    console.error("Error loading user favorites:", error);
  }
}

// Toggle favorite restaurant
async function toggleFavorite(restaurantId, restaurantName, event) {
  event.stopPropagation();
  
  if (!isLoggedIn || !userId) {
    showNotification('Please login to save favorite restaurants', 'warning');
    return;
  }
  
  const heartBtn = event.currentTarget;
  const isCurrentlyFavorite = heartBtn.classList.contains('active');
  
  try {
    if (isCurrentlyFavorite) {
      // Remove from favorites
      const favoriteQuery = await db.collection("favorite_restaurants")
        .where("userId", "==", userId)
        .where("restaurantId", "==", restaurantId)
        .get();
      
      if (!favoriteQuery.empty) {
        await favoriteQuery.docs[0].ref.delete();
        delete favoriteRestaurantsMap[restaurantId];
        heartBtn.classList.remove('active');
        showNotification('Removed from favorites', 'success');
      }
    } else {
      // Add to favorites
      await db.collection("favorite_restaurants").add({
        userId: userId,
        restaurantId: restaurantId,
        restaurantName: restaurantName,
        addedAt: firebase.firestore.FieldValue.serverTimestamp()
      });
      
      favoriteRestaurantsMap[restaurantId] = true;
      heartBtn.classList.add('active');
      showNotification('Added to favorites', 'success');
    }
  } catch (error) {
    console.error("Error toggling favorite:", error);
    showNotification('Error saving favorite', 'error');
  }
}



// ================= ENHANCED CREATE RESTAURANT CARD =================
async function createRestaurantCard(restaurant, container, type) {
  // Format distance
  let distanceText = '';
  let timeText = '';
  let distanceBadge = '';
  
  if (restaurant.distance !== null && restaurant.distance !== undefined && !isNaN(restaurant.distance)) {
    if (restaurant.distance < 1) {
      distanceText = `${(restaurant.distance * 1000).toFixed(0)}m`;
      timeText = `${Math.round(restaurant.distance * 1000 / 80)} min`; // Assuming 80m/min walking speed
    } else {
      distanceText = `${restaurant.distance.toFixed(1)}km`;
      timeText = `${Math.round(restaurant.distance * 15)} min`; // Assuming 15 min/km
    }
    
    distanceBadge = `
      <div class="restaurant-distance-badge">
        <i class="fas fa-walking"></i> ${timeText}
      </div>
    `;
  } else {
    distanceBadge = `
      <div class="restaurant-distance-badge" style="background: var(--warning);">
        <i class="fas fa-map-marker-question"></i> Distance unknown
      </div>
    `;
  }
  
  // Calculate rating
  const reviewsCount = restaurant.reviewsCount || 0;
  const reviewsSum = restaurant.reviewsSum || 0;
  const rating = reviewsCount > 0 ? (reviewsSum / reviewsCount).toFixed(1) : "0.0";
  
  // Check if restaurant is in favorites
  const isFavorite = favoriteRestaurantsMap[restaurant.id] || false;
  
  // Get restaurant category
  const restaurantCategory = restaurant.categoryTitle || restaurant.category || restaurant.type || 'Restaurant';
  
  const card = document.createElement("div");
  card.className = "restaurant-card";
  card.style.animation = 'fadeIn 0.5s ease forwards';
  card.setAttribute('data-restaurant-id', restaurant.id);
  card.setAttribute('data-category', restaurantCategory.toLowerCase());
  
  card.innerHTML = `
    <div class="restaurant-image-container">
      <img src="${restaurant.photo || restaurant.image || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           alt="${restaurant.title || restaurant.name || 'Restaurant'}"
           loading="lazy"
           onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';">
      
      ${distanceBadge}
      
      ${type === 'top' ? `
        <div class="top-rated-badge">
          <i class="fas fa-crown"></i> Top Rated
        </div>
      ` : ''}
      
      <button class="heart-btn ${isFavorite ? 'active' : ''}" 
              onclick="toggleFavorite('${restaurant.id}', '${restaurant.title || restaurant.name || 'Restaurant'}', event)"
              aria-label="${isFavorite ? 'Remove from favorites' : 'Add to favorites'}">
        <i class="fas fa-heart"></i>
      </button>
    </div>
    
    <div class="restaurant-info">
      <div class="restaurant-header">
        <div class="restaurant-name">${restaurant.title || restaurant.name || 'Unnamed Restaurant'}</div>
        <div class="restaurant-distance">
          <i class="fas fa-location-dot"></i>
          ${distanceText ? `${distanceText} away` : 'Location data needed'}
        </div>
        <div style="font-size: 0.8rem; color: var(--gray); margin-top: 5px;">
          <i class="fas fa-tag"></i> ${restaurantCategory}
        </div>
      </div>
      
      <div class="restaurant-rating">
        <div class="rating-stars">
          ${generateStarRating(parseFloat(rating))}
        </div>
        <span class="rating-value">${rating}</span>
        <span class="rating-count">(${reviewsCount})</span>
      </div>
      
      <div style="margin-top: auto; display: flex; gap: 10px;">
        <a href="restaurant.php?id=${restaurant.id}" class="view-all-btn" style="flex: 1; text-align: center; padding: 8px 15px; font-size: 0.9rem;">
          <i class="fas fa-utensils"></i> View Menu
        </a>
        <button class="view-all-btn" onclick="viewRestaurantDetails('${restaurant.id}')" 
                style="background: var(--info); padding: 8px 15px; font-size: 0.9rem; border: none;">
          <i class="fas fa-info-circle"></i> Details
        </button>
      </div>
    </div>
  `;
  
  // Add click handler for the entire card (except buttons)
  card.addEventListener('click', function(e) {
    if (!e.target.closest('.heart-btn') && !e.target.closest('button') && !e.target.closest('a')) {
      window.location.href = `restaurant.php?id=${restaurant.id}`;
    }
  });
  
  container.appendChild(card);
}

// ================= ENHANCED LOAD PRODUCTS FROM RESTAURANTS =================
async function loadProductsFromRestaurants(restaurants, container, type) {
  if (!restaurants || restaurants.length === 0) {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
        <i class="fas fa-utensils fa-2x" style="margin-bottom: 15px;"></i>
        <h3 style="color: var(--secondary); margin-bottom: 8px;">No foods available</h3>
        <p>These restaurants haven't added menu items yet</p>
      </div>
    `;
    return;
  }
  
  try {
    // Get restaurant IDs
    const restaurantIds = restaurants.map(r => r.id);
    
    console.log(`Loading products from ${restaurantIds.length} restaurants...`);
    
    // Firestore "in" query has a limit of 10, so we need to batch if more than 10
    let allProducts = [];
    
    if (restaurantIds.length <= 10) {
      // Single query if 10 or fewer restaurants
      const productsSnapshot = await db.collection("vendor_products")
        .where("publish", "==", true)
        .where("vendorID", "in", restaurantIds)
        .limit(20)
        .get();
      
      if (!productsSnapshot.empty) {
        productsSnapshot.forEach(doc => {
          allProducts.push({
            id: doc.id,
            ...doc.data()
          });
        });
      }
    } else {
      // Batch queries if more than 10 restaurants
      const batchSize = 10;
      for (let i = 0; i < restaurantIds.length; i += batchSize) {
        const batchIds = restaurantIds.slice(i, i + batchSize);
        
        const productsSnapshot = await db.collection("vendor_products")
          .where("publish", "==", true)
          .where("vendorID", "in", batchIds)
          .limit(10)
          .get();
        
        if (!productsSnapshot.empty) {
          productsSnapshot.forEach(doc => {
            allProducts.push({
              id: doc.id,
              ...doc.data()
            });
          });
        }
      }
    }
    
    console.log(`Found ${allProducts.length} total products from nearby restaurants`);
    
    if (allProducts.length === 0) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
          <i class="fas fa-utensils fa-2x" style="margin-bottom: 15px;"></i>
          <h3 style="color: var(--secondary); margin-bottom: 8px;">No menu items available</h3>
          <p>Check back later for menu updates</p>
        </div>
      `;
      return;
    }
    
    // Filter by price for "nearby" type (sulit meals)
    let filteredProducts = allProducts;
    if (type === 'nearby') {
      filteredProducts = allProducts.filter(product => {
        try {
          const price = parseFloat(product.disPrice) || parseFloat(product.price) || 0;
          return price > 0 && price <= SULIT_PRICE_LIMIT;
        } catch (e) {
          return false;
        }
      });
      
      if (filteredProducts.length === 0) {
        // If no sulit meals, show regular products
        filteredProducts = allProducts;
      }
    }
    
    // Shuffle and take limit (6 items)
    const shuffled = [...filteredProducts].sort(() => 0.5 - Math.random());
    const displayProducts = shuffled.slice(0, 6);
    
    console.log(`Displaying ${displayProducts.length} products in ${type} section`);
    
    // Clear container
    container.innerHTML = '';
    
    // Display products
    for (const product of displayProducts) {
      await createProductCard(product, container, type);
    }
    
  } catch (error) {
    console.error("Error loading products from restaurants:", error);
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
        <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
        <h3 style="color: var(--secondary); margin-bottom: 8px;">Error loading menu items</h3>
        <p>Please try again later</p>
      </div>
    `;
  }
}

// ================= VIEW RESTAURANT DETAILS =================
function viewRestaurantDetails(restaurantId) {
  // You can implement a modal or redirect
  console.log(`Viewing details for restaurant: ${restaurantId}`);
  // For now, redirect to restaurant page
  window.location.href = `restaurant.php?id=${restaurantId}`;
}

// ================= ENHANCED FETCH RESTAURANT DETAILS =================
async function fetchRestaurantDetails(vendorId) {
  // Check cache first
  if (restaurantCache[vendorId]) {
    return restaurantCache[vendorId];
  }
  
  try {
    const vendorDoc = await db.collection("vendors").doc(vendorId).get();
    if (vendorDoc.exists) {
      const vendorData = vendorDoc.data();
      
      // Extract location from various possible fields
      let latitude = null;
      let longitude = null;
      
      // Try different possible location fields
      if (vendorData.latitude && vendorData.longitude) {
        latitude = vendorData.latitude;
        longitude = vendorData.longitude;
      } else if (vendorData.coordinates && Array.isArray(vendorData.coordinates) && vendorData.coordinates.length >= 2) {
        latitude = vendorData.coordinates[0];
        longitude = vendorData.coordinates[1];
      } else if (vendorData.location && vendorData.location.latitude && vendorData.location.longitude) {
        latitude = vendorData.location.latitude;
        longitude = vendorData.location.longitude;
      } else if (vendorData.geolocation) {
        latitude = vendorData.geolocation.latitude;
        longitude = vendorData.geolocation.longitude;
      }
      
      const restaurantData = {
        name: vendorData.title || vendorData.name || "Unknown Restaurant",
        logo: vendorData.photo || vendorData.logo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
        category: vendorData.categoryTitle || vendorData.category || 'Restaurant',
        deliveryCharge: vendorData.minimum_delivery_charges ? `₱${vendorData.minimum_delivery_charges}` : 'Free',
        rating: vendorData.reviewsCount > 0 ? (vendorData.reviewsSum / vendorData.reviewsCount).toFixed(1) : "0.0",
        reviewsCount: vendorData.reviewsCount || 0,
        latitude: latitude,
        longitude: longitude
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
    rating: "0.0",
    reviewsCount: 0,
    latitude: null,
    longitude: null
  };
  
  restaurantCache[vendorId] = defaultData;
  return defaultData;
}

// ================= ENHANCED LOAD TOP RESTAURANTS =================
async function loadTopRestaurants() {
  console.log('Loading top restaurants...');
  const topRestaurantsContainer = document.getElementById('topRestaurantsContainer');
  const topProductsContainer = document.getElementById('topProductsContainer');
  
  if (!topRestaurantsContainer || !topProductsContainer) {
    console.error('Top restaurants containers not found!');
    return;
  }
  
  try {
    // Get all vendors (restaurants) that are published
    const vendorsSnapshot = await db.collection("vendors")
      .where("publish", "==", true)
      .get();
    
    console.log(`Found ${vendorsSnapshot.size} total vendors`);
    
    if (vendorsSnapshot.empty) {
      showNoTopRestaurants("No restaurants found in database");
      return;
    }
    
    // Calculate restaurant ratings and sort by rating
    const restaurantsWithRating = [];
    
    vendorsSnapshot.forEach(doc => {
      const vendor = {
        id: doc.id,
        ...doc.data()
      };
      
      // Calculate rating (default to 0 if no reviews)
      const reviewsCount = vendor.reviewsCount || 0;
      const reviewsSum = vendor.reviewsSum || 0;
      const rating = reviewsCount > 0 ? reviewsSum / reviewsCount : 0;
      
      // Only include restaurants with ratings above 0
      if (rating > 0) {
        restaurantsWithRating.push({
          ...vendor,
          calculatedRating: rating,
          reviewsCount: reviewsCount
        });
      }
    });
    
    if (restaurantsWithRating.length === 0) {
      showNoTopRestaurants("No restaurants with ratings found");
      return;
    }
    
    // Sort by rating (highest first), then by number of reviews
    restaurantsWithRating.sort((a, b) => {
      if (b.calculatedRating !== a.calculatedRating) {
        return b.calculatedRating - a.calculatedRating;
      }
      return b.reviewsCount - a.reviewsCount;
    });
    
    // Take top restaurants (limit 6)
    const topRestaurants = restaurantsWithRating.slice(0, 6);
    
    console.log(`Found ${topRestaurants.length} top restaurants`);
    
    // Clear containers
    topRestaurantsContainer.innerHTML = '';
    topProductsContainer.innerHTML = '';
    
    if (topRestaurants.length === 0) {
      showNoTopRestaurants();
      return;
    }
    
    // Display top restaurants
    for (const restaurant of topRestaurants) {
      await createRestaurantCard(restaurant, topRestaurantsContainer, 'top');
    }
    
    // Load trending foods from these restaurants
    await loadProductsFromRestaurants(topRestaurants, topProductsContainer, 'top');
    
  } catch (error) {
    console.error("Error loading top restaurants:", error);
    showNoTopRestaurants("Error loading top restaurants");
  }
}


// ================= CREATE RESTAURANT CARD =================
async function createRestaurantCard(restaurant, container, type) {
  // Format distance
  let distanceText = '';
  let timeText = '';
  
  if (restaurant.distance !== undefined) {
    if (restaurant.distance < 1) {
      distanceText = `${(restaurant.distance * 1000).toFixed(0)}m`;
      timeText = `${Math.round(restaurant.distance * 1000 / 80)} min`; // Assuming 80m/min walking speed
    } else {
      distanceText = `${restaurant.distance.toFixed(1)}km`;
      timeText = `${Math.round(restaurant.distance * 15)} min`; // Assuming 15 min/km
    }
  }
  
  // Calculate rating
  const reviewsCount = restaurant.reviewsCount || 0;
  const reviewsSum = restaurant.reviewsSum || 0;
  const rating = reviewsCount > 0 ? (reviewsSum / reviewsCount).toFixed(1) : "0.0";
  
  // Check if restaurant is in favorites
  const isFavorite = favoriteRestaurantsMap[restaurant.id] || false;
  
  const card = document.createElement("div");
  card.className = "restaurant-card";
  card.style.animation = 'fadeIn 0.5s ease forwards';
  
  card.innerHTML = `
    <div class="restaurant-image-container">
      <img src="${restaurant.photo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           alt="${restaurant.title || 'Restaurant'}"
           onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      
      ${type === 'nearby' ? `
        <div class="restaurant-distance-badge">
          <i class="fas fa-walking"></i> ${timeText}
        </div>
      ` : ''}
      
      ${type === 'top' ? `
        <div class="top-rated-badge">
          <i class="fas fa-crown"></i> Top Rated
        </div>
      ` : ''}
      
      <button class="heart-btn ${isFavorite ? 'active' : ''}" 
              onclick="toggleFavorite('${restaurant.id}', '${restaurant.title || 'Restaurant'}', event)">
        <i class="fas fa-heart"></i>
      </button>
    </div>
    
    <div class="restaurant-info">
      <div class="restaurant-header">
        <div class="restaurant-name">${restaurant.title || 'Unnamed Restaurant'}</div>
        <div class="restaurant-distance">
          <i class="fas fa-location-dot"></i>
          ${distanceText ? `${distanceText} away` : 'Distance not available'}
        </div>
      </div>
      
      <div class="restaurant-rating">
        <div class="rating-stars">
          ${generateStarRating(parseFloat(rating))}
        </div>
        <span class="rating-value">${rating}</span>
        <span class="rating-count">(${reviewsCount})</span>
      </div>
      
      <div style="margin-top: auto;">
        <a href="restaurant.php?id=${restaurant.id}" class="view-all-btn" style="width: 100%; text-align: center; padding: 8px 15px; font-size: 0.9rem;">
          <i class="fas fa-utensils"></i> View Menu
        </a>
      </div>
    </div>
  `;
  
  container.appendChild(card);
}

// ================= LOAD PRODUCTS FROM RESTAURANTS =================
async function loadProductsFromRestaurants(restaurants, container, type) {
  try {
    // Get restaurant IDs
    const restaurantIds = restaurants.map(r => r.id);
    
    // Get products from these restaurants
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .where("vendorID", "in", restaurantIds.slice(0, 10)) // Firestore limit: 10 items in "in" query
      .limit(20)
      .get();
    
    if (productsSnapshot.empty) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
          <i class="fas fa-utensils fa-2x" style="margin-bottom: 15px;"></i>
          <h3 style="color: var(--secondary); margin-bottom: 8px;">No foods available</h3>
          <p>Check back later for menu items</p>
        </div>
      `;
      return;
    }
    
    // Convert to array and filter by price for "nearby" type
    const allProducts = [];
    productsSnapshot.forEach(doc => {
      const product = {
        id: doc.id,
        ...doc.data()
      };
      
      // For nearby section, only show sulit meals (₱150 & below)
      if (type === 'nearby') {
        const price = parseFloat(product.disPrice) || parseFloat(product.price) || 0;
        if (price <= SULIT_PRICE_LIMIT) {
          allProducts.push(product);
        }
      } else {
        // For top restaurants, show all products
        allProducts.push(product);
      }
    });
    
    // Shuffle and take limit (6 items)
    const shuffled = [...allProducts].sort(() => 0.5 - Math.random());
    const displayProducts = shuffled.slice(0, 6);
    
    if (displayProducts.length === 0) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
          <i class="fas fa-utensils fa-2x" style="margin-bottom: 15px;"></i>
          <h3 style="color: var(--secondary); margin-bottom: 8px;">No foods available</h3>
          <p>Check back later for menu items</p>
        </div>
      `;
      return;
    }
    
    // Display products
    for (const product of displayProducts) {
      await createProductCard(product, container, type);
    }
    
  } catch (error) {
    console.error("Error loading products from restaurants:", error);
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--gray);">
        <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
        <h3 style="color: var(--secondary); margin-bottom: 8px;">Error loading foods</h3>
        <p>Please try again later</p>
      </div>
    `;
  }
}

// ================= CREATE PRODUCT CARD =================
async function createProductCard(product, container, type) {
  // Handle price safely
  let price = 0;
  let originalPrice = null;
  
  try {
    const disPrice = parseFloat(product.disPrice);
    const regPrice = parseFloat(product.price);
    
    if (!isNaN(disPrice) && disPrice > 0 && disPrice < regPrice) {
      price = disPrice;
      originalPrice = regPrice;
    } else if (!isNaN(regPrice)) {
      price = regPrice;
    }
  } catch (e) {
    console.warn(`Error parsing price for ${product.id}`);
    price = 0;
  }
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${product.id}`;
  card.className = "product-card";
  card.style.animation = 'fadeIn 0.5s ease forwards';
  
  card.innerHTML = `
    <div class="product-image-container">
      <img src="${product.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           alt="${product.name || 'Food item'}"
           onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      
      ${type === 'nearby' && price <= SULIT_PRICE_LIMIT ? `
        <div class="sulit-badge">
          <i class="fas fa-tag"></i> ₱${SULIT_PRICE_LIMIT} & below
        </div>
      ` : ''}
      
      ${type === 'top' ? `
        <div class="top-rated-badge" style="background: var(--warning);">
          <i class="fas fa-fire"></i> Trending
        </div>
      ` : ''}
    </div>
    
    <div class="product-info">
      <div class="product-name">${product.name || 'Unnamed Item'}</div>
      <div class="product-price">
        ₱${price.toFixed(2)}
        ${originalPrice ? `<span class="original-price">₱${originalPrice.toFixed(2)}</span>` : ''}
      </div>
    </div>
  `;
  
  container.appendChild(card);
}

// ================= GENERATE STAR RATING HTML =================
function generateStarRating(rating) {
  let starsHTML = '';
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 >= 0.5;
  
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

// ================= SHOW NO RESULTS =================
function showNoNearbyRestaurants(message = null) {
  const nearbyRestaurantsContainer = document.getElementById('nearbyRestaurantsContainer');
  const nearbyProductsContainer = document.getElementById('nearbyProductsContainer');
  
  if (!nearbyRestaurantsContainer) return;
  
  let specificMessage = message || "No nearby restaurants found";
  let specificDetail = "Try changing your location or check back later";
  
  if (!userLocation.latitude) {
    specificMessage = "Set your location to see nearby restaurants";
    specificDetail = "Click 'Set Location' to get started";
  }
  
  nearbyRestaurantsContainer.innerHTML = `
    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
      <i class="fas fa-map-marker-alt fa-3x" style="color: var(--gray-light); margin-bottom: 20px;"></i>
      <h3 style="color: var(--secondary); margin-bottom: 10px;">${specificMessage}</h3>
      <p style="color: var(--gray); margin-bottom: 25px; max-width: 400px; margin-left: auto; margin-right: auto;">${specificDetail}</p>
      ${!userLocation.latitude ? `
        <button class="set-location-btn" onclick="showLocationModal()">
          <i class="fas fa-crosshairs"></i> Set Location
        </button>
      ` : ''}
    </div>
  `;
  
  if (nearbyProductsContainer) {
    nearbyProductsContainer.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--gray);">
        <i class="fas fa-utensils fa-3x" style="margin-bottom: 20px;"></i>
        <h3 style="color: var(--secondary); margin-bottom: 10px;">No foods available</h3>
        <p>Set your location to see nearby restaurants and their menus</p>
      </div>
    `;
  }
}


// ================= UPDATE LOAD ALL SECTIONS FUNCTION =================
async function loadAllSections() {
  // Load random foods
  await loadRandomFoods();
  
  // Load categories from Firebase
  await loadCategories();
  
  // Load nearby foods
  await loadNearbyFoods();
  
  // Load sulit meals
  await loadSulitMeals();
  
  // Load recommended foods
  await loadRecommendedFoods();
  
  // Load user favorites
  await loadUserFavorites();
}

// ================= UPDATE INITIALIZATION =================
document.addEventListener('DOMContentLoaded', async function() {
  console.log('LalaGO Initializing...');
  console.log('User location from session:', userLocation);
  
  // Setup refresh buttons
  setupRefreshButtons();
  
  // Check if user needs to set location
  await checkLocationStatus();
  
  // Load advertisements first
  await loadAdvertisements();
  
  // Load food categories from database
  await loadFoodCategoriesFromDatabase();
  
  // Load all other sections (including new restaurant sections)
  await loadAllSections();
  
  console.log('LalaGO initialized');
});



// ================= CALCULATE DISTANCE =================
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat/2) * Math.sin(dLat/2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
    Math.sin(dLon/2) * Math.sin(dLon/2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  return R * c;
}

// ================= FETCH RESTAURANT DETAILS WITH LOCATION =================
async function fetchRestaurantDetails(vendorId) {
  // Check cache first
  if (restaurantCache[vendorId]) {
    return restaurantCache[vendorId];
  }
  
  try {
    const vendorDoc = await db.collection("vendors").doc(vendorId).get();
    if (vendorDoc.exists) {
      const vendorData = vendorDoc.data();
      
      // Extract location from various possible fields
      let latitude = null;
      let longitude = null;
      
      // Try different possible location fields
      if (vendorData.latitude && vendorData.longitude) {
        latitude = vendorData.latitude;
        longitude = vendorData.longitude;
      } else if (vendorData.coordinates && Array.isArray(vendorData.coordinates) && vendorData.coordinates.length >= 2) {
        latitude = vendorData.coordinates[0];
        longitude = vendorData.coordinates[1];
      } else if (vendorData.location && vendorData.location.latitude && vendorData.location.longitude) {
        latitude = vendorData.location.latitude;
        longitude = vendorData.location.longitude;
      } else if (vendorData.geolocation) {
        latitude = vendorData.geolocation.latitude;
        longitude = vendorData.geolocation.longitude;
      }
      
      const restaurantData = {
        name: vendorData.title || vendorData.name || "Unknown Restaurant",
        logo: vendorData.photo || vendorData.logo || 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80',
        category: vendorData.categoryTitle || vendorData.category || 'Restaurant',
        deliveryCharge: vendorData.minimum_delivery_charges ? `₱${vendorData.minimum_delivery_charges}` : 'Free',
        rating: vendorData.reviewsCount > 0 ? (vendorData.reviewsSum / vendorData.reviewsCount).toFixed(1) : "0.0",
        reviewsCount: vendorData.reviewsCount || 0,
        latitude: latitude,
        longitude: longitude
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
    rating: "0.0",
    reviewsCount: 0,
    latitude: null,
    longitude: null
  };
  
  restaurantCache[vendorId] = defaultData;
  return defaultData;
}


// ================= DEBUG RESTAURANT LOCATIONS =================
async function debugRestaurantLocations() {
  console.log('Debugging restaurant locations...');
  
  try {
    const vendorsSnapshot = await db.collection("vendors")
      .where("publish", "==", true)
      .limit(5)
      .get();
    
    console.log(`Found ${vendorsSnapshot.size} published vendors`);
    
    if (vendorsSnapshot.empty) {
      console.warn('No published vendors found!');
      return;
    }
    
    vendorsSnapshot.forEach(doc => {
      const vendor = doc.data();
      console.log(`Restaurant: ${vendor.title || vendor.name || doc.id}`);
      console.log('Location data available:');
      console.log('- latitude:', vendor.latitude);
      console.log('- longitude:', vendor.longitude);
      console.log('- coordinates:', vendor.coordinates);
      console.log('- location object:', vendor.location);
      console.log('- geolocation:', vendor.geolocation);
      console.log('---');
    });
    
  } catch (error) {
    console.error('Debug error:', error);
  }
}

// Call this in your initialization:
document.addEventListener('DOMContentLoaded', async function() {
  console.log('LalaGO Initializing...');
  
  // Debug Firebase data
  await debugRestaurantLocations();
  
  // ... rest of your initialization
});

// ================= LOAD RANDOM FOODS =================
async function loadRandomFoods() {
  console.log('Loading random foods...');
  const randomSwiperWrapper = document.getElementById('randomSwiperWrapper');
  
  if (!randomSwiperWrapper) {
    console.error('Random swiper wrapper not found!');
    return;
  }
  
  // Show loading state
  randomSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-spinner fa-spin fa-2x"></i>
        <h3>Loading random picks...</h3>
        <p>Finding delicious foods just for you</p>
      </div>
    </div>
  `;
  
  try {
    // Get all published products
    const snapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(50)
      .get();
    
    console.log(`Found ${snapshot.size} total products`);
    
    if (snapshot.empty) {
      showNoRandomResults();
      return;
    }
    
    // Convert to array and shuffle
    const allProducts = [];
    snapshot.forEach(doc => {
      allProducts.push({
        id: doc.id,
        ...doc.data()
      });
    });
    
    // Shuffle array to get random items
    const shuffled = [...allProducts].sort(() => 0.5 - Math.random());
    
    // Take first 10 items
    randomProducts = shuffled.slice(0, Math.min(RANDOM_LIMIT, shuffled.length));
    
    // Clear container
    randomSwiperWrapper.innerHTML = '';
    
    if (randomProducts.length === 0) {
      showNoRandomResults();
      return;
    }
    
    // Display random products
    for (const product of randomProducts) {
      const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
      await createFoodSlide(product, restaurantDetails, null, randomSwiperWrapper, 'random');
    }
    
    // Initialize or update Swiper
    if (!randomSwiper) {
      initializeRandomSwiper();
    } else {
      randomSwiper.update();
    }
    
    console.log(`Displayed ${randomProducts.length} random products`);
    
  } catch (error) {
    console.error("Error loading random foods:", error);
    showNoRandomResults("Error loading random picks");
  }
}

// ================= INITIALIZE SWIPERS =================
function initializeRandomSwiper() {
  randomSwiper = new Swiper('.mySwiperRandom', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    centeredSlides: false,
    loop: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: { slidesPerView: 1.1, spaceBetween: 10 },
      480: { slidesPerView: 1.3, spaceBetween: 15 },
      640: { slidesPerView: 1.8, spaceBetween: 15 },
      768: { slidesPerView: 2.2, spaceBetween: 20 },
      1024: { slidesPerView: 2.8, spaceBetween: 20 },
      1200: { slidesPerView: 3.5, spaceBetween: 25 }
    }
  });
}

function initializeNearbySwiper() {
  nearbySwiper = new Swiper('.mySwiperNearby', {
    slidesPerView: 'auto',
    spaceBetween: 15,
    centeredSlides: false,
    loop: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: { slidesPerView: 1.1, spaceBetween: 10 },
      480: { slidesPerView: 1.3, spaceBetween: 10 },
      640: { slidesPerView: 1.8, spaceBetween: 12 },
      768: { slidesPerView: 2.2, spaceBetween: 12 },
      1024: { slidesPerView: 2.8, spaceBetween: 15 },
      1200: { slidesPerView: 3.2, spaceBetween: 15 },
      1400: { slidesPerView: 3.5, spaceBetween: 15 }
    }
  });
}

function initializeSulitSwiper() {
  sulitSwiper = new Swiper('.mySwiperSulit', {
    slidesPerView: 'auto',
    spaceBetween: 20,
    centeredSlides: false,
    loop: false,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      320: { slidesPerView: 1.1, spaceBetween: 10 },
      480: { slidesPerView: 1.3, spaceBetween: 15 },
      640: { slidesPerView: 1.8, spaceBetween: 15 },
      768: { slidesPerView: 2.2, spaceBetween: 20 },
      1024: { slidesPerView: 2.8, spaceBetween: 20 },
      1200: { slidesPerView: 3.5, spaceBetween: 25 }
    }
  });
}

// ================= CREATE FOOD SLIDE (GENERIC) =================
async function createFoodSlide(food, restaurantDetails, distance, container, type) {
  // Get product rating using the utility function
  const ratingData = getProductRating(food);
  const rating = ratingData.average;
  const reviewsCount = ratingData.count;
  
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
  
  // Generate single yellow star (ONE STAR ONLY)
  const productStarsHTML = generateSingleStarHTML();
  
  // Format distance
  let distanceText = '';
  if (distance !== null && distance !== undefined) {
    if (distance < 1) {
      distanceText = `${(distance * 1000).toFixed(0)}m`;
    } else {
      distanceText = `${distance.toFixed(1)}km`;
    }
  }
  
  const slide = document.createElement("div");
  slide.className = `swiper-slide swiper-${type}-slide`;
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${food.id}`;
  card.className = "food-card";
  card.setAttribute('data-product', food.id);
  card.style.animation = 'fadeIn 0.5s ease forwards';

  // FIXED: Removed cart controls and improved layout
  card.innerHTML = `
    <div class="food-image-container">
      <img src="${food.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           loading="lazy" alt="${food.name || 'Food item'}"
           onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      ${distanceText ? `<div class="distance-badge">${distanceText} away</div>` : ''}
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
          ₱${price.toFixed(2)}
          ${originalPrice ? `<span class="original-price">₱${originalPrice.toFixed(2)}</span>` : ''}
        </div>
      </div>
    </div>
  `;

  slide.appendChild(card);
  container.appendChild(slide);
}

// ================= CREATE COMPACT NEARBY FOOD SLIDE =================
async function createCompactNearbySlide(food, restaurantDetails, distance, container) {
  // Get product rating
  const ratingData = getProductRating(food);
  const rating = ratingData.average;
  const reviewsCount = ratingData.count;
  
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
  
  // Format distance
  let distanceText = '';
  if (distance !== null && distance !== undefined) {
    if (distance < 1) {
      distanceText = `${(distance * 1000).toFixed(0)}m`;
    } else {
      distanceText = `${distance.toFixed(1)}km`;
    }
  }
  
  // Truncate description to ~5 words
  let description = food.description || 'Delicious food item';
  const words = description.split(' ');
  if (words.length > 5) {
    description = words.slice(0, 5).join(' ') + '...';
  }
  
  // Generate single star
  const productStarsHTML = generateSingleStarHTML();
  
  const slide = document.createElement("div");
  slide.className = "swiper-slide swiper-nearby-slide";
  
  const card = document.createElement("a");
  card.href = `foods/product.php?id=${food.id}`;
  card.className = "nearby-food-compact";
  card.setAttribute('data-product', food.id);
  card.style.animation = 'fadeIn 0.5s ease forwards';

  card.innerHTML = `
    <div class="nearby-food-image">
      <img src="${food.photo || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'}" 
           loading="lazy" alt="${food.name || 'Food item'}"
           onerror="this.src='https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'">
      ${distanceText ? `<div class="nearby-distance-badge">${distanceText}</div>` : ''}
      <div class="nearby-restaurant-avatar">
        <img src="${restaurantDetails.logo}" 
             alt="${restaurantDetails.name}"
             onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80'">
      </div>
    </div>
    
    <div class="nearby-food-content">
      <div class="nearby-food-header">
        <div class="nearby-food-name">${food.name || 'Unnamed Item'}</div>
        <div class="nearby-food-restaurant">
          <i class="fas fa-store"></i>
          <span>${restaurantDetails.name}</span>
        </div>
      </div>
      
      <div class="nearby-food-description truncate-description">${description}</div>
      
      <div class="nearby-food-rating">
        <div class="rating-stars">${productStarsHTML}</div>
        <span class="rating-value">${rating}</span>
        <span class="rating-count">(${reviewsCount})</span>
      </div>
      
      <div class="nearby-food-price-section">
        <div class="nearby-food-price">
          ₱${price.toFixed(2)}
          ${originalPrice ? `<span class="original-price">₱${originalPrice.toFixed(2)}</span>` : ''}
        </div>
      </div>
    </div>
  `;

  slide.appendChild(card);
  container.appendChild(slide);
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
  
  // If no reviews, return 0
  if (reviewsCount === 0) {
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

// ================= GENERATE SINGLE YELLOW STAR HTML =================
function generateSingleStarHTML() {
  // Just return ONE yellow star
  return '<i class="fas fa-star"></i>';
}

// ================= SHOW NO RESULTS =================
function showNoRandomResults(message = "No random picks available") {
  const randomSwiperWrapper = document.getElementById('randomSwiperWrapper');
  if (!randomSwiperWrapper) return;
  
  <?php if ($isLoggedIn): ?>
  randomSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-utensils"></i>
        <h3>${message}</h3>
        <p>Try refreshing or check back later</p>
      </div>
    </div>
  `;
  <?php else: ?>
  randomSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="auth-prompt">
        <div class="auth-prompt-content">
          <i class="fas fa-dice fa-3x"></i>
          <h3>Register to see personalized picks</h3>
          <p>Login or register to get personalized food recommendations</p>
          <div class="auth-prompt-buttons">
            <a href="login.php" class="auth-prompt-btn login">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="register.php" class="auth-prompt-btn register">
              <i class="fas fa-user-plus"></i> Register
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
  <?php endif; ?>
  
  if (randomSwiper) {
    randomSwiper.update();
  }
}

// ================= SHOW NO NEARBY RESULTS =================
function showNoNearbyResults(message = null) {
  const nearbySwiperWrapper = document.getElementById('nearbySwiperWrapper');
  if (!nearbySwiperWrapper) return;
  
  <?php if ($isLoggedIn): ?>
  let specificMessage = message || "No nearby foods found";
  let specificDetail = "Try changing your location or check back later";
  
  if (!userLocation.latitude) {
    specificMessage = "Set your location to see nearby foods";
    specificDetail = "Click 'Set Location' or add an address in your profile";
  }
  
  nearbySwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%; padding: 40px 20px; text-align: center;">
        <i class="fas fa-map-marker-alt fa-3x" style="color: var(--gray-light); margin-bottom: 20px;"></i>
        <h3 style="color: var(--secondary); margin-bottom: 10px; font-size: 1.3rem;">${specificMessage}</h3>
        <p style="color: var(--gray); margin-bottom: 25px; font-size: 0.95rem; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5;">${specificDetail}</p>
        ${!userLocation.latitude ? `
          <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; justify-content: center; align-items: center;">
            <button class="set-location-btn" onclick="showLocationModal()">
              <i class="fas fa-crosshairs"></i> Set Current Location
            </button>
            <button class="set-location-btn" onclick="goToProfileLocationSettings()" style="background: var(--success);">
              <i class="fas fa-user"></i> Use Profile Address
            </button>
          </div>
        ` : ''}
      </div>
    </div>
  `;
  <?php else: ?>
  nearbySwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="auth-prompt" style="margin: 20px;">
        <div class="auth-prompt-content">
          <i class="fas fa-map-marker-alt fa-3x"></i>
          <h3>Discover restaurants near you</h3>
          <p>Login or register to set your location and see nearby restaurants</p>
          <div class="auth-prompt-buttons">
            <a href="login.php" class="auth-prompt-btn login">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="register.php" class="auth-prompt-btn register">
              <i class="fas fa-user-plus"></i> Register
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
  <?php endif; ?>
  

  if (nearbySwiper) {
    nearbySwiper.update();
  }
}

function showNoSulitResults(message = null) {
  const sulitSwiperWrapper = document.getElementById('sulitSwiperWrapper');
  if (!sulitSwiperWrapper) return;
  
  <?php if ($isLoggedIn): ?>
  let specificMessage = message || "No affordable meals found";
  let specificDetail = "Check back later for budget-friendly options";
  
  sulitSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="no-results" style="width: 100%;">
        <i class="fas fa-tag"></i>
        <h3>${specificMessage}</h3>
        <p>${specificDetail}</p>
      </div>
    </div>
  `;
  <?php else: ?>
  sulitSwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div class="auth-prompt">
        <div class="auth-prompt-content">
          <i class="fas fa-tag fa-3x"></i>
          <h3>Find affordable meals</h3>
          <p>Register or login to see all budget-friendly options</p>
          <div class="auth-prompt-buttons">
            <a href="login.php" class="auth-prompt-btn login">
              <i class="fas fa-sign-in-alt"></i> Login
            </a>
            <a href="register.php" class="auth-prompt-btn register">
              <i class="fas fa-user-plus"></i> Register
            </a>
          </div>
        </div>
      </div>
    </div>
  `;
  <?php endif; ?>
  
  if (sulitSwiper) {
    sulitSwiper.update();
  }
}

// ================= SETUP REFRESH BUTTONS =================
function setupRefreshButtons() {
  // Nearby refresh button
  const refreshNearbyBtn = document.getElementById('refreshNearby');
  if (refreshNearbyBtn) {
    refreshNearbyBtn.addEventListener('click', async function() {
      const originalHTML = refreshNearbyBtn.innerHTML;
      refreshNearbyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshNearbyBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadNearbyFoods();
      
      refreshNearbyBtn.innerHTML = originalHTML;
      refreshNearbyBtn.disabled = false;
      
      showNotification('Nearby foods refreshed!', 'success');
    });
  }
  
  // Random refresh button
  const refreshRandomBtn = document.getElementById('refreshRandom');
  if (refreshRandomBtn) {
    refreshRandomBtn.addEventListener('click', async function() {
      const originalHTML = refreshRandomBtn.innerHTML;
      refreshRandomBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshRandomBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadRandomFoods();
      
      refreshRandomBtn.innerHTML = originalHTML;
      refreshRandomBtn.disabled = false;
      
      showNotification('Random picks refreshed!', 'success');
    });
  }
  
  // Recommended refresh button
  const refreshRecommendedBtn = document.getElementById('refreshRecommended');
  if (refreshRecommendedBtn) {
    refreshRecommendedBtn.addEventListener('click', async function() {
      const originalHTML = refreshRecommendedBtn.innerHTML;
      refreshRecommendedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshRecommendedBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadRecommendedFoods();
      
      refreshRecommendedBtn.innerHTML = originalHTML;
      refreshRecommendedBtn.disabled = false;
      
      showNotification('Recommendations refreshed!', 'success');
    });
  }
  
  // Sulit meals refresh button (if you have one, otherwise add it to your HTML)
  const refreshSulitBtn = document.getElementById('refreshSulit');
  if (refreshSulitBtn) {
    refreshSulitBtn.addEventListener('click', async function() {
      const originalHTML = refreshSulitBtn.innerHTML;
      refreshSulitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshSulitBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadSulitMeals();
      
      refreshSulitBtn.innerHTML = originalHTML;
      refreshSulitBtn.disabled = false;
      
      showNotification('Sulit meals refreshed!', 'success');
    });
  }
  
  // Nearby restaurants refresh button
  const refreshNearbyRestaurantsBtn = document.getElementById('refreshNearbyRestaurants');
  if (refreshNearbyRestaurantsBtn) {
    refreshNearbyRestaurantsBtn.addEventListener('click', async function() {
      const originalHTML = refreshNearbyRestaurantsBtn.innerHTML;
      refreshNearbyRestaurantsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshNearbyRestaurantsBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadNearbyRestaurants();
      
      refreshNearbyRestaurantsBtn.innerHTML = originalHTML;
      refreshNearbyRestaurantsBtn.disabled = false;
      
      showNotification('Nearby restaurants refreshed!', 'success');
    });
  }
  
  // Top restaurants refresh button
  const refreshTopRestaurantsBtn = document.getElementById('refreshTopRestaurants');
  if (refreshTopRestaurantsBtn) {
    refreshTopRestaurantsBtn.addEventListener('click', async function() {
      const originalHTML = refreshTopRestaurantsBtn.innerHTML;
      refreshTopRestaurantsBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
      refreshTopRestaurantsBtn.disabled = true;
      
      // Clear restaurant cache
      restaurantCache = {};
      
      await loadTopRestaurants();
      
      refreshTopRestaurantsBtn.innerHTML = originalHTML;
      refreshTopRestaurantsBtn.disabled = false;
      
      showNotification('Top restaurants refreshed!', 'success');
    });
  }
  
  // Add event listener for location changes to refresh restaurant sections
  document.addEventListener('locationUpdated', function() {
    console.log('Location updated, refreshing restaurant sections...');
    
    // Refresh nearby restaurants when location changes
    if (userLocation.latitude && userLocation.longitude) {
      loadNearbyRestaurants();
      loadNearbyFoods(); // Also refresh nearby foods
    }
  });
  
}


// ================= UPDATE INITIALIZATION =================
document.addEventListener('DOMContentLoaded', async function() {
  console.log('LalaGO Initializing...');
  console.log('User location from session:', userLocation);
  
  // Setup refresh buttons (including restaurant sections)
  setupRefreshButtons();
  
  // Check if user needs to set location
  await checkLocationStatus();
  
  // Load advertisements first
  await loadAdvertisements();
  
  // Load food categories from database
  await loadFoodCategoriesFromDatabase();
  
  // Load all other sections (including new restaurant sections)
  await loadAllSections();
  
  console.log('LalaGO initialized');
});

// ================= LOAD NEARBY FOODS =================
async function loadNearbyFoods() {
  console.log('Loading nearby foods...');
  const nearbySwiperWrapper = document.getElementById('nearbySwiperWrapper');
  
  if (!nearbySwiperWrapper) {
    console.error('Nearby swiper wrapper not found!');
    return;
  }
  
  // Show loading state with compact style
  nearbySwiperWrapper.innerHTML = `
    <div class="swiper-slide" style="width: 100%;">
      <div style="display: flex; justify-content: center; align-items: center; padding: 40px 20px;">
        <div style="text-align: center;">
          <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary); margin-bottom: 15px;"></i>
          <h3 style="color: var(--secondary); margin-bottom: 8px;">Finding nearby foods...</h3>
          <p style="color: var(--gray);">Searching within ${MAX_DISTANCE_KM}km of your location</p>
        </div>
      </div>
    </div>
  `;
  
  // Check if location is available
  if (!userLocation.latitude || !userLocation.longitude) {
    showNoNearbyResults();
    return;
  }
  
  try {
    // Get all published products
    const productsSnapshot = await db.collection("vendor_products")
      .where("publish", "==", true)
      .limit(100)
      .get();
    
    console.log(`Found ${productsSnapshot.size} total products`);
    
    if (productsSnapshot.empty) {
      showNoNearbyResults();
      return;
    }
    
    // Convert to array
    const allProducts = [];
    productsSnapshot.forEach(doc => {
      allProducts.push({
        id: doc.id,
        ...doc.data()
      });
    });
    
    // Get restaurant locations for each product
    const productsWithDistance = [];
    
    for (const product of allProducts) {
      try {
        const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
        
        // Check if restaurant has location
        if (restaurantDetails.latitude && restaurantDetails.longitude) {
          const distance = calculateDistance(
            userLocation.latitude,
            userLocation.longitude,
            restaurantDetails.latitude,
            restaurantDetails.longitude
          );
          
          if (distance <= MAX_DISTANCE_KM) {
            productsWithDistance.push({
              ...product,
              distance: distance,
              restaurantDetails: restaurantDetails
            });
          }
        } else {
          console.log(`Restaurant ${restaurantDetails.name} has no location data`);
        }
      } catch (error) {
        console.error("Error processing product:", error);
      }
    }
    
    // Sort by distance
    productsWithDistance.sort((a, b) => a.distance - b.distance);
    
    // Take nearest products
    nearbyProducts = productsWithDistance.slice(0, Math.min(NEARBY_LIMIT, productsWithDistance.length));
    
    console.log(`Found ${productsWithDistance.length} products within ${MAX_DISTANCE_KM}km`);
    
    // Clear container
    nearbySwiperWrapper.innerHTML = '';
    
    if (nearbyProducts.length === 0) {
      showNoNearbyResults("No restaurants found near your location");
      return;
    }
    
    // Display nearby products in compact layout
    for (const product of nearbyProducts) {
      let restaurantDetails;
      
      if (product.restaurantDetails) {
        restaurantDetails = product.restaurantDetails;
      } else {
        restaurantDetails = await fetchRestaurantDetails(product.vendorID);
      }
      
      // Use the new compact slide function for nearby foods
      await createCompactNearbySlide(product, restaurantDetails, product.distance, nearbySwiperWrapper);
    }
    
    // Initialize or update Swiper
    if (!nearbySwiper) {
      initializeNearbySwiper();
    } else {
      nearbySwiper.update();
    }
    
    console.log(`Displayed ${nearbyProducts.length} nearby products in compact layout`);
    
  } catch (error) {
    console.error("Error loading nearby foods:", error);
    showNoNearbyResults("Error loading nearby foods");
  }
}
// ================= LOAD CATEGORIES FROM FIREBASE =================
async function loadCategories() {
  try {
    const categoriesSnapshot = await db.collection("vendor_categories")
      .where("publish", "==", true)
      .get();
    
    const categories = [];
    
    if (!categoriesSnapshot.empty) {
      const raw = [];
      categoriesSnapshot.forEach(doc => {
        const d = doc.data();
        const name = d.name || d.title || "Unnamed Category";
        raw.push({ id: doc.id, name, icon: getCategoryIcon(name), count: 0, active: false });
      });
      raw.sort((a, b) => a.name.localeCompare(b.name));
      categories.push(...raw);
    } else {
      // Use sample categories if Firebase has none
      const sampleCategories = [
        { name: "Burgers", icon: "fas fa-hamburger" },
        { name: "Pizza", icon: "fas fa-pizza-slice" },
        { name: "Sushi", icon: "fas fa-fish" },
        { name: "Pasta", icon: "fas fa-utensil-spoon" },
        { name: "Salads", icon: "fas fa-leaf" },
        { name: "Desserts", icon: "fas fa-ice-cream" },
        { name: "Drinks", icon: "fas fa-cocktail" },
        { name: "Asian", icon: "fas fa-utensils" },
        { name: "Mexican", icon: "fas fa-pepper-hot" }
      ];
      
      sampleCategories.forEach((cat, index) => {
        categories.push({
          id: `sample-${index}`,
          name: cat.name,
          icon: cat.icon,
          count: Math.floor(Math.random() * 20),
          active: false
        });
      });
    }
    
    renderCategories(categories);
  } catch (error) {
    console.error("Error loading categories:", error);
    showNotification("Error loading categories", "warning");
  }
}

// ================= FOOD CATEGORY ICONS FROM DATABASE =================
let foodCategoriesFromDB = [];
let categoryIconsCache = {};
let currentFilterCategory = null; // Add this variable

// Function to load categories from Firebase database
async function loadFoodCategoriesFromDatabase() {
    try {
        console.log('Loading food categories from database...');
        
        const categoriesSnapshot = await db.collection("vendor_categories")
            .where("publish", "==", true)
            .get();
        
        if (categoriesSnapshot.empty) {
            console.log('No categories found in database, using default icons');
            await loadDefaultCategories();
            return;
        }
        
        // Clear existing categories
        foodCategoriesFromDB = [];
        categoryIconsCache = {};
        
        categoriesSnapshot.forEach(doc => {
            const d = doc.data();
            const name = d.name || d.title || "Unnamed Category";
            const category = {
                id: doc.id,
                name,
                photo: d.photo || null,
                description: d.description || "",
                publish: d.publish || false,
                fallbackIcon: getCategoryIconByCategoryName(name)
            };
            
            categoryIconsCache[category.name.toLowerCase()] =
                category.photo || category.fallbackIcon;
            foodCategoriesFromDB.push(category);
        });
        foodCategoriesFromDB.sort((a, b) => a.name.localeCompare(b.name));
        
        console.log(`Loaded ${foodCategoriesFromDB.length} categories from database`);
        
        // Now render the category icons
        renderFoodCategoryIcons();
        
    } catch (error) {
        console.error("Error loading categories from database:", error);
        // Fallback to default categories
        await loadDefaultCategories();
    }
}

// Function to get fallback icon based on category name
function getCategoryIconByCategoryName(categoryName) {
    if (!categoryName) return 'fas fa-utensils';
    
    const category = categoryName.toLowerCase();
    
    // Map common category names to Font Awesome icons
    const iconMap = {
        'burger': 'fas fa-hamburger',
        'pizza': 'fas fa-pizza-slice',
        'sushi': 'fas fa-fish',
        'pasta': 'fas fa-utensil-spoon',
        'salad': 'fas fa-leaf',
        'dessert': 'fas fa-ice-cream',
        'drink': 'fas fa-cocktail',
        'beverage': 'fas fa-cocktail',
        'breakfast': 'fas fa-egg',
        'asian': 'fas fa-utensils',
        'mexican': 'fas fa-pepper-hot',
        'indian': 'fas fa-mortar-pestle',
        'vegetable': 'fas fa-carrot',
        'vegetarian': 'fas fa-leaf',
        'vegan': 'fas fa-seedling',
        'seafood': 'fas fa-fish',
        'chicken': 'fas fa-drumstick-bite',
        'beef': 'fas fa-cow',
        'soup': 'fas fa-utensil-spoon',
        'sandwich': 'fas fa-bread-slice',
        'coffee': 'fas fa-coffee',
        'tea': 'fas fa-mug-hot',
        'smoothie': 'fas fa-blender',
        'fruit': 'fas fa-apple-alt',
        'bread': 'fas fa-bread-slice',
        'cake': 'fas fa-birthday-cake',
        'bakery': 'fas fa-birthday-cake',
        'ice cream': 'fas fa-ice-cream',
        'fast food': 'fas fa-hamburger',
        'chinese': 'fas fa-utensils',
        'japanese': 'fas fa-fish',
        'korean': 'fas fa-utensils',
        'filipino': 'fas fa-utensils',
        'thai': 'fas fa-utensils',
        'vietnamese': 'fas fa-utensils',
        'noodles': 'fas fa-utensil-spoon',
        'rice': 'fas fa-utensil-spoon',
        'appetizer': 'fas fa-utensils',
        'main course': 'fas fa-utensils',
        'side dish': 'fas fa-utensils'
    };
    
    // Check for exact matches first
    for (const [key, icon] of Object.entries(iconMap)) {
        if (category === key) {
            return icon;
        }
    }
    
    // Check for partial matches
    for (const [key, icon] of Object.entries(iconMap)) {
        if (category.includes(key)) {
            return icon;
        }
    }
    
    // Default icon
    return 'fas fa-utensils';
}

// Function to load default categories if database is empty
async function loadDefaultCategories() {
    const defaultCategories = [
        { name: "Burger", fallbackIcon: "fas fa-hamburger" },
        { name: "Pizza", fallbackIcon: "fas fa-pizza-slice" },
        { name: "Sushi", fallbackIcon: "fas fa-fish" },
        { name: "Pasta", fallbackIcon: "fas fa-utensil-spoon" },
        { name: "Salad", fallbackIcon: "fas fa-leaf" },
        { name: "Dessert", fallbackIcon: "fas fa-ice-cream" },
        { name: "Drink", fallbackIcon: "fas fa-cocktail" },
        { name: "Breakfast", fallbackIcon: "fas fa-egg" },
        { name: "Asian", fallbackIcon: "fas fa-utensils" },
        { name: "Mexican", fallbackIcon: "fas fa-pepper-hot" },
        { name: "Indian", fallbackIcon: "fas fa-mortar-pestle" },
        { name: "Vegetarian", fallbackIcon: "fas fa-leaf" },
        { name: "Seafood", fallbackIcon: "fas fa-shrimp" },
        { name: "Chicken", fallbackIcon: "fas fa-drumstick-bite" },
        { name: "Coffee", fallbackIcon: "fas fa-coffee" },
        { name: "Tea", fallbackIcon: "fas fa-mug-hot" },
        { name: "Fruit", fallbackIcon: "fas fa-apple-alt" },
        { name: "Bread", fallbackIcon: "fas fa-bread-slice" },
        { name: "Cake", fallbackIcon: "fas fa-birthday-cake" },
        { name: "Ice Cream", fallbackIcon: "fas fa-ice-cream" }
    ];
    
    foodCategoriesFromDB = defaultCategories.map(cat => ({
        ...cat,
        photo: null,
        id: `default-${cat.name.toLowerCase().replace(' ', '-')}`,
        description: "",
        publish: true
    }));
    
    // Cache fallback icons
    defaultCategories.forEach(cat => {
        categoryIconsCache[cat.name.toLowerCase()] = cat.fallbackIcon;
    });
    
    renderFoodCategoryIcons();
}

// Function to get icon for a category
function getIconForCategory(categoryName) {
    if (!categoryName) return 'fas fa-utensils';
    
    const categoryLower = categoryName.toLowerCase();
    
    // Check cache first
    if (categoryIconsCache[categoryLower]) {
        return categoryIconsCache[categoryLower];
    }
    
    // Try to find in loaded categories
    const foundCategory = foodCategoriesFromDB.find(cat => 
        cat.name.toLowerCase() === categoryLower
    );
    
    if (foundCategory) {
        // Use photo if available, otherwise use fallback icon
        const icon = foundCategory.photo || foundCategory.fallbackIcon || 'fas fa-utensils';
        categoryIconsCache[categoryLower] = icon;
        return icon;
    }
    
    // If not found, get fallback icon
    const fallbackIcon = getCategoryIconByCategoryName(categoryName);
    categoryIconsCache[categoryLower] = fallbackIcon;
    return fallbackIcon;
}

// Updated function to render food category icons
function renderFoodCategoryIcons() {
    const nearbySection = document.querySelector('.nearby-section-header');
    if (!nearbySection) {
        console.error('Nearby section header not found!');
        return;
    }
    
    // Create or get the category icons container
    let iconsContainer = document.getElementById('foodCategoryIcons');
    if (!iconsContainer) {
        iconsContainer = document.createElement('div');
        iconsContainer.className = 'food-category-icons';
        iconsContainer.id = 'foodCategoryIcons';
        
        // Insert after the nearby section header (before the swiper)
        const nearbySwiperContainer = document.querySelector('.swiper-nearby-container');
        if (nearbySwiperContainer) {
            nearbySwiperContainer.insertAdjacentElement('beforebegin', iconsContainer);
        } else {
            // Fallback: insert after nearby section
            nearbySection.insertAdjacentElement('afterend', iconsContainer);
        }
    }
    
    // Create icon items
    let iconsHTML = '';
    
    // Add clear filter button first
    iconsHTML += `
        <a href="javascript:void(0)" class="category-icon-item" onclick="clearFoodCategoryFilter()">
            <div class="category-icon-circle" style="background: var(--gray);">
                <i class="fas fa-times"></i>
            </div>
            <span class="category-icon-name">Clear</span>
        </a>
    `;
    
    // Add category icons
    foodCategoriesFromDB.forEach(category => {
        const displayName = category.name;
        const categoryKey = category.name.toLowerCase();
        
        // Check if it's a Font Awesome icon or an image URL
        const isFontAwesomeIcon = !category.photo || category.photo.startsWith('fas fa-');
        const iconContent = isFontAwesomeIcon 
            ? `<i class="${getIconForCategory(displayName)}"></i>`
            : `<img src="${category.photo}" alt="${displayName}" 
                 onerror="this.onerror=null; this.parentElement.innerHTML='<i class=\'fas fa-utensils\'></i>';" />`;
        
        iconsHTML += `
            <a href="javascript:void(0)" class="category-icon-item" 
               onclick="filterAllFoodsByCategory('${categoryKey}')" 
               data-category="${categoryKey}"
               title="${category.description || ''}">
                <div class="category-icon-circle" style="${!isFontAwesomeIcon ? 'background: var(--white); padding: 5px;' : ''}">
                    ${iconContent}
                </div>
                <span class="category-icon-name">${displayName}</span>
            </a>
        `;
    });
    
    iconsContainer.innerHTML = iconsHTML;
}

// Helper function to show loading state for all sections
function showLoadingForAllSections(categoryName) {
    // Nearby section
    const nearbySwiperWrapper = document.getElementById('nearbySwiperWrapper');
    if (nearbySwiperWrapper) {
        nearbySwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <h3>Loading ${categoryName} foods...</h3>
                    <p>Filtering nearby foods by category</p>
                </div>
            </div>
        `;
    }
    
    // Random section
    const randomSwiperWrapper = document.getElementById('randomSwiperWrapper');
    if (randomSwiperWrapper) {
        randomSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <h3>Loading ${categoryName} foods...</h3>
                    <p>Filtering random picks by category</p>
                </div>
            </div>
        `;
    }
    
    // Recommended section
    const recommendedSwiperWrapper = document.getElementById('recommendedSwiperWrapper');
    if (recommendedSwiperWrapper) {
        recommendedSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <h3>Loading ${categoryName} foods...</h3>
                    <p>Filtering recommendations by category</p>
                </div>
            </div>
        `;
    }
    
    // Sulit section
    const sulitSwiperWrapper = document.getElementById('sulitSwiperWrapper');
    if (sulitSwiperWrapper) {
        sulitSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <h3>Loading ${categoryName} foods...</h3>
                    <p>Filtering affordable meals by category</p>
                </div>
            </div>
        `;
    }
}

// Helper function to show no results for all sections
function showNoResultsInAllSections(message) {
    // Nearby section
    const nearbySwiperWrapper = document.getElementById('nearbySwiperWrapper');
    if (nearbySwiperWrapper) {
        nearbySwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-search"></i>
                    <h3>${message}</h3>
                    <p>Try a different category or clear the filter</p>
                </div>
            </div>
        `;
    }
    
    // Random section
    const randomSwiperWrapper = document.getElementById('randomSwiperWrapper');
    if (randomSwiperWrapper) {
        randomSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-search"></i>
                    <h3>${message}</h3>
                    <p>Try a different category or clear the filter</p>
                </div>
            </div>
        `;
    }
    
    // Recommended section
    const recommendedSwiperWrapper = document.getElementById('recommendedSwiperWrapper');
    if (recommendedSwiperWrapper) {
        recommendedSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-search"></i>
                    <h3>${message}</h3>
                    <p>Try a different category or clear the filter</p>
                </div>
            </div>
        `;
    }
    
    // Sulit section
    const sulitSwiperWrapper = document.getElementById('sulitSwiperWrapper');
    if (sulitSwiperWrapper) {
        sulitSwiperWrapper.innerHTML = `
            <div class="swiper-slide" style="width: 100%;">
                <div class="no-results" style="width: 100%;">
                    <i class="fas fa-search"></i>
                    <h3>${message}</h3>
                    <p>Try a different category or clear the filter</p>
                </div>
            </div>
        `;
    }
}

// Function to process filtered products for all sections
async function processFilteredProductsForAllSections(filteredProducts, categoryName) {
    // Process for nearby section
    const nearbySwiperWrapper = document.getElementById('nearbySwiperWrapper');
    if (nearbySwiperWrapper) {
        nearbySwiperWrapper.innerHTML = '';
        
        // Filter nearby products by location
        const nearbyFilteredProducts = [];
        for (const product of filteredProducts) {
            try {
                const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
                
                // Check if restaurant has location and is nearby
                if (restaurantDetails.latitude && restaurantDetails.longitude && userLocation.latitude && userLocation.longitude) {
                    const distance = calculateDistance(
                        userLocation.latitude,
                        userLocation.longitude,
                        restaurantDetails.latitude,
                        restaurantDetails.longitude
                    );
                    
                    if (distance <= MAX_DISTANCE_KM) {
                        nearbyFilteredProducts.push({...product, distance, restaurantDetails});
                    }
                }
            } catch (error) {
                console.error("Error processing product for nearby:", error);
            }
        }
        
        // Sort by distance
        nearbyFilteredProducts.sort((a, b) => (a.distance || 999) - (b.distance || 999));
        
        // Take nearest products
        const nearbyDisplayProducts = nearbyFilteredProducts.slice(0, NEARBY_LIMIT);
        
        if (nearbyDisplayProducts.length > 0) {
            for (const product of nearbyDisplayProducts) {
                await createCompactNearbySlide(product, product.restaurantDetails, product.distance, nearbySwiperWrapper);
            }
        } else {
            nearbySwiperWrapper.innerHTML = `
                <div class="swiper-slide" style="width: 100%;">
                    <div class="no-results" style="width: 100%;">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>No ${categoryName} foods nearby</h3>
                        <p>Try a different location or category</p>
                    </div>
                </div>
            `;
        }
        
        if (nearbySwiper) nearbySwiper.update();
    }
    
    // Process for random section
    const randomSwiperWrapper = document.getElementById('randomSwiperWrapper');
    if (randomSwiperWrapper) {
        randomSwiperWrapper.innerHTML = '';
        
        // Shuffle filtered products
        const shuffled = [...filteredProducts].sort(() => 0.5 - Math.random());
        const randomDisplayProducts = shuffled.slice(0, RANDOM_LIMIT);
        
        if (randomDisplayProducts.length > 0) {
            for (const product of randomDisplayProducts) {
                const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
                await createFoodSlide(product, restaurantDetails, null, randomSwiperWrapper, 'random');
            }
        } else {
            randomSwiperWrapper.innerHTML = `
                <div class="swiper-slide" style="width: 100%;">
                    <div class="no-results" style="width: 100%;">
                        <i class="fas fa-utensils"></i>
                        <h3>No ${categoryName} foods found</h3>
                        <p>Try a different category</p>
                    </div>
                </div>
            `;
        }
        
        if (randomSwiper) randomSwiper.update();
    }
    
    // Process for recommended section
    const recommendedSwiperWrapper = document.getElementById('recommendedSwiperWrapper');
    if (recommendedSwiperWrapper) {
        recommendedSwiperWrapper.innerHTML = '';
        
        // Score and sort filtered products
        const scoredProducts = await scoreProductsForRecommendation(filteredProducts, getUserPreferences());
        const recommendedDisplayProducts = scoredProducts.slice(0, RECOMMENDED_LIMIT);
        
        if (recommendedDisplayProducts.length > 0) {
            for (const product of recommendedDisplayProducts) {
                const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
                await createRecommendedSlide(product, restaurantDetails, recommendedSwiperWrapper);
            }
        } else {
            recommendedSwiperWrapper.innerHTML = `
                <div class="swiper-slide" style="width: 100%;">
                    <div class="no-results" style="width: 100%;">
                        <i class="fas fa-star"></i>
                        <h3>No ${categoryName} recommendations</h3>
                        <p>Try exploring more foods</p>
                    </div>
                </div>
            `;
        }
        
        if (recommendedSwiper) recommendedSwiper.update();
    }
    
    // Process for sulit section
    const sulitSwiperWrapper = document.getElementById('sulitSwiperWrapper');
    if (sulitSwiperWrapper) {
        sulitSwiperWrapper.innerHTML = '';
        
        // Filter by price
        const sulitFilteredProducts = filteredProducts.filter(product => {
            try {
                const disPrice = parseFloat(product.disPrice);
                const regPrice = parseFloat(product.price);
                const price = (!isNaN(disPrice) && disPrice > 0 && disPrice < regPrice) ? disPrice : (!isNaN(regPrice) ? regPrice : 0);
                return price > 0 && price <= SULIT_PRICE_LIMIT;
            } catch (e) {
                return false;
            }
        });
        
        // Shuffle and take limit
        const shuffledSulit = [...sulitFilteredProducts].sort(() => 0.5 - Math.random());
        const sulitDisplayProducts = shuffledSulit.slice(0, SULIT_LIMIT);
        
        if (sulitDisplayProducts.length > 0) {
            for (const product of sulitDisplayProducts) {
                const restaurantDetails = await fetchRestaurantDetails(product.vendorID);
                await createFoodSlide(product, restaurantDetails, null, sulitSwiperWrapper, 'sulit');
            }
        } else {
            sulitSwiperWrapper.innerHTML = `
                <div class="swiper-slide" style="width: 100%;">
                    <div class="no-results" style="width: 100%;">
                        <i class="fas fa-tag"></i>
                        <h3>No affordable ${categoryName} meals</h3>
                        <p>Try a different category for budget options</p>
                    </div>
                </div>
            `;
        }
        
        if (sulitSwiper) sulitSwiper.update();
    }
}

// Updated function to filter foods by category
async function filterAllFoodsByCategory(categoryKey) {
    console.log(`Filtering all sections by category: ${categoryKey}`);
    
    // Find the category in our database
    const category = foodCategoriesFromDB.find(cat => cat.name.toLowerCase() === categoryKey);
    if (!category) {
        console.error(`Category not found: ${categoryKey}`);
        showNotification('Category not found', 'error');
        return;
    }
    
    // Update active state
    document.querySelectorAll('.category-icon-item').forEach(item => {
        item.classList.remove('active');
    });
    
    const activeItem = document.querySelector(`.category-icon-item[data-category="${categoryKey}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
    
    // Scroll to the active item
    if (activeItem) {
        activeItem.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
    
    // Store current filter
    currentFilterCategory = categoryKey;
    
    // Show loading state for all sections
    showLoadingForAllSections(category.name);
    
    try {
        // Get all published products
        const productsSnapshot = await db.collection("vendor_products")
            .where("publish", "==", true)
            .limit(200)
            .get();
        
        if (productsSnapshot.empty) {
            showNoResultsInAllSections(`No ${category.name} foods found`);
            return;
        }
        
        // Filter products by category
        const filteredProducts = [];
        productsSnapshot.forEach(doc => {
            const product = {
                id: doc.id,
                ...doc.data()
            };
            
            // Get product category from multiple possible fields
            const productCategory = (product.category || '').toLowerCase();
            const productCategoryName = (product.categoryName || '').toLowerCase();
            const productVendorCategory = (product.vendorCategory || '').toLowerCase();
            const productName = (product.name || '').toLowerCase();
            const productDescription = (product.description || '').toLowerCase();
            const productTags = (product.tags || []).map(tag => tag.toLowerCase());
            
            // Check if product matches the category
            if (productCategory.includes(categoryKey) || 
                productCategoryName.includes(categoryKey) ||
                productVendorCategory.includes(categoryKey) ||
                productName.includes(categoryKey) || 
                productDescription.includes(categoryKey) ||
                productTags.includes(categoryKey)) {
                filteredProducts.push(product);
            }
        });
        
        console.log(`Found ${filteredProducts.length} products in ${category.name} category`);
        
        if (filteredProducts.length === 0) {
            showNoResultsInAllSections(`No ${category.name} foods found`);
            return;
        }
        
        // Process and display filtered products in each section
        await processFilteredProductsForAllSections(filteredProducts, category.name);
        
        showNotification(`Showing ${category.name} foods across all sections`, 'success');
        
    } catch (error) {
        console.error(`Error filtering ${category.name} foods:`, error);
        showNotification(`Error filtering ${category.name} foods`, 'error');
        clearFoodCategoryFilter();
    }
}

// Function to clear food category filter
function clearFoodCategoryFilter() {
    console.log('Clearing food category filter');
    
    // Remove active state from all category icons
    document.querySelectorAll('.category-icon-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Clear current filter
    currentFilterCategory = null;
    
    // Reload all sections
    loadAllSections();
    
    showNotification('Filter cleared, showing all foods', 'info');
}

// ================= INITIALIZE ON LOAD ================= 
document.addEventListener('DOMContentLoaded', async function() {
    console.log('LalaGO Initializing...');
    console.log('User location from session:', userLocation);
    
    // Check if user needs to set location
    await checkLocationStatus();
    
    // Load advertisements first
    await loadAdvertisements();
    
    // Load food categories from database (this will render them)
    await loadFoodCategoriesFromDatabase();
    
    console.log('LalaGO initialized');
});

// Add CSS for image icons
const categoryIconStyles = document.createElement('style');
categoryIconStyles.textContent = `
/* Additional styles for image icons */
.category-icon-circle img {
    width: 70%;
    height: 70%;
    object-fit: contain;
    border-radius: 50%;
}

.category-icon-item:hover .category-icon-circle img {
    transform: scale(1.1);
}

/* Responsive adjustments for image icons */
@media (max-width: 768px) {
    .category-icon-circle img {
        width: 60%;
        height: 60%;
    }
}

@media (max-width: 480px) {
    .category-icon-circle img {
        width: 50%;
        height: 50%;
    }
}
`;
document.head.appendChild(categoryIconStyles);
// ================= RENDER CATEGORIES =================
function renderCategories(categories) {
  const categoriesContainer = document.getElementById('categoriesContainer');
  if (!categoriesContainer) return;
  
  // Clear existing categories (except header)
  const existingCategories = categoriesContainer.querySelectorAll('.category-item');
  existingCategories.forEach(cat => cat.remove());
  
  // Create categories HTML
  const categoriesHTML = categories.map(category => `
    <div class="category-item ${category.active ? 'active' : ''}" 
         data-category="${category.name.toLowerCase()}"
         onclick="filterByCategory('${category.id}', '${category.name}')">
      <div class="category-icon">
        <i class="${category.icon}"></i>
      </div>
      <div class="category-content">
        <div class="category-name">${category.name}</div>
        <span class="category-count">${category.count} items</span>
      </div>
    </div>
  `).join('');

  // Insert categories after the header
  const categoryHeader = categoriesContainer.querySelector('.category-header');
  if (categoryHeader) {
    categoryHeader.insertAdjacentHTML('afterend', categoriesHTML);
  }
}

// ================= FILTER BY CATEGORY =================
async function filterByCategory(categoryId, categoryName) {
  showNotification(`Filtering by ${categoryName} is coming soon!`, 'info');
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
    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
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
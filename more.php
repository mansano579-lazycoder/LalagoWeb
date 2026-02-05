<?php include 'inc/firebase.php'; ?> 
<?php include 'assets/header.php'; ?>

<!DOCTYPE html>
<html>
<head>
  <title>All Foods - LalaGO</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/more.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<main class="container">

  <!-- Optional Carousel at top -->
  <div class="carousel-container">
    <div class="carousel-title">Featured Foods</div>
    <button class="carousel-btn prev">&#10094;</button>
    <button class="carousel-btn next">&#10095;</button>
    <div class="carousel-track" id="foodsCarousel"></div>
  </div>

  <!-- Page Title -->
  <h2 class="page-title">All Foods</h2>

  <!-- Foods Grid -->
  <div id="foodsGrid" class="foods-grid"></div>

</main>

<!-- Firebase -->
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-firestore-compat.js"></script>
<script src="js/firebase.js"></script>

<!-- Separate JS for All Foods -->
<script src="js/all_Foods.js"></script>

</body>
</html>

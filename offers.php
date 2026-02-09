<?php
session_start();
$user_name = $_SESSION['user_name'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>LalaGO | Earn With Us</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body{
    background:#f8f9fa;
}
.hero{
    background:linear-gradient(135deg,#ff6b00,#ff9800);
    color:#fff;
    padding:60px 20px;
    text-align:center;
}
.hero h1{
    font-weight:bold;
}
.section{
    padding:50px 0;
}
.card-custom{
    border:none;
    border-radius:15px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
    transition:.3s;
}
.card-custom:hover{
    transform:translateY(-5px);
}
.icon{
    font-size:50px;
    color:#ff6b00;
}
.btn-orange{
    background:#ff6b00;
    color:#fff;
    border-radius:30px;
    padding:10px 25px;
}
.btn-orange:hover{
    background:#e85c00;
}
.office{
    background:#fff;
    border-radius:15px;
    padding:30px;
    box-shadow:0 5px 15px rgba(0,0,0,.08);
}
</style>
</head>

<body>

<!-- HERO -->
<div class="hero">
    <h1>Earn With LalaGO</h1>
    <p>Partner with us as a Restaurant or Rider and start earning today</p>
</div>

<!-- OFFERS -->
<div class="container section">
    <div class="row g-4">

        <!-- RESTAURANT -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 text-center">
                <i class="fa-solid fa-store icon mb-3"></i>
                <h3>Become a Restaurant Partner</h3>
                <p>
                    Grow your business by reaching more customers.
                    Accept online orders and increase your daily income.
                </p>
                <ul class="text-start">
                    <li>No upfront registration fee</li>
                    <li>Easy menu upload</li>
                    <li>Daily & weekly payouts</li>
                    <li>Marketing support</li>
                </ul>
                <a href="apply_resto.php" class="btn btn-orange mt-3">
                    Apply as Restaurant
                </a>
            </div>
        </div>

        <!-- RIDER -->
        <div class="col-md-6">
            <div class="card card-custom p-4 h-100 text-center">
                <i class="fa-solid fa-motorcycle icon mb-3"></i>
                <h3>Become a Delivery Rider</h3>
                <p>
                    Earn money by delivering food around your area.
                    Flexible working hours, more deliveries = more income.
                </p>
                <ul class="text-start">
                    <li>Flexible schedule</li>
                    <li>Daily earnings</li>
                    <li>Bonuses & incentives</li>
                    <li>Support team available</li>
                </ul>
                <a href="apply_rider.php" class="btn btn-orange mt-3">
                    Apply as Rider
                </a>
            </div>
        </div>

    </div>
</div>

<!-- OFFICE LOCATION -->
<div class="container section">
    <div class="office text-center">
        <i class="fa-solid fa-location-dot icon mb-3"></i>
        <h3>Visit Our Office</h3>
        <p>
            Interested applicants may visit our office for verification
            and faster approval.
        </p>
        <p class="fw-bold">
            📍 LalaGO Office <br>
            Ground Floor, ABC Building <br>
            Jolo, Sulu, Philippines
        </p>
        <p>
            🕘 Office Hours: Monday – Friday (9:00 AM – 5:00 PM)
        </p>
        <p>
            📞 Contact: 09XX-XXX-XXXX <br>
            📧 Email: support@lalago.com
        </p>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-3 text-muted">
    © <?php echo date("Y"); ?> LalaGO. All rights reserved.
</footer>

</body>
</html>

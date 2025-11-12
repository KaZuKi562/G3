<?php
include "db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About | Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Center rectangle styling */
        .service-box {
            max-width: 800px;
            margin: 130px auto;
            background: #d8d8d8ff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }

        .service-box h2 {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }

        .service-box p {
            font-size: 1rem;
            color: #555;
            line-height: 1.8;
            text-align: justify;
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="brand"><a href="index.php" style="text-decoration:none;color:inherit;">Swastecha</a></div>
        <div class="search-bar">
            <form method="get" action="index.php">
                <input type="text" name="search" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="user-cart" style="position:relative;">
            <a href="javascript:void(0);" id="cartIcon">
                <img src="icon/cart.png" alt="Cart" class="icon">
                <span class="cart-badge" id="cartBadge">0</span>
            </a>
            <span><a href="login.php">Log In</a></span>
            <span>|</span>
            <span><a href="signup.php">Sign Up</a></span>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-popup">
            <button class="close-btn" id="closeCart">&times;</button>
            <div id="cartItems"></div>
            <div class="cart-summary" id="cartSummary"></div>
            <button class="checkout-btn" onclick="window.location.href='login.php';">Login to Checkout</button>
        </div>
    </div>

    <!-- Ads Banner -->
    <div class="ads-banner">
        <div class="ads-track">
            <img src="ads/ad.png" alt="Ad 0">
            <img src="ads/ad1.png" alt="Ad 1">
        </div>
        <div class="ads-dots"></div>
    </div>

    <!-- Main Content -->
    <div class="main-container">
        <div class="content">
            <div class="service-box">
                <h2>Service</h2>
                <p>
                    Swasteche Center also operates Phone Authorized and Premium Service Providers that give the best repair 
                    and maintenance services. As one of the Phone shop Providers in the Philippines, our Phone Certified 
                    Technicians and friendly customer service officers are always ready to provide you with great advice and 
                    technical support to get you and your Phone back up and running. You can go to our service center located in
                    Jenra Dau Mabalacat Pampanga.
                </p>
            </div>
        </div>
    </div>

    <!--Footer-->
<section class="features">
        <div>
            <img src="delivery.png" alt="">
            <span>Delivery</span>
            <small>Delivery coverage around Pampanga</small>
        </div>
        <div>
            <img src="payment.png" alt="">
            <span>Secured Payment</span>
            <small>Your payment information is processed securely</small>
        </div>
        <div>
            <img src="support.png" alt="">
            <span>Customer Support</span>
            <small>Customer support to help you all the way</small>
        </div>
    </section>
    <footer>
        <div class="footer-columns">
            <div>
                <strong>3 Group</strong>
                <ul>
                    <a href="about.php"><li>About Us</li></a>
                    <li>Locations</li>
                    <li>Terms of Service</li>
                    <li>Refund Policy</li>
                    <li>Corporate Sales</li>
                    <li>Contact Us</li>
                </ul>
            </div>
            <div>
                <strong>Policies</strong>
                <ul>
                    <li>Privacy Policy</li>
                    <li>Terms and Condition</li>
                    <li>FAQs</li>
                    <li>Do not sell my personal information</li>
                </ul>
            </div>
            <div>
                <strong>Product Categories</strong>
                <ul>
                    <li>Cellphone</li>
                    <li>Apple</li>
                    <li>Infinix</li>
                    <li>Realme</li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', () => {
    document.getElementById('filterForm').submit();
  });
});

</script>
    <script src="main.js"></script>
</body>
</html>

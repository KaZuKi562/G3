<?php
session_start();

$user_logged_in = isset($_SESSION['user_id']);
$cart_item_count = 0; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - Swastecha</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="top-bar">
        <div class="brand">Swastecha</div>
      <div class="user-cart" style="position:relative;">
                <a href="javascript:void(0);" id="cartIcon">
                    <img src="icon/cart.png" alt="Cart" class="icon">
                    <span class="cart-badge2" id="cartBadge">0</span>
                    <a href="user.php">
                    <img src="icon/user.png" alt="User" class="icon">
                    </a>
                </a>
            </div>
    </div>

    <nav class="tabs">
        <a href="main_home.php">
            <button class="tab">Home</button>
        </a>
        <a href="phone.php">
            <button class="tab">Cellphone</button>
        </a>
        <a href="tablet.php">
            <button class="tab">Tablet</button>
         </a>
        <a href="laptop.php">
             <button class="tab">Laptop</button>
        </a>
    </nav>

    <div class="success-container">
        <div class="order-placed-box">
            
            <div class="checkmark-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>

            <h1>Order Placed</h1>
            
            <a href="main_home.php" class="view-order-btn">
                Back Home
            </a>
        </div>
    </div>

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
                <strong>Swastecha</strong>
                <ul>
                    <li>Reliable online shop to purchase phones.</li>
                </ul>
            </div>
            <div>
                <strong>Contact</strong>
                <ul>
                    <li>Email: support@swastecha.com</li>
                    <li>Phone: +63 1234-567-7898</li>
                </ul>
            </div>
            <div>
                <strong>Follow Us</strong>
                <ul>
                    <li>Facebook</li>
                    <li>Instagram</li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>

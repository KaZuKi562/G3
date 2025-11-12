<?php
include "db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terms and Conditions | Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Page Layout */
        .terms-container {
            max-width: 900px;
            margin: 130px auto;
            background: #d8d8d8ff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: left;
        }

        .terms-container h2 {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }

        .terms-container h3 {
            font-size: 1.2rem;
            margin-top: 25px;
            color: #333;
        }

        .terms-container p {
            font-size: 1rem;
            color: #555;
            line-height: 1.7;
            margin-top: 10px;
            text-align: justify;
        }

        /* Blue Primary Button */
        .primary-btn {
            display: inline-block;
            background-color: #007BFF;
            color: #fff;
            font-size: 1.1rem;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 25px;
            transition: background-color 0.3s ease, transform 0.15s ease;
        }

        .primary-btn:hover {
            background-color: #0056b3;
            transform: scale(1.03);
        }

        @media (max-width: 768px) {
            .terms-container {
                margin: 100px 20px;
                padding: 30px;
            }
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

    <!-- Main Content -->
    <div class="main-container">
        <div class="content">
            <div class="terms-container">
                <h2>Terms and Conditions</h2>
                <p>Welcome to Swastecha Points Redemption Store. By accessing or using our website, you agree to be bound by the following terms and conditions. Please read them carefully before using our services.</p>

                <h3>1. General Use</h3>
                <p>By using this website, you confirm that you are at least 18 years old or have the permission of a parent or guardian. You agree to use the website for lawful purposes only and not to engage in any activity that could harm Swastecha or its users.</p>

                <h3>2. Account Registration</h3>
                <p>Users must register for an account to make purchases or redeem points. You are responsible for maintaining the confidentiality of your login credentials and all activities that occur under your account.</p>

                <h3>3. Points Redemption</h3>
                <p>Points accumulated through Swastecha activities can be redeemed for products listed in the redemption store. Points have no cash value and cannot be exchanged for money. Swastecha reserves the right to modify or discontinue the points system at any time.</p>

                <h3>4. Product Availability</h3>
                <p>All products are subject to availability. In case a product becomes unavailable after order placement, we will notify you and offer an alternative or refund of the redeemed points.</p>

                <h3>5. Payments</h3>
                <p>Any payments made through the Swastecha Store are processed through secure channels. Swastecha is not responsible for payment issues caused by third-party processors.</p>

                <h3>6. Limitation of Liability</h3>
                <p>Swastecha will not be held responsible for any direct, indirect, or consequential damages arising from the use of this website, including issues related to service interruption or data loss.</p>

                <h3>7. Privacy Policy</h3>
                <p>By using our website, you also agree to our <a href="privacy.php" style="color:#007BFF; text-decoration:none;">Privacy Policy</a>, which outlines how we collect and use your information.</p>

                <h3>8. Changes to Terms</h3>
                <p>Swastecha reserves the right to update these Terms and Conditions at any time. Any changes will be posted on this page with an updated effective date.</p>

                <h3>9. Contact Us</h3>
                <p>If you have any questions regarding these Terms and Conditions, please reach out to us at <strong>support@swastecha.com</strong> or visit our <a href="contactus.php" style="color:#007BFF; text-decoration:none;">Contact Us</a> page.</p>

                <button class="primary-btn" onclick="window.location.href='index.php'">Home</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
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
                    <a href="about.php"><li>About Us</li></a>
                    <a href="contactus.php"><li>Contact Us</li></a>
                </ul>
            </div>
            <div>
                <strong>Policies</strong>
                <ul>
                    <a href="privacy.php"><li>Privacy Policy</li></a>
                    <a href="terms.php"><li>Terms and Condition</li></a>
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

    <script src="main.js"></script>
</body>
</html>

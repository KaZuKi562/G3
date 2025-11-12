<?php
include "db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Privacy Policy | Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Privacy Page Layout */
        .policy-container {
            max-width: 900px;
            margin: 130px auto;
            background: #d8d8d8ff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: left;
        }

        .policy-container h2 {
            text-align: center;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 25px;
            color: #222;
        }

        .policy-container h3 {
            font-size: 1.2rem;
            color: #333;
            margin-top: 25px;
        }

        .policy-container p {
            font-size: 1rem;
            color: #555;
            line-height: 1.7;
            margin-top: 10px;
            text-align: justify;
        }

        /* Primary Button (Shared Style) */
        .primary-btn {
            background-color: #007BFF;
            color: #fff;
            font-size: 1.1rem;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.15s ease;
        }

        .primary-btn:hover {
            background-color: #0056b3;
            transform: scale(1.03);
        }

        .home-btn {
            display: block;
            margin: 35px auto 10px auto;
        }

        @media (max-width: 768px) {
            .policy-container {
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
            <div class="policy-container">
                <h2>Privacy Policy</h2>
                <p>
                    At <strong>Swastecha Points Redemption Store</strong>, we value your privacy and are committed to protecting your personal information. 
                    This Privacy Policy explains how we collect, use, and protect the data you provide while using our website and services.
                </p>

                <h3>1. Information We Collect</h3>
                <p>
                    We may collect personal details such as your name, email address, contact number, and delivery address 
                    when you create an account, place an order, or contact us for support.
                </p>

                <h3>2. How We Use Your Information</h3>
                <p>
                    Your information is used to process orders, provide customer support, send updates, and improve our products and services. 
                    We do not sell, rent, or share your personal data with third parties without your consent.
                </p>

                <h3>3. Data Security</h3>
                <p>
                    We implement strict security measures to protect your data from unauthorized access, disclosure, or misuse. 
                    However, please note that no online transmission is 100% secure.
                </p>

                <h3>4. Cookies</h3>
                <p>
                    Our website may use cookies to enhance your browsing experience. 
                    You can choose to disable cookies through your browser settings, but this may affect site functionality.
                </p>

                <h3>5. Third-Party Services</h3>
                <p>
                    We may use trusted third-party tools for payment processing or analytics. 
                    These providers are bound by their own privacy policies and are committed to data protection.
                </p>

                <h3>6. Your Rights</h3>
                <p>
                    You have the right to access, update, or delete your personal information by contacting us. 
                    We will respond to your request within a reasonable timeframe.
                </p>

                <h3>7. Changes to This Policy</h3>
                <p>
                    Swastecha reserves the right to update this policy from time to time. 
                    Any changes will be reflected on this page with an updated “Last Modified” date.
                </p>

                <h3>8. Contact Us</h3>
                <p>
                    If you have any questions about this Privacy Policy, please contact us at:  
                    <strong>support@swastecha.com</strong> or visit our <a href="contactus.php" style="color:#007BFF; text-decoration:none;">Contact Us</a> page.
                </p>

                <button class="primary-btn home-btn" onclick="window.location.href='index.php'">Home</button>
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

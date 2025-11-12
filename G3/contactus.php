<?php
include "db_connect.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Contact Page Layout */
        .contact-container {
            max-width: 800px;
            margin: 130px auto;
            background: #d8d8d8ff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }

        .contact-container h2 {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #222;
        }

        .contact-container p {
            font-size: 1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Contact Form Styling */
        form.contact-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        form.contact-form input,
        form.contact-form textarea {
            width: 100%;
            max-width: 600px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            resize: none;
        }

        form.contact-form textarea {
            height: 120px;
        }

        /* Primary button (shared for Send Message and Home) */
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

        /* Give the Home button a bit more top margin */
        .home-btn {
            margin-top: 25px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .contact-container {
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
            <div class="contact-container">
                <h2>Contact Us</h2>
                <p>
                    Have a question, feedback, or need assistance?  
                    Our friendly team is here to help you. Fill out the form below and we’ll get back to you as soon as possible!
                </p>

                <form class="contact-form" method="post" action="#">
                    <input type="text" name="name" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea name="message" placeholder="Your Message" required></textarea>
                    <button type="submit" class="primary-btn">Send Message</button>
                </form>

                <div style="margin-top:30px; color:#444;">
                    <p><strong>Address:</strong> Jenra Dau, Mabalacat, Pampanga</p>
                    <p><strong>Phone:</strong> +63 912 345 6789</p>
                    <p><strong>Email:</strong> support@swastecha.com</p>
                </div>

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

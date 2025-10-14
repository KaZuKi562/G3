<?php
// Sample data for products
$products = [
    [
        "brand" => "Apple",
        "name" => "IPHONE 15 PRO 256GB WHITE TITANIUM",
        "price" => "₱63,990",
        "points" => "80,000 P",
        "getpoints" => "GET 35,000 P",
        "img" => "iphone15pro_white.jpg"
    ],
    [
        "brand" => "Apple",
        "name" => "IPHONE 13 128GB MIDNIGHT",
        "price" => "₱31,005",
        "points" => "50,000 P",
        "getpoints" => "GET 15,000 P",
        "img" => "iphone13_midnight.jpg"
    ],
    [
        "brand" => "Infinix",
        "name" => "INFINIX NOTE 30 PRO 4G",
        "price" => "₱10,199",
        "points" => "18,000 P",
        "getpoints" => "GET 6,000 P",
        "img" => "infinix_note30pro.jpg"
    ],
    [
        "brand" => "Infinix",
        "name" => "INFINIX GT 10 PRO",
        "price" => "₱14,199",
        "points" => "22,000 P",
        "getpoints" => "GET 8,000 P",
        "img" => "infinix_gt10pro.jpg"
    ],
    [
        "brand" => "Realme",
        "name" => "REALME 10 PRO 5G 128GB - 128GB SUEDE GRAY",
        "price" => "₱23,990",
        "points" => "89,000 P",
        "getpoints" => "GET 25,000 P",
        "img" => "realme10pro_suede.jpg"
    ],
    [
        "brand" => "Realme",
        "name" => "REALME 10 PRO 5G 128GB - VELVET GREEN",
        "price" => "₱27,990",
        "points" => "40,000 P",
        "getpoints" => "GET 20,000 P",
        "img" => "realme10pro_green.jpg"
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="main-container">
    <header>
        <div class="top-bar">
            <div class="brand">Swastecha</div>
            <div class="search-bar">
                <input type="text" placeholder="Search">
            </div>
            <div class="user-cart">
                <span class="cart">&#128722;</span>
                <span><a href="#">Log In</a></span>
                <span>|</span>
                <span><a href="#">Sign Up</a></span>
            </div>
        </div>
        <div class="hero-banner">
            <div class="hero-content">
                <h1>Order now to get<br>Points</h1>
                <p>Collected points can be used to redeem free product</p>
                <button class="order-btn">Order Now!</button>
            </div>
            <div class="hero-img">
                <img src="banner-phone.png" alt="Banner Phone">
            </div>
        </div>
        <nav class="tabs">
            <button class="tab active" onclick="showTab('cellphone')">Cellphone</button>
            <button class="tab" onclick="showTab('tablet')">Tablet</button>
            <button class="tab" onclick="showTab('headset')">Headset</button>
            <button class="tab" onclick="showTab('laptop')">Laptop</button>
        </nav>
    </header>
    <div class="content">
        <aside class="filters">
            <h2>Filters</h2>
            <div class="filter-group">
                <strong>Brands</strong>
                <div><input type="checkbox" id="apple"> <label for="apple">Apple</label></div>
                <div><input type="checkbox" id="infinix"> <label for="infinix">Infinix</label></div>
                <div><input type="checkbox" id="realme"> <label for="realme">Realme</label></div>
            </div>
            <hr>
            <div class="filter-group">
                <strong>Price</strong>
                <div><input type="radio" name="price" id="under10k"><label for="under10k"> Under ₱ 10,000</label></div>
                <div><input type="radio" name="price" id="10k30k"><label for="10k30k"> ₱ 10,000 to ₱ 30,000</label></div>
                <div><input type="radio" name="price" id="above50k"><label for="above50k"> Above ₱ 50,000</label></div>
            </div>
        </aside>
        <section class="product-list" id="products">
            <?php foreach($products as $p): ?>
            <div class="product-card" data-brand="<?= $p['brand'] ?>" data-price="<?= $p['price'] ?>">
                <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>">
                <div class="product-title"><?= $p['name'] ?></div>
                <div class="product-prices">
                    <span><?= $p['price'] ?></span> <span><?= $p['points'] ?></span>
                </div>
                <div class="product-getpoints"><?= $p['getpoints'] ?></div>
                <button class="cart-btn">Add to cart</button>
            </div>
            <?php endforeach; ?>
        </section>
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
                <strong>ZGroup</strong>
                <ul>
                    <li>Customer Support</li>
                    <li>Store Locations</li>
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
                    <li>Return and Exchange Policy</li>
                    <li>FAQs</li>
                    <li>Do not sell my personal information</li>
                </ul>
            </div>
            <div>
                <strong>Product Categories</strong>
                <ul>
                    <li>Cellphone</li>
                    <li>Tablet</li>
                    <li>Headset</li>
                    <li>Laptop</li>
                    <li>Corporate Sales</li>
                </ul>
            </div>
        </div>
    </footer>
</div>
<script src="main.js"></script>
</body>
</html>
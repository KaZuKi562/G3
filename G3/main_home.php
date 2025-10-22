<?php
// Sample data for products
$products = [
    [
        "brand" => "Apple",
        "name" => "IPHONE 15 PRO 256GB",
        "price" => "₱63,990",
        "points" => "80,000 P",
        "getpoints" => "GET 35,000 P",
        "img" => "img/iphone15pro_white.PNG"
    ],
    [
        "brand" => "Apple",
        "name" => "IPHONE 13 128GB",
        "price" => "₱31,005",
        "points" => "50,000 P",
        "getpoints" => "GET 15,000 P",
        "img" => "img/iPhone13_Midnight.png"
    ],
    [
        "brand" => "Infinix",
        "name" => "INFINIX NOTE 50 PRO 4G",
        "price" => "₱10,199",
        "points" => "18,000 P",
        "getpoints" => "GET 6,000 P",
        "img" => "img/infinix_note_50.png"
    ],
    [
        "brand" => "Infinix",
        "name" => "INFINIX GT 30 PRO",
        "price" => "₱14,199",
        "points" => "22,000 P",
        "getpoints" => "GET 8,000 P",
        "img" => "img/infinix_gt_30.png"
    ],
    [
        "brand" => "Realme",
        "name" => "REALME 14 PRO+ 5G (12GB + 512GB)",
        "price" => "₱23,990",
        "points" => "89,000 P",
        "getpoints" => "GET 25,000 P",
        "img" => "img/realme_14.png"
    ],
    [
        "brand" => "Realme",
        "name" => "REALME 15 PRO 5G (12GB + 256GB)",
        "price" => "₱27,990",
        "points" => "40,000 P",
        "getpoints" => "GET 20,000 P",
        "img" => "img/realme_15_pro.png"
    ],
    [
        "brand" => "INFINIX",
        "name" => "INFINIX HOT 50i",
        "price" => "₱4,499",
        "points" => "3,000 P",
        "getpoints" => "GET 1,500 P",
        "img" => "img/infinix-hot-50i.png"
    ],

];

// Search logic
$search = '';
$filtered_products = $products;
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $filtered_products = array_filter($products, function($p) use ($search) {
        return stripos($p['brand'], $search) !== false || stripos($p['name'], $search) !== false;
    });
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
 <div class="top-bar">
        <div class="brand">Swastecha</div>
        <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="user-cart">
            <a href="cart.html">
                <img src="icon/cart.png" alt="Cart" class="icon">
            </a>
            <a href="user.html">
                <img src="icon/user.png" alt="User" class="icon">
            </a>
        </div>
    </div>

    <div class="ads-banner">
        <div class="ads-track">
            <img src="ads/ad.png" alt="Ad 0">
            <img src="ads/ad1.png" alt="Ad 1">          
        </div>
        <div class="ads-dots"></div>
    </div>

    <nav class="tabs">
        <a href="main_home.php">
            <button class="tab active">Home</button>
        </a>
        <a href="main_phone.php">
            <button class="tab">Cellphone</button>
        </a>
        <a href="tablet.php">
            <button class="tab">Tablet</button>
         </a>
        <a href="laptop.php">
             <button class="tab">Laptop</button>
        </a>
    </nav>

    <div class="main-container">
        
        <div class="content">
            <section class="product-list" id="products">
                <?php if (empty($filtered_products)): ?>
                    <div style="padding:20px;text-align:center;">No products found.</div>
                <?php else: ?>
                    <?php foreach($filtered_products as $p): ?>
                    <div class="product-card" data-brand="<?= htmlspecialchars($p['brand']) ?>" data-price="<?= htmlspecialchars($p['price']) ?>">
                        <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                        <div class="product-prices">
                            <span><?= htmlspecialchars($p['price']) ?></span> <span><?= htmlspecialchars($p['points']) ?></span>
                        </div>
                        <div class="product-getpoints"><?= htmlspecialchars($p['getpoints']) ?></div>
                        <button class="buy-btn">Buy now</button>
                        <button class="cart-btn">Add to cart</button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
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
                    <strong>3 Group</strong>
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


    <script src="main.js"></script>
</body>
</html>
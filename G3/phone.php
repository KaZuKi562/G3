<?php
// Sample data for products
$products = [
    [
        "brand" => "Apple",
        "name" => "IPHONE 15 PRO 256GB WHITE TITANIUM",
        "price" => "₱63,990",
        "points" => "80,000 P",
        "getpoints" => "GET 35,000 P",
        "img" => "img/iphone15pro_white.PNG"
    ],
    [
        "brand" => "Apple",
        "name" => "IPHONE 13 128GB MIDNIGHT",
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
        "name" => "REALME 14 PRO+ 5G (12GB + 512GB) SUEDE GRAY",
        "price" => "₱23,990",
        "points" => "89,000 P",
        "getpoints" => "GET 25,000 P",
        "img" => "img/realme_14.png"
    ],
    [
        "brand" => "Realme",
        "name" => "REALME 15 PRO 5G (12GB + 256GB) VELVET GREEN",
        "price" => "₱27,990",
        "points" => "40,000 P",
        "getpoints" => "GET 20,000 P",
        "img" => "img/realme_15_pro.png"
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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

    <nav class="tabs">
        <a href="index.php">
            <button class="tab">Home</button>
        </a>
        <a href="phone.php">
            <button class="tab active">Cellphone</button>
        </a>
        <a href="tablet.php">
            <button class="tab">Tablet</button>
         </a>
        <a href="laptop.php">
             <button class="tab">Laptop</button>
        </a>
    </nav>
    
    <div class="main-container">
        <header>
        
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
                <div><input type="checkbox" name= "price" id="under10k"><label for="under10k"> Under ₱ 10,000</label></div>
                <div><input type="checkbox" name="price" id="10k30k"><label for="10k30k"> ₱ 10,000 to ₱ 30,000</label></div>
                <div><input type="checkbox" name="price" id="above50k"><label for="above50k"> Above ₱ 50,000</label></div>
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
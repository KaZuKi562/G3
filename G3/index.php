<?php
include "db_connect.php";

$search = '';
$where = '';
$params = [];
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $where = "WHERE brand LIKE ? OR name LIKE ?";
    $search_param = "%$search%";
    $params = [$search_param, $search_param];
}

$sql = "SELECT * FROM products $where";
$stmt = $conn->prepare($sql);
if ($where) {
    $stmt->bind_param("ss", ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$filtered_products = $result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
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

<!-- BUY NOW MODAL -->
<div class="buy-modal" id="buyModal">
  <div class="buy-content">
    <button class="close-btn" id="closeBuyModal">&times;</button>

    <!-- PRODUCT INFO SECTION -->
    <div class="buy-header">
      <img id="productImage" src="" alt="Product Image" class="buy-img">
      <div class="buy-info">
        <h3 id="productName"></h3>
        <p id="productBrand"></p>
        <p id="productPrice"></p>
        <p id="productPoints"></p>
        <p id="productGetPoints"></p>

        <div class="quantity-control">
          <button id="qtyMinus">−</button>
          <span id="qtyValue">1</span>
          <button id="qtyPlus">+</button>
        </div>
      </div>
    </div>

    <hr>

    <!-- SPECS SECTION -->
    <ul class="specs">
      <li><strong>Processor:</strong> <span id="specProcessor"></span></li>
      <li><strong>OS:</strong> <span id="specOS"></span></li>
      <li><strong>Resolution:</strong> <span id="specResolution"></span></li>
      <li><strong>Dimension:</strong> <span id="specDimension"></span></li>
      <li><strong>Camera:</strong> <span id="specCamera"></span></li>
      <li><strong>Battery:</strong> <span id="specBattery"></span></li>
    </ul>

<!-- OPTIONS -->
<div class="option-group">
    <label for="memorySelect"><strong>Memory:</strong></label>
    <select id="memorySelect">
        <option value="128" selected>128GB</option> 
        <option value="256">256GB</option>
    </select>
</div>


<!-- TOTALS SECTION -->
    <div class="total-section">
        <p><strong>Total Price:</strong> <span id="totalPrice">₱0</span></p>
        <p><strong>Total Points:</strong> <span id="totalPoints">0 P</span></p>
        <p><strong>Total Get Points:</strong> <span id="totalGetPoints">GET 0 P</span></p>
    </div>

<!-- ACTION BUTTONS -->
    <div class="modal-actions">
        <button id="addToCartBtn" class="buy-btn">Add to Cart</button>
        <button id="loginToBuyBtn" onclick="window.location.href='login.php';">Login to buy</button>
    </div>
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
        <a href="index.php">
            <button class="tab active">Home</button>
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

    <div class="main-container">
        
        <div class="content">
            <section class="product-list" id="products">
                <?php if (empty($filtered_products)): ?>
                    <div style="padding:20px;text-align:center;">No products found.</div>
                <?php else: ?>
                    <?php foreach($filtered_products as $p): ?>
                    <div class="product-card" data-brand="<?= htmlspecialchars($p['brand']) ?>" data-price="<?= htmlspecialchars($p['price']) ?>" class="product-card" 
                     data-brand="<?= htmlspecialchars($p['brand']) ?>" 
                     data-price="<?= htmlspecialchars($p['price']) ?>"
                     data-name="<?= htmlspecialchars($p['name']) ?>"
                     data-img="<?= htmlspecialchars($p['img']) ?>"
                     data-points="<?= htmlspecialchars($p['points']) ?>"
                     data-getpoints="<?= htmlspecialchars($p['getpoints']) ?>"
                     data-processor="<?= htmlspecialchars($p['processor']) ?>"
                     data-os="<?= htmlspecialchars($p['os']) ?>"
                     data-resolution="<?= htmlspecialchars($p['resolution']) ?>"
                     data-dimension="<?= htmlspecialchars($p['dimention']) ?>"
                     data-camera="<?= htmlspecialchars($p['camera']) ?>"
                     data-battery="<?= htmlspecialchars($p['battery']) ?>">
                        <img src="<?= htmlspecialchars($p['img']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <div class="product-title">
                            <?= htmlspecialchars($p['name']) ?></div>
                        <div class="product-prices">
                            <span><?= htmlspecialchars($p['price']) ?></span> <span><?= htmlspecialchars($p['points']) ?></span>
                        </div>
                        <div class="product-getpoints"><?= htmlspecialchars($p['getpoints']) ?></div>
                        <button class="buy-btn">Buy now</button>

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
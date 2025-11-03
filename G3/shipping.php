<?php
include "db_connect.php";
session_start();

// Initialize variables with base values, then override with GET parameters
$product_id = null;
$product_name = "No Product Selected";
$product_price = '0';
$product_points = '0 P';
$product_getpoints = 'GET 0 P';
$product_img = '';
$quantity = 1;
$memory = '128'; // Default memory

// 1. Check if the product_id is passed
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // 2. Fetch base product details from the database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $product_name = htmlspecialchars($product['name']);
        $product_img = htmlspecialchars($product['img']);

        // Set base values (will be overwritten if final parameters exist)
        $product_price = htmlspecialchars($product['price']);
        $product_points = htmlspecialchars($product['points']);
        $product_getpoints = htmlspecialchars($product['getpoints']);

        // 3. Override with final calculated values from the URL
        if (isset($_GET['final_price'])) {
            // Remove non-numeric characters for safety and formatting
            $final_price = number_format(floatval($_GET['final_price']), 0, '', ',');
            $product_price = "₱" . $final_price;
        }

        if (isset($_GET['final_points'])) {
            $final_points = number_format(floatval($_GET['final_points']), 0, '', ',');
            $product_points = $final_points . " P";
        }

        if (isset($_GET['final_getpoints'])) {
            $final_getpoints = number_format(floatval($_GET['final_getpoints']), 0, '', ',');
            $product_getpoints = "GET " . $final_getpoints . " P";
        }
        
        if (isset($_GET['qty'])) {
            $quantity = intval($_GET['qty']);
        }

        if (isset($_GET['memory'])) {
            $memory = htmlspecialchars($_GET['memory']);
        }

    } else {
        $product_name = "Product Not Found";
    }

} else {
    // No product ID was passed
    $product_id = null;
    $product_name = "No Product Selected";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart - Swastecha</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <!-- Header -->
  <div class="top-bar">
        <div class="brand">Swastecha</div>
        <div class="search-bar">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Search">
                <button type="submit">Search</button>
            </form>
        </div>
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

     <!-- Cart Modal -->
    <div class="cart-modal" id="cartModal">
        <div class="cart-popup">
            <button class="close-btn" id="closeCart">&times;</button>
            <div id="cartItems"></div>
            <div class="cart-summary" id="cartSummary"></div>
            <button class="checkout-btn">Checkout</button>
        </div>
    </div>

  <!-- Main Cart Section -->
 <div class="cart-container">
    <div class="cart-left">
      <h2>My Cart</h2>
      <div class="cart-item">
        <div class="cart-product">
          <?php if ($product): ?>
            <img id="productImage" src="<?= $product_img ?>" alt="Product Image" class="buy-img">
            <p class="product-name">
              <strong>
                <?= $product_name ?> 
                <?= ($memory == '256') ? ' (256GB)' : ' (128GB)' ?>
              </strong>
            </p>
            <p class="product-price">
              <span id="totalPriceDisplay"><?= $product_price ?></span>
            </p>
            <p class="product-getpoints">
              <span id="totalGetPointsDisplay"><?= $product_getpoints ?></span>
            </p>
            <p class="product-points">
              <span id="totalPointsDisplay"><?= $product_points ?></span>
            </p>
          <?php elseif (is_null($product)): ?>
            <p>Invalid product ID.</p>
          <?php endif; ?>
        </div>

        <div class="quantity-control">
          <span id="qtyValue">Qty <?= $quantity ?></span>
        </div>
      </div>

    </div>

    <div class="cart-right">
      <div class="total-box">
        <h3>Total</h3>
        <p class="total-amount"><?= $product_price ?></p>

        <div class="shipping-options">
          <p>Select an option</p>
          <div class="buttons">
            <a href="shipping.php"><button class="btn active">Shipping</button></a>
            <a href="BuyNow.php"><button class="btn ">Pickup</button></a>
          </div>
        </div>

        <div class="pickup-info">
          <h4>Mabalacat City</h4>
          <p>Jenna Dau</p>
        </div>

        <div class="payment-methods">
          <h4>Payment method</h4>
          <button>Cash on pickup</button>
          <button>GCash</button>
          <button>Credit/debit card</button>
          <button>Use points</button>
          <button>Installment</button>
        </div>

        <div class="points">
          <p><?= $product_getpoints ?></p>
        </div>

        <button class="checkout-btn">Checkout</button>
      </div>
    </div>
  </div>

  <script src="main.js"></script>
</body>
</html>

<?php
include "db_connect.php";
session_start();

// Initialize variables 
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$product_id = null;
$product_name = "No Product Selected";
$product_price = '0';
$product_points = '0 P';
$product_getpoints = 'GET 0 P';
$product_img = '';
$quantity = 1;
$memory = '128';

$user = null;
if ($user_id) {
    include "db_connect.php";

$message = '';
$message_type = '';

$current_address_detail = '';
$current_postal_code = '';
$current_phone_number = '';
$user_data = null;

// fetch the user id
$sql_fetch = "SELECT user_address, user_number, getpoints FROM account WHERE user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
if ($stmt_fetch) {
    $stmt_fetch->bind_param("i", $user_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $user_data = $result_fetch->fetch_assoc();
    $stmt_fetch->close();

    if ($user_data) {
        $current_phone_number = htmlspecialchars($user_data['user_number']);
        $user_current_points = intval($user_data['getpoints']);
        $full_address = $user_data['user_address'];
        
        if (preg_match('/^(.*)\s\((?:Postal Code|PC):\s*(\w+)\)$/i', $full_address, $matches)) {
            $current_address_detail = htmlspecialchars(trim($matches[1]));
            $current_postal_code = htmlspecialchars($matches[2]);
        } else {
            $current_address_detail = htmlspecialchars($full_address);
        }
    }
}
}
$points_to_get = 0;
$points_to_use = 0;

if (isset($_GET['final_getpoints'])) {
    $points_to_get = intval(str_replace(',', '', $_GET['final_getpoints']));
}
if (isset($_GET['final_points'])) {
    $points_to_use = intval(str_replace(',', '', $_GET['final_points']));
}

//updating sql
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $address_detail = htmlspecialchars($_POST['address_detail']);
    $postal_code = htmlspecialchars($_POST['postal_code']);
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    $full_address = $address_detail . ' (Postal Code: ' . $postal_code . ')';
    $checkout_success = false;

    if ($payment_method == 'point') {

        if ($user_current_points >= $points_to_use && $points_to_use > 0) {
            $new_points_balance = $user_current_points - $points_to_use;

            $sql_checkout = "UPDATE account SET points = ? WHERE user_id = ?";
            $stmt_checkout = $conn->prepare($sql_checkout);
            
            if ($stmt_checkout) {
                $stmt_checkout->bind_param("ii", $new_points_balance, $user_id);
                if ($stmt_checkout->execute()) {
                    $message = "Order placed successfully! Points used: " . number_format($points_to_use) . ".";
                    $checkout_success = true;
                    $message_type = 'success';
                } else {
                    $message = "Error completing purchase (Points transaction): " . $stmt_checkout->error;
                    $message_type = 'error';
                }
                $stmt_checkout->close();
            }
        } else {
            $message = "You do not have enough points to purchase this item.";
            $message_type = 'error';
            goto end_post_logic; 
        }

    } else {
        if ($points_to_get > 0) {
            $new_points_balance = $user_current_points + $points_to_get;

            $sql_checkout = "UPDATE account SET getpoints = ? WHERE user_id = ?";
            $stmt_checkout = $conn->prepare($sql_checkout);
            
            if ($stmt_checkout) {
                $stmt_checkout->bind_param("ii", $new_points_balance, $user_id);
                if ($stmt_checkout->execute()) {
                    $message = "Order placed successfully! You earned " . number_format($points_to_get) . " points.";
                    $checkout_success = true;
                    $message_type = 'success';
                } else {
                    $message = "Order placed, but error awarding points: " . $stmt_checkout->error;
                    $checkout_success = true; 
                    $message_type = 'warning';
                }
                $stmt_checkout->close();
            }
        } else {
            $message = "Order placed successfully!";
            $checkout_success = true;
            $message_type = 'success';
        }
    }

    if ($checkout_success || $payment_method != 'point') { 
        $full_address = $address_detail . ' (Postal Code: ' . $postal_code . ')';
        $sql_update = "UPDATE account SET user_address = ?, user_number = ? WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);

        if ($stmt_update) {
            $stmt_update->bind_param("ssi", $full_address, $phone_number, $user_id);
            
            if (!$stmt_update->execute()) {
                $message .= " (Error updating address: " . $stmt_update->error . ")";
                $message_type = ($message_type == 'success' || $message_type == 'warning') ? 'warning' : 'error';
            }
            $stmt_update->close();
        } else {
            $message .= " (SQL Prepare Error for Address: " . $conn->error . ")";
            $message_type = ($message_type == 'success' || $message_type == 'warning') ? 'warning' : 'error';
        }
    }

    if ($checkout_success || $message_type == 'error') {
         header("Location: order.php?status=" . $message_type . "&message=" . urlencode($message));
          exit(); 
    }
    
    end_post_logic: ; 

} 
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $product_name = htmlspecialchars($product['name']);
        $product_img = htmlspecialchars($product['img']);
        $product_price = htmlspecialchars($product['price']);
        $product_points = htmlspecialchars($product['points']);
        $product_getpoints = htmlspecialchars($product['getpoints']);

        if (isset($_GET['final_price'])) {
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
            <button class="btn" id="shippingBtn">Shipping</button>
            <button class="btn active" id="pickupBtn">Pickup</button>
          </div>
        </div>

        <div id="deliveryInfoContainer">
          <div id="pickupInfo">
            <h4>Mabalacat City</h4>
            <p>Jenna Dau</p>
          </div>
      <form action="" method="POST" onsubmit="return validatePoints(event);">
      <div id="shippingFields" class="shippingFields">
        <input type="text" id="address_detail" name="address_detail" placeholder="Address" value="<?php echo $current_address_detail; ?>" required >
    
        <input type="tel" id="phone_number" name="phone_number" placeholder="Phone Number" value="<?php echo $current_phone_number; ?>" required>
    
        <input type="text" id="postal_code" name="postal_code" placeholder="Postal Code" value="<?php echo $current_postal_code; ?>" required>
      </div>

    <div class="payment-methods">
        <h4>Payment method</h4>
        
        <div class="method-option">
            <input type="radio" id="cashDelivery" name="payment_method" value="cashDelivery" required>
            <label for="cashDelivery">Cash on Delivery</label>
        </div>
        
        <div class="method-option">
            <input type="radio" id="point" name="payment_method" value="point" required>
            <label for="point">Use points</label>
        </div>
    </div>

    <div class="points">
        <p><?= $product_getpoints ?></p>
    </div>
    
    <input type="hidden" id="requiredPoints" value="<?= $points_to_use ?>"> 
    <input type="hidden" id="userPoints" value="<?= $user_current_points ?>"> 

    <button type="submit" class="checkout-btn">Checkout</button> 
</form>
      </div>
    </div>
  </div>

  <script src="buynow.js"></script>
</body>
</html>

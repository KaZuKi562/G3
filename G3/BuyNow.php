<?php
include "db_connect.php";
session_start();

// Initialize default variables (only used if no product_id or fetch fails)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$product_name = "No Product Selected";
$product_price = 0.0; 
$product_points = '0 P';
$product_getpoints = 'GET 0 P';
$product_img = '';
$quantity = 1;
$memory = '128'; 
$product_id = null;

// Fetch product if product_id is provided
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);
    $sql = "SELECT * FROM phone WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        // Set fetched values (no longer overridden below)
        $product_name = htmlspecialchars($product['name']);
        // Clean the VARCHAR price: remove ₱ and commas, then convert to float
        $product_price = floatval(str_replace(['₱', ','], '', $product['price']));
        $product_points = htmlspecialchars($product['points']);
        $product_getpoints = htmlspecialchars($product['getpoints']);
        $product_img = htmlspecialchars($product['img']);
    } else {
        // Keep defaults if fetch fails
        $product_name = "Product Not Found";
    }
}


// Fetch user data if logged in
$user_current_points = 0;
$current_address_detail = '';
$current_postal_code = '';
$current_phone_number = '';
if ($user_id) {
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

// Handle points from POST (form submission)
$points_to_get = 0;
$points_to_use = 0;
if (isset($_POST['final_points'])) {
    $points_to_use = intval(str_replace(',', '', $_POST['final_points']));
}
if (isset($_POST['final_getpoints'])) {  // If needed for earning points
    $points_to_get = intval(str_replace(',', '', $_POST['final_getpoints']));
}

// Update quantity and memory from GET (likely set by JS)
if (isset($_GET['qty'])) {
    $quantity = intval($_GET['qty']);
}
if (isset($_GET['memory'])) {
    $memory = htmlspecialchars($_GET['memory']); // Should be '128' or '256'
}

// Handle POST (checkout)
$message = '';
$message_type = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $address_detail = htmlspecialchars($_POST['address_detail']);
    $postal_code = htmlspecialchars($_POST['postal_code']);
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    if (isset($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
    } else {
        $quantity = 1; 
    }

     if (isset($_POST['memory'])) {
        $memory = htmlspecialchars($_POST['memory']); // Set to '128' or '256'
    }

    if (!$payment_method) {
        $message = "Please select a payment method.";
        $message_type = 'error';
        goto end_post_logic;
    }

    $full_address = $address_detail . ' (Postal Code: ' . $postal_code . ')';
    $checkout_success = false;

    if ($payment_method == 'point') {
        // Using points
        if ($user_current_points >= $points_to_use && $points_to_use > 0) {
            $new_points_balance = $user_current_points - $points_to_use;
            $sql_checkout = "UPDATE account SET getpoints = ? WHERE user_id = ?";
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
        // Cash on delivery or other
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

    // Update user address/phone if checkout succeeded
    if ($checkout_success) {
        $sql_update = "UPDATE account SET user_address = ?, user_number = ? WHERE user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("ssi", $full_address, $phone_number, $user_id);
            if (!$stmt_update->execute()) {
                $message .= " (Error updating address: " . $stmt_update->error . ")";
                $message_type = 'warning';
            }
            $stmt_update->close();
        }

        // Insert into orders table
        $order_sql = "INSERT INTO orders (
            user_id, 
            product_id, 
            product_name, 
            product_price, 
            quantity, 
            selected_memory, 
            payment_method, 
            address, 
            phone_number, 
            status, 
            order_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";

        $stmt_order = $conn->prepare($order_sql);
        if ($stmt_order) {
            $stmt_order->bind_param(
                "iisdiisss",
                $user_id,
                $product_id,
                $product_name, // Now uses fetched value
                $product_price, // Numeric value
                $quantity,
                $memory, // Uses selected memory (128 or 256)
                $payment_method, // 'cashDelivery' or 'point'
                $full_address,
                $phone_number
            );
            if (!$stmt_order->execute()) {
                $message .= " (Error saving order: " . $stmt_order->error . ")";
                $message_type = 'warning';
            }
            $stmt_order->close();
        } else {
            $message .= " (SQL Prepare Error for order insert: " . $conn->error . ")";
            $message_type = 'warning';
        }
    }

    // Redirect after processing
    header("Location: order.php?status=" . $message_type . "&message=" . urlencode($message));
    exit();

    end_post_logic: ; // For goto on error
}

// Prepare display values (for HTML, after any GET overrides)
$display_price = htmlspecialchars($product['price'] ?? '₱0'); // Default if no product
$display_points = $product_points;
$display_getpoints = $product_getpoints;
if (isset($_GET['final_price'])) {
    $final_price = number_format(floatval($_GET['final_price']), 0, '', ',');
    $display_price = "₱" . $final_price;
}
if (isset($_GET['final_points'])) {
    $final_points = number_format(floatval($_GET['final_points']), 0, '', ',');
    $display_points = $final_points . " P";
}
if (isset($_GET['final_getpoints'])) {
    $final_getpoints = number_format(floatval($_GET['final_getpoints']), 0, '', ',');
    $display_getpoints = "GET " . $final_getpoints . " P";
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
          <?php if ($product_id && $product): ?>
            <img id="productImage" src="<?= $product_img ?>" alt="Product Image" class="buy-img">
            <p class="product-name">
              <strong>
                <?= $product_name ?>
                <?= ($memory == '256') ? ' (256GB)' : ' (128GB)' ?>
              </strong>
            </p>
            <p class="product-price">
              <span id="totalPriceDisplay"><?= $display_price ?></span>
            </p>
            <p class="product-getpoints">
              <span id="totalGetPointsDisplay"><?= $display_getpoints ?></span>
            </p>
            <p class="product-points">
              <span id="totalPointsDisplay"><?= $display_points ?></span>
            </p>
          <?php else: ?>
            <p>Invalid or no product selected.</p>
          <?php endif; ?>
        </div>

        <div class="quantity-control">         
            <span id="qtyValue">Qty<?= $quantity ?></span>
        </div>

    </div>
</div>

    <div class="cart-right">
      <div class="total-box">
        <h3>Total</h3>
        <div class="total-amount">
            <span><?= $display_price ?></span>
            <span><?= $display_points ?></span>
        </div>
        

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
      <form action="BuyNow.php?product_id=<?= $product_id ?>" method="POST">
      <div id="shippingFields" class="shippingFields">
        <input type="hidden" name="quantity" id="quantityInput" value="<?= $quantity ?>">

        <input type="hidden" name="final_points" id="finalPointsInput" value="">

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

<!-- NEW: Add memory selection here -->
<div class="memory-options">
    <h4>Select Memory</h4>
    <div class="method-option">
        <input type="radio" id="memory128" name="memory" value="128" <?= ($memory == '128') ? 'checked' : '' ?> required>
        <label for="memory128">128GB</label>
    </div>
    <div class="method-option">
        <input type="radio" id="memory256" name="memory" value="256" <?= ($memory == '256') ? 'checked' : '' ?> required>
        <label for="memory256">256GB</label>
    </div>
</div>

<div class="points">
    <p><?= $display_getpoints ?></p>
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


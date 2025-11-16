<?php
include "db_connect.php";
session_start();

// --- Helper -----------------------------------------------------------------
function redirect_with_message($type, $msg) {
    header("Location: order.php?status=" . urlencode($type) . "&message=" . urlencode($msg));
    exit;
}

function clean($v) {
    return htmlspecialchars(trim($v));
}

// --- Basic state ------------------------------------------------------------
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : null;

// Defaults for display
$product = [
    'id' => null,
    'name' => 'No Product Selected',
    'price' => '₱0',
    'price_numeric' => 0.0,
    'points' => '0 P',
    'getpoints' => 0,
    'img' => ''
];

$user = [
    'user_id' => $user_id,
    'user_address' => '',
    'user_number' => '',
    'getpoints' => 0
];

// --- Load product -----------------------------------------------------------
if ($product_id) {
    $sql = "SELECT id, name, price, points, getpoints, img, stock FROM phone WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $product['id'] = intval($row['id']);
            $product['name'] = clean($row['name']);
            $product['stock'] = intval($row['stock']);
            $price_numeric = floatval(str_replace([',', '₱', 'PHP', 'php'], '', $row['price']));
            $product['price_numeric'] = $price_numeric;
            $product['price'] = '₱' . number_format($price_numeric, 0, '', ',');
            $product['points'] = isset($row['points']) ? clean($row['points']) : '0 P';
            $product['getpoints'] = isset($row['getpoints']) ? intval($row['getpoints']) : 0;
            $product['img'] = clean($row['img']);
        }
        $stmt->close();
    }
}

if ($user_id) {
    $sql = "SELECT user_id, user_address, postal_code, user_number, getpoints FROM account WHERE user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $user['user_id'] = intval($row['user_id']);
            $user['user_address'] = clean($row['user_address']);
            $user['user_postal'] = clean($row['postal_code']);
            $user['user_number'] = clean($row['user_number']);
            $user['getpoints'] = intval($row['getpoints']);
        }
        $stmt->close();
    }
}

// --- POST (checkout) -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Require login for checkout
    if (!$user_id) {
        redirect_with_message('error', 'You must be logged in to checkout.');
    }

    // Read and sanitize inputs
    $phone_number = isset($_POST['phone_number']) ? clean($_POST['phone_number']) : '';
    $address_detail = isset($_POST['address_detail']) ? clean($_POST['address_detail']) : '';
    $postal_code = isset($_POST['postal_code']) ? clean($_POST['postal_code']) : '';
    $payment_method = isset($_POST['payment_method']) ? clean($_POST['payment_method']) : '';
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
    $memory = isset($_POST['memory']) ? clean($_POST['memory']) : '128';

    // Points fields (may be empty)
    $points_to_use = isset($_POST['final_points']) ? intval(str_replace(',', '', $_POST['final_points'])) : 0;
    $points_to_get = isset($_POST['final_getpoints']) ? intval(str_replace(',', '', $_POST['final_getpoints'])) : intval($product['getpoints']);

    // Basic validations
    if (!$payment_method) {
        redirect_with_message('error', 'Please select a payment method.');
    }

    if (!$product['id']) {
        redirect_with_message('error', 'Invalid product selected.');
        // Prevent ordering more than stock
    if ($quantity > $product['stock']) {
        redirect_with_message(
            'error',
            'Insufficient stock. Available: ' . $product['stock']
        );
    }
    }

    // If paying with points, ensure user has enough
    if ($payment_method === 'point') {
        if ($points_to_use <= 0) {
            redirect_with_message('error', 'No points specified for payment.');
        }
        if ($user['getpoints'] < $points_to_use) {
            redirect_with_message('error', 'You do not have enough points to complete this purchase.');
        }
    }
    $conn->begin_transaction();

    try {
        $current_points_balance = $user['getpoints'];

        // Step 1: If paying with points, deduct first
        if ($payment_method === 'point') {
            $new_points_balance = $current_points_balance - $points_to_use;
        } else {
            // no deduction
            $new_points_balance = $current_points_balance;
        }

        // Step 2: Award product getpoints (always)
        if ($points_to_get > 0) {
            $new_points_balance += $points_to_get;
        }

        // Update account getpoints and contact info (address & phone) in one statement
        $sql_update_acc = "UPDATE account SET getpoints = ?, user_address = ?, user_number = ? WHERE user_id = ?";
        if (!($stmt = $conn->prepare($sql_update_acc))) {
            throw new Exception("Prepare failed (update account): " . $conn->error);
        }
        $stmt->bind_param("issi", $new_points_balance, $address_detail, $phone_number, $user_id);
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("Execute failed (update account): " . $stmt->error);
        }
        $stmt->close();

        // Step 3: Insert order into orders table
        $sql_insert_order = "INSERT INTO orders
        (user_id, product_id, product_name, product_price, quantity, selected_memory, payment_method, address, phone_number, status, order_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        if (!($stmt = $conn->prepare($sql_insert_order))) {
            throw new Exception("Prepare failed (insert order): " . $conn->error);
        }

        // product_price numeric - multiply by quantity if you want total price stored; keep as single-unit price here
        $unit_price = $product['price_numeric'];
        $stmt->bind_param(
            "iisdiisss",
            $user_id,
            $product['id'],
            $product['name'],
            $unit_price,
            $quantity,
            $memory,
            $payment_method,
            $full_address,
            $phone_number
        );

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("Execute failed (insert order): " . $stmt->error);
        }
        $stmt->close();

        // Re-check stock inside transaction for absolute safety
        $sql_check_stock = "SELECT stock FROM phone WHERE id = ? FOR UPDATE";
        if (!($stmt = $conn->prepare($sql_check_stock))) {
            throw new Exception("Prepare failed (check stock): " . $conn->error);
        }
        $stmt->bind_param("i", $product['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $current_stock = intval($row['stock']);
        $stmt->close();

        if ($quantity > $current_stock) {
            throw new Exception("Insufficient stock. Available: {$current_stock}");
        }


        // ---------- NEW STOCK DEDUCTION LOGIC ----------
        $sql_update_stock = "UPDATE phone SET stock = stock - ? WHERE id = ?";

        if (!($stmt = $conn->prepare($sql_update_stock))) {
            throw new Exception("Prepare failed (update stock): " . $conn->error);
        }

        $stmt->bind_param("ii", $quantity, $product['id']);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("Execute failed (update stock): " . $stmt->error);
        }

        $stmt->close();
        // ------------------------------------------------

        // Commit transaction
        $conn->commit();

        // Build success message
        $msg = "Order placed successfully!";
        if ($payment_method === 'point') {
            $msg .= " Points used: " . number_format($points_to_use) . ".";
        }
        if ($points_to_get > 0) {
            $msg .= " You earned " . number_format($points_to_get) . " points.";
        }
        redirect_with_message('success', $msg);

    } catch (Exception $e) {
        $conn->rollback();
        // Log $e->getMessage() if you have logging
        redirect_with_message('error', 'Checkout failed: ' . $e->getMessage());
    }
}

// --- Prepare display variables for GET / initial render --------------------
$display_price = $product['price'];
$display_points = $product['points'];
$display_getpoints = 'GET ' . number_format(intval($product['getpoints']), 0, '', ',');

// If GET overrides (e.g., from JS), sanitize them
if (isset($_GET['final_price'])) {
    $final_price = number_format(floatval($_GET['final_price']), 0, '', ',');
    $display_price = '₱' . $final_price;
}
if (isset($_GET['final_points'])) {
    $final_points = number_format(floatval($_GET['final_points']), 0, '', ',');
    $display_points = $final_points . ' P';
}
if (isset($_GET['final_getpoints'])) {
    $final_getpoints = number_format(floatval($_GET['final_getpoints']), 0, '', ',');
    $display_getpoints = 'GET ' . $final_getpoints . ' P';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Buy Now - Checkout</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* Minimal inline tweaks for clarity */
    .cart-container { max-width: 980px; margin: 40px auto; display:flex; gap:24px; }
    .cart-left { flex:1; }
    .cart-right { width:380px; }
    .buy-img { max-width:180px; display:block; margin-bottom:12px; }
    .checkout-btn { padding:10px 14px; cursor:pointer; }
  </style>
</head>
<body>
  <div class="top-bar">
        <div class="brand">Swastecha</div>
            <div class="search-bar">
            
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

  <div class="cart-container">
    <div class="cart-left">
      <h2>My Cart</h2>

      <div class="cart-item">
        <?php if ($product['id']): ?>
          <img id="productImage" src="<?= $product['img'] ?>" alt="Product Image" class="buy-img">
          <p class="product-name"><strong id="productName"><?= $product['name'] ?> (<?= $memory ?? '128' ?>GB)</strong></p>
          <p class="product-price"><span id="totalPriceDisplay"><?= $display_price ?></span></p>
          <p class="product-getpoints"><span id="totalGetPointsDisplay"><?= $display_getpoints ?></span></p>
          <p class="product-points"><span id="totalPointsDisplay"><?= $display_points ?></span></p>
          <p class="product-stock" style="color:#d9534f;">Stock: <span id="stockDisplay"><?= $product['stock'] ?></span></p>
        <?php else: ?>
          <p>No product selected.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="cart-right">
      <div class="total-box">
        <h3>Total</h3>
        <div class="total-amount">
          <span id="summaryPrice"><?= $display_price ?></span>
          <span id="summaryPoints"><?= $display_points ?></span>
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

          <form id="checkoutForm" action="BuyNow.php?product_id=<?= $product['id'] ?>" method="POST">
            <input type="hidden" name="quantity" id="quantityInput" value="1">
            <input type="hidden" name="final_getpoints" id="finalGetPointsInput" value="<?= intval($product['getpoints']) ?>">
            <input type="hidden" name="final_points" id="finalPointsInput" value="0">

            <div id="shippingFields" class="shippingFields" style="display:none;">
                <input type="text" id="address_detail" name="address_detail" placeholder="Address" value="<?php echo $user['user_address']; ?>" required > 
                <input type="text" id="postal_code" name="postal_code" placeholder="postal_code" value="<?php echo $user['user_postal']; ?>" required > 
                <input type="tel" id="phone_number" name="phone_number" placeholder="Phone Number" value="<?php echo $user['user_number']; ?>" required>
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

            <div class="memory-options">
                <h4>Select Memory</h4>
                <div class="method-option">
                    <input type="radio" id="memory128" name="memory" value="128" checked required>
                    <label for="memory128">128GB</label>
                </div>
                <div class="method-option">
                    <input type="radio" id="memory256" name="memory" value="256" required>
                    <label for="memory256">256GB</label>
                </div>
            </div>

            <div class="points">
                <p id="displayGetPoints"><?= $display_getpoints ?></p>
            </div>

            <!-- expose current user points for client validation only -->
            <input type="hidden" id="userPoints" value="<?= intval($user['getpoints']) ?>">

            <button type="submit" class="checkout-btn">Checkout</button>
          </form>
        </div>
      </div>
    </div>
  </div>

<script>
// --------- Client-side JS (cleaned) ---------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const shippingBtn = document.getElementById('shippingBtn');
    const pickupBtn = document.getElementById('pickupBtn');
    const pickupInfoDiv = document.getElementById('pickupInfo');
    const shippingFieldsDiv = document.getElementById('shippingFields');
    const form = document.getElementById('checkoutForm');

    function toggleDeliveryOption(selected) {
        if (selected === 'shipping') {
            shippingBtn.classList.add('active');
            pickupBtn.classList.remove('active');
            pickupInfoDiv.style.display = 'none';
            shippingFieldsDiv.style.display = 'block';
        } else {
            pickupBtn.classList.add('active');
            shippingBtn.classList.remove('active');
            shippingFieldsDiv.style.display = 'none';
            pickupInfoDiv.style.display = 'block';
        }
    }
    shippingBtn.addEventListener('click', () => toggleDeliveryOption('shipping'));
    pickupBtn.addEventListener('click', () => toggleDeliveryOption('pickup'));

    // Helper to parse integers from "1,234 P" or "GET 1,234 P"
    function parsePoints(text) {
        return parseInt(text.replace(/[^\d]/g, '')) || 0;
    }

    // Initialize final_getpoints (from visible display)
    const getPointsDisplay = document.getElementById('displayGetPoints');
    const finalGetPointsInput = document.getElementById('finalGetPointsInput');
    if (getPointsDisplay && finalGetPointsInput) {
        finalGetPointsInput.value = parsePoints(getPointsDisplay.textContent);
    }

    // When memory option changes, you might change getpoints / price in future
    document.querySelectorAll('input[name="memory"]').forEach(r => {
        r.addEventListener('change', function() {
            // placeholder if you want to update price/getpoints dynamically
            finalGetPointsInput.value = parsePoints(getPointsDisplay.textContent);
            const nameElem = document.getElementById('productName');
            if (nameElem) {
                const base = nameElem.textContent.replace(/\s*\(\d+GB\)\s*$/, '');
                nameElem.textContent = base + ' (' + this.value + 'GB)';
            }
        });
    });

    // When payment changes, set final_points to the displayed price-in-points (if any)
    document.querySelectorAll('input[name="payment_method"]').forEach(r => {
        r.addEventListener('change', function() {
            const pointsNeeded = parsePoints(document.getElementById('totalPointsDisplay') ? document.getElementById('totalPointsDisplay').textContent : '0');
            const finalPointsInput = document.getElementById('finalPointsInput');
            if (this.value === 'point') {
                finalPointsInput.value = pointsNeeded;
            } else {
                finalPointsInput.value = 0;
            }
        });
    });

    const stock = parseInt(document.getElementById("stockDisplay").textContent);
    form.addEventListener('submit', function(e) {
        const qty = parseInt(document.getElementById("quantityInput").value);
        if (qty > stock) {
            e.preventDefault();
            alert("Insufficient stock. Available: " + stock);
            return;
        }
    });


    // Validate points on submit
    form.addEventListener('submit', function(e) {
        const method = document.querySelector('input[name="payment_method"]:checked');
        const userPoints = parseInt(document.getElementById('userPoints').value || '0');
        const finalPoints = parseInt(document.getElementById('finalPointsInput').value || '0');

        if (!method) {
            e.preventDefault();
            alert('Please select a payment method.');
            return;
        }

        if (method.value === 'point' && userPoints < finalPoints) {
            e.preventDefault();
            alert('You do not have enough points (' + finalPoints.toLocaleString() + ' P required).');
            return;
        }
    });

    // Show server messages (from redirect)
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const message = urlParams.get('message');
    if (status && message) {
        alert(status.toUpperCase() + ": " + decodeURIComponent(message));
    }
});
</script>
</body>
</html>

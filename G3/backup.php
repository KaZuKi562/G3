<?php
include "db_connect.php";
session_start();

// Helpers
function redirect_with_message($type, $msg) {
    header("Location: order.php?status=" . urlencode($type) . "&message=" . urlencode($msg));
    exit;
}
function clean($v) {
    return htmlspecialchars(trim($v));
}

// Current user
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;

$user_current_points = 0;
$current_address_detail = '';
$current_postal_code = '';
$current_phone_number = '';

// Load full account (including postal_code)
if ($user_id) {
    $sql_fetch = "SELECT user_address, postal_code, user_number, getpoints FROM account WHERE user_id = ?";
    if ($stmt_fetch = $conn->prepare($sql_fetch)) {
        $stmt_fetch->bind_param("i", $user_id);
        $stmt_fetch->execute();
        $result_fetch = $stmt_fetch->get_result();
        if ($row = $result_fetch->fetch_assoc()) {
            $current_address_detail = htmlspecialchars($row['user_address']);
            $current_postal_code = htmlspecialchars($row['postal_code']);
            $current_phone_number = htmlspecialchars($row['user_number']);
            $user_current_points = intval($row['getpoints']);
        }
        $stmt_fetch->close();
    }
}

// POST handling
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ensure login
    if (!$user_id) {
        redirect_with_message('error', 'You must log in to checkout.');
    }

    // Read posted totals & customer fields
    $points_to_get = isset($_POST['final_getpoints']) ? intval(str_replace(',', '', $_POST['final_getpoints'])) : 0;
    $points_to_use = isset($_POST['final_points']) ? intval(str_replace(',', '', $_POST['final_points'])) : 0;
    $phone_number = isset($_POST['phone_number']) ? clean($_POST['phone_number']) : '';
    $address_detail = isset($_POST['address_detail']) ? clean($_POST['address_detail']) : '';
    $postal_code = isset($_POST['postal_code']) ? clean($_POST['postal_code']) : '';
    $payment_method = isset($_POST['payment_method']) ? clean($_POST['payment_method']) : null;

    // Validate posted product arrays
    if (empty($_POST['product_id']) || !is_array($_POST['product_id'])) {
        redirect_with_message('error', 'No products selected.');
    }

    $product_ids = $_POST['product_id'];
    $quantities = isset($_POST['quantity']) && is_array($_POST['quantity']) ? $_POST['quantity'] : array_fill(0, count($product_ids), 1);
    $memories = isset($_POST['memory']) && is_array($_POST['memory']) ? $_POST['memory'] : array_fill(0, count($product_ids), '128');

    // Validate payment method
    if (!$payment_method) {
        redirect_with_message('error', 'Please select a payment method.');
    }

    // If paying with points, ensure user has enough points (server-side)
    if ($payment_method === 'point') {
        if ($points_to_use <= 0) {
            redirect_with_message('error', 'No points specified for payment.');
        }
        if ($user_current_points < $points_to_use) {
            redirect_with_message('error', 'You do not have enough points to complete this purchase.');
        }
    }

    // Build full_address (separate columns in DB)
    $full_address = $address_detail;

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert each product into orders
        $insert_sql = "INSERT INTO orders (
            user_id, product_id, product_name, product_price, quantity, selected_memory,
            payment_method, address, phone_number, status, order_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";

        if (!($stmt_order = $conn->prepare($insert_sql))) {
            throw new Exception('Prepare failed (insert order): ' . $conn->error);
        }

        // We'll fetch product details from DB for each id (price & name).
        $select_product_sql = "SELECT id, name, price, getpoints FROM products WHERE id = ?"; // your checkout used products table
        if (!($stmt_product = $conn->prepare($select_product_sql))) {
            $stmt_order->close();
            throw new Exception('Prepare failed (select product): ' . $conn->error);
        }

        // Loop items
        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = intval($product_ids[$i]);
            $qty = intval($quantities[$i]) > 0 ? intval($quantities[$i]) : 1;
            $mem = isset($memories[$i]) ? clean($memories[$i]) : '128';

            // Fetch product (with stock)
        $stmt_product->bind_param("i", $pid);
        if (!$stmt_product->execute()) {
            throw new Exception('Execute failed (select product): ' . $stmt_product->error);
        }
        $res = $stmt_product->get_result();
        $prod = $res->fetch_assoc();
        if (!$prod) continue;

        $stock_check_sql = "SELECT stock FROM products WHERE id = ? FOR UPDATE";
        if (!($stmt_stock = $conn->prepare($stock_check_sql))) {
            throw new Exception("Prepare failed (stock check): " . $conn->error);
        }
        $stmt_stock->bind_param("i", $pid);
        $stmt_stock->execute();
        $stock_res = $stmt_stock->get_result();
        $row_stock = $stock_res->fetch_assoc();
        $stmt_stock->close();

        $current_stock = intval($row_stock['stock']);
        if ($qty > $current_stock) {
            throw new Exception("Insufficient stock for product: {$prod['name']}. Available: {$current_stock}");
        }

            $pname = $prod['name'];
            // Normalize price column (string like "₱12,000" or "12000")
            $price_numeric = floatval(str_replace([',','₱','PHP','php'], '', $prod['price']));

            // Insert order row
            // Bind types: i i s d i s s s s  => "iisdissss"? Actually: i,i,s,d,i,s,s,s,s -> "iisdissss"
            $stmt_order->bind_param(
                "iisdissss",
                $user_id,
                $pid,
                $pname,
                $price_numeric,
                $qty,
                $mem,
                $payment_method,
                $full_address,
                $phone_number
            );

            if (!$stmt_order->execute()) {
                throw new Exception('Execute failed (insert order): ' . $stmt_order->error);
            }
        }
        // Deduct stock AFTER inserting order
        $update_stock_sql = "UPDATE phone SET stock = stock - ? WHERE id = ?";
        if (!($stmt_update_stock = $conn->prepare($update_stock_sql))) {
            throw new Exception("Prepare failed (stock deduct): " . $conn->error);
        }
        $stmt_update_stock->bind_param("ii", $qty, $pid);

        if (!$stmt_update_stock->execute()) {
            $stmt_update_stock->close();
            throw new Exception("Execute failed (stock deduct): " . $stmt_update_stock->error);
        }
        $stmt_update_stock->close();
        $stmt_product->close();
        $stmt_order->close();

        $new_points_balance = $user_current_points;

        if ($payment_method === 'point') {
            // Deduct usage
            $new_points_balance -= $points_to_use;
        }

        // Award getpoints (always on purchase)
        if ($points_to_get > 0) {
            $new_points_balance += $points_to_get;
        }

        // Ensure new_points_balance is not negative (extra safety)
        if ($new_points_balance < 0) $new_points_balance = 0;

        $update_acc_sql = "UPDATE account SET getpoints = ?, user_address = ?, postal_code = ?, user_number = ? WHERE user_id = ?";
        if (!($stmt_update = $conn->prepare($update_acc_sql))) {
            throw new Exception('Prepare failed (update account): ' . $conn->error);
        }
        $stmt_update->bind_param("isssi", $new_points_balance, $address_detail, $postal_code, $phone_number, $user_id);
        if (!$stmt_update->execute()) {
            $stmt_update->close();
            throw new Exception('Execute failed (update account): ' . $stmt_update->error);
        }
        $stmt_update->close();

        // Commit transaction
        $conn->commit();

        // Build message
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
        // You can log $e->getMessage() to server logs here
        redirect_with_message('error', 'Checkout failed: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Checkout - Swastecha</title>
<link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="top-bar">
    <div class="brand">Swastecha</div><div class="brand">Swastecha</div>
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
</div>

<div class="cart-container">
    <div class="cart-left">
        <h2>My Cart</h2>
        <div id="checkoutCartList"></div>
    </div>

    <div class="cart-right">
        <div class="total-box">
           <h3>Total</h3>
            <div class="total-amount">
                <span id="totalPriceDisplay">₱0</span>
                <span id="totalPointsDisplay">0 P</span>
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

                <form id="checkoutForm" action="" method="POST" onsubmit="return validatePoints(event);">
                    <div id="shippingFields" class="shippingFields" style="display:none;">
                        <input type="text" name="address_detail" placeholder="Address" value="<?= $current_address_detail ?>" required>
                        <input type="tel" name="phone_number" placeholder="Phone Number" value="<?= $current_phone_number ?>" required>
                        <input type="text" name="postal_code" placeholder="Postal Code" value="<?= $current_postal_code ?>" required>
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
                        <p id="totalGetPointsDisplay">GET 0 P</p>
                    </div>

                    <input type="hidden" name="final_getpoints" id="finalGetPoints" value="0">
                    <input type="hidden" name="final_points" id="finalPoints" value="0">
                    <input type="hidden" id="requiredPoints" value="0">
                    <input type="hidden" id="userPoints" value="<?= $user_current_points ?>">

                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side: builds list from localStorage cart and sets totals
function getCart() { return JSON.parse(localStorage.getItem('cart')) || []; }
function formatPrice(num) { return '₱' + num.toLocaleString(); }
function formatPoints(num) { return num.toLocaleString() + ' P'; }
function formatGetPoints(num) { return 'GET ' + num.toLocaleString() + ' P'; }

function validatePoints(event){
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if(paymentMethod && paymentMethod.value === 'point'){
        const required = parseInt(document.getElementById('requiredPoints').value) || 0;
        const userPoints = parseInt(document.getElementById('userPoints').value) || 0;
        if(userPoints < required){
            event.preventDefault();
            alert('You do not have enough points ('+required.toLocaleString()+' P required).');
            return false;
        }
    }
    return true;
}

document.addEventListener('DOMContentLoaded', ()=>{
    const cartList = document.getElementById('checkoutCartList');
    const form = document.getElementById('checkoutForm');
    const cart = getCart();

    if(cart.length === 0){
        cartList.innerHTML = "<p>Your cart is empty.</p>";
        return;
    }

    let totalPrice = 0, totalPoints = 0, totalGetPoints = 0;

    cart.forEach(item=>{
        const qty = item.qty ? parseInt(item.qty) : 1;
        const price = parseFloat((item.price+"").replace(/[₱,]/g,'')) || 0;
        const points = parseInt((item.points+"").replace(/[^\d]/g,'')) || 0;
        const getPoints = parseInt((item.getpoints+"").replace(/[^\d]/g,'')) || 0;

        totalPrice += price*qty;
        totalPoints += points*qty;
        totalGetPoints += getPoints*qty;

        // hidden inputs for each item
        form.insertAdjacentHTML('beforeend',`
            <input type="hidden" name="product_id[]" value="${item.id}">
            <input type="hidden" name="quantity[]" value="${qty}">
            <input type="hidden" name="memory[]" value="${item.memory}">
        `);

        cartList.insertAdjacentHTML('beforeend',`
            <div class="cart-item">
                <img src="${item.img}" alt="${item.name}" class="buy-img">
                <p><strong>${item.name} (${item.memory}GB)</strong></p>
                <p>${formatPrice(price*qty)} | ${formatGetPoints(getPoints*qty)} | ${formatPoints(points*qty)}</p>
            </div>
        `);
    });

    document.getElementById('totalPriceDisplay').textContent = formatPrice(totalPrice);
    document.getElementById('totalPointsDisplay').textContent = formatPoints(totalPoints);
    document.getElementById('totalGetPointsDisplay').textContent = formatGetPoints(totalGetPoints);
    document.getElementById('finalGetPoints').value = totalGetPoints;
    document.getElementById('finalPoints').value = totalPoints;
    document.getElementById('requiredPoints').value = totalPoints;
});

// Clear cart after redirect to order.php is handled elsewhere (your existing script)
// Optionally uncomment to clear when navigating to order.php
// if(window.location.href.includes("order.php")){ localStorage.removeItem('cart'); }
</script>

<script src="buynow.js"></script>
</body>
</html>

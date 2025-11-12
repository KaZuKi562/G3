<?php
include "db_connect.php";
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$user_current_points = 0;
$current_address_detail = '';
$current_postal_code = '';
$current_phone_number = '';

if ($user_id) {
    $sql_fetch = "SELECT user_address, user_number, getpoints FROM account WHERE user_id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $user_id);
    $stmt_fetch->execute();
    $result_fetch = $stmt_fetch->get_result();
    $user_data = $result_fetch->fetch_assoc();
    $stmt_fetch->close();

    if ($user_data) {
        $user_current_points = intval($user_data['getpoints']);
        $full_address = $user_data['user_address'];
        $current_phone_number = htmlspecialchars($user_data['user_number']);

        if (preg_match('/^(.*)\s\((?:Postal Code|PC):\s*(\w+)\)$/i', $full_address, $matches)) {
            $current_address_detail = htmlspecialchars(trim($matches[1]));
            $current_postal_code = htmlspecialchars($matches[2]);
        } else {
            $current_address_detail = htmlspecialchars($full_address);
        }
    }
}

$message = '';
$message_type = '';
$checkout_success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $points_to_get = isset($_POST['final_getpoints']) ? intval($_POST['final_getpoints']) : 0;
    $points_to_use = isset($_POST['final_points']) ? intval($_POST['final_points']) : 0;
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $address_detail = htmlspecialchars($_POST['address_detail']);
    $postal_code = htmlspecialchars($_POST['postal_code']);
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $full_address = $address_detail . ' (Postal Code: ' . $postal_code . ')';

    if (!empty($_POST['product_id']) && is_array($_POST['product_id'])) {
        $product_ids = $_POST['product_id'];
        $quantities = $_POST['quantity'];
        $memories = $_POST['memory'];

        for ($i = 0; $i < count($product_ids); $i++) {
            $pid = intval($product_ids[$i]);
            $qty = intval($quantities[$i]);
            $mem = htmlspecialchars($memories[$i]);

            $sql = "SELECT name, price FROM products WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                $pname = $product['name'];
                $price = floatval(str_replace([',','₱'], '', $product['price'])); // Clean price

                $order_sql = "INSERT INTO orders (user_id, product_id, product_name, product_price, quantity, selected_memory, payment_method, address, phone_number, status, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())";
                $stmt_order = $conn->prepare($order_sql);
                $stmt_order->bind_param("iisdiisss", $user_id, $pid, $pname, $price, $qty, $mem, $payment_method, $full_address, $phone_number);
                $stmt_order->execute();
                $stmt_order->close();
            }
        }
    } else {
        $message = "No products selected.";
        $message_type = "error";
        header("Location: order.php?status=" . $message_type . "&message=" . urlencode($message));
        exit();
    }

    // Points or cash payment handling
    if ($payment_method === 'point') {
        if ($user_current_points >= $points_to_use && $points_to_use > 0) {
            $new_points_balance = $user_current_points - $points_to_use;
            $sql = "UPDATE account SET getpoints = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_points_balance, $user_id);
            $stmt->execute();
            $stmt->close();
            $message = "Order placed successfully! Points used: " . number_format($points_to_use) . ".";
            $checkout_success = true;
            $message_type = "success";
        } else {
            $message = "You do not have enough points to complete this purchase.";
            $message_type = "error";
        }
    } else {
        if ($points_to_get > 0) {
            $new_points_balance = $user_current_points + $points_to_get;
            $sql = "UPDATE account SET getpoints = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $new_points_balance, $user_id);
            $stmt->execute();
            $stmt->close();
            $message = "Order placed successfully! You earned " . number_format($points_to_get) . " points.";
        } else {
            $message = "Order placed successfully!";
        }
        $checkout_success = true;
        $message_type = "success";
    }

    // Update address & phone
    if ($checkout_success || $payment_method !== 'point') {
        $sql = "UPDATE account SET user_address = ?, user_number = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $full_address, $phone_number, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: order.php?status=" . $message_type . "&message=" . urlencode($message));
    exit();
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
        </a>
        <a href="user.php">
            <img src="icon/user.png" alt="User" class="icon">
        </a>
    </div>
</div>

<div class="cart-modal" id="cartModal">
    <div class="cart-popup">
        <button class="close-btn" id="closeCart">&times;</button>
        <div id="cartItems"></div>
        <div class="cart-summary" id="cartSummary"></div>
        <button class="checkout-btn">Checkout</button>
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
                    <div id="shippingFields" class="shippingFields">
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
function getCart() { return JSON.parse(localStorage.getItem('cart')) || []; }
function formatPrice(num) { return '₱' + num.toLocaleString(); }
function formatPoints(num) { return num.toLocaleString() + ' P'; }
function formatGetPoints(num) { return 'GET ' + num.toLocaleString() + ' P'; }

function validatePoints(event){
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
    if(paymentMethod && paymentMethod.value === 'point'){
        const required = parseInt(document.getElementById('requiredPoints').value);
        const userPoints = parseInt(document.getElementById('userPoints').value);
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
        const price = parseFloat(item.price.replace(/[₱,]/g,''));
        const points = parseInt(item.points.replace(' P','').replace(/,/g,''));
        const getPoints = parseInt(item.getpoints.replace('GET ','').replace(' P','').replace(/,/g,''));
        totalPrice += price*qty;
        totalPoints += points*qty;
        totalGetPoints += getPoints*qty;

        // hidden inputs
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

if(window.location.href.includes("order.php")){
    localStorage.removeItem('cart');
}
</script>

<script src="buynow.js"></script>

</body>
</html>

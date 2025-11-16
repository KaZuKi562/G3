<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ----------------------------------
    FETCH USER DETAILS
---------------------------------- */
$sqlUser = $conn->prepare("SELECT user_address, user_number, postal_code, getpoints FROM account WHERE user_id = ?");
$sqlUser->bind_param("i", $user_id);
$sqlUser->execute();
$userData = $sqlUser->get_result()->fetch_assoc();

$user_points = intval($userData['getpoints']);
$user_address = $userData['user_address'];
$user_number = $userData['user_number'];
$user_postal = $userData['postal_code'];

/* ----------------------------------
    FETCH PRODUCT
---------------------------------- */
if (!isset($_GET['product_id'])) {
    die("Product not found.");
}

$product_id = intval($_GET['product_id']);

$sql = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$sql->bind_param("i", $product_id);
$sql->execute();
$product = $sql->get_result()->fetch_assoc();

if (!$product) {
    die("Product does not exist.");
}

$stock = intval($product['stock']);
$base_price = intval(str_replace([",", "₱"], "", $product['price']));
$base_points = intval(str_replace(["P", " ", ","], "", $product['points']));
$base_getpoints = intval(preg_replace('/[^0-9]/', '', $product['getpoints']));

/* ----------------------------------
    HANDLE ORDER SUBMIT
---------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $memory = $_POST['memory'];
    $payment_method = $_POST['payment'];
    $shipping_method = $_POST['shipping'];
    $quantity = intval($_POST['quantity']);
    $address_detail = trim($_POST['address_detail']);
    $postal_code = trim($_POST['postal_code']);
    $phone_number = trim($_POST['phone_number']);

    if ($quantity <= 0) $quantity = 1;

    // PRICE & POINTS UPDATE
    $final_price = $base_price;
    $final_points_needed = $base_points;
    $final_getpoints = $base_getpoints;

    if ($memory == "256") {
        $final_price += 7000;
        $final_points_needed += 4000;
        $final_getpoints += 4000;
    }

    $final_price *= $quantity;
    $final_points_needed *= $quantity;
    $final_getpoints *= $quantity;

    // VAT (12%)
    $vat = round($final_price * 0.12);
    $final_price_with_vat = $final_price + $vat;

    // Check points if paying with points
    if ($payment_method === "points" && $user_points < $final_points_needed) {
        $error = "You do not have enough points.";
    } else if ($quantity > $stock) {
        $error = "Not enough stock available.";
    } else {

        // --- Reduce stock ---
        $newStock = $stock - $quantity;
        $updateStock = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
        $updateStock->bind_param("ii", $newStock, $product_id);
        $updateStock->execute();

        // --- Handle points ---
        if ($payment_method === "points") {
            // Deduct user points for payment
            $newUserPoints = $user_points - $final_points_needed;
        } else {
            // Cash on Delivery, user gains points
            $newUserPoints = $user_points + $final_getpoints;
        }

        // Update account points
        $updatePoints = $conn->prepare("UPDATE account SET getpoints = ? WHERE user_id = ?");
        $updatePoints->bind_param("ii", $newUserPoints, $user_id);
        $updatePoints->execute();

        // --- Save order ---
        $insert = $conn->prepare("
            INSERT INTO orders (
                user_id, product_id, product_name, product_price, quantity,
                selected_memory, payment_method, shipping_method,
                address, phone_number, status, order_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pre order', NOW())
        ");

        $insert->bind_param(
            "iisiissssi",
            $user_id,
            $product_id,
            $product['name'],
            $final_price_with_vat,
            $quantity,
            $memory,
            $payment_method,
            $shipping_method,
            $address_detail,
            $phone_number,
        );

        $insert->execute();

        header("Location: order.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Preorder Checkout</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, sans-serif;
    background: #f5f5f7;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 900px;
    margin: 50px auto;
    background: #fff;
    padding: 32px;
    border-radius: 14px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}
.product-img {
    width: 350px;
    border-radius: 12px;
}
.row {
    display: flex;
    gap: 40px;
    flex-wrap: wrap;
}
.section { margin-top: 25px; }
.memory button {
    padding: 10px 20px;
    border: 1px solid #ccc;
    background: #fafafa;
    border-radius: 8px;
    cursor: pointer;
}
.memory button.active {
    border-color:#0071e3;
    background:#e0efff;
}
.quantity-box {
    display:flex;
    align-items:center;
    gap:10px;
    margin-top:10px;
}
.qty-btn {
    padding:5px 15px;
    border-radius:6px;
    background:#e0e0e0;
    cursor:pointer;
    font-size:18px;
    border:none;
}
.qty-number {
    font-size:18px;
    width:40px;
    text-align:center;
}
.submit-btn {
    width: 100%;
    padding: 14px;
    font-size: 18px;
    background:#0071e3;
    color:#fff;
    border:none;
    border-radius:10px;
    cursor:pointer;
}
.submit-btn:hover { background:#005bb5; }
.price { font-size: 28px; font-weight: bold; color:#0071e3; }
.user-box { background:#f0f0f5; padding:15px; border-radius:10px; }
.vat-box {
    margin-top:10px;
    padding:12px;
    background:#f7f7ff;
    border-left:4px solid #0071e3;
    font-size:15px;
}
.error { color:red; font-weight:bold; margin-bottom:10px; }
</style>

<script>
function updateAll() {
    let basePrice = <?= $base_price ?>;
    let basePoints = <?= $base_points ?>;
    let baseGetPoints = <?= $base_getpoints ?>;

    let qty = parseInt(document.getElementById("quantity").value);
    let memory = document.getElementById("memory").value;

    let finalPrice = basePrice;
    let finalPoints = basePoints;
    let finalGetPoints = baseGetPoints;

    if (memory === "256") {
        finalPrice += 7000;
        finalPoints += 4000;
        finalGetPoints += 4000;
    }

    finalPrice *= qty;
    finalPoints *= qty;
    finalGetPoints *= qty;

    let vat = Math.round(finalPrice * 0.12);
    let priceWithVAT = finalPrice + vat;

    document.getElementById("displayPrice").innerText = "₱" + priceWithVAT.toLocaleString();
    document.getElementById("displayPoints").innerText = finalPoints + " P";
    document.getElementById("displayGetPoints").innerText = finalGetPoints + " P";
    document.getElementById("vatAmount").innerText = "₱" + vat.toLocaleString();
}

function selectMemory(option) {
    document.getElementById("memory").value = option;

    document.getElementById("btn128").classList.remove("active");
    document.getElementById("btn256").classList.remove("active");
    document.getElementById("btn" + option).classList.add("active");

    updateAll();
}

function changeQty(val) {
    let qtyField = document.getElementById("quantity");
    let current = parseInt(qtyField.value);

    current += val;
    if (current < 1) current = 1;
    qtyField.value = current;

    updateAll();
}

window.onload = updateAll;
</script>
</head>

<body>
<div class="container">

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">

<div class="row">

    <div>
        <img src="<?= $product['img'] ?>" class="product-img">
    </div>

    <div>
        <h1><?= $product['name'] ?></h1>

        <?php 
        $vat_amount = round($base_price * 0.12);
        $initial_total = $base_price + $vat_amount;
        ?>
        <p class="price" id="displayPrice">₱<?= number_format($initial_total) ?></p>

        <p>Required Points: <strong id="displayPoints"><?= $base_points ?> P</strong></p>
        <p>Get Points Reward: <strong id="displayGetPoints"><?= $base_getpoints ?> P</strong></p>

        <!-- Quantity -->
        <div class="section">
            <label>Quantity</label>
            <div class="quantity-box">
                <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                <input type="text" id="quantity" name="quantity" value="1" class="qty-number" readonly>
                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
            </div>
        </div>

        <!-- Memory Options -->
        <div class="section">
            <label>Memory Options</label><br><br>
            <button id="btn128" class="active" onclick="selectMemory('128'); return false;">128GB</button>
            <button id="btn256" onclick="selectMemory('256'); return false;">256GB</button>
            <input type="hidden" id="memory" name="memory" value="128">
        </div>

        <!-- Delivery -->
        <div class="section">
            <label>Delivery Option</label><br><br>
            <input type="radio" name="shipping" value="shipping" checked> Shipping <br>
            <input type="radio" name="shipping" value="pickup"> Store Pickup
        </div>

        <!-- USER DELIVERY INFO -->
        <div class="section user-box">
            <strong>Your Delivery Info:</strong><br><br>

            <label>Address</label><br>
            <input type="text" name="address_detail" value="<?= htmlspecialchars($user_address) ?>" required><br><br>

            <label>Postal Code</label><br>
            <input type="text" name="postal_code" value="<?= htmlspecialchars($user_postal) ?>" required><br><br>

            <label>Phone Number</label><br>
            <input type="text" name="phone_number" value="<?= htmlspecialchars($user_number) ?>" required>
        </div>

        <!-- Payment -->
        <div class="section">
            <label>Payment Method</label><br><br>
            <input type="radio" name="payment" value="CashDelivery" checked> Cash on Delivery <br>
            <input type="radio" name="payment" value="points"> Use Points
        </div>

        <!-- VAT -->
        <div class="vat-box">
            <strong>VAT (12%):</strong> 
            <span id="vatAmount">₱<?= number_format($base_price * 0.12) ?></span>
        </div>

        <!-- Submit -->
        <div class="section">
            <button class="submit-btn" type="submit">Place Order</button>
        </div>

    </div>
</div>

</form>

</div>

</body>
</html>

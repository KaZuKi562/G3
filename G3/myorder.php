<?php
session_start();
include "db_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// ✅ Fetch user details
$user_sql = "SELECT username FROM account WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();
    $user = $user_result->fetch_assoc();
    $stmt->close();
} else {
    die("Error fetching user details: " . $conn->error);
}

// ✅ Fetch user's orders with LEFT JOIN to products table
$sql = "
    SELECT 
        o.order_id,
        o.product_name,
        (o.product_price * o.quantity) AS total_price,
        o.product_price,
        o.quantity,
        o.selected_memory,
        o.payment_method,
        o.status,
        o.order_date,
        COALESCE(p.img, ph.img) AS img,        -- Take product img if exists, else phone img
        COALESCE(p.brand, ph.brand) AS brand,  -- Same for brand
        COALESCE(p.getpoints, ph.getpoints) AS points
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    LEFT JOIN phone ph ON o.product_id = ph.id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();
} else {
    die("Error fetching orders: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Swastecha</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        .orders-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            margin: 20px auto;
            box-shadow: 5px 10px #919191ff;
        }
        .orders-container h2 {
            margin-bottom: 10px;
        }
        .order-card {
            border-top: 1px solid #ccc;
            padding: 20px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .order-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .order-info img {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
        }
        .order-details {
            line-height: 1.5;
        }
        .order-details strong {
            display: block;
            font-size: 18px;
        }
        .order-meta {
            text-align: right;
        }
        .order-meta p {
            margin: 5px 0;
        }
        .status {
            color: #007bff;
            font-weight: bold;
        }
        .sidebar a.active {
            font-weight: bold;
            color: #007bff;
        }
        .cancel-btn { 
            padding: 6px 12px; 
            background-color: #ff4d4d; 
            color: #fff; border: none; 
            border-radius: 5px; cursor: pointer;
             margin-top: 5px; 
            }
        .cancel-btn:disabled { 
            background-color: #ccc; 
            cursor: not-allowed; 
}
    </style>
</head>
<body>
   <div class="top-bar">
        <div class="brand">Swastecha</div>
        <div class="user-cart">
            <a href="cart.html">
                <img src="icon/cart.png" alt="Cart" class="icon">
            </a>
            <a href="user.php">
                <img src="icon/user.png" alt="User" class="icon">
            </a>
        </div>
    </div>

    <div class="main-container">
        <div class="content">
            <div class="sidebar">
                <a href="main_home.php">Home</a>
                <a href="user.php">My Account</a>
                <a href="address.php">My Address</a>
                <a href="myorder.php" >My Orders</a>
                <a href="index.php">Logout</a>
            </div>

            <div class="orders-container">
                <h2>My Orders</h2>

                <?php if ($orders && $orders->num_rows > 0): ?>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-info">
                                <img src="<?= htmlspecialchars($row['img'] ?? 'placeholder.png') ?>" alt="Product">
                                <div class="order-details">
                                    <strong><?= htmlspecialchars($row['product_name']) ?></strong>
                                    <span><?= htmlspecialchars($row['selected_memory']) ?>GB</span><br>
                                    <span>Payment: <?= htmlspecialchars($row['payment_method']) ?></span><br>
                                    <span class="status"><?= htmlspecialchars($row['status']) ?></span>
                                    <?php if ($row['status'] != 'Cancelled'): ?>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                        <input type="hidden" name="cancel_order_id" value="<?= $row['order_id'] ?>">
                                        <button type="submit" class="cancel-btn">Cancel Order</button>
                                    </form>
                                <?php else: ?>
                                     <button class="cancel-btn" disabled>Cancelled</button>
                                <?php endif; ?>
                                </div>
                            </div>
                            <div class="order-meta">
                                <p>Qty: x<?= htmlspecialchars($row['quantity']) ?></p>
                                <p>Total: ₱<?= number_format(floatval($row['total_price'])) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>
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
                <strong>Swastecha</strong>
                <ul>
                    <li>Reliable online shop to purchase phones.</li>
                </ul>
            </div>
            <div>
                <strong>Contact</strong>
                <ul>
                    <li>Email: support@swastecha.com</li>
                    <li>Phone: +63 1234-567-7898</li>
                </ul>
            </div>
            <div>
                <strong>Follow Us</strong>
                <ul>
                    <li>Facebook</li>
                    <li>Instagram</li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>

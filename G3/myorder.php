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

// ✅ Fetch user's orders with JOIN to products table for missing fields
$sql = "
    SELECT 
        o.order_id,
        o.product_name,
        (o.product_price * o.quantity) AS total_price,  -- Compute total dynamically
        o.product_price,
        o.quantity,
        o.selected_memory,
        o.payment_method,
        o.status,
        o.order_date,
        p.img,        -- From products table
        p.brand,      -- From products table
        p.getpoints AS points  -- From products table (adjust if column name differs)
    FROM orders o
    JOIN products p ON o.product_id = p.id  -- JOIN with products table
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
                <a href="myorder.php" >My Orders</a>  <!-- Added 'active' class for styling -->
                <a href="index.php">Logout</a>
            </div>


                <div class="orders-container">
                    <h2>My Orders</h2>

                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <?php while ($row = $orders->fetch_assoc()): ?>
                            <div class="order-card">
                                <div class="order-info">
                                    <img src="<?php echo htmlspecialchars($row['img'] ?? ''); ?>" alt="Product">  <!-- Added fallback for missing img -->
                                    <div class="order-details">
                                        <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                                        <span><?php echo htmlspecialchars($row['brand'] ?? ''); ?></span><br>  <!-- Added fallback -->
                                        <span><?php echo htmlspecialchars($row['selected_memory']); ?>GB</span><br>  <!-- Added 'GB' for clarity -->
                                        <span>Payment: <?php echo htmlspecialchars($row['payment_method']); ?></span><br>
                                        <span class="status"><?php echo htmlspecialchars($row['status']); ?></span>
                                    </div>
                                </div>
                                <div class="order-meta">
                                    <p>Qty: x<?php echo htmlspecialchars($row['quantity']); ?></p>
                                    <p>Total: ₱<?php echo number_format($row['total_price']); ?></p>
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

<?php
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$user = null;
if ($user_id) {
    include "db_connect.php";
    
    $sql = "SELECT user_id, username, email, user_address, getpoints FROM account WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
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
                <a href="myorder.php">My Orders</a>
                <a href="index.php">Logout</a>
        </div>
    </aside>

<div class="main-content">
    <h2>My Account</h2>
    <hr>
    <?php if ($user): ?>
      <p style="font-size: 30px;">Hi <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
      <p style="font-size: 20px;">Available Points: <strong><?php echo number_format($user['getpoints']); ?></strong></p>
    <?php elseif (is_null($user_id)): ?>
      <p>You are not logged in.</p>
    <?php else: ?>
      <p>User not found.</p>
    <?php endif; ?>
    <br>
    <br>
    <hr>
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
<script>
document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
  cb.addEventListener('change', () => {
    document.getElementById('filterForm').submit();
  });
});
</script>

<script src="main.js"></script>
</body>
</html>
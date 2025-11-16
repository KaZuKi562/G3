<?php
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

include "db_connect.php";  // MOVED: Always include for consistency

$message = '';
$message_type = '';

if (!$user_id) {
    // NEW: Handle non-logged-in users
    $message = "You must be logged in to update your address.";
    $message_type = 'error';
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $phone_number = htmlspecialchars($_POST['phone_number']);
        $address_detail = htmlspecialchars($_POST['address_detail']);
        $postal_code = htmlspecialchars($_POST['postal_code']);

        // Prepare the SQL UPDATE statement (FIXED: Added comma)
        $sql = "UPDATE account SET user_address = ?, postal_code = ?, user_number = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssi", $address_detail, $postal_code, $phone_number, $user_id);
            
            if ($stmt->execute()) {
                $message = "Address updated successfully!";
                $message_type = 'success';
                header("Location: address.php?status=success");
                exit();
            } else {
                $message = "Error updating address: " . $stmt->error;
                $message_type = 'error';
            }
            $stmt->close();
        } else {
            $message = "SQL Prepare Error: " . $conn->error;
            $message_type = 'error';
        }
    }
}

$conn->close();  // MOVED: Always close at the end
?>

<!-- Rest of HTML remains unchanged -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>Adding address - Swastecha</title>
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
   <div class="top-bar">
        <div class="brand">Swastecha</div>
       <div class="user-cart">
            <a href="cart.html">
                <img src="icon/cart.png" alt="Cart" class="icon">
            </a>
            <a href="user.html">
                <img src="icon/user.png" alt="User" class="icon">
            </a>
        </div>
    </div>
    
    <div class="main-container">
        <div class="content">


            <div class="main-content">
                <h2>Add new address</h2>
                
                <?php if ($message): ?>
                    <p class="form-message <?php echo $message_type; ?>"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST" action="AddAddress.php" class="address-form">
                    <div class="form-group">
                        <input type="tel" id="phone_number" name="phone_number" placeholder="Phone number" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="address_detail" name="address_detail" placeholder="Address detail" required>
                    </div>
                    <div class="form-group">
                        <input type="text" id="postal_code" name="postal_code" placeholder="Postal code" required>
                    </div>

                    <button type="submit" class="save-btn">Save</button>
                </form>
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
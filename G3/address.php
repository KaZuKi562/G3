<?php
// Database connection
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$user = null;
if ($user_id) {
    include "db_connect.php";

$sql = "SELECT user_id, username, email, user_address, user_number, getpoints FROM account WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

// ... (rest of DB connection and fetch) ...

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

// --- PHP LOGIC: Check if the user already has an address saved ---
$has_address = false;
if ($user && !empty(trim($user['user_address']))) {
    $has_address = true;
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <title>Swastecha Points Redemption Store</title>
    <link rel="stylesheet" href="styles.css?v=2">
    <style>
        /* Modal Backdrop */
        .modal-backdrop {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000; /* Ensure it's on top of everything */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        /* Modal Content Box */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* Center vertically */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be adjusted */
            max-width: 400px; /* Fixed maximum width */
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .modal-content h3 {
            color: #d8000c;
            margin-top: 0;
        }

        /* OK Button Style */
        .modal-ok-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <div class="brand">Swastecha</div>
       <div class="user-cart">
            <a href="">
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
                <a href="orders.php">My Orders</a>
                <a href="reset-password.php">Reset Password</a>
                <a href="logout.php">Logout</a>
            </div>

            <div class="main-content">
                <div class="main-header">
                    <h2>My address</h2>
                    <?php if ($has_address): ?>
                        <button class="add-address-btn" onclick="showWarningModal()">Add address</button>
                    <?php else: ?>
                        <a href="AddAddress.php" class="add-address-btn">Add address</a>
                    <?php endif; ?>
                </div>
                <hr>
                <?php if ($user): ?>
            <div class="address-item">
            <p style="font-size: 25px;"><strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
             <p tyle="font-size: 20px;"><strong>Phone number: </strong> <?php echo htmlspecialchars($user['user_number']); ?></p>
            <p tyle="font-size: 20px;"><strong>Address: </strong><?php echo htmlspecialchars($user['user_address']); ?></p>

            <div class="button-container">
                <button class="edit-btn" onclick="window.location.href='editAddress.php';">Edit</button>
                <a href="delete.php?user_id=<?php echo $user_id;?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this Address   ?');">Delete</a>
                
            </div> 
        </div>
    <?php else: ?> 
      <p>User not found.</p>
    <?php endif; ?>
                </div>
        </div>
    </div>
    
    <div id="warningModal" class="modal-backdrop" style="display: none;">
        <div class="modal-content">
            <h3>Warning</h3>
            <p>You already have an address, cannot add a new one</p>
            <button class="modal-ok-btn" onclick="closeWarningModal()">OK</button>
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
                <strong>3 Group</strong>
                <ul>
                    <li>Customer Support</li>
                    <li>Store Locations</li>
                    <li>Terms of Service</li>
                    <li>Refund Policy</li>
                    <li>Corporate Sales</li>
                    <li>Contact Us</li>
                </ul>
            </div>
            <div>
                <strong>Policies</strong>
                <ul>
                    <li>Privacy Policy</li>
                    <li>Terms and Condition</li>
                    <li>Return and Exchange Policy</li>
                    <li>FAQs</li>
                    <li>Do not sell my personal information</li>
                </ul>
            </div>
            <div>
                <strong>Product Categories</strong>
                <ul>
                    <li>Cellphone</li>
                    <li>Tablet</li>
                    <li>Headset</li>
                    <li>Laptop</li>
                    <li>Corporate Sales</li>
                </ul>
            </div>
        </div>
    </footer>
<script>
    // Function to display the modal
    function showWarningModal() {
        document.getElementById('warningModal').style.display = 'flex';
    }

    // Function to hide the modal (called by the OK button)
    function closeWarningModal() {
        document.getElementById('warningModal').style.display = 'none';
    }

    // Existing script (keep this)
    document.querySelectorAll('#filterForm input[type="checkbox"]').forEach(cb => {
      cb.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
      });
    });
    </script>
    
    <script src="main.js"></script>
</body>
</html>
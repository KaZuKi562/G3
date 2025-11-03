<?php
include 'db_connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email already exists in users table
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "Email already exists!";
    } else {
        // Begin transaction to ensure both inserts succeed together
        $conn->begin_transaction();

        try {
            // 1️⃣ Insert into users table
            $stmt1 = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt1->bind_param("sss", $username, $email, $password);
            $stmt1->execute();

            // 2️⃣ Insert into account table
            $stmt2 = $conn->prepare("INSERT INTO account (username, email, password) VALUES (?, ?, ?)");
            $stmt2->bind_param("sss", $username, $email, $password);
            $stmt2->execute();

            // If both succeed, commit
            $conn->commit();

            // Close statements
            $stmt1->close();
            $stmt2->close();

            // Redirect to login
            header("Location: login.php");
            exit();
        } catch (Exception $e) {
            // Rollback if there’s any error
            $conn->rollback();
            $message = "Error occurred during registration: " . $e->getMessage();
        }
    }

    $check->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create your account</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
<div class="form-container">
    <h2>Create your account</h2>
    <p>Please fill in the information below</p>
    <?php if ($message): ?>
        <div class="error"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign up</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in here</a></p>
</div>
</body>
</html>
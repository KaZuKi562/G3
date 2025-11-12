<?php
include 'db_connect.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ✅ Check for admin login first
    if ($email === 'admin12@gmail.com' && $password === '12345') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['username'] = 'Administrator';
        header("Location: admin.php"); // redirect to admin page
        exit;
    }

    // Regular user login
    $stmt = $conn->prepare("SELECT user_id, username, password FROM account WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            // Store user_id
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: main_home.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No account found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login my account</title>
    <link rel="stylesheet" href="account.css">
</head>
<body>
<div class="form-container">
    <h2>Login my account</h2>
    <p>Enter your email and password</p>

    <form method="POST" action="login.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Sign in</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <p>New customer? <a href="signup.php">Create your account</a></p>
</div>
</body>
</html>
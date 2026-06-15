<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['name'];
header("Location: index.php");
exit;
        } else {
            echo "Wrong password!";
        }
    } else {
        echo "User not found!";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f6fa;
        }

        .login-card {
            width: 350px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            background: white;
            text-align: center;
        }

        .logo {
            width: 80px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="login-card">

    <!-- LOGO -->
    <img src="uploads/logo.png" class="logo">

    <h4 class="mb-3">Login</h4>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" class="form-control mb-3" required>

        <input type="password" name="password" placeholder="Password" class="form-control mb-3" required>

        <button type="submit" class="btn btn-dark w-100">Login</button>

        <p class="mt-3">Don't have an account? <a href="signup.php">Signup</a></p>
    </form>

</div>

</body>
</html>

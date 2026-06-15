<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password) 
            VALUES ('$name', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f5f6fa;
        }

        .signup-card {
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

<div class="signup-card">

    <!-- LOGO -->
    <img src="uploads/logo.png" class="logo">

    <h4 class="mb-3">Signup</h4>

    <form method="POST">

        <input type="text" name="name" placeholder="Name" class="form-control mb-3" required>

        <input type="email" name="email" placeholder="Email" class="form-control mb-3" required>

        <input type="password" name="password" placeholder="Password" class="form-control mb-3" required>

        <button type="submit" class="btn btn-dark w-100">Signup</button>

    </form>

    <p class="mt-3">
        Already have an account? <a href="login.php">Login</a>
    </p>

</div>

</body>
</html>
<?php
session_start();
include "../db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND role='admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Wrong password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-dark {
            background: #6366f1;
            border: none;
        }

        .btn-dark:hover {
            background: #4f46e5;
        }
    </style>
</head>

<body>

<div class="login-card">

    <!-- LOGO -->
    <div class="logo">
        <i class="fa fa-store"></i> MyStore Admin
    </div>

    <div class="subtitle">Login to manage your store</div>

    <!-- ERROR -->
    <?php if ($error != "") { ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php } ?>

    <!-- FORM -->
    <form method="POST">

        <input type="email" name="email" 
               class="form-control mb-3" 
               placeholder="Enter email" required>

        <input type="password" name="password" 
               class="form-control mb-3" 
               placeholder="Enter password" required>

        <button type="submit" class="btn btn-dark w-100">
            Login
        </button>

    </form>

</div>

</body>
</html>
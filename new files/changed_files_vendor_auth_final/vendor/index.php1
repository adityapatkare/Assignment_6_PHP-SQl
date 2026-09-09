<?php
session_start();
include "../db.php";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    unset($_SESSION['vendor_id']);

    $sql = "SELECT u.* FROM users u
            JOIN vendors v ON v.user_id = u.id
            WHERE u.email='$email'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['vendor_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Vendor Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: #0f172a;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-container {
            height: 100vh;
        }

        .login-card {
            background: #020617;
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        .form-control {
            background: #020617;
            border: 1px solid #334155;
            color: #fff;
        }

        .form-control:focus {
            background: #020617;
            color: #fff;
            border-color: #6366f1;
            box-shadow: none;
        }

        .btn-dark {
            background: #6366f1;
            border: none;
        }

        .btn-dark:hover {
            background: #4f46e5;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

<div class="d-flex justify-content-center align-items-center login-container">

    <div class="login-card text-center">

        <!-- LOGO -->
        <div class="logo">
            🛍 Vendor Panel
        </div>

        <h4 class="mb-4">Login to your account</h4>

        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>

        <form method="POST">

            <div class="mb-3 text-start">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3 text-start">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button name="login" class="btn btn-dark w-100">
                Login
            </button>

        </form>

        <div class="mt-3">
            <small class="text-secondary">
                Not a vendor? Contact admin
            </small>
        </div>

    </div>

</div>

</body>
</html>

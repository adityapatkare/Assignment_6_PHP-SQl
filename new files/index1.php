<?php
session_start();
include "../db.php";
echo "<pre>"; print_r($_POST); echo "</pre>";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user   = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            if ($user['role'] === 'vendor') {

                $_SESSION['vendor_id'] = $user['id'];
                header("Location: dashboard.php");
                exit;

            } else {
                $error = "Access denied. This portal is for vendors only.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0f172a;
            color: white;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-container {
            min-height: 100vh;
        }
        .login-card {
            background: #020617;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }
        .form-control {
            background: #1e293b;
            border: 1px solid #334155;
            color: white;
        }
        .form-control:focus {
            background: #1e293b;
            border-color: #6366f1;
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center align-items-center login-container">
        <div class="col-md-5">
            <div class="login-card">

                <h2 class="text-center mb-4">Vendor Login</h2>

                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <!-- ✅ action="" submits to the same page -->
                <form method="POST" action="submit">

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" name="login" value="1" class="btn btn-primary w-100">
                        Login
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
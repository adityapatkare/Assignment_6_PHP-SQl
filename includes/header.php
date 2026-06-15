<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MyStore</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f6fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .navbar {
            padding: 12px 20px;
        }

        .btn-outline-light {
    color: #ffffff !important;
    border: 1px solid #6366f1 !important;
}

.btn-outline-light:hover {
    background: #6366f1 !important;
    color: white !important;
}

.btn-outline-primary {
    color: #818cf8 !important;
    border: 1px solid #818cf8 !important;
}

.btn-outline-primary:hover {
    background: #818cf8 !important;
    color: white !important;
}
.card {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    transition: 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center" href="/ecommerce/index.php">

    <img src="/ecommerce/uploads/logo.png" 
         alt="Logo" 
         style="height:40px; margin-right:10px;">

    <!-- <span class="fw-bold">MyStore</span> -->

</a>

        <div class="ms-auto">

            <a href="/ecommerce/index.php" class="btn btn-outline-light btn-sm">Home</a>
            <a href="/ecommerce/cart.php" class="btn btn-outline-light btn-sm">Cart</a>
            <a href="/ecommerce/profile.php" class="btn btn-outline-light btn-sm">Profile</a>
            <a href="/ecommerce/contact.php" class="btn btn-outline-light btn-sm">Contact</a>

            <?php if (isset($_SESSION['user'])) { ?>
                <a href="/ecommerce/logout.php" class="btn btn-danger btn-sm">Logout</a>
            <?php } else { ?>
                <a href="/ecommerce/login.php" class="btn btn-light btn-sm">Login</a>
            <?php } ?>

        </div>
    </div>
</nav>

<div class="container mt-4">
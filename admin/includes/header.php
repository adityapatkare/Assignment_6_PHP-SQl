<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            transition: 0.3s;
        }

        /* DARK MODE */
        body.dark {
            background-color: #0f172a;
            color: #e2e8f0;
        }

        .dark .sidebar {
            background: #020617;
        }

        .dark .card {
            background: #020617;
            color: white;
        }

        .dark .sidebar a {
            color: #94a3b8;
        }

        .dark .sidebar a:hover {
            background: #1e293b;
            color: #fff;
        }

        /* LIGHT MODE */
        body.light {
            background-color: #f8fafc;
            color: #000;
        }

        .light .sidebar {
            background: #ffffff;
            border-right: 1px solid #ddd;
        }

        .light .card {
            background: #ffffff;
            color: #000;
            border: 1px solid #eee;
        }

        .light .sidebar a {
            color: #333;
        }

        .light .sidebar a:hover {
            background: #f1f5f9;
        }

        /* COMMON */
        .sidebar {
            height: 100vh;
            padding: 20px;
            position: fixed;
            width: 230px;
        }

        .sidebar h4 {
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
        }

        .btn-theme {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
        }
    </style>
</head>

<body id="body">

<!-- THEME BUTTON -->
<button id="themeBtn" class="btn btn-light btn-theme" onclick="toggleTheme()">
    <i class="fa fa-moon"></i>
</button>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>Admin Panel</h4>

    <a href="dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="products.php"><i class="fa fa-box"></i> Products</a>
    <a href="categories.php"><i class="fa fa-list"></i> Categories</a>
    <a href="vendors.php"><i class="fa fa-store"></i> Vendors</a>
    <a href="orders.php"><i class="fa fa-shopping-cart"></i> Orders</a>
    <a href="users.php"><i class="fa fa-users"></i> Users</a>
    <a href="banners.php"><i class="fa fa-image"></i> Banners</a>
    <a href="coupons.php"><i class="fa fa-ticket"></i> Coupons</a>
    <a href="reports.php"><i class="fa fa-chart-bar"></i> Reports</a>
    <a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
    <a href="../logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
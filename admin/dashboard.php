<?php
session_start();
require '../db.php';

// Check admin login
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Fetch stats
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Online Computer Store</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>

    <div class="row my-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Products</h5>
                    <p class="card-text display-4"><?= $total_products ?></p>
                    <a href="products_manage.php" class="btn btn-light">Manage Products</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text display-4"><?= $total_orders ?></p>
                    <a href="orders_manage.php" class="btn btn-light">View Orders</a>
                </div>
            </div>
        </div>
    </div>

    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

</body>
</html>

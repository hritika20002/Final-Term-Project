<?php
session_start();
require 'includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

// Fetch orders joined with user info
$stmt = $pdo->query("
    SELECT o.id, o.total_price, o.order_date, u.name, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Orders - Admin</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1>All Orders</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if (count($orders) === 0): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Total Price</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= htmlspecialchars($order['name']) ?></td>
                        <td><?= htmlspecialchars($order['email']) ?></td>
                        <td>$<?= number_format($order['total_price'], 2) ?></td>
                        <td><?= $order['order_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

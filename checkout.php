<?php
session_start();
require 'includes/db.php';


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch cart items for order summary
$stmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

if (count($cart_items) === 0) {
    $error = "Your cart is empty. Please add products before checkout.";
}

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $shipping_address = trim($_POST['shipping_address'] ?? '');

    if (empty($shipping_address)) {
        $error = "Please provide a shipping address.";
    } else {
        // Insert order
        $insert = $pdo->prepare("INSERT INTO orders (user_id, total_price, order_date) VALUES (?, ?, NOW())");
        $insert->execute([$user_id, $total_price]);

        $order_id = $pdo->lastInsertId();

        // Optionally, you could create an order_items table to store details of each item
        // For now, we just reduce stock and clear cart

        // Update product stock
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        // Clear cart
        $clear = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear->execute([$user_id]);

        $success = "Order placed successfully! Your order ID is #$order_id.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Online Computer Store</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1>Checkout</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <h4>Order Summary</h4>
        <ul class="list-group mb-4">
            <?php foreach ($cart_items as $item): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?>
                    <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                Total
                <span>$<?= number_format($total_price, 2) ?></span>
            </li>
        </ul>

        <form method="POST" style="max-width: 500px;">
            <div class="mb-3">
                <label for="shipping_address" class="form-label">Shipping Address</label>
                <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" required></textarea>
            </div>

            <button type="submit" class="btn btn-success">Place Order</button>
            <a href="cart.php" class="btn btn-secondary ms-2">Back to Cart</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

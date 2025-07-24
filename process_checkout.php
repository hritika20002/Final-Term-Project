<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];
$payment_method = $_POST['payment_method'] ?? '';

// Fetch cart items and calculate total price
$stmt = $pdo->prepare("
    SELECT c.product_id, p.price, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    header("Location: checkout.php");
    exit;
}

foreach ($items as $item) {
    $total_price = $item['price'] * $item['quantity'];
    
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, product_id, quantity, total_price, payment_method, order_date)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$user_id, $item['product_id'], $item['quantity'], $total_price, $payment_method]);
}

// Clear cart after order placed
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);

// Redirect to thank you page
header("Location: thankyou.php");
exit;

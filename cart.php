<?php
session_start();
require 'includes/db.php';
include 'includes/header2.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Handle remove request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_cart_id'])) {
    $remove_id = $_POST['remove_cart_id'];
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$remove_id, $user_id]);
    header("Location: cart.php");
    exit;
}

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, p.name, p.price, p.image AS image_url, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>


<body class="bg-light">

<div class = "d-flex justify-content-center align-items-center" style="min-height: 100vh;">


<div class="card p-4 shadow" style="min-width: 400px; max-width: 600px; width: 100%; ">
    <h3 class="text-center mb-4">Your Cart</h3>

    <?php if (empty($cart_items)): ?>
        <p class="text-center">Your cart is empty.</p>
    <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="d-flex align-items-center mb-3 border-bottom pb-2">
                <img src="pics/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 60px; height: 60px; object-fit: contain; margin-right: 15px;">
                <div class="flex-grow-1">
                    <div><strong><?= htmlspecialchars($item['name']) ?></strong></div>
                    <div>Qty: <?= $item['quantity'] ?></div>
                    <div>$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                </div>
                <div style ="padding-bottom:15%;">
                <form method="POST" class="ms-3 ">
                    <input type="hidden" name="remove_cart_id" value="<?= $item['cart_id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                </form></div>
            </div>
        <?php endforeach; ?>

        <div class="text-end mb-3">
            <strong>Total: $<?= number_format($total, 2) ?></strong>
        </div>

        <div class="d-grid gap-2 mb-2">
            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            <a href="index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    <?php endif; ?>
</div></div>
<?php include 'includes/footer.php';?>

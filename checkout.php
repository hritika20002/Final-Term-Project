<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT p.name, p.price, c.quantity
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 600px;">
            <h2 class="mb-4 text-center text-primary">Checkout</h2>

            <?php if (empty($cart_items)): ?>
                <p class="text-center">Your cart is empty.</p>
            <?php else: ?>
                <h5>Order Summary:</h5>
                <ul class="list-group mb-4">
                    <?php foreach ($cart_items as $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)
                            <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                        Total
                        <span>$<?= number_format($total, 2) ?></span>
                    </li>
                </ul>

                <form method="POST" action="process_checkout.php">
                    <div class="mb-3">
                        <label class="form-label">Select Payment Method</label>
                        <select class="form-select" name="payment_method" id="payment_method" required>
                            <option value="">Choose one</option>
                            <option value="credit">Credit Card</option>
                            <option value="debit">Debit Card</option>
                            <option value="paypal">PayPal</option>
                        </select>
                    </div>

                    <div id="card_details" class="hidden">
                        <div class="mb-3">
                            <label class="form-label">Cardholder Name</label>
                            <input type="text" class="form-control" name="card_name" placeholder="John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" name="card_number" placeholder="1111 2222 3333 4444">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" name="expiry" placeholder="MM/YY">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" name="cvv" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Place Order</button>
                    </div>
                </form>

                <!-- Back to Home button -->
                <div class="d-grid">
                    <a href="index.php" class="btn btn-secondary">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const methodSelect = document.getElementById('payment_method');
        const cardDetails = document.getElementById('card_details');

        methodSelect.addEventListener('change', function () {
            const method = this.value;
            if (method === 'credit' || method === 'debit') {
                cardDetails.classList.remove('hidden');
            } else {
                cardDetails.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

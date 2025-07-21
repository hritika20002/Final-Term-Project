<?php
session_start();
require 'db.php';  

// Redirect to login if user not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// If the user submitted the form to update quantities or remove an item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Loop through all quantities submitted and update if valid
        foreach ($_POST['quantities'] as $cart_id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) $qty = 1;

            // Check the stock availability of the product
            $stmt = $pdo->prepare("SELECT p.stock FROM products p JOIN cart c ON p.id = c.product_id WHERE c.id = ? AND c.user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            $stock = $stmt->fetchColumn();

            // Only update if requested quantity is available
            if ($stock !== false && $qty <= $stock) {
                $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $update->execute([$qty, $cart_id, $user_id]);
            }
        }
        $message = "Cart updated successfully.";
    } elseif (isset($_POST['remove'])) {
        // Remove item from cart
        $cart_id = (int)$_POST['remove'];
        $delete = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $delete->execute([$cart_id, $user_id]);
        $message = "Item removed from cart.";
    }
}

// Fetch all cart items for the user
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image_url, c.quantity, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart - Online Computer Store</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1>Your Shopping Cart</h1>

    <?php if (isset($message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty. <a href="products.php">Shop now</a></p>
    <?php else: ?>
        <form method="POST">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th style="width:120px;">Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="height:50px; width:50px; object-fit:contain;">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>$<?= $item['price'] ?></td>
                            <td>
                                <input
                                  type="number"
                                  name="quantities[<?= $item['cart_id'] ?>]"
                                  value="<?= $item['quantity'] ?>"
                                  min="1"
                                  max="<?= $item['stock'] ?>"
                                  class="form-control"
                                  style="width:80px;"
                                >
                            </td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                            <td>
                                <button type="submit" name="remove" value="<?= $item['cart_id'] ?>" class="btn btn-sm btn-danger">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2"><strong>$<?= number_format($total_price, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <button type="submit" name="update" class="btn btn-primary">Update Cart</button>
            <a href="checkout.php" class="btn btn-success ms-3">Proceed to Checkout</a>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

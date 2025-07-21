<?php
session_start();
require 'db.php';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Fetch product from DB
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit;
}

// Handle Add to Cart form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user'])) {
        // Redirect to login if not logged in
        header("Location: login.php");
        exit;
    }

    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($quantity < 1) $quantity = 1;

    // Check stock availability
    if ($quantity > $product['stock']) {
        $error = "Requested quantity exceeds available stock.";
    } else {
        // Add to cart logic: insert or update cart table

        // Check if product already in cart for this user
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user']['id'], $product_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update quantity
            $new_qty = $cart_item['quantity'] + $quantity;
            if ($new_qty > $product['stock']) {
                $error = "Total quantity exceeds stock.";
            } else {
                $update = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                $update->execute([$new_qty, $cart_item['id']]);
                $success = "Cart updated successfully.";
            }
        } else {
            // Insert new cart record
            $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$_SESSION['user']['id'], $product_id, $quantity]);
            $success = "Product added to cart.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($product['name']) ?> - Online Computer Store</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <a href="products.php" class="btn btn-secondary mb-3">&laquo; Back to Products</a>

    <div class="row">
        <div class="col-md-6">
            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" style="max-height:400px; object-fit:contain;">
        </div>
        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p><strong>Category:</strong> <?= htmlspecialchars(ucwords($product['category'])) ?></p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <h4>$<?= $product['price'] ?></h4>
            <p><strong>Stock:</strong> <?= $product['stock'] ?></p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php elseif (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" class="mt-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width:100px;">
                <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>

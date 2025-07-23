<?php
session_start();
require 'includes/db.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);
    $category = trim($_POST['category']);
    $stock = (int)$_POST['stock'];

    if ($name && $price >= 0 && $stock >= 0) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ?, category = ?, stock = ? WHERE id = ?");
        $stmt->execute([$name, $description, $price, $image_url, $category, $stock, $product_id]);
        $success = "Product updated successfully.";
        // Refresh product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - Admin</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1>Edit Product</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:600px;">
        <div class="mb-3">
            <label>Name*</label>
            <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label>Price* (e.g., 299.99)</label>
            <input type="number" name="price" step="0.01" min="0" required class="form-control" value="<?= $product['price'] ?>">
        </div>
        <div class="mb-3">
            <label>Image URL</label>
            <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($product['image_url']) ?>">
        </div>
        <div class="mb-3">
            <label>Category*</label>
            <input type="text" name="category" required class="form-control" value="<?= htmlspecialchars($product['category']) ?>">
        </div>
        <div class="mb-3">
            <label>Stock*</label>
            <input type="number" name="stock" min="0" required class="form-control" value="<?= $product['stock'] ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
</body>
</html>

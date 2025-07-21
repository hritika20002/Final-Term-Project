<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
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
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_url, category, stock) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image_url, $category, $stock]);
        $success = "Product added successfully.";
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Admin</title>
    <link href="../css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1>Add Product</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" style="max-width:600px;">
        <div class="mb-3">
            <label>Name*</label>
            <input type="text" name="name" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label>Price* (e.g., 299.99)</label>
            <input type="number" name="price" step="0.01" min="0" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Image URL</label>
            <input type="text" name="image_url" class="form-control" placeholder="http://example.com/image.jpg">
        </div>
        <div class="mb-3">
            <label>Category*</label>
            <input type="text" name="category" required class="form-control" placeholder="e.g., laptops, desktops">
        </div>
        <div class="mb-3">
            <label>Stock*</label>
            <input type="number" name="stock" min="0" required class="form-control" value="0">
        </div>
        <button type="submit" class="btn btn-success">Add Product</button>
    </form>
</div>
</body>
</html>

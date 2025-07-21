<?php
session_start();
require 'db.php';

// Get selected category from URL if exists
$category = $_GET['category'] ?? '';

// Fetch distinct categories for filter dropdown
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Prepare product query with optional category filter
if ($category && in_array($category, $categories)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
    $stmt->execute([$category]);
} else {
    $category = '';
    $stmt = $pdo->query("SELECT * FROM products");
}
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Products - Online Computer Store</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1>All Products</h1>

    <!-- Navigation -->
    <nav class="mb-4">
        <a href="index.php" class="btn btn-outline-primary me-2">Home</a>

        <?php if (isset($_SESSION['user'])): ?>
            <span class="me-3">Hello, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong></span>
            <a href="logout.php" class="btn btn-outline-danger me-2">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-success me-2">Login</a>
            <a href="register.php" class="btn btn-outline-secondary me-2">Register</a>
        <?php endif; ?>

        <!-- Placeholder Cart link -->
        <a href="cart.php" class="btn btn-outline-warning">Cart</a>
    </nav>

    <!-- Category Filter -->
    <form method="GET" class="mb-4">
        <label for="category" class="form-label">Filter by Category:</label>
        <select id="category" name="category" class="form-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $cat === $category ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucwords($cat)) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- Products Grid -->
    <div class="row">
        <?php if (count($products) === 0): ?>
            <p>No products found in this category.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="card-img-top" style="height:200px;object-fit:contain;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text">$<?= $product['price'] ?></p>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

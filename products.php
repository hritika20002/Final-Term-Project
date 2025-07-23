<?php
session_start();
require 'includes/db.php';

// Get selected category from URL if any
$category = $_GET['category'] ?? '';

// Fetch all distinct categories for dropdown
$stmt = $pdo->query("SELECT DISTINCT category FROM products");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Prepare query based on category filter
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
    <title>Browse Products - Computer Store</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .product-card {
            margin-bottom: 30px;
        }
        .product-img {
            height: 250px;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-light">

<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <h1 class="mb-4">Products</h1>

    <!-- Category Filter -->
    <form method="GET" class="mb-4">
        <label for="category" class="form-label">Filter by Category:</label>
        <select id="category" name="category" class="form-select w-auto d-inline-block" onchange="this.form.submit()">
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
        <?php if (empty($products)): ?>
            <p>No products found in this category.</p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 product-card">
                    <div class="card shadow-sm h-100">
                        <img src="pics/<?= htmlspecialchars($product['image']) ?>" 
                             class="card-img-top product-img" 
                             alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-success fw-bold">$<?= number_format($product['price'], 2) ?></p>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary mt-auto">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>

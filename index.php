<?php
session_start();
include 'includes/header.php';
require 'includes/db.php';

// Fetch latest 6 products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 6");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online Computer Store - Home</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4">Welcome to the Online Computer Store</h1>

    <?php if (isset($_SESSION['user'])): ?>
        <p>Hello, <strong><?= htmlspecialchars($_SESSION['user']['name']) ?></strong> |
            <a href="logout.php">Logout</a>
        </p>
    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="register.php">Register</a></p>
    <?php endif; ?>

    <h3 class="mt-5 mb-3">Featured Products</h3>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="pics/<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height:200px;object-fit:contain;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">$<?= number_format($product['price'], 2) ?></p>
                        <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-secondary me-2">Browse All Products</a>
        <a href="cart.php" class="btn btn-success">View Cart</a>
    </div>
</div>

</body>
</html>

<?php include 'includes/footer.php'; ?>

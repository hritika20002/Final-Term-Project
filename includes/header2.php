<?php
echo '<link rel="stylesheet" href="./css/bootstrap.css" />';
echo '<link rel="stylesheet" href="./css/style.css" />';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db.php';

$cart_count = 0;
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user']['id']]);
    $cart_count = $stmt->fetchColumn() ?? 0;
}
?>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Computer Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        Cart <?php if ($cart_count > 0) echo "($cart_count)"; ?>
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div style="margin-top: 70px;"></div>
<script src="/js/bootstrap.bundle.min.js"></script>

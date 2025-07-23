<?php
session_start();
include 'includes/header.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<div class="container py-5 text-center">
    <h1 class="mb-4 text-success">Thank You for Your Order!</h1>
    <p>Your order has been placed successfully.</p>
    <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
</div>

<?php include 'includes/footer.php';  ?>

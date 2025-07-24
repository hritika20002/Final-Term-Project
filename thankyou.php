<?php
session_start();
include 'includes/header2.php';
?>

<div class="container py-5 text-center">
    <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
        <h2 class="text-success mb-3">Thank You for Your Purchase!</h2>
        <p>Your order has been successfully placed.</p>
        <div class="d-grid gap-2 mt-4">
            <a href="index.php" class="btn btn-primary">Return to Home</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

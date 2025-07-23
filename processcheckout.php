<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Here you would normally validate payment info, save order, charge card, etc.
// For now, let's just simulate a successful order.


// Clear the user's cart after order placed (optional)
$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$user_id]);

// Redirect to thank you page
header("Location: thankyou.php");
exit;

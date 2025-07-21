<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product_id = (int)$_GET['id'];

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

header("Location: products_manage.php");
exit;
?>

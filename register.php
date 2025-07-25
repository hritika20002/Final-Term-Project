<?php
$title = 'Register';
session_start();
require 'includes/db.php';
include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($insert->execute([$name, $email, $hash])) {
                $success = "Registration successful. <a href='login.php'>Login here</a>";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<body class="bg-light">

<div class = "d-flex justify-content-center align-items-center" style="min-height: 100vh;">
<div class="card p-4 shadow" style="min-width: 400px; max-width: 600px; width: 100%; ">
    <h2 class="text-center mb-4">Create a New Account</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" id="name" name="name" required class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" required class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>

        <div class="mb-3">
            <label for="confirm" class="form-label">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
        <p class="mt-3 text-center">Already have an account? <a href="login.php">Login here</a></p>
  <div class="d-grid mt-3">
    <a href="index.php" class="btn btn-primary">Back to Home</a>
</div>

    </form>
    </div> </div>
    <?php include 'includes/footer.php';?>

<?php
session_start();
// upload.php

include '../includes/db.php';
$db = new Database();
$pdo = $db->getConnection();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to get the user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start a session
        $_SESSION['username'] = $user['name'];
        echo "Login successful!";
        // Redirect to a protected page or dashboard
        header("location: ../index.php");
    } else {
        // Invalid credentials
        echo "Invalid email or password.";
    }
}
?>

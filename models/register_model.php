<?php
session_start();
// upload.php

include '../includes/db.php';
$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone= $_POST['phone'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];
    $password1 = $_POST['password1'];
    $repassword1 = $_POST['repassword1'];

    if ($password !== $repassword) {
        die("Passwords do not match.");
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    $passwordHash1 = password_hash($password1, PASSWORD_BCRYPT);

    if ($role == 'employee') {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, password) VALUES (:name, :email, :phone, :role, :password)");
        $stmt->execute(['name' => $name, 'email' => $email, 'phone'=>$phone, 'role' => $role, 'password' => $passwordHash]);
    } else if ($role == 'employer') {
        $company = $_POST['company'];
        $location = $_POST['location'];
        $description = $_POST['description'];

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, role, company, location, description, password) VALUES (:name, :email, :phone, :role, :company, :location, :description, :password1)");
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'company' => $company,
            'location' => $location,
            'description' => $description,
            'password1' => $passwordHash1
        ]);
    }

    $_SESSION["username"] = $name;
    echo "Registration successful!";
    header("location:../login.php");
}
?>
<?php
// upload.php

include 'includes/db.php';

$uploadDir = 'uploads/';
$uploadedFiles = [];
$db = new Database();
$conn = $db->getConnection();

// Assuming user ID is retrieved from session or authentication
$userId = 1;


foreach ($_FILES['resumes']['tmp_name'] as $key => $tmpName) {
    $fileName = basename($_FILES['resumes']['name'][$key]);
    $uploadFilePath = $uploadDir . $fileName;

    if (move_uploaded_file($tmpName, $uploadFilePath)) {
        $stmt = $conn->prepare("INSERT INTO resumes (user_id, file_path) VALUES (:user_id, :file_path)");
        $stmt->execute([':user_id' => $userId, ':file_path' => $uploadFilePath]);
        $uploadedFiles[]= $uploadFilePath;
    } else {
        echo "Failed to upload file: $fileName";
    }
}

if (!empty($uploadedFiles)) {
    $_SESSION['uploaded_files']=$uploadFilePath;
    // Redirect to processing page with uploaded file paths
    header('Location: process.php');
}
?>

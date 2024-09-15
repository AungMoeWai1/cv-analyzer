<?php
include '../includes/db.php';

$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $jobId = $_POST['id'];

    // Prepare and execute the delete statement
    $stmt1 = $pdo->prepare('DELETE FROM job_applications WHERE job_id = :id');
    $stmt1->bindParam(':id', $jobId, PDO::PARAM_INT);
    $stmt1->execute();

    // Prepare and execute the delete statement
    $stmt = $pdo->prepare('DELETE FROM jobs WHERE id = :id');
    $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirect to a success or the job list page
        header('Location: ../dash_board.php');
        exit;
    } else {
        echo 'Error deleting job.';
    }
}
?>
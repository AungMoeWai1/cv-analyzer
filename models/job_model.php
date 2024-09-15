<?php
require '../includes/db.php'; // Include your database connection file
require '../includes/job_class.php';
require '../includes/user.php';

session_start();
if (!isset($_SESSION["username"])) {
    header('location:login.php');
}
$db = new Database();
$pdo = $db->getConnection();


$name = $_SESSION["username"];
$user1 = new User($pdo);
$user = $user1->getUser($name);
$uid=$user['id'];

$job = new Job($pdo);

$data = [
    'name' => $_POST['name'],
    'location'=> $_POST['location'],
    'salary'=> $_POST['salary'],
    'campus' => $_POST['campus'],
    'posted_date' => $_POST['posted_date'],
    'closing_date' => $_POST['closing_date'],
    'job_description' => $_POST['job_description'],
    'job_requirement' => $_POST['job_requirement'],
    'benefits' => $_POST['benefits'],
    'job_type' => $_POST['job_type'],
    'uid'=>$uid,
];

$job->createJob($data);

header("Location: ../index.php"); // Redirect to the job list page
?>
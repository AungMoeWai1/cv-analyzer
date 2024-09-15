<?php
// index.php
include 'templates/header.php';
require 'includes/job_class.php';

if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
} else {
    $db = new Database();
    $pdo = $db->getConnection();
    $job1 = new Job($pdo);

    $name = $_SESSION["username"];
    $user1 = new User($pdo);
    $user = $user1->getUser($name);

    $jobs = $job1->getSpecificJobs($user['id']);
    ?>
    <style>
        .card.position-relative {
            position: relative;
        }

        .expire-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-weight: bold;
            text-align: center;
            z-index: 10;
        }
    </style>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Created Jobs</h2>
        <div class="row">
            <?php foreach ($jobs as $job):
                // Check if the job has expired
                $currentDate = new DateTime();
                $closingDate = new DateTime($job['closing_date']); // Make sure 'closing_date' is in a valid date format
                $isExpired = $closingDate < $currentDate;

                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card border-light shadow-sm">
                        <?php if ($isExpired): ?>
                            <div class="expire-indicator">Expire</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <a href="detail_job.php?id=<?= $job['id'] ?>">
                                <h5 class="card-title"><?= htmlspecialchars($job['name']) ?></h5>
                            </a>
                            <a href="company_detail.php?company=<?= urlencode($job['userID']) ?>">
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($user['company']) ?></h6>
                            </a>
                            <p class="card-text">
                                <strong>Salary:</strong> <?= htmlspecialchars($job['salary']) ?><br>
                                <strong>Working Hour:</strong> <?= htmlspecialchars($job['campus']) ?><br>
                                <strong>Location:</strong> <?= htmlspecialchars($job['location']) ?>
                            </p>
                            <div class="d-flex justify-content-between">
                                <!-- <a href="edit_job.php?id=" class="btn btn-warning">Edit</a> -->
                                <form action="models/delete_job.php" method="post" class="d-inline">
                                    <input type="hidden" name="id" value="<?= $job['id'] ?>">
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
}
include 'templates/footer.php';
?>
<script>
    document.querySelectorAll('form[action="models/delete_job.php"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!confirm('Are you sure you want to delete this job?')) {
                event.preventDefault();
            }
        });
    });
</script>
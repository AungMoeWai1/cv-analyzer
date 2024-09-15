<?php
// index.php

include 'templates/header.php';
require 'includes/job_class.php';

if (!isset($_SESSION["username"])) {
    header('location:login.php');
} else {
    $db = new Database();
    $pdo = $db->getConnection();
    $job1 = new Job($pdo);
    $search = '';

    // Determine the current page number
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $jobsPerPage = 5;
    $offset = ($page - 1) * $jobsPerPage;

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $jobs = $job1->searchJobs($search, $jobsPerPage, $offset);
    } else {
        $jobs = $job1->getJobsWithPagination($jobsPerPage, $offset);
    }

    $name = $_SESSION["username"];
    $user1 = new User($pdo);
    $user = $user1->getUser($name);
    $appledjobs = $job1->getAppledJobs($user['id']);

    ?>
    <!-- Body part -->

    <?php
    if (isset($_GET['search'])) {
        ?>
        <h2>Search Results:</h2>
        <?php
    } else {
        ?>
        <h2>Available Jobs</h2>
        <?php
    }
    ?>
    <div>
        <ol style="list-style-type:none; padding:0;">
            <?php foreach ($jobs as $job): ?>
                <li style="width:70%; margin:0 auto;">
                    <?php
                    // Get job company
                    $uid = $job['userID'];
                    $stmt1 = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                    $stmt1->execute([$uid]);
                    $userofjob = $stmt1->fetch(PDO::FETCH_ASSOC);

                    // Check if the job has expired
                    $currentDate = new DateTime();
                    $closingDate = new DateTime($job['closing_date']); // Make sure 'closing_date' is in a valid date format
            
                    if ($closingDate < $currentDate) {
                        // Skip this job if it has expired
                        continue;
                    }
                    ?>
                    <a style="text-decoration:none; color:#333;" href="detail_job.php?id=<?= $job['id'] ?>">
                        <div
                            style="border:1px solid #ddd; border-radius:8px; margin-bottom:15px; padding:20px; background-color:#f9f9f9; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); position:relative; transition:transform 0.3s;">
                            <div>
                                <a href="detail_job.php?id=<?= $job['id'] ?>"><span
                                        style="font-size:1.5em; font-weight:bold; color:#007bff;"><?= htmlspecialchars($job['name']) ?></span></a>
                                <br>
                                <a href="company_detail.php?company=<?= urlencode($job['userID']) ?>"><span
                                        style="font-size:1.2em; color:#555;"><?= htmlspecialchars($userofjob['company']) ?></span></a>
                            </div>
                            <div style="display:flex; justify-content:space-between; margin-top:10px;">
                                <p style="margin:0; color:#777;">
                                    <span><b>Salary:</b> <?= htmlspecialchars($job['salary']) ?></span>
                                </p>
                                <p style="margin:0; color:#777;">
                                    <span><b>Working Hour:</b> <?= htmlspecialchars($job['campus']) ?></span>
                                </p>
                                <p style="margin:0; color:#777;">
                                    <span><b>Location:</b> <?= htmlspecialchars($job['location']) ?></span>
                                </p>
                            </div>
                            <?php if ($user['role'] == 'employee') {
                                $count = 0;
                                foreach ($appledjobs as $ajob) {
                                    if ($job['id'] == $ajob['job_id']) {
                                        $count++;
                                    }
                                }
                                if ($count == 0) {
                                    ?>
                                    <form action="models/job_application.php" method="get" style="display:inline;">
                                        <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                                        <button type="submit"
                                            style="background:#ffc107; color:white; position:absolute; right:20px; top:20px; width:100px; height:40px; border:none; border-radius:8px; cursor:pointer; transition:background-color 0.3s;">Apply
                                            Now</button>
                                    </form>
                                    <?php
                                } else {
                                    ?>
                                    <button type="submit"
                                        style="background:#6FC276; color:white; position:absolute; right:20px; top:20px; width:100px; height:50px; border:none; border-radius:8px; cursor:pointer; transition:background-color 0.3s;"
                                        disabled>Already Applied</button>
                                    <?php
                                }
                            } ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>

    <?php
    // Get total job count for pagination
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM jobs");
    $totalJobs = $stmt2->fetchColumn();
    $totalPages = ceil($totalJobs / $jobsPerPage);
    ?>

    <!-- Pagination -->
    <div style="text-align:center; margin-top:20px;">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" style="margin-right:10px;">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" style="<?= $i == $page ? 'font-weight:bold;' : '' ?> margin-right:10px;"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Next</a>
        <?php endif; ?>
    </div>

    </body>

    </html>

    <?php
}
include 'templates/footer.php';
?>
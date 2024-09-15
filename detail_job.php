<?php
// Assuming you have a database connection file
session_start();
include 'includes/db.php';
include 'includes/user.php';
require 'includes/job_class.php';

$db = new Database();
$conn = $db->getConnection();
$job1 = new Job($conn);

if (isset($_SESSION["username"])) {
    $nameOfloggined = $_SESSION["username"];
    $userOfLoggined = new User($conn);
    $userOfLoggined = $userOfLoggined->getUser($nameOfloggined);
    $appledjobs = $job1->getAppledJobs($userOfLoggined['id']);
}

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Prepare and execute query to fetch job details by ID
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE id = :id");
    $stmt->bindParam(':id', $job_id, PDO::PARAM_INT);
    $stmt->execute();

    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if job exists
    if ($job) {
        $name = htmlspecialchars($job['name']);
        $campus = htmlspecialchars($job['campus']);
        $posted_date = htmlspecialchars($job['posted_date']);
        $closing_date = htmlspecialchars($job['closing_date']);
        $job_description = nl2br(htmlspecialchars($job['job_description']));
        $job_requirement = nl2br(htmlspecialchars($job['job_requirement']));
        $benefits = nl2br(htmlspecialchars($job['benefits']));
        $job_type = htmlspecialchars($job['job_type']);
        $salary = htmlspecialchars($job['salary']);
        $uid = htmlspecialchars($job['userID']);

        // Prepare and execute query to fetch user details by userID
        $stmt1 = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt1->bindParam(':id', $uid, PDO::PARAM_INT);
        $stmt1->execute();
        $user = $stmt1->fetch(PDO::FETCH_ASSOC);

        $company = $user['company'];

        // Prepare and execute query to fetch apply details by job_ID
        $stmt1 = $conn->prepare("SELECT * FROM job_applications WHERE job_id = :id");
        $stmt1->bindParam(':id', $job_id, PDO::PARAM_INT);
        $stmt1->execute();
        $jobapplications = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Job not found.";
        exit;
    }
} else {
    echo "Invalid job ID.";
    exit;
}

// Function to calculate TF-IDF
function calculateTFIDF($document, $corpus)
{
    $tfidf = [];
    $docTerms = array_map('strtolower', explode(' ', $document));
    $corpusTerms = array_map('strtolower', explode(' ', $corpus));

    $docTermFreq = array_count_values($docTerms);
    $totalDocTerms = count($docTerms);

    // Calculate TF
    foreach ($docTermFreq as $term => $count) {
        $tfidf[$term] = $count / $totalDocTerms;
    }

    // Calculate IDF
    $corpusTermFreq = array_count_values($corpusTerms);
    $totalCorpusTerms = count($corpusTerms);

    foreach ($tfidf as $term => $tf) {
        if (isset($corpusTermFreq[$term])) {
            $idf = log($totalCorpusTerms / $corpusTermFreq[$term]);
            $tfidf[$term] = $tf * $idf;
        } else {
            $tfidf[$term] = 0;
        }
    }

    return $tfidf;
}

// Function to calculate the match score based on term matches
function calculateMatchScore($jobCorpus, $applicantCorpus)
{
    $jobTerms = array_map('strtolower', explode(' ', $jobCorpus));
    $applicantTerms = array_map('strtolower', explode(' ', $applicantCorpus));

    $jobTermFreq = array_count_values($jobTerms);
    $applicantTermFreq = array_count_values($applicantTerms);

    $matchScore = 0;

    // Count matching terms and increase the score
    foreach ($applicantTermFreq as $term => $count) {
        if (isset($jobTermFreq[$term])) {
            $matchScore += min($count, $jobTermFreq[$term]);
        }
    }

    return $matchScore;
}

// Fetch job applications and calculate match scores
$applicationMatches = [];

foreach ($jobapplications as $jobapp) {
    $userID = htmlspecialchars($jobapp['user_id']);
    $user = new User($conn);
    $uservalue = $user->getUserByID($userID);
    $appID = htmlspecialchars($jobapp['id']);

    $certificate=$jobapp['certificate'];

    $cerValue="";

    $certificateData = json_decode($certificate, true);

    if (!empty($certificateData)) {
        foreach ($certificateData as $cert) {
            $cerValue .= htmlspecialchars($cert['name']);
        }
    }
    // Prepare applicant text
    $applicantText = $jobapp['skill'] . " " . $jobapp['experience'] . " " . $cerValue . " " . $jobapp['education'];

    // Calculate match score by comparing job and applicant terms
    $jobCorpus = $job_description . " " . $job_requirement;
    $applicantCorpus = $applicantText;

    $matchScore = calculateMatchScore($jobCorpus, $applicantCorpus);

    // Calculate the total rating for adaptability, communication, etc.
    $totalRating = $jobapp['adaptability'] + $jobapp['communication'] + $jobapp['resilience'] + $jobapp['leadership'] + $jobapp['emotional_intelligence'];

    $applicationMatches[] = [
        'application_id' => $appID,
        'user' => $uservalue,
        'match_score' => $matchScore,
        'total_rating' => $totalRating
    ];
}

// Sort by percentage in descending order
usort($applicationMatches, function ($a, $b) {
    if ($b['match_score'] === $a['match_score']) {
        return $b['total_rating'] <=> $a['total_rating'];
    }
    return $b['match_score'] <=> $a['match_score'];
});

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .job-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .job-header h1 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }

        .job-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .job-details div {
            flex: 1 1 calc(50% - 20px);
            display: flex;
            align-items: center;
        }

        .job-details label {
            font-weight: bold;
            margin-right: 10px;
            color: #555;
            min-width: 120px;
        }

        .job-details p {
            margin: 0;
            color: #333;
        }

        .job-description,
        .job-requirements,
        .job-benefits {
            margin-bottom: 40px;
        }

        .job-description h2,
        .job-requirements h2,
        .job-benefits h2 {
            font-size: 22px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .apply-button {
            display: block;
            width: 97%;
            text-align: center;
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s;
            margin-bottom: 20px;
        }

        .apply-button:hover {
            background-color: #0056b3;
        }

        .user-application {
            border-top: 2px solid #dee2e6;
            padding-top: 20px;
            margin-top: 30px;
        }

        .user-application h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }

        .user-box {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
        }

        .user-box p {
            margin: 0;
            padding: 0;
        }

        @media (max-width: 600px) {
            .job-details div {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Show detail information of job -->
    <div class="container">
        <a href="#" onclick="window.history.back(); return false;" class="apply-button">Go Back</a>
        <div class="job-header">
            <h1><?= $name ?></h1>
        </div>
        <div class="job-details">
            <div>
                <label>Salary:</label>
                <p><?= $salary ?></p>
            </div>
            <div>
                <label>Company:</label>
                <p><?= $company ?></p>
            </div>
            <div>
                <label>Working Hour:</label>
                <p><?= $campus ?></p>
            </div>
            <div>
                <label>Job Type:</label>
                <p><?= $job_type ?></p>
            </div>
            <div>
                <label>Posted Date:</label>
                <p><?= $posted_date ?></p>
            </div>
            <div>
                <label>Closing Date:</label>
                <p><?= $closing_date ?></p>
            </div>
        </div>
        <div class="job-description">
            <h2>Job Description</h2>
            <p><?= $job_description ?></p>
        </div>
        <div class="job-requirements">
            <h2>Job Requirements</h2>
            <p><?= $job_requirement ?></p>
        </div>
        <div class="job-benefits">
            <h2>Benefits</h2>
            <p><?= $benefits ?></p>
        </div>

        <!-- Check if logged-in user is an employee to show the apply now button -->
        <?php if ($userOfLoggined['role'] == 'employee') {
            $count = 0;
            foreach ($appledjobs as $ajob) {
                if ($job['id'] == $ajob['job_id']) {
                    $count++;
                }
            }
            if ($count == 0) {
                ?>
                <form action="models/job_application.php" method="get" style="display:inline;">
                    <input type="hidden" name="job_id" value="<?= $job_id ?>">
                    <button type="submit" class="apply-button"
                        style="background:#ffc107; color:white; border:none; border-radius:8px; cursor:pointer; transition:background-color 0.3s;">Apply
                        Now</button>
                </form>
                <!-- <a href="apply.php?job_id=<?= $job_id ?>" class="apply-button">Apply Now</a> -->
                <?php
            } else {
                ?>
                <button type="submit" class="apply-button"
                    style="background:#6FC276; color:white; border:none; border-radius:8px; cursor:pointer; transition:background-color 0.3s;"
                    disabled>Already Applied</button>
                <?php
            }
        } ?>
    </div>

    <!-- For emplyer view of resume rancked list --->
    <?php if ($userOfLoggined['id'] == $uid) { ?>
    <div class="container">
        <h2>Applicant Information</h2>
        <?php
        if ($applicationMatches) {
            foreach ($applicationMatches as $match) {
                $user = $match['user'];
                $match_score = $match['match_score'];

                $app_id = $match['application_id'];

                // Remove the first occurrence of '../' from the image path
                $imagePath = str_replace('../', '', $user['image'], $count);

                // Ensure only the first occurrence is removed
                if ($count > 1) {
                    $imagePath = '../' . str_replace('../', '', $user['image']);
                }

                ?>
        <div class="user-box">
            <img width="70px" height="70px" style="border-radius:30%" src="<?php echo htmlspecialchars($imagePath); ?>"
                alt="User Image">
            <p><strong>Name:</strong>
                <?php echo htmlspecialchars($user['name']); ?>
            </p>
            <p><strong>Location:</strong>
                <?php echo htmlspecialchars($user['location']); ?>
            </p>
            <p><strong>Phone Number:</strong>
                <?php echo htmlspecialchars($user['phone']); ?>
            </p>
            <p><strong>Compatibility:</strong>
                <?php echo $match_score; ?> points
            </p>
            <a href="cv_detail.php?application_id=<?php echo htmlspecialchars($app_id); ?>" class="btn btn-primary">View
                Details</a>
        </div>
        <?php
            }
        } else {
            ?>
        <p>No employees have applied.</p>
        <?php
        }
        ?>
    </div>
    <?php } ?>
</body>

</html>
<?php
session_start();
include 'includes/db.php';
include 'includes/user.php';

if (!isset($_SESSION["username"])) {
    header('location:login.php');
    exit();
}

$db = new Database();
$pdo = $db->getConnection();
$name = $_SESSION["username"];
$user1 = new User($pdo);
$user = $user1->getUser($name);

if (isset($_GET['application_id'])) {
    $application_id = htmlspecialchars($_GET['application_id']);
}

if ($application_id > 0) {
    // Fetch job application details
    $stmt = $pdo->prepare("SELECT * FROM job_applications WHERE id = :id");
    $stmt->bindParam(':id', $application_id, PDO::PARAM_INT);
    $stmt->execute();
    $application = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($application) {
        $skill = htmlspecialchars($application['skill']);
        $experience = htmlspecialchars($application['experience']);
        $certificate = $application['certificate'];
        $education = htmlspecialchars($application['education']);
        $applied_at = htmlspecialchars($application['applied_at']);
        $adaptability = htmlspecialchars($application['adaptability']);
        $communication = htmlspecialchars($application['communication']);
        $emotional_intelligence = htmlspecialchars($application['emotional_intelligence']);
        $leadership = htmlspecialchars($application['leadership']);
        $resilience = htmlspecialchars($application['resilience']);


        // Fetch user profile information
        $user_id = htmlspecialchars($application['user_id']);
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmtUser->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmtUser->execute();
        $userProfile = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($userProfile) {
            $userName = htmlspecialchars($userProfile['name']);
            $userEmail = htmlspecialchars($userProfile['email']);
            $userRole = htmlspecialchars($userProfile['role']);
            $userPhone = htmlspecialchars($userProfile['phone']);
            $userCompany = htmlspecialchars($userProfile['company']);
            $userLocation = htmlspecialchars($userProfile['location']);
            $image = htmlspecialchars($userProfile['image']);
            $userImage = "assets/{$image}";
            $userDescription = htmlspecialchars($userProfile['description']);
            $dob = htmlspecialchars($userProfile['DOB']);
            $nrc = htmlspecialchars($userProfile['NRC']);
            $gender = htmlspecialchars($userProfile['gender']);

        } else {
            die("User not found.");
        }
    } else {
        die("Application not found.");
    }
} else {
    die("Invalid application ID.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application Details</title>
    <!-- Bootstrap CSS -->
    <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .goback {
            position: fixed;
            top: 20px;
            left: 20px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .container {
            max-width: 900px;
            margin: auto;
            padding: 20px;
        }

        .profile-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .profile-card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .profile-card img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 5px solid #f3f4f6;
            margin-bottom: 15px;
        }

        .profile-card .card-body {
            padding: 30px;
            text-align: left;
        }

        .profile-card .card-header {
            background: #007bff;
            color: #fff;
            text-align: center;
            padding: 15px 0;
        }

        .profile-card h3 {
            font-size: 1.75rem;
            margin-bottom: 20px;
            color: #007bff;
        }

        .profile-card p {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .profile-card table {
            width: 100%;
            margin-top: 20px;
        }

        .profile-card table tr td {
            padding: 10px;
            vertical-align: top;
        }

        .profile-card .card-footer {
            background: #f8f9fa;
            padding: 15px;
            text-align: center;
        }

        .profile-card .card-footer p {
            margin-bottom: 0;
            color: #007bff;
            font-size: 1.25rem;
            font-weight: bold;
        }

        .star-rating {
            font-size: 1.5rem;
            color: #f39c12;
        }

        .star-rating i {
            cursor: pointer;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            padding: 30px;
        }

        .user-info img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid #007bff;
        }

        .user-info div {
            flex: 1;
        }

        .user-info p {
            margin-bottom: 5px;
        }

        .user-info p strong {
            color: #333;
        }

        .certificate-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .certificate-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .certificate-text {
            font-size: 1rem;
            color: #333;
            flex: 1;
        }

        .certificate-download {
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            color: #007bff;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .certificate-download:hover {
            background-color: #007bff;
            color: #ffffff;
            border-color: #007bff;
        }

        .certificate-download i {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <a href="#" onclick="window.history.back(); return false;" class="goback">
            &lt; Back</a>
        <div class="profile-card">
            <div class="card-header">
                <h3>User Profile Information</h3>
            </div>
            <div class="user-info">
                <img src="<?= htmlspecialchars($userImage) ?>" alt="User Image">
                <div>
                    <p><strong>Name:</strong> <?= $userName ?></p>
                    <p><strong>Email:</strong> <?= $userEmail ?></p>
                    <p><strong>Phone:</strong> <?= $userPhone ?></p>
                    <p><strong>Gender:</strong> <?= $gender ?></p>
                    <p><strong>NRC No:</strong> <?= $nrc ?></p>
                    <p><strong>DOB:</strong> <?= $dob ?></p>
                    <p><strong>Location:</strong> <?= $userLocation ?></p>
                </div>
            </div>
            <div class="card-body">
                <h3>Job Application Details</h3>
                <table>
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td><strong>Applied At:</strong></td>
                                    <td><?= $applied_at ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Skills:</strong></td>
                                    <td><?= $skill ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Education:</strong></td>
                                    <td><?= $education ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Experience:</strong></td>
                                    <td><?= $experience ?></td>
                                </tr>
                            </table>

                            <?php
                            $certificateData = json_decode($certificate, true);

                            if (!empty($certificateData)) {
                                echo '&nbsp;&nbsp;<strong>Certificates:</strong>';
                                echo '<ul>';
                                foreach ($certificateData as $cert) {
                                    $certificateName = htmlspecialchars($cert['name']);
                                    $certificatePath = htmlspecialchars($cert['path']);
                                    $certificateImage = "assets/{$certificatePath}";
                                    echo '<li class="certificate-item">';
                                    echo '<span class="certificate-text">' . $certificateName . '</span>';
                                    echo '<a href="' . $certificateImage . '" download class="certificate-download">';
                                    echo '<i class="fas fa-download"></i> Download CV';
                                    echo '</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p>No certificates available.</p>';
                            }
                            ?>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td><strong>Adaptability:</strong></td>
                                    <td><span class="star-rating" data-rating="<?= $adaptability ?>"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Communication:</strong></td>
                                    <td><span class="star-rating" data-rating="<?= $communication ?>"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Emotional Intelligence:</strong></td>
                                    <td><span class="star-rating" data-rating="<?= $emotional_intelligence ?>"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Leadership:</strong></td>
                                    <td><span class="star-rating" data-rating="<?= $leadership ?>"></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Resilience:</strong></td>
                                    <td><span class="star-rating" data-rating="<?= $resilience ?>"></span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <p>Thank you for reviewing the application</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const starContainers = document.querySelectorAll('.star-rating');

            starContainers.forEach(container => {
                const rating = container.getAttribute('data-rating');

                for (let i = 1; i <= 5; i++) {
                    const star = document.createElement('i');
                    star.classList.add('fas', 'fa-star');
                    star.style.color = i <= rating ? '#f39c12' : '#e0e0e0';
                    container.appendChild(star);
                }
            });
        });
    </script>

</body>

</html>
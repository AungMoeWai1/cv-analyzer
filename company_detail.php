<!-- Person_details.php -->
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

if (isset($_GET['company'])) {
    $employer_id = htmlspecialchars($_GET['company']);
}

if ($employer_id > 0) {
    $employer = $user1->getUserByID($employer_id);

    if ($employer) {
        $name1 = htmlspecialchars($employer['name']);
        $email = htmlspecialchars($employer['email']);
        $role = htmlspecialchars($employer['role']);
        $phone = htmlspecialchars($employer['phone']);
        $company = htmlspecialchars($employer['company']);
        $location = htmlspecialchars($employer['location']);
        $image = htmlspecialchars($employer['image']);
        $description = htmlspecialchars($employer['description']);
        $nrc=htmlspecialchars($employer['NRC']);
        $dob=htmlspecialchars($employer['DOB']);
        $gender=htmlspecialchars($employer['gender']);
    } else {
        die("Employer not found.");
    }
} else {
    die("Invalid employer ID.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Person Details</title>
    <!-- Bootstrap CSS -->
    <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .goback {
            position: absolute;
            top: 10px;
            left: 10px;
            text-decoration: none;
            font-size: 3rem;
            font-weight: bold;
            color: white;
        }

        .hero-section {
            background: linear-gradient(135deg, #6f42c1, #e83e8c);
            color: #fff;
            padding: 60px 20px;
            text-align: center;
            border-bottom: 5px solid #fff;
        }

        .hero-section img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: fill;
            border: 5px solid #fff;
            margin-bottom: 20px;
        }

        .profile-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: #fff;
            margin-top: -75px;
            padding-top: 75px;
        }

        .profile-card h1 {
            font-size: 2rem;
            color: #333;
        }

        .profile-card p {
            color: #666;
            font-size: 1rem;
        }

        .profile-card .card-body {
            padding: 20px;
        }

        .profile-card .card-footer {
            background: #f1f1f1;
            padding: 15px;
        }

        .description {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .contact-info i {
            font-size: 1.2rem;
            margin-right: 10px;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <?php
        // Determine the correct image path
        $imagePath = ($image == "") ? "assets/default_person.png" : "assets/{$image}";
        ?>
        <img src="<?= $imagePath ?>" alt="profile Image">
        <h1><?= $name1 ?></h1>
    </div>
    <div class="container mt-5">
        <a href="#" onclick="window.history.back(); return false;" class="goback">
            < </a>
                <div class="profile-card card">
                    <div class="card-body">
                        <div class="contact-info">
                            <?php if ($role == 'employer') { ?>
                                <p><i class="fas fa-building"></i><strong>Company:</strong> <?= $company ?></p>
                            <?php } ?>
                            <p><i class="fas fa-map-marker-alt"></i><strong>Location:</strong> <?= $location ?></p>
                            <p><i class="fas fa-envelope"></i><strong>Email:</strong> <?= $email ?></p>
                            <?php if($phone!=""){ ?>
                                <p><i class="fas fa-phone-alt"></i><strong>Phone:</strong> <?= $phone ?></p>
                            <?php } ?>
                            <?php if($phone!=""){ ?>
                                <p><i class="fas fa-phone-alt"></i><strong>Phone:</strong> <?= $phone ?></p>
                            <?php } ?>
                            <?php if($nrc!="" and $name1==$name){ ?>
                                <p><i class="fas fa-phone-alt"></i><strong>NRC:</strong> <?= $nrc ?></p>
                            <?php } ?>
                            <?php if($dob!=""){ ?>
                                <p><i class="fas fa-phone-alt"></i><strong>DOB:</strong> <?= $dob ?></p>
                            <?php } ?>
                            <?php if($gender!=""){ ?>
                                <p><i class="fas fa-phone-alt"></i><strong>gender:</strong> <?= $gender ?></p>
                            <?php } ?>
                            <p><i class="fas fa-user-tie"></i><strong>Role:</strong> <?= $role ?></p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="description">
                            <h5>Details:</h5>
                            <p><?= $description ?></p>
                        </div>
                    </div>
                </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="library/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
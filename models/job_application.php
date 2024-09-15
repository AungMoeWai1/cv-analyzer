<?php

use Vtiful\Kernel\Format;

// Connect to the database
session_start();
include '../includes/db.php';
$db = new Database();
$pdo = $db->getConnection();

// Get the job ID from the query parameter
if (isset($_GET['job_id'])) {
    $jobId = $_GET['job_id'];

    // Fetch the job details (if needed)
    $stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = :id');
    $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);
    $stmt->execute();
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the user details
    $username = $_SESSION['username']; // Assuming the user ID is stored in the session
    $stmt = $pdo->prepare('SELECT * FROM users WHERE name = :name');
    $stmt->bindParam(':name', $username, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo 'No job ID specified.';
    exit;
}

// Handle form submission to update user information and insert into job_applications
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $skill = $_POST['skill'];
    $experience = $_POST['experience'];
    $education = $_POST['education'];
    // $certificate = $_POST['certificate'];
    $adaptability = $_POST['adaptability'];
    $communication = $_POST['communication'];
    $emotionalIntelligence = $_POST['emotional_intelligence'];
    $leadership = $_POST['leadership'];
    $resilience = $_POST['resilience'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    //need to add nrc
    $nrcState = htmlspecialchars($_POST['nrc_state']);
    $nrcDistrict = htmlspecialchars($_POST['nrc_district']);
    $nrcNationalCode = htmlspecialchars($_POST['nrc_national_code']);
    $nrcNumber = htmlspecialchars($_POST['nrc_number']);

    // Construct NRC string
    $nrc = $nrcState . "/" . $nrcDistrict . "(" . $nrcNationalCode . ")" . $nrcNumber;

    // Handling certificate uploads
    $certificates = [];
    if (isset($_FILES['certificate'])) {
        foreach ($_FILES['certificate']['tmp_name'] as $index => $tmpName) {
            if (is_uploaded_file($tmpName)) {
                $certificateName = htmlspecialchars($_POST['certificate_name'][$index] ?? '');
                $fileName = $_FILES['certificate']['name'][$index];
                $targetDir = "../assets/certificates/";
                $targetFile = $targetDir . basename($fileName);

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $certificates[] = [
                        'name' => $certificateName,
                        'path' => $targetFile
                    ];
                }

            }
        }
    }
    $certificateJson = json_encode($certificates);


    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = '../assets/';
        $uploadFile = $uploadDir . basename($_FILES['image']['name']);

        // Move the file to the assets directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {

            // Update the user information in the database
            $stmt1 = $pdo->prepare('UPDATE users SET phone = :phone, DOB=:dob, gender=:gender, NRC=:nrc , location = :location, image = :file_path WHERE id = :id');

            $stmt1->bindParam(':phone', $phone);
            $stmt1->bindParam(':dob', $dob);
            $stmt1->bindParam(':gender', $gender);
            $stmt1->bindParam(':nrc', $nrc);
            $stmt1->bindParam(':location', $location);
            $stmt1->bindParam(':file_path', $uploadFile);
            $stmt1->bindParam(':id', $user['id'], PDO::PARAM_INT);


            if ($stmt1->execute()) {
                // Insert application details
                $stmt = $pdo->prepare('INSERT INTO job_applications (user_id, job_id, skill, experience, certificate, education, applied_at, adaptability, communication, emotional_intelligence, leadership, resilience) VALUES (:user_id, :job_id, :skill, :experience, :certificate, :education, :applied_at, :adaptability, :communication, :emotional_intelligence, :leadership, :resilience)');
                $stmt->bindParam(':user_id', $user['id'], PDO::PARAM_INT);
                $stmt->bindParam(':job_id', $jobId, PDO::PARAM_INT);
                $stmt->bindParam(':skill', $skill);
                $stmt->bindParam(':experience', $experience);
                $stmt->bindParam(':certificate', $certificateJson);
                $stmt->bindParam(':education', $education);
                $stmt->bindParam(':adaptability', $adaptability);
                $stmt->bindParam(':communication', $communication);
                $stmt->bindParam(':emotional_intelligence', $emotionalIntelligence);
                $stmt->bindParam(':leadership', $leadership);
                $stmt->bindParam(':resilience', $resilience);

                $date = new DateTime();
                $dateFormatted = $date->format('Y-m-d H:i:s');
                $stmt->bindParam(':applied_at', $dateFormatted);

                if ($stmt->execute()) {
                    echo '<div class="notification success">Application submitted successfully.</div>';
                    header('Location: ../index.php');
                    exit;
                } else {
                    echo '<div class="notification error">Error submitting application.</div>';
                }
            } else {
                echo '<div class="notification error">Error updating information.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            position: relative;
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

        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://www.w3schools.com/w3images/forest.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: brightness(0.5);
            animation: zoomIn 30s infinite linear;
            z-index: -1;
        }

        @keyframes zoomIn {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            animation: fadeIn 1s ease-out;
        }

        h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
            animation: slideDown 0.5s ease-out;
        }

        .form-group1 {
            display: inline;
        }

        .form-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.5s ease-out;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group1 input,
        .form-group1 select {
            padding: 7px;
            border: 1px solid #333;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-sizing: border-box;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 8px rgba(74, 144, 226, 0.3);
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            gap: 10px;
            /* Add space between the buttons */
        }

        .btn-submit {
            display: block;
            width: 70%;
            padding: 15px;
            background: #4a90e2;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-submit:hover {
            background: #357abd;
            transform: scale(1.03);
        }

        .btn-warning {
            width: 25%;
            /* Clear button takes 30% of the width */
            padding: 15px;
            background: #f0ad4e;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            text-align: center;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-warning:hover {
            background: #ec971f;
            transform: scale(1.03);
        }

        .star-rating {
            display: flex;
            cursor: pointer;
            margin-top: 10px;
        }

        .star-rating i {
            font-size: 28px;
            color: #ddd;
            transition: color 0.3s ease;
        }

        .star-rating i.selected,
        .star-rating i:hover {
            color: #f39c12;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>

        //for listening the input field
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('job-application-form');
            const dobInput = document.getElementById('dob');
            const submitBtn = document.getElementById('submit-btn');
            const requiredInputs = form.querySelectorAll('[required]');

            // Function to check if all required fields are filled
            function checkFormCompletion() {
                let allFilled = true;

                requiredInputs.forEach(input => {
                    if (input.type === 'file') {
                        if (input.files.length === 0) {
                            allFilled = false;
                        }
                    } else if (input.value.trim() === '') {
                        allFilled = false;
                    }
                });

                // Check if age is 18 or older
                const dobValue = dobInput.value;
                if (dobValue) {
                    const age = calculateAge(new Date(dobValue));
                    if (age < 18) {
                        allFilled = false;
                    }
                } else {
                    allFilled = false;
                }

                // Show or hide the submit button based on form completeness
                if (allFilled) {
                    submitBtn.style.display = 'block';
                } else {
                    submitBtn.style.display = 'none';
                }
            }
            function calculateAge(birthDate) {
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDifference = today.getMonth() - birthDate.getMonth();
                if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age;
            }

            // Attach event listeners to dynamically check for form completion
            requiredInputs.forEach(input => {
                input.addEventListener('input', checkFormCompletion);
                if (input.type === 'file') {
                    input.addEventListener('change', checkFormCompletion);
                }
            });

            // Check on page load
            checkFormCompletion();
        });
        //for rating star
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.star-rating').forEach(function (starRating) {
                let stars = starRating.querySelectorAll('i');
                let hiddenInput = starRating.querySelector('input[type="hidden"]');

                stars.forEach(function (star, index) {
                    star.addEventListener('click', function () {
                        let rating = index + 1;
                        hiddenInput.value = rating;

                        stars.forEach(function (s, i) {
                            if (i < rating) {
                                s.classList.add('selected');
                            } else {
                                s.classList.remove('selected');
                            }
                        });
                    });

                    star.addEventListener('mouseover', function () {
                        let rating = index + 1;

                        stars.forEach(function (s, i) {
                            if (i < rating) {
                                s.classList.add('selected');
                            } else {
                                s.classList.remove('selected');
                            }
                        });
                    });

                    star.addEventListener('mouseout', function () {
                        stars.forEach(function (s, i) {
                            s.classList.remove('selected');
                        });

                        let selectedRating = hiddenInput.value;
                        stars.forEach(function (s, i) {
                            if (i < selectedRating) {
                                s.classList.add('selected');
                            } else {
                                s.classList.remove('selected');
                            }
                        });
                    });
                });
            });
        });

        //For date of birth checking under 18 or not
        document.addEventListener('DOMContentLoaded', function () {
            const dobInput = document.getElementById('dob');
            const dobError = document.getElementById('dob-error');
            const form = document.getElementById('form');

            function calculateAge(birthDate) {
                const today = new Date();
                const birth = new Date(birthDate);
                let age = today.getFullYear() - birth.getFullYear();
                const month = today.getMonth() - birth.getMonth();
                if (month < 0 || (month === 0 && today.getDate() < birth.getDate())) {
                    age--;
                }
                return age;
            }

            function validateDOB() {
                const dobValue = dobInput.value;
                const age = calculateAge(dobValue);

                if (age < 18) {
                    dobError.textContent = 'You must be at least 18 years old.';
                    dobError.style.color = 'red';
                    dobInput.classList.add('input-error');
                    return false;
                } else {
                    dobError.textContent = '';
                    dobInput.classList.remove('input-error');
                    return true;
                }
            }

            dobInput.addEventListener('change', validateDOB);

            form.addEventListener('submit', function (event) {
                if (!validateDOB()) {
                    event.preventDefault();
                }
            });
        });

        //for NRC Field
        document.addEventListener('DOMContentLoaded', function () {
            event.preventDefault()
            const stateSelect = document.getElementById('nrc-state');
            const districtSelect = document.getElementById('nrc-district');
            const nationalitySelect = document.getElementById('nrc-national-code');
            const numberInput = document.getElementById('nrc-number');
            const nrcOutput = document.getElementById('nrc-output');
            const nrcError = document.getElementById('nrc-error');

            // State options
            const states = [{
                value: '1',
                text: '1'
            },
            {
                value: '2',
                text: '2'
            },
            {
                value: '3',
                text: '3'
            },
            {
                value: '4',
                text: '4'
            },
            {
                value: '5',
                text: '5'
            },
            {
                value: '6',
                text: '6'
            },
            {
                value: '7',
                text: '7'
            },
            {
                value: '8',
                text: '8'
            },
            {
                value: '9',
                text: '9'
            },
            {
                value: '10',
                text: '10'
            },
            {
                value: '11',
                text: '11'
            },
            {
                value: '12',
                text: '12'
            },
            {
                value: '13',
                text: '13'
            },
            {
                value: '14',
                text: '14'
            }
            ];

            /*
            Later to add if necessary
            <option value="">Select NRC Type</option>
<option value="C">(C) Citizen</option>
<option value="N">(N) National Registration</option>
<option value="P">(P) Associated</option>
<option value="T">(T) Temporary</option>
<option value="AC">(AC) Associate Citizen</option>
<option value="NC">(NC) Naturalized Citizen</option>
<option value="SC">(SC) Special Citizen</option>
<option value="S">(S) Service/Official</option>
<option value="D">(D) Diplomat</option>
<option value="FN">(FN) Foreign National</option>
<option value="AL">(AL) Alien</option>
*/
            // Nationality options
            const nationalities = [{
                value: 'C',
                text: '(C)'
            },
            {
                value: 'N',
                text: '(N)'
            },
            {
                value: 'P',
                text: '(P)'
            },
            {
                value: 'T',
                text: '(T)'
            }
            ];

            // Set state options
            states.forEach(state => {
                let option = document.createElement('option');
                option.value = state.value;
                option.textContent = state.text;
                stateSelect.appendChild(option);
            });

            // Set nationality options
            nationalities.forEach(nationality => {
                let option = document.createElement('option');
                option.value = nationality.value;
                option.textContent = nationality.text;
                nationalitySelect.appendChild(option);
            });

            // District options based on state
            const districts = {
                '1': ['BaMaNa', 'KhaHpaNa', 'DaHpaYa', 'HaPaNa', 'HpaKaNa', 'AhGaYa', 'AhGaYa', 'KaPaTa', 'KaPaTa', 'LaGaNa', 'MaKhaBa', 'MaSaNa', 'MaKaTa', 'MaNyaNa', 'MaMaNa', 'MaKaNa', 'MaLaNa', 'NaMaNa', 'NaMaNa', 'PaNaDa', 'PaNaDa', 'SaDaNa', 'YaBaYa', 'YaKaNa', 'SaBaNa', 'SaPaYa', 'TaNaNa', 'TaSaLa', 'WaMaNa'],
                '2': ['BaLaKha', 'DaMaSa', 'HpaSaNa', 'HpaYaSa', 'LaKaNa', 'MaSaNa', 'YaTaNa', 'YaThaNa'],
                '3': ['BaGaLa', 'LaBaNa', 'BaAhNa', 'HpaPaNa', 'BaThaSa', 'KaMaMa', 'KaKaYa', 'KaDaNa', 'KaSaKa', 'KaSaKa', 'LaThaNa', 'MaWaTa', 'PaKaNa', 'YaYaTha', 'SaKaLa', 'ThaTaNa', 'ThaTaKa', 'WaLaMa'],
                '4': ['KaKhaNa', 'HpaLaNa', 'HaKhaNa', 'KaPaLa', 'KaPaLa', 'MaTaPa', 'MaTaNa', 'PaLaWa', 'YaZaNa', 'YaKhaDa', 'SaMaNa', 'TaTaNa', 'HtaTaLa', 'TaZaNa'],
                '5': ['TaZaNa', 'BaMaNa', 'BaTaLa', 'KhaOuTa', 'KhaTaNa', 'HaMaLa', 'AhTaNa', 'KaLaHta', 'KaLaWa', 'KaBaLa', 'KaNaNa', 'KaThaNa', 'KaLaTa', 'KaLaTa', 'KaLaNa', 'LaHaNa', 'LaYaNa', 'MaLaNa', 'MaKaNa', 'MaYaNa', 'MaMaNa', 'MaMaTa', 'NaYaNa', 'NgaZaNa', 'PaLaNa', 'HpaPaNa', 'PaLaBa', 'SaKaNa', 'SaLaKa', 'YaBaNa', 'DaPaYa', 'TaMaNa', 'TaSaNa', 'TaSaNa', 'WaLaNa', 'WaThaNa', 'YaOuNa', 'YaMaPa', 'KaMaNa', 'KhaPaNa'],
                '6': ['BaPaNa', 'HtaWaNa', 'KaLaAh', 'KaThaNa', 'KaSaNa', 'LaLaNa', 'MaMaNa', 'PaLaNa', 'TaThaYa', 'ThaYaKha', 'YaHpaNa', 'KhaMaNa', 'MaTaNa', 'PaLaTa', 'KaYaYa'],
                '7': ['DaOuNa', 'KaPaKa', 'KaWaNa', 'KaKaNa', 'KaTaKha', 'LaPaTa', 'MaLaNa', 'MaNyaNa', 'NaTaLa', 'NyaLaPa', 'AhHpaNa', 'AhTaNa', 'PaTaNa', 'PaKhaTa', 'PaKhaNa', 'PaTaTa', 'PaNaKa', 'HpaMaNa', 'PaMaNa', 'YaTaNa', 'YaKaNa', 'HtaTaPa', 'TaNgaNa', 'ThaNaPa', 'ThaWaTa', 'ThaKaNa', 'ThaSaNa', 'WaMaNa', 'YaTaYa', 'ZaKaNa', 'PaTaSa'],
                '8': ['AhLaNa', 'KhaMaNa', 'GaGaNa', 'KaMaNa', 'MaKaNa', 'MaBaNa', 'MaTaNa', 'MaLaNa', 'MaMaNa', 'MaHtaNa', 'MaThaNa', 'NaMaNa', 'NgaHpaNa', 'PaKhaKa', 'PaMaNa', 'PaHpaNa', 'SaLaNa', 'SaMaNa', 'SaHpaNa', 'SaTaYa', 'SaPaWa', 'TaTaKa', 'ThaYaNa', 'HtaLaNa', 'YaNaKha', 'YaSaKa', 'KaHtaNa'],
                '9': ['AhMaYa', 'AhMaZa', 'KhaAhZa', 'KhaMaSa', 'KaPaTa', 'KaSaNa', 'MaTaYa', 'MaHaMa', 'MaLaNa', 'MaHtaLa', 'MaKaNa', 'MaKhaNa', 'MaThaNa', 'NaHtaKa', 'NgaThaYa', 'NgaZaNa', 'NyaOuNa', 'PaThaKa', 'PaBaNa', 'PaKaKha', 'PaOuLa', 'SaKaNa', 'SaKaTa', 'ThaPaKa', 'TaTaOu', 'TaThaNa', 'ThaSaNa', 'WaTaNa', 'YaMaTha', 'TaKaTa', 'MaMaNa', 'DaKhaTha', 'LaWaNa', 'OuTaTha', 'PaBaTha', 'PaMaNa', 'TaKaNa', 'ZaBaTha', 'ZaYaTha'],
                '10': ['BaLaNa', 'KhaSaNa', 'KhaZaNa', 'KaMaYa', 'KaHtaNa', 'LaMaNa', 'MaLaMa', 'MaDaNa', 'PaMaNa', 'ThaHpaYa', 'ThaHtaNa', 'YaMaNa'],
                '11': ['AhMaNa', 'BaThaTa', 'GaMaNa', 'KaHpaNa', 'KaTaNa', 'MaAhTa', 'MaTaNa', 'MaPaNa', 'MaAhNa', 'MaOuNa', 'MaPaTa', 'PaTaNa', 'PaNaTa', 'YaBaNa', 'YaThaTa', 'SaTaNa', 'ThaTaNa', 'TaKaNa', 'KaTaLa', 'TaPaWa'],
                '12': ['AhLaNa', 'BaHaNa', 'BaTaHta', 'KaKaKa', 'DaGaYa', 'DaGaMa', 'DaGaSa', 'DaGaTa', 'DaGaNa', 'DaLaNa', 'DaPaNa', 'LaThaYa', 'LaMaNa', 'LaKaNa', 'MaBaNa', 'HtaTaPa', 'AhSaNa', 'KaMaYa', 'KaMaNa', 'KhaYaNa', 'KaKhaKa', 'KaTaTa', 'KaTaNa', 'KaMaTa', 'LaMaTa', 'LaThaNa', 'MaYaKa', 'MaGaDa', 'MaGaTa', 'OuKaMa', 'PaBaTa', 'PaZaTa', 'SaKhaNa', 'SaKaKha', 'SaKaNa', 'YaPaTha', 'OuKaTa', 'TaTaHta', 'TaKaNa', 'TaMaNa', 'ThaKaTa', 'ThaLaNa', 'ThaGaKa', 'ThaKhaNa', 'TaTaNa', 'YaKaNa', 'OuKaNa'],
                '13': ['AhKhaNa', 'KhaYaHa', 'KhaMaNa', 'HaTaNa', 'HaPaNa', 'HaPaTa', 'SaHpaNa', 'ThaNaNa', 'SaSaNa', 'ThaPaNa', 'KaLaHpa', 'KaLaNa', 'KaLaDa', 'KaMaSa', 'KaTaNa', 'KaYaNa', 'KaTaTa', 'KaHaNa', 'KaLaTa', 'KaKhaNa', 'KaMaNa', 'KaTaLa', 'KaThaNa', 'LaKhaNa', 'LaKhaTa', 'LaYaNa', 'LaKaNa', 'LaHaNa', 'LaLaNa', 'MaBaNa', 'MaMaSa', 'MaTaNa', 'MaTaTa', 'MaMaNa', 'MaHpaNa', 'MaKaNa', 'MaPaNa', 'MaHpaNa', 'MaSaNa', 'MaYaNa', 'MaKaNa', 'MaKhaNa', 'MaLaNa', 'MaMaTa', 'MaMaTa', 'MaNaNa', 'MaPaNa', 'MaTaNa', 'MaYaTa', 'MaYaNa', 'MaSaTa', 'NaKhaWa', 'NaTaNa', 'NaKhaNa', 'NaMaTa', 'NaHpaNa', 'NaSaNa', 'NaKaNa', 'NaWaNa', 'NaPhaNa', 'NaKhaNa', 'NaKhaTa', 'NyaYaNa', 'PaKhaNa', 'PaYaNa', 'PaSaNa', 'PaWaNa', 'HpaKhaNa', 'PaTaYa', 'PaLaNa', 'TaKhaLa', 'TaYaNa', 'TaKaNa', 'YaLaNa', 'YaSaNa', 'YaHpaNa', 'YaNgaNa', 'NaTaYa', 'PaLaTa', 'KhaLaNa', 'MaHaYa', 'PaPaKa', 'TaMaNya', 'MaBaTa', 'MaNgaNa', 'AhTaNa', 'TaLaNa'],
                '14': ['AhMaTa', 'BaKaLa', 'DaNaHpa', 'DaDaYa', 'AhMaNa', 'HaKaKa', 'HaThaTa', 'AhGaPa', 'KaKaHta', 'KaLaNa', 'KaKhaNa', 'KaKaNa', 'KaPaNa', 'LaPaTa', 'LaMaNa', 'MaAhPa', 'MaMaKa', 'MaAhNa', 'MaMaNa', 'NgaPaTa', 'NgaThaKha', 'NgaYaKa', 'NgaSaNa', 'NgaThaYa', 'NyaTaNa', 'PaTaNa', 'PaThaNa', 'HpaPaNa', 'PaSaLa', 'YaThaYa', 'ThaPaNa', 'WaKhaMa', 'YaKaNa', 'ZaLaNa', '']
            };

            stateSelect.addEventListener('change', function () {
                event.preventDefault()
                const selectedState = stateSelect.value;
                districtSelect.innerHTML = '<option value="">Select District</option>'; // Clear existing options

                if (districts[selectedState]) {
                    districts[selectedState].forEach(district => {
                        let option = document.createElement('option');
                        option.value = district;
                        option.textContent = district;
                        districtSelect.appendChild(option);
                    });
                }
            });
        });

        //For Certificate field
        document.addEventListener('DOMContentLoaded', function () {
            let certificateCounter = 1;

            document.getElementById('add-certificate-btn').addEventListener('click', function () {
                certificateCounter++;
                const certificateContainer = document.getElementById('certificate-container');

                const newCertificateGroup = document.createElement('div');
                newCertificateGroup.classList.add('form-group', 'certificate-group');
                newCertificateGroup.id = `certificate-group-${certificateCounter}`;

                const newCertificateNameLabel = document.createElement('label');
                newCertificateNameLabel.setAttribute('for', `certificate-name-${certificateCounter}`);
                newCertificateNameLabel.innerText = 'Certificate Name:';
                newCertificateGroup.appendChild(newCertificateNameLabel);

                const newCertificateNameInput = document.createElement('input');
                newCertificateNameInput.type = 'text';
                newCertificateNameInput.name = 'certificate_name[]';
                newCertificateNameInput.id = `certificate-name-${certificateCounter}`;
                newCertificateNameInput.classList.add('form-control');
                newCertificateNameInput.placeholder = 'Certificate Name';
                newCertificateNameInput.required = true;
                newCertificateGroup.appendChild(newCertificateNameInput);

                const newCertificateFileLabel = document.createElement('label');
                newCertificateFileLabel.setAttribute('for', `certificate-file-${certificateCounter}`);
                newCertificateFileLabel.innerText = 'Certificate File:';
                newCertificateGroup.appendChild(newCertificateFileLabel);

                const newCertificateFileInput = document.createElement('input');
                newCertificateFileInput.type = 'file';
                newCertificateFileInput.name = 'certificate[]';
                newCertificateFileInput.id = `certificate-file-${certificateCounter}`;
                newCertificateFileInput.classList.add('form-control');
                newCertificateFileInput.accept = 'image/*';
                newCertificateFileInput.required = true;
                newCertificateGroup.appendChild(newCertificateFileInput);

                // Create and append the Remove button
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.classList.add('btn', 'btn-danger', 'remove-certificate-btn');
                removeButton.innerText = 'Remove';

                // Add event listener to the Remove button
                removeButton.addEventListener('click', function () {
                    this.parentNode.remove(); // Remove the current certificate group
                });

                newCertificateGroup.appendChild(removeButton);


                certificateContainer.appendChild(newCertificateGroup);
            });

            //Not need part .
            document.getElementById('certificate-form').addEventListener('submit', function (event) {
                event.preventDefault();

                const certificateNames = document.querySelectorAll('input[name="certificate_name[]"]');
                const certificateFiles = document.querySelectorAll('input[name="certificate[]"]');

                const formData = new FormData();

                certificateNames.forEach((input, index) => {
                    formData.append('certificate_name[]', input.value);
                });

                certificateFiles.forEach((input, index) => {
                    if (input.files[0]) {
                        formData.append('certificate[]', input.files[0]);
                    }
                });

                // Now, you can send formData to the server using fetch or any other method
                fetch('/your-server-endpoint', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Success:', data);
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            });
        });

    </script>
</head>

<body>
    <div class="background-animation"></div>
    <a href="#" onclick="window.history.back(); return false;" class="goback">
        < </a>
            <div class="container">
                <h1>Apply for Job: <?= htmlspecialchars($job['name']) ?></h1>
                <form action="job_application.php?job_id=<?= $jobId ?>" method="post" enctype="multipart/form-data"
                    id="job-application-form">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="location">Location:</label>
                        <textarea name="location" required><?= htmlspecialchars($user['location']) ?></textarea>
                    </div>

                    <fieldset>
                        <legend style="color:black;">NRC Details</legend>

                        <div class="form-group1">
                            <select id="nrc-state" name="nrc_state" class="form-control" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        /
                        <div class="form-group1">
                            <select id="nrc-district" name="nrc_district" class="form-control" required>
                                <option value="">Select District</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <select id="nrc-national-code" name="nrc_national_code" class="form-control" required>
                                <option value="">Select Nationality</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <input type="text" id="nrc-number" name="nrc_number" class="form-control"
                                placeholder="Number" required oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                        </div>
                        <input type="hidden" id="nrc-output" name="nrc-output">
                    </fieldset>

                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" required>
                        <div id="dob-error" class="error"></div>
                    </div>

                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Upload Image:</label>
                        <input type="file" name="image" required accept=".jpg, .jpeg, .png">
                    </div>
                    <div class="form-group">
                        <label for="skill">Skill:</label>
                        <textarea name="skill" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="experience">Experience:</label>
                        <textarea name="experience" required></textarea>
                    </div>

                    <div id="certificate-container">

                    </div>
                    <button type="button" style="width:100px;height:40px;" class="btn btn-secondary ml-2"
                        id="add-certificate-btn">Add Certificate</button>


                    <div class="form-group">
                        <label for="education">Education:</label>
                        <textarea name="education" required></textarea>
                    </div>

                    <!-- Extra field of star selection -->
                    <div class="form-group">
                        <label for="adaptability">Adaptability:</label>
                        <div id="adaptability" class="star-rating">
                            <input type="hidden" name="adaptability" id="adaptability_value" value="0">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="communication">Communication:</label>
                        <div id="communication" class="star-rating">
                            <input type="hidden" name="communication" id="communication_value" value="0">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="emotional_intelligence">Emotional Intelligence:</label>
                        <div id="emotional_intelligence" class="star-rating">
                            <input type="hidden" name="emotional_intelligence" id="emotional_intelligence_value"
                                value="0">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="leadership">Leadership:</label>
                        <div id="leadership" class="star-rating">
                            <input type="hidden" name="leadership" id="leadership_value" value="0">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resilience">Resilience:</label>
                        <div id="resilience" class="star-rating">
                            <input type="hidden" name="resilience" id="resilience_value" value="0">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="submit-btn" style="display: none;">Apply</button>
                        <button type="button" class="btn btn-warning" id="clear-form-btn">Clear All</button>
                    </div>
                </form>
            </div>
</body>

<script>
    //To clear all input
    // Add event listener to Clear All button
    document.getElementById('clear-form-btn').addEventListener('click', function () {
        document.getElementById('job-application-form').reset(); // This will reset the entire form
        // Optional: Clear star rating visuals if you use custom stars
        document.querySelectorAll('.star-rating i').forEach(star => {
            star.classList.remove('selected'); // If you have a 'selected' class on stars
        });
    });
</script>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Job</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="library/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #eef2f5;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .apply-button {
            display: block;
            width: 100%;
            text-align: center;
            background-color: #4a90e2;
            color: #fff;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 30px;
            transition: background-color 0.3s;
        }

        .apply-button:hover {
            background-color: #357abd;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-weight: 600;
            font-size: 28px;
        }

        label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        input, textarea, select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            margin-bottom: 20px;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #4a90e2;
            outline: none;
        }

        button {
            background-color: #4a90e2;
            color: #fff;
            padding: 14px;
            border-radius: 8px;
            width: 100%;
            font-size: 18px;
            font-weight: 600;
            border: none;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #357abd;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="#" onclick="window.history.back(); return false;" class="apply-button">Go Back</a>
        <h1>Create a Job</h1>
        <form action="models/job_model.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Job Name:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="campus" class="form-label">Working Hour:</label>
                <select class="form-select" id="campus" name="campus" required>
                    <option value="full time">Full Time</option>
                    <option value="part time">Part Time</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="salary" class="form-label">Salary:</label>
                <input type="text" class="form-control" id="salary" name="salary" required>
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location:</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>

            <div class="mb-3">
                <label for="posted_date" class="form-label">Posted Date:</label>
                <input type="date" class="form-control" id="posted_date" name="posted_date" required>
            </div>

            <div class="mb-3">
                <label for="closing_date" class="form-label">Closing Date:</label>
                <input type="date" class="form-control" id="closing_date" name="closing_date" required disabled>
            </div>

            <div class="mb-3">
                <label for="job_description" class="form-label">Job Description:</label>
                <textarea class="form-control" id="job_description" name="job_description" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="job_requirement" class="form-label">Job Requirement:</label>
                <textarea class="form-control" id="job_requirement" name="job_requirement" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="benefits" class="form-label">Benefits:</label>
                <textarea class="form-control" id="benefits" name="benefits" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="job_type" class="form-label">Job Type:</label>
                <input type="text" class="form-control" id="job_type" name="job_type" required>
            </div>

            <button type="submit" class="btn btn-primary">Create Job</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const postedDateInput = document.getElementById('posted_date');
            const closingDateInput = document.getElementById('closing_date');

            postedDateInput.addEventListener('change', function () {
                const postedDate = new Date(postedDateInput.value);
                const minClosingDate = new Date(postedDate);

                if (postedDateInput.value) {
                    closingDateInput.disabled = false;
                    closingDateInput.min = postedDate.toISOString().split('T')[0];
                } else {
                    closingDateInput.disabled = true;
                    closingDateInput.value = '';
                }
            });

            closingDateInput.addEventListener('change', function () {
                const postedDate = new Date(postedDateInput.value);
                const closingDate = new Date(closingDateInput.value);

                if (closingDate < postedDate) {
                    alert('Closing Date cannot be earlier than Posted Date.');
                    closingDateInput.value = '';
                }
            });
        });
    </script>

    <script src="library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>

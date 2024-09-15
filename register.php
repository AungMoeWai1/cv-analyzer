<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #333333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            margin-bottom: 5px;
            color: #555555;
        }
        input[type="text"], input[type="email"], input[type="password"], textarea {
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        input[type="radio"] {
            margin-right: 5px;
        }
        button {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        #employee-fields, #employer-fields {
            display: none;
        }
        .role-selection {
            display: flex;
            align-items: center;
        }
        .role-selection label {
            margin-right: 10px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
        }
    </style>
    <script>
        function toggleFields() {
            const role = document.querySelector('input[name="role"]:checked').value;
            const employeeFields = document.getElementById('employee-fields');
            const employerFields = document.getElementById('employer-fields');

            if (role === 'employee') {
                employeeFields.style.display = 'block';
                employerFields.style.display = 'none';
            } else {
                employeeFields.style.display = 'none';
                employerFields.style.display = 'block';
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="models/register_model.php" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone">
                </div>
            <div class="form-group role-selection">
                <label>Role:</label>
                <input type="radio" id="employee" name="role" value="employee" onclick="toggleFields()" required>
                <label for="employee">Employee</label>
                <input type="radio" id="employer" name="role" value="employer" onclick="toggleFields()" required>
                <label for="employer">Employer</label>
            </div>
            <div id="employee-fields" class="form-group">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form-group">
                    <label for="repassword">Re-enter Password:</label>
                    <input type="password" id="repassword" name="repassword">
                </div>
            </div>
            <div id="employer-fields" class="form-group">
                <div class="form-group">
                    <label for="company">Company:</label>
                    <input type="text" id="company" name="company">
                </div>
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" id="location" name="location">
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="password1">Password:</label>
                    <input type="password" id="password1" name="password1">
                </div>
                <div class="form-group">
                    <label for="repassword1">Re-enter Password:</label>
                    <input type="password" id="repassword1" name="repassword1">
                </div>
            </div>
            <button type="submit">Register</button>
        </form>
        <div class="register-link">
            <p>If you have account, <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>

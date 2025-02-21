<?php
session_start();
include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = $_POST['patient_name'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT patient_id, password FROM patients WHERE patient_name = :patient_name");
        $stmt->bindParam(':patient_name', $patient_name, PDO::PARAM_STR);
        $stmt->execute();
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $hashed_password = $patient['password'];
            if (password_verify($password, $hashed_password)) {
                $_SESSION['username'] = $patient_name;
                $_SESSION['status'] = 'Patient';
                $_SESSION['id_number'] = $patient['patient_id'];
                header("Location: patient_portal.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username.";
        }
    } catch (Exception $e) {
        $error = "Database connection error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
        }
        h2 {
            text-align: center;
            color: #007BFF;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }
        .signup-btn {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Patient Login</h2>
    <form action="" method="POST">
        <div class="form-group">
            <label for="patient_name">Patient Name:</label>
            <input type="text" id="patient_name" name="patient_name" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </form>
    <div class="signup-btn">
        <a href="patient_sign_up.php">Sign Up</a>
    </div>
</div>

</body>
</html>

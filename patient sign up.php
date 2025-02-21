<?php
session_start();
include 'db_connection.php'; // Ensure this path is correct

$error = ''; // Variable to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_name = trim($_POST['patient_name']);
    $contact_info = trim($_POST['contact_info']);
    $password = trim($_POST['password']);

    // Check if the patient already exists
    $stmt = $conn->prepare("SELECT patient_name FROM patients WHERE patient_name = ?");
    $stmt->bind_param("s", $patient_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already exists. Please choose a different name.";
    } else {
        // Hash the password before saving it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the query to insert new patient
        $stmt = $conn->prepare("INSERT INTO patients (patient_name, contact_info, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $patient_name, $contact_info, $hashed_password);

        if ($stmt->execute()) {
            $_SESSION['username'] = $patient_name; // Store patient_name in session
            $_SESSION['status'] = 'Patient'; // Assuming status is 'Patient'
            header("Location: patient_portal.php"); // Redirect to patient portal
            exit();
        } else {
            $error = "Error signing up. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Sign Up</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Add your styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .signup-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .signup-container h2 {
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
        .signup-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .signup-container button {
            width: 100%;
            padding: 10px;
            background: #35424a;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .signup-container button:hover {
            background: #2a2e32;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Patient Sign Up</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="patient_name" placeholder="Patient Name" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p style="text-align: center;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>

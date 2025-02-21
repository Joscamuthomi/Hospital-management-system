<?php
// Database connection
$host = "localhost";
$dbUsername = "root";  // Change this to your DB username
$dbPassword = "";      // Change this to your DB password
$dbname = "robins_hms"; // Change this to your DB name

// Create connection
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $status = $_POST['status'];

    // Basic validation
    if (empty($user_id) || empty($username) || empty($phone_number) || empty($password) || empty($status)) {
        echo "All fields are required!";
        exit();
    }

    // Hash the password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into the respective table based on user status
    switch (strtolower($status)) {
        case "doctor":
            $stmt = $conn->prepare("INSERT INTO doctor (user_id, username, phone_number, password) VALUES (?, ?, ?, ?)");
            break;
        case "recepsionist":
            $stmt = $conn->prepare("INSERT INTO receptionists (user_id, username, phone_number, password) VALUES (?, ?, ?, ?)");
            break;
        case "nurse":
            $stmt = $conn->prepare("INSERT INTO nurses (user_id, username, phone_number, password) VALUES (?, ?, ?, ?)");
            break;
        case "lab tach":
            $stmt = $conn->prepare("INSERT INTO lab_technicians (user_id, username, phone_number, password) VALUES (?, ?, ?, ?)");
            break;
        case "pharmacist":
            $stmt = $conn->prepare("INSERT INTO pharmacists (user_id, username, phone_number, password) VALUES (?, ?, ?, ?)");
            break;
        default:
            echo "Invalid status!";
            exit();
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("isss", $user_id, $username, $phone_number, $hashed_password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<?php

// Database connection details
$host = 'localhost'; // Replace with your host
$db = 'patient_db'; // Replace with your database name
$user = 'root'; // Replace with your database username
$pass = ''; // Replace with your database password

// Create a connection to the database
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get data from the POST request
$patientName = $_POST['patientName'];
$age = $_POST['age'];
$gender = $_POST['gender'];
$diagnosis = $_POST['diagnosis'];
$treatment = $_POST['treatment'];

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO patients (patient_name, age, gender, diagnosis, treatment) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisss", $patientName, $age, $gender, $diagnosis, $treatment);

// Execute the statement and check for success
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database insertion failed: ' . $conn->error]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>

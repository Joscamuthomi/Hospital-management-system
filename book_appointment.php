<?php
// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "robins_hms"; // Corrected the database name to use underscores instead of spaces

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection and throw an error if it fails
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure all required fields are set before attempting to process them
if (isset($_POST['patient_id'], $_POST['doctor_id'], $_POST['appointment_date'], $_POST['appointment_time'], $_POST['reason_for_visit'], $_POST['status'])) {
    // Prepare and bind the statement
    $stmt = $conn->prepare("INSERT INTO Appointments (PatientID, DoctorID, AppointmentDate, AppointmentTime, ReasonForVisit, Status) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Check if the statement was successfully prepared
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    // Bind parameters to the prepared statement
    $stmt->bind_param("iissss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason_for_visit, $status);

    // Get values from POST request and escape them for security
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason_for_visit = $conn->real_escape_string($_POST['reason_for_visit']); // Escaping the textarea input to avoid SQL injection
    $status = $_POST['status'];

    // Execute the prepared statement and handle success or error
    if ($stmt->execute()) {
        echo "Appointment booked successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    echo "Error: All fields are required.";
}

// Close the database connection
$conn->close();
?>

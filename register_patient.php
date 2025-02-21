<?php
// Database connection (replace with your actual DB connection details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "robins_hms";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$emergency_contact_name = $_POST['emergency_contact_name'];
$emergency_contact_relationship = $_POST['emergency_contact_relationship'];
$emergency_contact_phone = $_POST['emergency_contact_phone'];

// SQL query to insert the patient record into the database
$sql = "INSERT INTO patient_admissions 
        (first_name, last_name, dob, gender, phone, address, emergency_contact_name, emergency_contact_relationship, emergency_contact_phone) 
        VALUES 
        ('$first_name', '$last_name', '$dob', '$gender', '$phone', '$address', '$emergency_contact_name', '$emergency_contact_relationship', '$emergency_contact_phone')";

if ($conn->query($sql) === TRUE) {
    echo "Patient registered successfully!";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

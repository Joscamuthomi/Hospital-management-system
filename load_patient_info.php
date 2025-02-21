<?php
// load_patient_info.php

$host = 'your_host';
$dbname = 'robins_hms'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

$patientId = $_GET['patientId'];

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT name, dob, contact, medicalHistory, currentMedications, allergies FROM patient WHERE patient_id = :patientId");
    $stmt->execute(['patientId' => $patientId]);

    // Fetch the result
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode($patient);
} catch (PDOException $e) {
    echo json_encode(null);
    // You can log the error or return a custom error message here if needed
}
?>

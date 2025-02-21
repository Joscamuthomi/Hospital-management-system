<?php
// Database connection
$host = 'localhost'; // Database host
$db_name = 'robins_hms'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if request is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $patientId = $_POST['patientId'];
        $doctorName = $_POST['doctorName'];
        $recordType = $_POST['recordType'];
        $recordData = $_POST['recordData'];

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO medical_records (patient_id, doctor_name, record_type, record_data, created_at) 
                                VALUES (:patientId, :doctorName, :recordType, :recordData, NOW())");
        $stmt->bindParam(':patientId', $patientId);
        $stmt->bindParam(':doctorName', $doctorName);
        $stmt->bindParam(':recordType', $recordType);
        $stmt->bindParam(':recordData', $recordData);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record saved successfully.";
        } else {
            echo "Error saving record.";
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<?php
// Include database connection file
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $patient_id = $_POST['patient_id'];
    $patient_name = $_POST['patient_name'];
    $discharge_date = $_POST['discharge_date'];
    $discharge_summary = $_POST['discharge_summary'];
    $discharge_instructions = $_POST['discharge_instructions'];
    $follow_up_date = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : NULL;
    $doctor_notes = !empty($_POST['doctor_notes']) ? $_POST['doctor_notes'] : NULL;

    // SQL query to insert discharge data into the database
    $sql = "INSERT INTO patient_discharge (patient_id, patient_name, discharge_date, discharge_summary, discharge_instructions, follow_up_date, doctor_notes)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and execute the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssss", $patient_id, $patient_name, $discharge_date, $discharge_summary, $discharge_instructions, $follow_up_date, $doctor_notes);
        
        if ($stmt->execute()) {
            echo "Patient discharge information successfully recorded.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>

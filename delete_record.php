<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['status'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if (isset($_GET['record_id'])) {
    $record_id = $_GET['record_id'];

    try {
        // Prepare the SQL statement to delete the record
        $stmt = $pdo->prepare("DELETE FROM medical_records WHERE record_id = :record_id");
        $stmt->bindParam(':record_id', $record_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Redirect to medical records page with success message
            header("Location: patient_portal.php#medical_reports&message=Record deleted successfully.");
            exit();
        } else {
            throw new Exception('Error deleting the record.');
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
} else {
    header("Location: patient_portal.php#medical_reports&error=No record ID provided.");
    exit();
}
?>

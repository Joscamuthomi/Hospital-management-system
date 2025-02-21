<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['status'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

if (!isset($_GET['record_id'])) {
    echo "No record ID provided.";
    exit();
}

$record_id = $_GET['record_id'];

try {
    // Prepare the SQL statement to retrieve the specific record by its ID
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE record_id = :record_id");
    $stmt->bindParam(':record_id', $record_id, PDO::PARAM_INT);
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        throw new Exception('Record not found.');
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Record</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #007BFF;
            text-align: center;
            margin-bottom: 20px;
        }
        .message {
            padding: 20px;
            background-color: #e0f7fa;
            border-radius: 8px;
            border: 1px solid #007BFF;
            font-size: 16px;
        }
        .message p {
            margin: 8px 0;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 20px;
        }
        .back-btn {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .back-btn a {
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-btn a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Medical Record Details</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php else: ?>
        <div class="message">
            <p><strong>Date Created:</strong> <?php echo date("d-m-Y", strtotime($record['date_created'])); ?></p>
            <p><strong>Doctor's Name:</strong> <?php echo htmlspecialchars($record['doctor_name']); ?></p>
            <p><strong>Record Type:</strong> <?php echo htmlspecialchars($record['record_type']); ?></p>
            <p><strong>Record Data:</strong> <?php echo nl2br(htmlspecialchars($record['record_data'])); ?></p>
        </div>
    <?php endif; ?>

    <div class="back-btn">
        <a href="patient_record_view.php"><i class="fas fa-arrow-left"></i> Back to Medical Records</a>
    </div>
</div>

</body>
</html>

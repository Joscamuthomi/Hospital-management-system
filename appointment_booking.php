<?php
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['username']) || $_SESSION['status'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

// Get patient name from session
$patient_name = $_SESSION['patient_name']; // Make sure 'patient_name' is stored in session
$patient_id = $_SESSION['patient_id']; // Assuming patient_id is stored in session

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = $_POST['reason']; // New input for reason

    // Validate the input
    if (!empty($doctor_id) && !empty($appointment_date) && !empty($appointment_time) && !empty($reason)) {
        try {
            // Prepare the SQL statement to insert the appointment
            $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason) VALUES (:patient_id, :doctor_id, :appointment_date, :appointment_time, :reason)");
            $stmt->bindParam(':patient_id', $patient_id);
            $stmt->bindParam(':doctor_id', $doctor_id);
            $stmt->bindParam(':appointment_date', $appointment_date);
            $stmt->bindParam(':appointment_time', $appointment_time);
            $stmt->bindParam(':reason', $reason); // Bind reason to the query

            if ($stmt->execute()) {
                $success_message = "Appointment booked successfully.";
            } else {
                $error_message = "Error booking the appointment.";
            }
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Fetch doctors for selection where status is 'doctor'
$stmt = $pdo->prepare("SELECT * FROM staff WHERE status = 'doctor'");
$stmt->execute();
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Book an Appointment</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form action="appointment_booking.php" method="POST">
        <div class="form-group">
            <label for="patient_name">Patient Name:</label>
            <input type="text" class="form-control" id="patient_name" name="patient_name" value="<?= htmlspecialchars($patient_name) ?>" readonly>
        </div>
        <div class="form-group">
            <label for="doctor_id">Select Doctor:</label>
            <select class="form-control" id="doctor_id" name="doctor_id" required>
                <option value="">-- Select Doctor --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?= htmlspecialchars($doctor['doctor_id']) ?>">
                        <?= htmlspecialchars($doctor['doctor_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="appointment_date">Appointment Date:</label>
            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
        </div>
        <div class="form-group">
            <label for="appointment_time">Appointment Time:</label>
            <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason for Appointment:</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Book Appointment</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

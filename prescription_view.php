<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db_connection.php'; // Ensure this file connects to the database

// Check if the necessary session variables are set
if (!isset($_SESSION['username']) || !isset($_SESSION['id_number'])) {
    header("Location: login.php"); // Redirecting to login page
    exit();
}

// Get patient name and ID from the session
$username = $_SESSION['username']; // Patient name
$idNumber = $_SESSION['id_number']; // Patient ID

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare a statement to fetch all prescriptions grouped by prescription_id
$stmt = $conn->prepare("SELECT p.prescription_id AS prescription_id, 
                                p.prescription_date AS prescription_date, 
                                pm.medication, 
                                pm.prescription, 
                                pm.days, 
                                p.doctor_name 
                         FROM prescriptions p
                         JOIN prescription_medications pm ON p.prescription_id = pm.prescription_id
                         WHERE p.id_number = ? 
                         ORDER BY p.prescription_date DESC"); // Ordering by prescription date
$stmt->bind_param("s", $idNumber); // Assuming id_number is a string

// Execute the statement
$stmt->execute();
$result = $stmt->get_result();

$prescriptions = [];
if ($result->num_rows > 0) {
    // Group prescriptions by prescription_id
    while ($row = $result->fetch_assoc()) {
        $prescriptions[$row['prescription_id']]['info'] = $row; // Store prescription info
        $prescriptions[$row['prescription_id']]['medications'][] = [
            'medication' => $row['medication'],
            'prescription' => $row['prescription'],
            'days' => $row['days'],
        ]; // Store medications
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for the entire page */
        }
        .container {
            background-color: #ffffff; /* White background for the content */
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #007bff; /* Blue color for the heading */
        }
        .table {
            background-color: #e9ecef; /* Light gray background for the table */
            border-radius: 10px;
            overflow: hidden;
        }
        th {
            background-color: #007bff; /* Blue header for the table */
            color: white; /* White text for the header */
        }
        .sub-table {
            background-color: #ffffff; /* White background for the medication sub-table */
        }
        .sub-table th {
            background-color: #f1f1f1; /* Light gray background for the medication header */
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
</head>
<body>

<div class="container mt-5">
    <h1>My Prescriptions</h1>

    <?php if (count($prescriptions) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Prescription ID</th>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription_id => $prescription_group): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($prescription_group['info']['prescription_id']); ?></td>
                        <td><?php echo htmlspecialchars($prescription_group['info']['prescription_date']); ?></td>
                        <td><?php echo htmlspecialchars($prescription_group['info']['doctor_name']); ?></td>
                        <td>
                            <!-- Pass the entire group of medications for a particular prescription_id to the JavaScript function -->
                            <button class="btn btn-primary" onclick="generatePrescriptionPDF(<?php echo htmlspecialchars(json_encode($prescription_group)); ?>, '<?php echo htmlspecialchars($username); ?>', '<?php echo htmlspecialchars($idNumber); ?>')">Download PDF</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <table class="table table-sm sub-table">
                                <thead>
                                    <tr>
                                        <th>Medication</th>
                                        <th>Prescription</th>
                                        <th>Number of Days</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($prescription_group['medications'] as $medication): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($medication['medication']); ?></td>
                                            <td><?php echo htmlspecialchars($medication['prescription']); ?></td>
                                            <td><?php echo htmlspecialchars($medication['days']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No prescriptions found.</p>
    <?php endif; ?>

</div>

<script>
    function generatePrescriptionPDF(prescriptionGroup, username, idNumber) {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        doc.setTextColor(0, 255, 0);
        doc.setFontSize(18);
        doc.text("Livecare Hospital", 70, 10);
        
        doc.setTextColor(0, 0, 255);
        doc.setFontSize(18);
        doc.text("Prescription Information", 70, 20);

        doc.setFontSize(12);
        doc.setTextColor(34, 139, 34);
        doc.text(`Name: ${username}`, 20, 30);
        doc.text(`ID Number: ${idNumber}`, 20, 40);

        // Create table data from the medications
        const prescriptionData = prescriptionGroup.medications.map(medication => [
            medication.medication,
            medication.prescription,
            medication.days,
        ]);

        // Generate a table with all medications in the same prescription session
        doc.autoTable({
            head: [['Medication', 'Prescription', 'Number of Days']],
            body: prescriptionData,
            startY: 60,
            theme: 'striped',
            styles: { halign: 'left' },
        });

        // Adding signature and contact information
        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text("Doctor's Name: ___________________________", 20, doc.autoTable.previous.finalY + 30);
        doc.text("Date: __________________", 20, doc.autoTable.previous.finalY + 40);
        doc.text("Stamp: __________________", 120, doc.autoTable.previous.finalY + 40);

        doc.setFontSize(12);
        doc.setTextColor(34, 139, 34);
        doc.text("Tel: +254724962943", 20, doc.autoTable.previous.finalY + 60);

        doc.setFontSize(12);
        doc.setTextColor(165, 42, 42);
        doc.text("Email: livecare@hotmail.com", 20, doc.autoTable.previous.finalY + 70);

        // Save the PDF
        doc.save('PrescriptionDetails.pdf');
    }
</script>

</body>
</html>

<?php
// Close the statement and database connection
$stmt->close();
$conn->close();
?>

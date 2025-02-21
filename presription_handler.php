<?php
session_start();
include 'db_connection.php'; // Ensure this file connects to the database

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username']; // Assuming the patient name is passed
    $idNumber = $_POST['idNumber']; // Assuming patient ID is passed
    $drName = $_SESSION['doctor_name']; // Get doctor's name from the session

    // Prepare to insert the overall prescription, including the date (automatically using NOW())
    $stmt = $conn->prepare("INSERT INTO prescriptions (patient_name, id_number, doctor_name, prescription_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $username, $idNumber, $drName);
    
    // Execute the statement to insert the prescription details
    if (!$stmt->execute()) {
        echo "Error inserting prescription details: " . $stmt->error;
        exit();
    }

    // Get the newly created prescription_id
    $prescriptionID = $stmt->insert_id;

    // Prepare to insert medications
    $medications = [];
    for ($i = 0; $i < count($_POST['medication']); $i++) {
        $medications[] = [
            'medication' => $_POST['medication'][$i],
            'prescription' => $_POST['prescription'][$i],
            'days' => $_POST['days'][$i]
        ];
    }

    // Prepare the SQL statement to insert medications
    $stmt = $conn->prepare("INSERT INTO prescription_medications (prescription_id, medication, prescription, days) VALUES (?, ?, ?, ?)");
    
    foreach ($medications as $med) {
        // Bind parameters using the same prescription_id
        $stmt->bind_param("isss", $prescriptionID, $med['medication'], $med['prescription'], $med['days']);
        
        // Execute the statement for each medication
        if (!$stmt->execute()) {
            echo "Error inserting medication details: " . $stmt->error;
        }
    }

    // Close the statement
    $stmt->close();

    // Show success message
    echo "Prescription saved successfully.<br>";
    // Output a download button
    echo '<button onclick="generatePrescriptionPDF(' . htmlspecialchars(json_encode($medications)) . ', \'' . addslashes($username) . '\', \'' . addslashes($idNumber) . '\', \'' . addslashes($drName) . '\')">Download Prescription</button>';
}

// Close database connection
$conn->close();
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>

<script>
    function generatePrescriptionPDF(medications, username, idNumber, drName) {
        const data = medications.map(med => [med.medication, med.prescription, med.days]);

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

        // Use autoTable to create the table
        doc.autoTable({
            head: [['Medication', 'Prescription', 'Number of Days']],
            body: data,
            startY: 60,
            theme: 'striped',
            styles: { halign: 'left' },
        });

        doc.setFontSize(12);
        doc.setTextColor(0, 0, 0);
        doc.text("Doctor's Name: ___________________________", 20, doc.autoTable.previous.finalY + 30);
        doc.text(drName, 70, doc.autoTable.previous.finalY + 30);

        doc.text("Date: __________________", 20, doc.autoTable.previous.finalY + 40);
        doc.text("Stamp: __________________", 120, doc.autoTable.previous.finalY + 40);

        doc.setFontSize(12);
        doc.setTextColor(34, 139, 34);
        doc.text("Tel: +254724962943", 20, doc.autoTable.previous.finalY + 60);

        doc.setFontSize(12);
        doc.setTextColor(165, 42, 42);
        doc.text("Email: livecare@hotmail.com", 20, doc.autoTable.previous.finalY + 70);

        doc.save('PrescriptionDetails.pdf');
    }
</script>

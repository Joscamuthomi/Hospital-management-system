<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "robins_hms";  // Your database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $fullName = $_POST['fullName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $emergencyName = $_POST['emergencyName'];
    $emergencyPhone = $_POST['emergencyPhone'];
    $admittingDepartment = $_POST['admittingDepartment'];
    $admissionDate = $_POST['admissionDate'];
    $reasonForAdmission = $_POST['reasonForAdmission'];

    // Collect ward allocation data
    $wardType = $_POST['wardType'];
    $roomNumber = $_POST['roomNumber'];

    // Insert data into the admissions table
    $stmt = $conn->prepare("INSERT INTO admissions (full_name, dob, gender, phone, address, emergency_name, emergency_phone, admitting_department, admission_date, reason_for_admission, ward_type, room_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $fullName, $dob, $gender, $phone, $address, $emergencyName, $emergencyPhone, $admittingDepartment, $admissionDate, $reasonForAdmission, $wardType, $roomNumber);

    if ($stmt->execute()) {
        // After successful insertion, output the message and button
        echo "<p>Patient admitted successfully!</p>";
        echo "<button id='downloadBtn'>Download PDF</button>";

        // Pass data to JavaScript via JSON
        $patientData = [
            "fullName" => $fullName,
            "dob" => $dob,
            "gender" => $gender,
            "phone" => $phone,
            "address" => $address,
            "emergencyName" => $emergencyName,
            "emergencyPhone" => $emergencyPhone,
            "admittingDepartment" => $admittingDepartment,
            "admissionDate" => $admissionDate,
            "reasonForAdmission" => $reasonForAdmission,
            "wardType" => $wardType,
            "roomNumber" => $roomNumber
        ];

        echo "<script>const patientData = " . json_encode($patientData) . ";</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    document.getElementById('downloadBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Set font and colors
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(31, 78, 121);  // Dark blue color
        doc.setFontSize(18);
        doc.text('Robins Hospital', 105, 20, null, null, 'center');  // Centered title
        
        // Horizontal line below title
        doc.setDrawColor(31, 78, 121); // Line color
        doc.line(15, 25, 195, 25);  // Draw a horizontal line

        // Subtitle
        doc.setFontSize(14);
        doc.setFont('helvetica', 'normal');
        doc.text('Patient Admission Details', 105, 35, null, null, 'center');

        // Patient Information Section
        doc.setTextColor(0, 0, 0);  // Black text for content
        doc.setFontSize(12);
        let startY = 50;  // Starting Y position

        // Create a consistent layout for labels and data
        doc.text('Full Name: ', 15, startY);
        doc.text(patientData.fullName, 60, startY);
        startY += 10;

        doc.text('Date of Birth: ', 15, startY);
        doc.text(patientData.dob, 60, startY);
        startY += 10;

        doc.text('Gender: ', 15, startY);
        doc.text(patientData.gender, 60, startY);
        startY += 10;

        doc.text('Phone: ', 15, startY);
        doc.text(patientData.phone, 60, startY);
        startY += 10;

        doc.text('Address: ', 15, startY);
        doc.text(patientData.address, 60, startY);
        startY += 10;

        doc.text('Emergency Contact: ', 15, startY);
        doc.text(patientData.emergencyName, 60, startY);
        startY += 10;

        doc.text('Emergency Phone: ', 15, startY);
        doc.text(patientData.emergencyPhone, 60, startY);
        startY += 10;

        doc.text('Admitting Department: ', 15, startY);
        doc.text(patientData.admittingDepartment, 60, startY);
        startY += 10;

        doc.text('Admission Date: ', 15, startY);
        doc.text(patientData.admissionDate, 60, startY);
        startY += 10;

        doc.text('Reason for Admission: ', 15, startY);
        doc.text(patientData.reasonForAdmission, 60, startY);
        startY += 10;

        doc.text('Ward Type: ', 15, startY);
        doc.text(patientData.wardType, 60, startY);
        startY += 10;

        doc.text('Room Number: ', 15, startY);
        doc.text(patientData.roomNumber, 60, startY);
        startY += 20;

        // Add a section for signature and stamp
        doc.setFont('helvetica', 'bold');
        doc.text('Signature and Stamp:', 15, startY);

        // Draw empty boxes for signature and stamp
        startY += 20;
        doc.setDrawColor(0, 0, 0);  // Black line for boxes
        doc.rect(15, startY, 80, 40);  // Signature box
        doc.text('Patient Signature', 25, startY + 30);  // Label inside box
        
        doc.rect(115, startY, 80, 40);  // Stamp box
        doc.text('Hospital Stamp', 130, startY + 30);  // Label inside box

        // Trigger PDF download
        doc.save('admission-details.pdf');
    });
</script>

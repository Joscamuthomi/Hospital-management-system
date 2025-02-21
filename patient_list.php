<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "robins_hms"; // Update with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle deletion of a patient record
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM patients WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Patient record deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting record.');</script>";
    }
    $stmt->close();
}

// Fetch all admitted patients
$sql = "SELECT * FROM admissions";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admitted Patients List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .edit-btn {
            background-color: #008CBA;
        }
        .delete-btn {
            background-color: #f44336;
        }
    </style>
</head>
<body>

    <h1>Admitted Patients List</h1>

    <table>
        <thead>
            <tr>
                <th>Patient ID</th>
                <th>Full Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Emergency Contact</th>
                <th>Admission Date</th>
                <th>Reason for Admission</th>
                <th>ward_type</th>
                <th>room_No</th>
                <th>admiting_department</th>
                <th>list_ Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['dob']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['emergency_name'] . ' (' . $row['emergency_phone'] . ')'); ?></td>
                        <td><?php echo htmlspecialchars($row['admission_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['reason_for_admission']); ?></td>
                        <td><?php echo htmlspecialchars($row['ward_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['admitting_department']); ?></td>
                        <td>
                            <a href="edit_admissions.php?id=<?php echo $row['id']; ?>"><button class="edit-btn">Edit</button></a>
                            <a href="view_admissions.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');"><button class="delete-btn">Delete</button></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr>
                    <td colspan="10">No patients admitted.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="admision form.html"><button>Admit New Patient</button></a>

</body>
</html>

<?php
$conn->close();
?>

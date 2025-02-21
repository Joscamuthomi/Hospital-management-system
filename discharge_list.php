<?php
// Connect to the database
include 'db_connection.php';

// Check if delete request is made
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM patient_discharge WHERE patient_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<p>Patient with ID $delete_id deleted successfully.</p>";
    } else {
        echo "<p>Error deleting patient.</p>";
    }
    $stmt->close();
}

// Query to fetch discharged patient data
$sql = "SELECT patient_id, patient_name, discharge_date, discharge_summary FROM patient_discharge";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discharge List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            color: blue;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .action-btn {
            padding: 5px 10px;
            color: white;
            background-color: #4CAF50;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .delete-btn {
            background-color: red;
        }
    </style>
</head>
<body>

<h2>List of Discharged Patients</h2>

<table>
    <thead>
        <tr>
            <th>Patient ID</th>
            <th>Patient Name</th>
            <th>Discharge Date</th>
            <th>Discharge Summary</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['patient_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['discharge_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['discharge_summary']) . "</td>";
                echo "<td>
                        <a class='action-btn' href='edit_discharge.php?patient_id=" . $row['patient_id'] . "'>Edit</a>
                        <a class='action-btn delete-btn' href='?delete_id=" . $row['patient_id'] . "' onclick='return confirm(\"Are you sure you want to delete?\");'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No discharged patients found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['status'] !== 'Patient') {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$id_number = $_SESSION['id_number']; // Retrieve patient ID from session

try {
    // Fetch the medical records of the logged-in patient
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = :id_number ORDER BY date_created DESC");
    $stmt->bindParam(':id_number', $id_number, PDO::PARAM_INT);
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$records) {
        throw new Exception('No records found for this patient.');
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
    <title>View Medical Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007BFF;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: left;
        }
        table th {
            background-color: #007BFF;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .actions {
            text-align: center;
        }
        .actions a {
            color: #007BFF;
            margin: 0 10px;
            text-decoration: none;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }

        /* Mobile responsive styles */
        @media (max-width: 768px) {
            table, th, td {
                display: block;
                width: 100%;
            }
            table thead {
                display: none;
            }
            table tr {
                margin-bottom: 10px;
                display: block;
            }
            table td {
                text-align: right;
                position: relative;
                padding-left: 50%;
            }
            table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                top: 0;
                padding-left: 10px;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Medical Records</h2>
    <?php if (isset($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php elseif (empty($records)): ?>
        <div class="error">No medical records found.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Doctor's Name</th>
                    <th>Record Data</th>
                    <th>Record Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <tr>
                        <td data-label="Date"><?php echo date("d-m-Y", strtotime($record['date_created'])); ?></td>
                        <td data-label="Doctor's Name"><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                        <td data-label=""="Record Data"><?php echo htmlspecialchars($record['record_data']); ?></td>
                        <td data-label="Record Type"><?php echo htmlspecialchars($record['record_type']); ?></td>
                        <td data-label="Actions" class="actions">
                            <a href="view_record.php?record_id=<?php echo $record['record_id']; ?>"><i class="fas fa-eye"></i> View</a>
                            <a href="delete_record.php?record_id=<?php echo $record['record_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

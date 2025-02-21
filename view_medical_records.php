<?php
// Database connection
$host = 'localhost'; // Change if necessary
$dbname = 'robins_hms'; // Your database name
$username = 'root'; // Your database username
$password = ''; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize records array
$records = [];

// Initialize patient_id
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

// Debugging output to check $_GET parameters
// Uncomment the following lines to see the output during development
// echo '<pre>';
// print_r($_GET);
// echo '</pre>';

if ($patient_id) {
    // Fetch medical records for the specified patient ID
    $sql = "SELECT mr.patient_id, p.patient_name, mr.doctor_name, mr.record_type, mr.record_data, mr.created_at 
            FROM medical_records mr 
            JOIN patients p ON mr.patient_id = p.patient_id 
            WHERE mr.patient_id = :patient_id"; // Filter by patient ID
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['patient_id' => $patient_id]);
} else {
    // Fetch all medical records if no patient ID is specified
    $sql = "SELECT mr.patient_id, p.patient_name, mr.doctor_name, mr.record_type, mr.record_data, mr.created_at 
            FROM medical_records mr 
            JOIN patients p ON mr.patient_id = p.patient_id"; // Assuming patient table is 'patients'
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medical Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-btn {
            padding: 8px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }

        .search-btn:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-btn {
            display: inline-block;
            padding: 8px 12px;
            font-size: 14px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #005f73;
        }

        .no-records {
            text-align: center;
            color: #999;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Medical Records</h1>

        <!-- Search Functionality -->
        <div class="search-container">
            <form action="" method="GET">
                <input type="text" name="patient_id" placeholder="Enter Patient ID to search" required>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Doctor/Staff Name</th>
                    <th>Record Type</th>
                    <th>Record Data</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records): ?>
                    <?php foreach ($records as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['patient_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['record_type']); ?></td>
                            <td><?php echo htmlspecialchars($record['record_data']); ?></td>
                            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                            <td>
                                <a href="view_record.php" class="action-btn">View</a>
                                <a href="edit_record.php?patient_id=<?php echo $record['patient_id']; ?>" class="action-btn">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-records">No medical records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

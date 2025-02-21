<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional: Link to a CSS file for external styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <form action="./book_appointment.php" method="post">
        <h1>Book an Appointment</h1>

        <label for="patient_id">Patient ID:</label>
        <input type="number" id="patient_id" name="patient_id" required>

        <label for="doctor_id">Select Doctor:</label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="" disabled selected>Select a doctor</option>
            <?php
            // Enable error reporting
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Include database connection
            include('db_connection.php');

            // Check the database connection
            if (!$conn) {
                echo '<option value="" disabled>Error connecting to the database</option>';
                error_log("Connection failed: " . mysqli_connect_error());
            } else {
                // Query to get staff members who are doctors
                $query = "SELECT id, name FROM staff WHERE status = 'Doctor'";
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo '<option value="" disabled>Error fetching doctors</option>';
                    error_log("Database query failed: " . mysqli_error($conn));
                } else {
                    // Loop through the result and create an option for each doctor
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                    }
                }
                
                // Close the connection after fetching the data
                mysqli_close($conn);
            }
            ?>
        </select>

        <label for="appointment_date">Appointment Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" required>

        <label for="appointment_time">Appointment Time:</label>
        <input type="time" id="appointment_time" name="appointment_time" required>

        <label for="reason_for_visit">Reason for Visit:</label>
        <textarea id="reason_for_visit" name="reason_for_visit" required></textarea>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit">Book Appointment</button>
    </form>
</body>
</html>

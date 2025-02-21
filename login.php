<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $status = $_POST['status']; // Get the role/status

    // Define which table to use based on the user status
    switch (strtolower($status)) {
        case 'doctor':
            $table = 'doctor';
            break;
        case 'recepsionist':
            $table = 'receptionists';
            break;
        case 'nurse':
            $table = 'nurses';
            break;
        case 'lab_tach':
            $table = 'lab_technicians';
            break;
        case 'pharmacist':
            $table = 'pharmacists';
            break;
        default:
            echo "Invalid role selected.";
            exit();
    }

    // Prepare a statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, phone_number FROM $table WHERE username = ?");
    $stmt->bind_param("s", $username); // Bind the username

    // Execute the statement
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password (assumes passwords are hashed in the database)
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['status'] = $status;     // Store user role/status in session
            $_SESSION['staff_id'] = $user['id'];  // Store user ID in session
            $_SESSION['staff_name'] = $user['username']; // Store staff's username in session
            $_SESSION['phone_number'] = $user['phone_number']; // Store phone number in session

            // Redirect based on the user's status
            switch (strtolower($status)) {
                case 'doctor':
                    $_SESSION['doctor_name'] = $user['username']; // Store doctor's username in session
                    header("Location: doctor_dashboard.php"); // Redirect Doctor to their dashboard
                    break;
                case 'recepsionist':
                    header("Location: recepsionist_dashboard.php"); // Redirect Receptionist to their dashboard
                    break;
                case 'nurse':
                    header("Location: nurse_dashboard.php"); // Redirect Nurse to their dashboard
                    break;
                case 'lab_tach':
                    header("Location: lab_technician_dashboard.php"); // Redirect Lab Technician to their dashboard
                    break;
                case 'pharmacist':
                    header("Location: pharmacist_dashboard.php"); // Redirect Pharmacist to their dashboard
                    break;
                default:
                    header("Location: staff_portal.php"); // Default redirect for other roles
                    break;
            }
            exit();
        } else {
            // Invalid password
            echo "Invalid username or password.";
        }
    } else {
        // Invalid login
        echo "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

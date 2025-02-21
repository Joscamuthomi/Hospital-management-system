<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header('Location: login.php');
    exit;
}

// Get the receptionist's username
$receptionist_username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
   
    <style>
        body {
            background-image: url('images/reception.jpg'); /* Replace with hospital-friendly background image */
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .navbar {
            background-color: rgba(0, 64, 128, 0.9); /* Semi-transparent hospital-friendly blue */
            border-radius: 0 0 15px 15px; /* Rounded bottom corners */
        }
        .navbar-toggler {
            border: none;
        }
        .navbar-nav {
            justify-content: space-between; /* Spread main menus evenly */
            flex-wrap: nowrap;
            width: 100%;
        }
        .navbar-nav a {
            text-align: center;
            color: white;
            margin-bottom: 15px;
            position: relative;
        }
        .navbar-nav i {
            font-size: 2rem;
        }
        .navbar-nav span {
            display: block;
            font-size: 0.8rem;
            margin-top: 5px;
        }
        .nav-item {
            position: relative; /* Important for submenu positioning */
        }
        .submenu {
            display: none; /* Hide submenus initially */
            background-color: green;
            border-radius: 5px;
            padding: 10px;
            position: absolute;
            top: 100%; /* Position submenu just below the parent item */
            left: 0;
            width: max-content; /* Set width to max-content for auto width based on content */
            z-index: 1;
        }
        .nav-item:hover .submenu, .nav-item:focus-within .submenu {
            display: block; /* Show submenu on hover or focus */
        }
        .submenu a {
            display: block;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .right-frame {
            margin-top: 70px; /* Adjust margin for the fixed navbar */
            padding: 20px;
        }
        #welcome-frame {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            bottom: 20px;
            position: absolute;
        }
        @media (min-width: 768px) {
            .navbar-brand {
                display: none; /* Hide brand name on larger screens */
            }
        }
    </style>
</head>
<body>
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person"></i>
                        <span>Patient Management</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Add Patient</a>
                        <a href="#">View Patients</a>
                        <a href="#">Edit Patient Info</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Schedule Appointment</a>
                        <a href="#">Upcoming Appointments</a>
                        <a href="#">Appointment History</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-cash"></i>
                        <span>Billing</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Invoices</a>
                        <a href="#">Payment History</a>
                        <a href="#">Insurance Info</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-envelope"></i>
                        <span>Messages</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Send Message</a>
                        <a href="#">View Inbox</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Reports</span>
                    </a>
                    <div class="submenu">
                        <a href="#">View Reports</a>
                        <a href="#">Generate Report</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-clock"></i>
                        <span>Reminders</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Set Reminder</a>
                        <a href="#">View Reminders</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-person-check"></i>
                        <span>Check-In</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Check-In Patient</a>
                        <a href="#">View Check-Ins</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-file-earmark-plus"></i>
                        <span>Documents</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Upload Document</a>
                        <a href="#">View Documents</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-gear"></i>
                        <span>Settings</span>
                    </a>
                    <div class="submenu">
                        <a href="#">Profile Settings</a>
                        <a href="#">System Settings</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Right Frame with Welcome Message and Photo -->
    <div class="right-frame">
        <div id="welcome-frame" class="right-frame">
            <h3>Welcome, <span id="username"><?php echo htmlspecialchars($receptionist_username); ?></span>!</h3>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

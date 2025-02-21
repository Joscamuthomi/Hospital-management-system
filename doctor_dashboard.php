<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] !== 'Doctor') {
    header("Location: login.php");
    exit();
}


// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get the username and status from the session
$username = $_SESSION['username'];
$status = $_SESSION['status'];

include 'db_connection.php'; // Ensure this path is correct
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            background: #35424a;
            color: #ffffff;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: width 0.3s;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: #ffffff;
            text-decoration: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar a:hover {
            background: #2a2e32;
        }

        .submenu {
            display: none;
            padding-left: 20px;
            background: #2a2e32;
        }

        .submenu a {
            padding: 10px;
            color: #ffffff;
        }

        .content-frame {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }

        iframe {
            width: 100%;
            height: calc(100vh - 40px);
            border: none;
        }

        .toggle-btn {
            font-size: 24px;
            cursor: pointer;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 10;
            color:blue;
        }

        h1 {
            margin: 0;
        }
    </style>
</head>
<body>
<div class="sidebar" id="sidebar">
    <!-- Dashboard Menu -->
    <a href="javascript:void(0);" onclick="loadDashboard();"><i ></i> </a>
    <a href="javascript:void(0);" onclick="loadDashboard();"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    
    <!-- Patient Management -->
    <a href="#patient-management" onclick="toggleMenu('patient-management-submenu')">
        <i class="fas fa-user-injured"></i> Patient Management
    </a>
    <div class="submenu" id="patient-management-submenu">
        <a href="admission options.html" target="content-frame"><i class="fas fa-procedures"></i> Patient Admission</a>
        <a href="patient_discharge.html" target="content-frame"><i class="fas fa-door-open"></i> Patient Discharge</a>
        <a href="patient_attendance_list.html" target="content-frame"><i class="fas fa-list"></i> Patient Attendance List</a>
    </div>

    <!-- Medical Records -->
    <a href="#medical_records" onclick="toggleMenu('medical-records-submenu')">
        <i class="fas fa-file-medical"></i> Medical Records
    </a>
    <div class="submenu" id="medical-records-submenu">
        <a href="view_medical_records.php" target="content-frame"><i class="fas fa-eye"></i> View Medical Records</a>
        <a href="medical_records.html" target="content-frame"><i class="fas fa-plus"></i> Add a Medical Record</a>
    </div>

    <!-- Medical Orders -->
    <a href="#medical-orders" onclick="toggleMenu('medical-orders-submenu')">
        <i class="fas fa-vials"></i> Medical Orders
    </a>
    <div class="submenu" id="medical-orders-submenu">
        <a href="lab_test_orders.html" target="content-frame"><i class="fas fa-flask"></i> Lab Test Orders</a>
        <a href="medical_imaging_orders.html" target="content-frame"><i class="fas fa-x-ray"></i> Medical Imaging Orders</a>
        <a href="pathology_orders.html" target="content-frame"><i class="fas fa-microscope"></i> Pathology Orders</a>
    </div>

    <!-- Appointments -->
    <a href="#appointments" onclick="toggleMenu('appointments-submenu')">
        <i class="fas fa-calendar-alt"></i> Appointments
    </a>
    <div class="submenu" id="appointments-submenu">
        <a href="upcoming_appointments.html" target="content-frame"><i class="fas fa-clock"></i> Upcoming Appointments</a>
        <a href="schedule_appointment.html" target="content-frame"><i class="fas fa-calendar"></i> Schedule an Appointment</a>
    </div>

    <!-- Prescriptions -->
    <a href="#prescriptions" onclick="toggleMenu('prescriptions-submenu')">
        <i class="fas fa-pills"></i> Prescriptions
    </a>
    <div class="submenu" id="prescriptions-submenu">
        <a href="prescriptions.html" target="content-frame"><i class="fas fa-prescription"></i> Prescribe a Patient</a>
    </div>

    <!-- Medical Reports -->
    <a href="#medical-reports" onclick="toggleMenu('medical-reports-submenu')">
        <i class="fas fa-notes-medical"></i> Medical Reports
    </a>
    <div class="submenu" id="medical-reports-submenu">
        <a href="lab_test_reports.html" target="content-frame"><i class="fas fa-flask"></i> Lab Test Reports</a>
        <a href="pathology_reports.html" target="content-frame"><i class="fas fa-microscope"></i> Pathology Reports</a>
        <a href="medical_imaging_reports.html" target="content-frame"><i class="fas fa-x-ray"></i> Medical Imaging Reports</a>
    </div>

    <!-- Referral Management -->
    <a href="#referral-management" onclick="toggleMenu('referral-management-submenu')">
        <i class="fas fa-user-md"></i> Referral Management
    </a>
    <div class="submenu" id="referral-management-submenu">
        <a href="download_referral_form.html" target="content-frame"><i class="fas fa-download"></i> Download Referral Form</a>
        <a href="refer_patient.html" target="content-frame"><i class="fas fa-paper-plane"></i> Refer a Patient</a>
    </div>

    <!-- Task Reminders -->
    <a href="task_reminders.html" target="content-frame"><i class="fas fa-tasks"></i> Task Reminders</a>
</div>

<span class="toggle-btn" onclick="toggleSidebar()">&#9776;</span>

<div class="content-frame">
    <iframe src="about:blank" name="content-frame" id="content-frame"></iframe>
    <script>
        function loadDashboard() {
            const username = "<?php echo htmlspecialchars($_SESSION['username']); ?>"; 
            document.getElementById("content-frame").srcdoc = `
                <html>
                <head>
                    <title>Dashboard</title>
                    <style>
                     body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }

        .image-container {
            position: relative;
            width: 100%;
            
            height: 100%;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 255, 0.5), rgba(0, 0, 255, 0));
            border-radius: 15px;
        }

        .content {
            position: absolute;
            bottom: 20px;
            left: 20px;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }
                        .welcome-message {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            height: 100vh;
                            font-size: 24px;
                            color: blue;
                            position: relative;
                            top: 10%;
                            left: 10%;
                        }
                    </style>
                </head>
                <body>
               
                <div class="image-container">
                
                   <img src="images/clinical mang2.jpg" alt="Sample Image">
                   <div class="overlay"></div>
                         <div class="content">Welcome Dr. ${username}!</div>
                        </div>
                    
                </body>
                </html>
            `;
        }

        function toggleMenu(id) {
            const menu = document.getElementById(id);
            const isDisplayed = menu.style.display === 'block';
            document.querySelectorAll('.submenu').forEach(sub => sub.style.display = 'none');
            if (!isDisplayed) menu.style.display = 'block';
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isSidebarVisible = sidebar.style.width !== '0px';
            sidebar.style.width = isSidebarVisible ? '0px' : '250px';
            document.querySelector('.content-frame').style.marginLeft = isSidebarVisible ? '0px' : '250px';
        }

        loadDashboard(); // Load Dashboard on page load
    </script>
</div>
</body>
</html>

<?php
session_start();
require_once '../partials/dbconnect.php';

// Assuming you have a session variable storing student ID after login
$studentID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";

if ($studentID) {
    // Fetch student information from the database
    $queryStudent = "SELECT ut.user_fname, ut.user_lname, sd.* FROM user_table ut
                     JOIN student_details sd ON ut.user_id = sd.user_id
                     WHERE ut.user_id = '$studentID'";
    $resultStudent = mysqli_query($conn, $queryStudent);

    if ($resultStudent && mysqli_num_rows($resultStudent) > 0) {
        $studentInfo = mysqli_fetch_assoc($resultStudent);

        // Concatenate first name and last name in PHP
        $studentInfo['full_name'] = $studentInfo['user_fname'] . ' ' . $studentInfo['user_lname'];

        // Fetch attendance status and date
        $queryAttendance = "SELECT attendance_date, status FROM attendance
                            WHERE user_id = '$studentID'";
        $resultAttendance = mysqli_query($conn, $queryAttendance);

        // Store attendance data in an array
        $attendanceData = array();
        while ($row = mysqli_fetch_assoc($resultAttendance)) {
            $attendanceData[] = $row;
        }
    } else {
        // Handle case where student ID is not found
        $studentInfo = array(); // or set to default values
        $attendanceData = array();
    }
} else {
    // Handle case where student is not logged in
    $studentInfo = array(); // or redirect to login page
    $attendanceData = array();
}

// Close the database connection
mysqli_close($conn);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Dashboard</title>
    <link rel="stylesheet" href="student-attendance-dashboard.css">
</head>

<body>

    <header>
        <h1>Daily Attendance Dashboard</h1>
    </header>

    <div class="dashboard">
    <h2>Personal Information</h2>
    <p>Student Name: <?php echo isset($studentInfo['full_name']) ? $studentInfo['full_name'] : ''; ?></p> 
<p>Student ID: <?php echo isset($studentInfo['user_id']) ? $studentInfo['user_id'] : ''; ?></p>
<p>Class: <?php echo isset($studentInfo['class']) ? $studentInfo['class'] : ''; ?></p>
<p>Section: <?php echo isset($studentInfo['section']) ? $studentInfo['section'] : ''; ?></p>
<p>Roll No: <?php echo isset($studentInfo['roll_num']) ? $studentInfo['roll_num'] : ''; ?></p>


<section class="attendance-status">
    <h2>Daily Attendance Status</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceData as $attendance) { ?>
                <tr>
                    <td><?php echo $attendance['attendance_date']; ?></td>
                    <td class="<?php echo strtolower($attendance['status']); ?>"><?php echo ucfirst($attendance['status']); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</section>

        <section class="attendance-summary">
            <!-- Display attendance summary -->
            <h2>Attendance Summary</h2>
            <p>Total Classes: [Total Classes]</p>
            <p>Classes Attended: [Classes Attended]</p>
            <p>Attendance Percentage: [Attendance Percentage]%</p>
        </section>
    </div>

</body>

</html>

<?php
session_start();
require_once '../partials/dbconnect.php';

// Initialize variables
$selectedFaculty = $selectedClass = $selectedSection = $attendanceDate = $attendanceData = array();
$attendanceResult = false;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have sanitized and validated the form data
    $selectedFaculty = isset($_POST['faculty']) ? $_POST['faculty'] : "";
    $selectedClass = isset($_POST['class']) ? $_POST['class'] : "";
    $selectedSection = isset($_POST['section']) ? $_POST['section'] : "";
    $attendanceDate = isset($_POST['attendanceDate']) ? $_POST['attendanceDate'] : "";
    $attendanceData = isset($_POST['attendance']) ? $_POST['attendance'] : array();  // Array containing attendance data for each student

    // Process Attendance Data
    foreach ($attendanceData as $userId => $status) {
        // Assuming you have sanitized and validated the data
        // Update your database with the attendance status
        $attendanceQuery = "INSERT INTO attendance (user_id, attendance_date, status)
             VALUES ('$userId', '$attendanceDate', '$status') ON DUPLICATE KEY UPDATE status = '$status'";
        $attendanceResult = mysqli_query($conn, $attendanceQuery);
    }
}

// Fetch Student Details
$selectedFaculty = isset($_POST['faculty']) ? mysqli_real_escape_string($conn, $_POST['faculty']) : "";
$selectedClass = isset($_POST['class']) ? mysqli_real_escape_string($conn, $_POST['class']) : "";
$selectedSection = isset($_POST['section']) ? mysqli_real_escape_string($conn, $_POST['section']) : "";


$query = "SELECT sd.*, ut.user_profile, ut.user_fname, ut.user_lname FROM student_details sd
          JOIN user_table ut ON sd.user_id = ut.user_id
          WHERE sd.faculty = '$selectedFaculty' AND sd.class = '$selectedClass' AND sd.section = '$selectedSection'
          ORDER BY sd.roll_num ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Attendance System</title>
    <link rel="stylesheet" href="attendance.css">
</head>
<body>

<header>
    <h1>College Attendance System</h1>
</header>

<form method="post" action="#">
    <div class="options">
        <label>
            Faculty:
            <select name="faculty">
                <option value="Science">Science</option>
                <option value="Management">Management</option>
            </select>
        </label>
        <label>
            Class:
            <select name="class">
                <option value="XI">XI</option>
                <option value="XII">XII</option>
            </select>
        </label>
        <label>
            Section:
            <select name="section">
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </label>
    </div>
    <input type="submit" value="Fetch Students">
</form>

<div class="container">
    <main>
    <form action="#" method="post" onsubmit="return validateForm()">
    <?php
    // Display student details if result is available
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $user_id = $row['user_id'];
            $user_fname = $row['user_fname'];
            $user_lname = $row['user_lname'];
            $user_profile = $row['user_profile'];
            $user_rollno = $row['roll_num'];

            // Assuming user_profile contains the file name of the profile image
            $profile_image_path = "../user_profile/" . $user_profile;
    ?>
            <!-- Repeat the following div block for each student -->
            <div class="student">
                <img src="<?php echo $profile_image_path; ?>" alt="<?php echo $user_fname . ' ' . $user_lname; ?>">
                <div class="student-info">
                <p>Roll No:<?php echo $user_rollno ?></p>
                    <p><?php echo $user_fname . ' ' . $user_lname; ?></p>
                    <label>
                        <input type="radio" name="attendance[<?php echo $user_id; ?>]" value="present" class="attendance-checkbox"> P
                    </label>
                    <label>
                        <input type="radio" name="attendance[<?php echo $user_id; ?>]" value="absent" class="attendance-checkbox"> A
                    </label>
                    <label>
                        <input type="radio" name="attendance[<?php echo $user_id; ?>]" value="leave" class="attendance-checkbox"> L
                    </label>
                </div>
            </div>
    <?php
        }
    }
    ?>
    <label>
        Date:
        <input type="date" name="attendanceDate">
    </label>
    <input type="submit" value="Submit Attendance">
</form>
    </main>
</div>

<script>
    function validateForm() {
        var checkboxes = document.querySelectorAll('input[type="radio"]');
        var checked = false;

        checkboxes.forEach(function (checkbox) {
            if (checkbox.checked) {
                checked = true;
            }
        });

        if (!checked) {
            // Commenting out the alert for now, you can uncomment it if needed
            // alert("Please select either 'Present', 'Absent', or 'Leave' for all students.");
            return false;
        }

        return true;
    }
</script>

</body>
</html>
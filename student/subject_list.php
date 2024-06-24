<?php
include('../connection.php');
include('../session_detector.php');

if ($connection) {
    if(isset($_SESSION['student_id'])) {
        $student_ID = $_SESSION['student_id'];
        
        $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
        $result = mysqli_query($connection, $query);
            
        if(mysqli_num_rows($result) > 0) {
            $student = mysqli_fetch_assoc($result);
            $studentId = $student['id']; 
        } else {
            echo "No student found with ID: $student_ID";
            exit();
        }
    
        $enrolledSubjectsQuery = "SELECT 
                                    enrollments.academic_year AS academic_year_id, 
                                    academic_list.year AS academic_year, 
                                    academic_list.semester, 
                                    subjects.subject_code, 
                                    subjects.Name AS subject_name, 
                                    facultymembers.Name AS faculty_name 
                                  FROM enrollments 
                                  INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code 
                                  INNER JOIN facultymembers ON subjects.FacultyID = facultymembers.FacultyID 
                                  INNER JOIN academic_list ON enrollments.academic_year = academic_list.id 
                                  WHERE enrollments.student_id = '$student_ID'
                                  ORDER BY academic_list.year DESC, academic_list.semester DESC";

        $enrolledSubjectsResult = mysqli_query($connection, $enrolledSubjectsQuery);

        $enrolledSubjects = [];
        if(mysqli_num_rows($enrolledSubjectsResult) > 0) {
            while($row = mysqli_fetch_assoc($enrolledSubjectsResult)) {
                $enrolledSubjects[] = $row;
            }
        }
    } else {
        echo "Student ID not provided.";
        exit();
    }
} else {
    echo "Failed to connect to the database.";
    exit();
}

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Student | Subject List</title>
<link rel="icon" href="../images/system-logo.png">

    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    .subject-button {
        background-color: darkblue;
        min-width: 120px;
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }
    .background-box {
        border-style: 10px;
        height: 80%;
        width: 103%;
        border-radius: 10px;
        text-align: center;
        padding: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }
    .box-body {
        margin-bottom: 5px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }
    .content{
        padding-bottom: 50px!important;
    }
</style>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <br><br>
        <h1>SUBJECTS MANAGEMENT</h1><br><br>
        <div class="box-header">
            <h2>Enrolled Subjects:</h2></div>
            <?php
            $current_academic_year = '';
            foreach($enrolledSubjects as $subject):
                if ($current_academic_year != $subject['academic_year'] . ' - ' . $subject['semester']) {
                    if ($current_academic_year != '') {
                        echo '</table></div>';
                    }
                    $current_academic_year = $subject['academic_year'] . ' - ' . $subject['semester'];
                    echo '<div class="box-body">';
                    echo '<h3>Academic Year: ' . $subject['academic_year'] . ', Semester: ' . $subject['semester'] . '</h3>';
                    echo '<table border="1">';
                    echo '<tr><th>Subject Code</th><th>Subject Name</th><th>Faculty Name</th></tr>';
                }
                echo '<tr>';
                echo '<td>' . $subject['subject_code'] . '</td>';
                echo '<td>' . $subject['subject_name'] . '</td>';
                echo '<td>' . $subject['faculty_name'] . '</td>';
                echo '</tr>';
            endforeach;
            if ($current_academic_year != '') {
                echo '</table></div>';
            }
            ?>
        
    </div>
</body>
</html>

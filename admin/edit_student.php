<?php
    ob_start(); 

?>
<?php
 session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Edit Student</title>
<link rel="icon" href="../images/system-logo.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .below-title {
        background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
        width: 3000px;
        padding: 20px;
        position: fixed;
        margin-top: 35px;
        margin-left: -19px;
        color: white;
        top: 30px;
        border-bottom: 3px solid #002594; 
    }

    .students-button {
        background-color: darkblue;
        min-width: 120px; 
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }

    .success {
        color: goldenrod;
    }
    .enrolled_subjects td {
        font-weight: normal; 
        font-size: 15px;
        text-align: center;
    }
    .enrolled_table {
        width: 90%;
    }
    th {
        font-size: 15px;
    }
    .popup {
        display: none; 
        color: goldenrod;
        font-size: 20px;
    }
    table {
        width: 100%!important;
    }
    .id-column {
        width: 10%!important;
    }
    .box-body {
        width: 1200px!important;
    }
    .box-header {
        margin-top: -10px;
        padding-top: -20px!important;
        width: 1200px!important;
    }
    .table-header td {
        border: 0px solid black;
        font-size: 15px;
    }
    .table-header {
        margin-bottom: -5px;
        margin-top: -110px;
    } 
    .update {
        text-align: right;
        
    }
    .button {
        padding-right: 16px!important;
        padding-left: 16px!important;
        margin-right: -75px ;
    }
    .title {
        text-align: right;
    }
    .bar {
        text-align: left;
    }
</style>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h1>Student Management</h1><br><br><br><h2 class="below-title">⇛ Manage Student<h2><br><br><br>
    <div id="updateMessage" class="popup" style="display: none;"></div>
    <?php
    if(isset($_GET['student_ID'])) {
        $student_ID = $_GET['student_ID'];
        
        include('../connection.php');
        if(isset($_GET['message'])) {
            echo "<p>" . htmlspecialchars($_GET['message']) . "</p>";
        }
        if ($connection) {
            $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
            $result = mysqli_query($connection, $query);
            
            if(mysqli_num_rows($result) > 0) {
                $student = mysqli_fetch_assoc($result);
                $studentId = $student['student_ID']; 
                
            } else {
                echo "No student found with ID: $student_ID";
                exit();
            }
    
            if(isset($_POST['submit'])) {
                $newStudentId = $_POST['new_id'];
                $newLastName = $_POST['new_last_name'];
                $newFirstName = $_POST['new_first_name'];
                $newMiddleName = $_POST['new_middle_name'];

                $newCourse = $_POST['new_course'];
                $newSection = $_POST['new_section'];
                $student_ID = $_POST['studentID'];
            
                $updateEnrollmentsQuery = "UPDATE enrollments SET student_ID = '$newStudentId' WHERE student_ID = '$student_ID'";
                $updateEnrollmentsResult = mysqli_query($connection, $updateEnrollmentsQuery);
            
                if($updateEnrollmentsResult) {
                    $updateQuery = "UPDATE student_info SET student_ID = '$newStudentId', student_lastName = '$newLastName',student_firstName = '$newFirstName',student_middleName = '$newMiddleName', student_COURSE = '$newCourse', student_SECTION = '$newSection' WHERE student_ID = '$student_ID'";
                    $updateResult = mysqli_query($connection, $updateQuery);
            
                    if ($updateResult) {
                        $message = "Student information updated successfully.";
                        header("Location: edit_student.php?student_ID=$newStudentId&message=" . urlencode($message));
                        exit();
                    } else {
                        echo "Error updating student information: " . mysqli_error($connection);
                    }
                } else {
                    echo "Error updating enrollments: " . mysqli_error($connection);
                }
            }
            
          
            $defaultAcademicYearQuery = "SELECT id FROM academic_list WHERE default_select = 1 AND status = 2 LIMIT 1";
$defaultAcademicYearResult = mysqli_query($connection, $defaultAcademicYearQuery);

if ($defaultAcademicYearResult) {
    $defaultAcademicYearData = mysqli_fetch_assoc($defaultAcademicYearResult);
    if ($defaultAcademicYearData !== null) {
        $defaultAcademicYear = $defaultAcademicYearData['id'];
    } else {
      
        echo "No default academic year found";
        exit(); 
    }
} else {
   
    echo "Error fetching default academic year: " . mysqli_error($connection);
    exit(); 
}


            $enrolledSubjectsQuery = "SELECT subjects.subject_code as subject_code, subjects.Name as subject_name, facultymembers.Name as faculty_name, enrollments.EnrollmentID 
                                      FROM enrollments 
                                      INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code
                                      INNER JOIN facultymembers ON subjects.FacultyID = facultymembers.FacultyID 
                                      WHERE enrollments.student_ID = '$studentId' AND enrollments.academic_year = '$defaultAcademicYear'";
            $enrolledSubjectsResult = mysqli_query($connection, $enrolledSubjectsQuery);
    
            $enrolledSubjects = [];
            if(mysqli_num_rows($enrolledSubjectsResult) > 0) {
                while($row = mysqli_fetch_assoc($enrolledSubjectsResult)) {
                    $enrolledSubjects[] = $row;
                }
            }
            
            mysqli_close($connection);
        } else {
            echo "Failed to connect to the database.";
            exit();
        }
    } else {
        echo "Student ID not provided.";
        exit();
    }
    ?>
    <div class="box-header">
        <div class="edit-student-form">
            <table class="table-header">
                <form action="" method="POST">
                    <input type="hidden" name="studentID" value="<?php echo $student['student_ID']; ?>">
                    <tr>
                    <td class="title"><label for="new_last_name">Last Name:</label></td>
                        <td class="bar"><input type="text" id="new_last_name" name="new_last_name" value="<?php echo $student['student_lastName']; ?>" ></td>
                        <br>
                        <td class="title"><label for="new_course">Course:</label></td>
                        <td class="bar"><input type="text" id="new_course" name="new_course" value="<?php echo $student['student_COURSE']; ?>" required></td>
                        
                        <td class="update"><button type="submit" name="submit"><i class='fa fa-refresh'>Update</i></button><br></td>
                        
                    </tr>
                    <br>
                    <tr>

                    <td class="title"><label for="new_first_name">First Name:</label></td>
                        <td class="bar"><input type="text" id="new_first_name" name="new_first_name" value="<?php echo $student['student_firstName']; ?>" ></td>
                        <br>
                        <td class="title"><label for="new_section">Section:</label></td>
                        <td class="bar"><input type="text" id="new_section" name="new_section" value="<?php echo $student['student_SECTION']; ?>" ></td>
                         
                        <td>
                             <a href="student_list.php" class="button"><i class='fas fa-arrow-left'>&nbsp;Back</i></a>
                        
                        </td>
                    </tr>
                    <tr>
                        <td class="title"><label for="new_middle_name">Middle Name:</label></td>
                        <td class="bar"><input type="text" id="new_middle_name" name="new_middle_name" value="<?php echo $student['student_middleName']; ?>" ></td>
                        <br>
                        <td class="title"><label for="new_id">Student ID:</label></td>
                        <td class="bar"><input type="text" id="new_id" name="new_id" value="<?php echo $student['student_ID']; ?>" required></td>
                        
                      
                    </tr>
                        
                </form>
            </table>

        </div>
    </div>

    <div class="box-body">
        <form action="enroll_subject.php" method="post">
        <input type="hidden" name="academic_year" id="academic_year" value="<?php echo $defaultAcademicYear; ?>">
            
            <label for="subject">Add Subject:</label>

            <input type="hidden" name="student_ID" id="student_ID" value="<?php echo $student['id']; ?>">
            <input type="hidden" name="studentID" id="studentID" value="<?php echo $student['student_ID']; ?>">

            <select name="subject" id="subject">
                <?php
                include('../connection.php');
                $subjectQuery = "SELECT subjects.Name AS subject_name, subjects.subject_code AS subject_code 
                                 FROM subjects";
                $subjectResult = mysqli_query($connection, $subjectQuery);
                if(mysqli_num_rows($subjectResult) > 0) {
                    while($row = mysqli_fetch_assoc($subjectResult)) {
                        echo "<option value='" . $row['subject_code'] . "'>" . $row['subject_name'] . "</option>";
                    }
                }
                ?>
            </select>

            <button type="submit">Enroll Subject</button>
           

        </form>

        <div class="enrolled_subjects">
            <h3>Enrolled Subjects:</h3>
            <table border="1" class="enrolled_table">
                <tr>
                    <th class="code-column">Subject Code</th>
                    <th class="name-column">Subject Name</th>
                    <th class="faculty-column">Faculty Name</th>
                    <th class="action-column">Action</th>
                </tr>
                <?php if (!empty($enrolledSubjects)): ?>
    <?php foreach($enrolledSubjects as $subject): ?>
        <tr>
            <td><?php echo $subject['subject_code']; ?></td>
            <td><?php echo $subject['subject_name']; ?></td>
            <td><?php echo $subject['faculty_name']; ?></td>
            <td>
                <form action="delete_enrolled_subject.php" method="POST">
                    <input type="hidden" name="studentID" id="student_ID" value="<?php echo $student['student_ID']; ?>">
                    <input type="hidden" name="enrollment_id" value="<?php echo $subject['EnrollmentID']; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4">No enrolled subjects found</td>
    </tr>
<?php endif; ?>

            </table>
        </div>
    </div>

    <script>
        function showUpdateMessage(message) {
            var updateMessageDiv = document.getElementById("updateMessage");
            updateMessageDiv.innerHTML = "<h3>" + message + "</h3>";
            updateMessageDiv.style.display = "block";
            setTimeout(function() {
                updateMessageDiv.style.display = "none";
            }, 3000);
        }
        var message = "<?php echo isset($message) ? $message : ''; ?>";
        if (message !== "") {
            showUpdateMessage(message);
        }
    </script>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    include('../connection.php');
    if (!$connection) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $studentID = $_POST['student_id'];
    $lastname = $_POST['new_last_name'];
    $firstname = $_POST['new_first_name'];
    $middlename = $_POST['new_middle_name'];

    $course = $_POST['new_course'];
    $section = $_POST['new_section'];
    $evaluated = isset($_POST['evaluated_checkbox']) ? 1 : 0;
    
    
    $randomPassword = generateRandomPassword();

    $studentID = mysqli_real_escape_string($connection, $studentID);
    $lastname = mysqli_real_escape_string($connection, $lastname);
    $firstname = mysqli_real_escape_string($connection, $firstname);
    $middlename = mysqli_real_escape_string($connection, $middlename);

    $course = mysqli_real_escape_string($connection, $course);
    $section = mysqli_real_escape_string($connection, $section);

    $query = "INSERT INTO student_info (student_ID,student_lastName, student_firstNAME, student_middleName, student_COURSE, student_SECTION, student_PASS) VALUES ('$studentID', '$lastname', '$firstname','$middlename','$course', '$section', '$randomPassword')";
    if (mysqli_query($connection, $query)) {
        echo "New student added successfully";
    } else {
        echo "Error adding student: " . mysqli_error($connection);
    }

    mysqli_close($connection);
} else {
    echo "Error: Invalid request";
}

function generateRandomPassword($length = 8) {
    
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    $maxIndex = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[mt_rand(0, $maxIndex)];
    }
    return $password;
}


header("Location: student_list.php");
exit(); 
?>

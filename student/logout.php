<?php
echo $_GET['studentId'];
session_start();

if (isset($_GET['studentId'])) {
    $studentId = $_GET['studentId'];
    
    include('../connection.php');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE user_id = :studentId");
        $stmt->bindParam(':studentId', $studentId, PDO::PARAM_STR);

        if ($stmt->execute()) {
            
            error_log("Successfully logged out student with ID: " . $studentId);
        } else {
            
            error_log("Failed to log out student with ID: " . $studentId);
        }

        session_unset();
        session_destroy();

    } catch (PDOException $e) {
       
        error_log("PDOException: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
        die();
    }
} else {
   
    error_log("No student ID found in session.");
}


header("Location: student_login.php");
exit();
?>

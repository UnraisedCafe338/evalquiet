<?php
echo $_GET['username'];
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    include('../connection.php');

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
        $stmt = $pdo->prepare("DELETE FROM active_sessions WHERE user_id = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        if ($stmt->execute()) {
            
            error_log("Successfully logged out student with ID: " . $username);
        } else {
            
            error_log("Failed to log out student with ID: " . $username);
        }

        
        session_unset();
        session_destroy();

    } catch (PDOException $e) {
        
        error_log("PDOException: " . $e->getMessage());
        echo "Error: " . $e->getMessage();
        die();
    }
} else {
 
    error_log("No Username found in session.");
}

header("Location: admin_login.php");
exit();
?>

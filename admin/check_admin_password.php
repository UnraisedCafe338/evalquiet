<?php
session_start();
include('../connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT admin_pass FROM admin_list WHERE admin_id = :admin_id");
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (password_verify($currentPassword, $row['admin_pass'])) {
                echo "success";
            } else {
                echo "failure";
            }
        } else {
            echo "failure";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $pdo = null;
}
?>

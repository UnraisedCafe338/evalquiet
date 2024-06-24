<?php

include('connection.php');

if (!isset($_SESSION['username'])) {

    header("Location: admin_login.php");
    exit();
}
$user_id = $_SESSION['username'];
$conn = $connection;
$sql = "SELECT COUNT(*) AS count FROM active_sessions WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {

    $stmt->close();
    $conn->close();
    header("Location: admin_login.php");
    exit();
}
$stmt->close();
?>
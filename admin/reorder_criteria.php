<?php
include('../connection.php');

if (isset($_GET['criteria_id']) && isset($_GET['action'])) {
    $criteria_id = (int) $_GET['criteria_id'];
    $action = $_GET['action'];

    $query = "SELECT criteria_order FROM criteria WHERE criteria_id = $criteria_id";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $current_order = $row['criteria_order'];

        if ($action == 'move_up') {
            
            $query = "SELECT criteria_id, criteria_order FROM criteria WHERE criteria_order < $current_order ORDER BY criteria_order DESC LIMIT 1";
        } elseif ($action == 'move_down') {
            
            $query = "SELECT criteria_id, criteria_order FROM criteria WHERE criteria_order > $current_order ORDER BY criteria_order ASC LIMIT 1";
        }

        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $swap_criteria_id = $row['criteria_id'];
            $swap_order = $row['criteria_order'];

         
            $update1 = "UPDATE criteria SET criteria_order = $swap_order WHERE criteria_id = $criteria_id";
            $update2 = "UPDATE criteria SET criteria_order = $current_order WHERE criteria_id = $swap_criteria_id";
            mysqli_query($connection, $update1);
            mysqli_query($connection, $update2);
        }
    }
}

mysqli_close($connection);
header("Location: criteria_list.php");
exit();
?>

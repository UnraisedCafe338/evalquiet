<?php
 session_start();
?>
<?php

include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
   
    $session_id = $_POST['session_id'];

    $logout_query = "DELETE FROM active_sessions WHERE session_id = '$session_id'";

    if (mysqli_query($connection, $logout_query)) {
        echo "<script>alert('User logged out successfully.');</script>";
    } else {
        echo "<script>alert('Error logging out user: " . mysqli_error($connection) . "');</script>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout_all'])) {

    $logout_all_query = "DELETE FROM active_sessions WHERE NOT user_id ='admin'";

    if (mysqli_query($connection, $logout_all_query)) {
        echo "<script>alert('All users logged out successfully.');</script>";
    } else {
        echo "<script>alert('Error logging out all users: " . mysqli_error($connection) . "');</script>";
    }
}


$query = "SELECT * FROM active_sessions";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin | Session Management</title>
  <link rel="icon" href="../images/system-logo.png">

  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
      .session-button {
        background-color: darkblue;
        min-width: 120px; 
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
      }
      .search-container {
      background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
      width: 3000px;
      padding: 20px;
      position: fixed;
      margin-top: -30px;
      margin-left: -19px;
      color: white;
      top: 100px;
      border-bottom: 2px solid rgb(23, 0, 116);
    }
    .search-container::after {
      content: "";
      position: fixed;
      left: 50%;
      top: 130px;
      transform: translateX(-50%) translateY(-50%);
      width: 3000px;
      height: 5px;
      background-color: #ffdd00;
      margin-bottom: 100px;
    }
    .content h1 {
  background: linear-gradient(to bottom, rgb(66, 78, 255), rgb(49, 0, 208));
  width: 3000px;
  height: 30px;
  padding: 20px;
  position: fixed;
  margin-top: -30px;
  margin-left: -10px;
  color: white;
  top: 30px;
  border-bottom: 0px solid #ffdd00!important; 
  z-index: 3;
}
.box-header{
  display: flex;
  width: 1215px!important;
}
.box-body{
  width: 1215px!important;
}
table{
  width: 100%!important;
}
.head{
 margin: 15px 40px 15px 400px;
}
.button{
  margin: 12px 0px 7px 10px;
}
  </style>
</head>
<body>


  
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <h1>Active Sessions</h1><br><br><br><br><br>
    <div class="search-container">
      <label for="search">Search:</label>
      <input type="text" id="search" name="search" placeholder="Search sessions...">
    </div>
    <br><br>
    <div class="box-header">
      <h2 class="head">Active Student Session Lists</h2>
      <form method="post" onsubmit="return confirmLogoutAll();">
        <input type="hidden" name="logout_all" value="1">
        <input type="submit" value="Logout All" class="button">
      </form>
    </div>
    <div class="box-body">
    <table border="1" id="sessionTable">
      <thead>
        <tr>
          <th>No.</th>
          <th>User ID</th>
          <th>Login Time</th>
          <th>Course</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
  <?php
    $query = "SELECT * FROM active_sessions WHERE NOT course = 'admin'";
    $result = mysqli_query($connection, $query);
    $rowCount = 1;
    if ($result && mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$rowCount}</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        $login_time_12hr = date('Y-m-d [ h:i:s A ]', strtotime($row['login_time']));
        echo "<td>" . $login_time_12hr . "</td>";
        echo "<td>" . $row['course'] . "</td>";
        echo "<td>";
        echo "<form method='post' onsubmit='return confirmLogout();'>";
        echo "<input type='hidden' name='session_id' value='" . $row['session_id'] . "'>";
        echo "<input type='submit' name='logout' value='Logout'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
        $rowCount++; 
      }
    } else {
      echo "<tr><td colspan='5'>No active sessions found.</td></tr>";
    }
  ?>
</tbody>
    </table>
    </div>
  </div>
  <script>
    document.getElementById('search').addEventListener('input', function() {
      var searchValue = this.value.toLowerCase();
      var rows = document.querySelectorAll('#sessionTable tbody tr');
      rows.forEach(function(row) {
        var cells = row.querySelectorAll('td');
        var found = false;
        cells.forEach(function(cell) {
          if (cell.textContent.toLowerCase().indexOf(searchValue) > -1) {
            found = true;
          }
        });
        if (found) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });

    function confirmLogout() {
      return confirm('Are you sure you want to log out this user?');
    }

    function confirmLogoutAll() {
      return confirm('Are you sure you want to log out all users?');
    }
  </script>
</body>
</html>

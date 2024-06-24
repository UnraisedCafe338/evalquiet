<?php
 session_start();
include('../connection.php');
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin | Student Management</title>
  <link rel="icon" href="../images/system-logo.png">

  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
  
    .content {
      margin-left: 0px;
      margin-bottom: 100px;
      padding: 20px;
      z-index: 0;
    }

    .actions-col {
      width: 150px;
      text-align: center;
    }
    .menu-btn {
      cursor: pointer;
      display: inline-block;
      padding: 5px;
    }
    .edit-delete-btns {
      display: inline-block;
    }
    .edit-delete-btns button {
      background-color: transparent;
      border: none;
      cursor: pointer;
      font-size: 14px;
    }
    .delete-button{
      margin-left: 100px;
      margin-right: -42px;
      margin-top: -80px;
      margin-bottom: 5px;
    }
    .edit-delete-btns button.delete-btn {
      color: red;
    }
    .edit-delete-btns button.delete-btn:hover {
      color: darkred; 
    }
    .students-button {
      background-color: darkblue;
      min-width: 120px; 
      margin-right: 0px;
      margin-left: -10px;
      padding-left: 15px;
      border-radius: 10px;
    }
    .content-addstud {
      background: linear-gradient(to bottom, rgb(66, 78, 255), rgb(49, 0, 208));
      width: 3000px;
      padding: 10px;
      position: fixed;
      margin-top: -100px;
      margin-left: -19px;
      color: white;
      bottom: 0px;
      border-top: 2px solid rgb(23, 0, 116);
    }
    .content-addstud::before {
      content: "";
      position: fixed;
      left: 50%;
      transform: translateX(-50%) translateY(-50%);
      width: 3000px;
      height: 3px;
      background-color: #ffee00;
      margin-bottom: 300px;
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
    .add-student-popup {
      display: none;
      position: fixed;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      background-color: white;
      padding-top: 30px;
      border-radius: 5px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
      width: 30%;
    }
    .add-student-popup h2{
      text-align: center;
    }
    .add-student-popup .addstudtable {
      margin-top: -90px;
    }
    .addstudtable td {
      border: 0px;
    }
    .overlay2 {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 200%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }
    .overlay2 h2 {
      left: 100px;
    }
    .close-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
      font-size: 20px;
      color: #aaa;
    }
    .close-btn:hover {
      color: #000; 
    }
    .overlay-box {
      top: 55%!important;
    }
    .overlay-box .addstudbutton {
      left: 100%;
    }
    .box-body {
      margin-right: -70px;
      margin-bottom: -60px!important;
      width: 1200px!important;
    }
    .box-header {
      margin-right: -70px;
      width: 1200px!important;
      height: auto!important;
    }
    table {
      width: 100%!important;
    }
    .id-column {
      width: 15%;
    }
    .name-column {
      width: 20%; 
    }
    .dep-column {
      width: 12.5%;
    }
    .pass-column {
      width: 12.5%; 
    }
    .box-header .addstudbutton {
      margin-right: -1070px;
      display: inline-block;
      text-align: center;
    }
    .box-header h3 {
      margin-bottom: 5px!important;
      margin-top: -30px;
      font-size: 20px;
      text-align: left!important;
    }
    .action {
      width: 10%;
    }
    .section{
      width: 12.5%;
    }
    .manage-button {
      margin-top: 15px;
    }
    .manage-button {
      background-color: #0055ff;
      color: rgb(255, 255, 255);
      padding: 7px 20px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .manage-button:hover {
      background-color: navy;
    }
    .batch-upload-container {
      display: flex;
      margin-top: -20PX;
      width: 490px;
      text-align: center;
    }
    .batch-upload-container button{
      padding-top: 7px!important;
      padding-bottom: 7px!important;
      margin-left: -70px;
    }
        
    @media print {
    @page {
        size: A4; 
        margin: 20mm 10mm;
        @top-center {
            content: "Header content"; 
        }
        @bottom-center {
            content: "Footer content"; 
        }
    }
    
    
    body * {
        visibility: hidden;
    }
    .box-body, .box-body * {
        visibility: visible;
    }
    .box-body {
                position: absolute;
                left: 50%!important;
                top: 0;
                transform: translateX(-50%);
                width: 90%!important;
                margin: 0 auto;
                box-shadow: none!important;
            border: none!important; 
            margin-right: 30px!important;

            }
    .header .footer{
      display: block;
      position: fixed!important;
    }
    .action, .actions-col, .manage-button {
        visibility: hidden;
        border-color: transparent;
    }
    table {
        margin-left: 65px;
    }
    @page {
        margin: 100px 50px;
        @top-center {
            content: element(header);
        }
    }

    /* footer for printing */
    @page {
        margin: 50px;
        @bottom-center {
            content: element(footer);
        }
    }
}

        .header {
            
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid blue;
            padding: 10px 20px;
        }
        .header img {
            width: 100px;
            height: 100px;
        }
        .header .title {
            flex-grow: 1;
            text-align: center;
        }
        .header .title h2 {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            font-weight: bold;
            font-size: 35px;
            color: blue;
        }
        .header .title h5 {
            margin: 0;
            font-size: 16px;
        }
        .header .contact {
            font-size: 14px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;

            padding: 8px;
            position: relative;
            color: black;
             
            bottom: 0px;
            border-top: 3px solid blue;
            z-index: 2;
        }

        .footer span {
            text-align: right; 
        }
        .print-button{
          margin-right: -1115px;
          padding: 10px 27px;
        }
    
  </style>
</head>
<body>
  
<?php include 'sidebar.php'; ?>
<div class="content">
  <h1>Student Management</h1><br><br><br><br><br><br><br>
  <div class="search-container">
    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for student...">
    <select id="courseFilter" onchange="filterByCourse()">
      <option value="">All Courses</option>
      <?php
        if ($connection) {
          $course_query = "SELECT DISTINCT student_COURSE FROM student_info";
          $course_result = mysqli_query($connection, $course_query);
          while ($course_row = mysqli_fetch_assoc($course_result)) {
            echo '<option value="' . htmlspecialchars($course_row['student_COURSE']) . '">' . htmlspecialchars($course_row['student_COURSE']) . '</option>';
          }
        }
      ?>
    </select>
    <select id="sectionFilter" onchange="filterBySection()">
      <option value="">All Sections</option>
      <?php
        if ($connection) {
          $section_query = "SELECT DISTINCT student_SECTION FROM student_info";
          $section_result = mysqli_query($connection, $section_query);
          while ($section_row = mysqli_fetch_assoc($section_result)) {
            echo '<option value="' . htmlspecialchars($section_row['student_SECTION']) . '">' . htmlspecialchars($section_row['student_SECTION']) . '</option>';
          }
        }
      ?>
    </select>
  </div>
<?php
$query = "SELECT * FROM academic_list WHERE default_select = 1";
$result = mysqli_query($connection, $query);

if ($result && mysqli_num_rows($result) > 0) {
  $academic_year = mysqli_fetch_assoc($result);
  $academic_year_id = $academic_year['id'];
  $semester_text = "";
  switch ($academic_year['semester']) {
    case 1:
      $semester_text = "1st";
      break;
    case 2:
      $semester_text = "2nd";
      break;
    case 3:
      $semester_text = "3rd";
      break;
    default:
      $semester_text = "Unknown";
  }

  $evaluation_status = "";
  switch ($academic_year['status']) {
    case 0:
      $evaluation_status = "Finished";
      break;
    case 1:
      $evaluation_status = "Pending";
      break;
    case 2:
      $evaluation_status = "On-going";
      break;
    default:
      $evaluation_status = "Unknown";
  }
}
?>
  <div class="box-header">
    <button class="addstudbutton" onclick="toggleAddStudentPopup()"><i class="fas fa-plus">&nbsp;&nbsp;New Student</i></button>
    <h3>Student List</h3><br>
    <h3> Academic Year: <?php echo $academic_year['year']; ?>, <?php echo $semester_text; ?> Semester</h3>
    <button class="print-button" onclick="printBoxBody()">Print</button>
    <div class="batch-upload-container">
      <h2></h2>
      <form action="batch_upload_student.php" method="post" enctype="multipart/form-data">
        <label for="file">Upload CSV file:</label>
        <input type="file" name="file" id="file" accept=".csv" required>
        <button type="submit" name="upload">Upload</button>
      </form>
    </div>
  </div>
  <div class="box-body">
  <div class="header" id="header">
        <img src="../images/exact logo.png" alt="Left Logo">
        <div class="title">
            <h2>EXACT COLLEGES OF ASIA</h2>
            <h5>Suclayin, Arayat, Pampanga</h5>
            <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
            <h5>Email address: exact.colleges@yahoo.com</h5>
        </div>
        <div class="blue-line"></div>
    </div><br><br> 
    <table id="studentTable">
      <thead>
        <tr>
          <th class="id-column">Student ID</th>
          <th class="name-column">Last Name</th>
          <th class="name-column">First Name</th>
          <th class="name-column">Middle Name</th>
          <th class="department">Department</th>
          <th class="section">Section</th>
          <th class="pass-column">Password</th>
          <th class="action">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
          include('../connection.php');
          if ($connection) {
            $query = "
              SELECT si.*
              FROM student_info si
              JOIN enrollments e ON si.student_ID = e.student_ID
              WHERE e.academic_year = $academic_year_id
              GROUP BY si.student_ID
              ORDER BY si.student_lastName
            ";
            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['student_ID']}</td>";
                echo "<td>{$row['student_lastName']}</td>";
                echo "<td>{$row['student_firstName']}</td>";
                echo "<td>{$row['student_middleName']}</td>";
                echo "<td>{$row['student_COURSE']}</td>";
                echo "<td>{$row['student_SECTION']}</td>";
                echo "<td>{$row['student_PASS']}</td>";
                echo "<td class='actions-col'>";
                echo "<a href='edit_student.php?student_ID={$row['student_ID']}' class='manage-button'><i class='fas fa-user-cog'></i></a>";
                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='8'>No students found.</td></tr>";
            }
            mysqli_close($connection);
          } else {
            echo "<tr><td colspan='6'>Failed to connect to the database.</td></tr>";
            mysqli_close($connection);
          }
        ?>
      </tbody>
    </table>
    <div class="footer">
        <span></span>
        <span></span>
    </div>
  </div>
</div>

<div class="overlay2" id="overlay2"></div>
<div class="add-student-popup" id="addStudentPopup">
  <span class="close-btn" onclick="toggleAddStudentPopup()">&times;</span>
  <form action="add_student.php" method="POST">
    <h2>Add New Student</h2><br><br><br><br>
    <table class="addstudtable">
      <tr>
        <td><label for="student_id">Student ID:</label></td>
        <td><input type="text" id="student_id" name="student_id" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_last_name">Last Name:</label></td>
        <td><input type="text" id="new_last_name" name="new_last_name" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_first_name">First Name:</label></td>
        <td><input type="text" id="new_first_name" name="new_first_name" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_middle_name">Middle Name:</label></td>
        <td><input type="text" id="new_middle_name" name="new_middle_name" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_course">Course:</label></td>
        <td><input type="text" id="new_course" name="new_course" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_section">Section:</label></td>
        <td><input type="text" id="new_section" name="new_section" required class="expand-input"></td>
      </tr>
      <tr>
        <td><label for="new_password">Password:</label></td>
        <td><input type="password" id="new_password" name="new_password" required class="expand-input"></td>
      </tr>
    </table><br><br><br><br>
    <button type="submit">Add Student</button>
  </form>
</div>

<script>
  function toggleAddStudentPopup() {
    var popup = document.getElementById('addStudentPopup');
    var overlay = document.getElementById('overlay2');
    popup.classList.toggle('active');
    overlay.classList.toggle('active');
  }

  function searchTable() {
    var input = document.getElementById("searchInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");
    
    for (var i = 1; i < rows.length; i++) {
      var cells = rows[i].getElementsByTagName("td");
      var found = false;
      
      for (var j = 0; j < cells.length - 1; j++) {
        if (cells[j]) {
          if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
            found = true;
            break;
          }
        }
      }
      rows[i].style.display = found ? "" : "none";
    }
  }

  function filterByCourse() {
    var courseFilter = document.getElementById("courseFilter").value.toLowerCase();
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");

    for (var i = 1; i < rows.length; i++) {
      var courseCell = rows[i].getElementsByTagName("td")[4];
      if (courseCell) {
        rows[i].style.display = courseCell.innerText.toLowerCase().indexOf(courseFilter) > -1 || courseFilter === "" ? "" : "none";
      }
    }
  }

  function filterBySection() {
    var sectionFilter = document.getElementById("sectionFilter").value.toLowerCase();
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");

    for (var i = 1; i < rows.length; i++) {
      var sectionCell = rows[i].getElementsByTagName("td")[5];
      if (sectionCell) {
        rows[i].style.display = sectionCell.innerText.toLowerCase().indexOf(sectionFilter) > -1 || sectionFilter === "" ? "" : "none";
      }
    }
  }

  function printBoxBody() {
    var printContents = document.querySelector('.box-body').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  }
</script>
</body>
</html>
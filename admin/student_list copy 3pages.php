<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Management</title>
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
      width: 20%;
    }
    .name-column {
      width: 40%; 
    }
    .dep-column {
      width: 10%;
    }
    .pass-column {
      width: 10%; 
    }
    .box-header .addstudbutton {
      margin-right: -1070px;
      display: inline-block;
      text-align: center;
    }
    .box-header h3 {
      margin-top: -30px;
      font-size: 30px;
      text-align: left!important;
    }
    .action {
      width: 10%;
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
      text-align: right;
    }
    @media print {
            body * {
                visibility: hidden;
            }
            .box-body, .box-body * {
                visibility: visible;
            }
            .box-body {
                position: absolute;
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                width: 80%;
                margin: 0 auto;
            }
            .action, .actions-col, .manage-button {
        visibility: hidden;
        border-color: transparent;
    }
    table {
        margin-left: 65px;
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
            
            width: 1190px;
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
        .page-buttons{
          display:flex;
          justify-content: center;
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
       
        $connection = mysqli_connect('localhost', 'admin', 'admin', 'evaluation_quiet');
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
          mysqli_close($connection);
        }
      ?>
    </select>
    <select id="studentCountFilter" onchange="updateStudentCount()">
    <option value="9999999">All</option>
    <option value="10">10 per page</option>
    <option value="20">20 per page</option>
    <option value="30">30 per page</option>
    <option value="40">40 per page</option>
    <option value="50">50 per page</option>
    
   
</select>


  </div>

  <div class="box-header">
    <button class="addstudbutton" onclick="toggleAddStudentPopup()"><i class="fas fa-plus">&nbsp;&nbsp;New Student</i></button>
    <h3>Student List</h3>
    <button class="print-button" onclick="printBoxBody()">Print</button>

    <div class="batch-upload-container">
      <h2></h2>
      <form action="batch_upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Upload CSV file:</label>
        <input type="file" name="file" id="file" accept=".csv" required>
        <button  type="submit" name="upload">Upload</button>
      </form>
      <div class="page-buttons">
  <button onclick="prevPage()"id="prevButton">Previous Page</button>
    <span id="currentPageInfo"></span>
    <button onclick="nextPage()" id="nextButton">Next Page</button>
    </div>
    </div>
  </div>
  <div class="box-body">
  <div class="header">
        <img src="../images/exact logo.png" alt="Left Logo">
        <div class="title">
            <h2>EXACT COLLEGES OF ASIA</h2>
            <h5>Suclayin, Arayat, Pampanga</h5>
            <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
            <h5>Email address: exact.colleges@yahoo.com</h5>
            
        </div>
        <img src="../images/bsis_logo.png" alt="Right Logo">
        <div class="blue-line"></div>
    </div><br><br> 
    <table id="studentTable">
      <thead>
        <tr>
          <th class="id-column">Student ID</th>
          <th class="name-column">Name</th>
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
            $query = "SELECT * FROM student_info";
            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['student_ID']}</td>";
                echo "<td>{$row['student_NAME']}</td>";
                echo "<td>{$row['student_COURSE']}</td>";
                echo "<td>{$row['student_SECTION']}</td>";
                echo "<td>{$row['student_PASS']}</td>";
                echo "<td class='actions-col'>";
                echo "<a href='edit_student.php?student_ID={$row['student_ID']}' class='manage-button'><i class='fas fa-user-cog'></i></a>";
                echo "</td>";
                echo "</tr>";
              }
            } else {
              echo "<tr><td colspan='6'>No students found.</td></tr>";
            }
            mysqli_close($connection);
          } else {
            echo "<tr><td colspan='6'>Failed to connect to the database.</td></tr>";
          }
        ?>
      </tbody>
    </table>
    <div class="footer">
        <span>2024 | Copyright Team Quiet</span>
        <span> Faculty Evaluation Management System</span>
        
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
        <td><label for="new_name">Name:</label></td>
        <td><input type="text" id="new_name" name="new_name" required class="expand-input"></td>
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
        <td colspan="2"><button type="submit">Add Student</button></td>
      </tr>
    </table>
  </form>
  
</div>

<script>

  function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  function filterByCourse() {
    var courseSelect, selectedCourse, table, tr, td, i;
    courseSelect = document.getElementById("courseFilter");
    selectedCourse = courseSelect.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[2]; 
      if (td) {
        if (selectedCourse === "" || td.textContent.toUpperCase() === selectedCourse) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
  function filterBySection() {
    var sectionSelect, selectedSection, table, tr, td, i;
    sectionSelect = document.getElementById("sectionFilter");
    selectedSection = sectionSelect.value.toUpperCase();
    table = document.getElementById("studentTable");
    tr = table.getElementsByTagName("tr");

    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[3];
      if (td) {
        if (selectedSection === "" || td.textContent.toUpperCase() === selectedSection) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  function toggleAddStudentPopup() {
    var popup = document.getElementById("addStudentPopup");
    var overlay = document.getElementById("overlay2");
    if (popup.style.display === "none" || popup.style.display === "") {
      popup.style.display = "block";
      overlay.style.display = "block";
    } else {
      popup.style.display = "none";
      overlay.style.display = "none";
    }
  }
  function printBoxBody() {
    window.print();
}

document.getElementById('subject').addEventListener('change', function() {
    document.getElementById('subjectForm').submit();
});
function updateStudentCount() {
    var countSelect = document.getElementById("studentCountFilter");
    var selectedCount = countSelect.value;
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");

    // Show all rows when "All" is selected
    if (selectedCount === "all") {
        for (var i = 1; i < rows.length; i++) { // Start from index 1 to skip the header row
            rows[i].style.display = "";
        }
        // Hide the previous and next buttons
        document.getElementById("prevButton").style.display = "none";
        document.getElementById("nextButton").style.display = "none";
    } else {
        var rowCount = 0;
        // Loop through rows and hide/show based on selected count
        for (var i = 1; i < rows.length; i++) { // Start from index 1 to skip the header row
            if (rowCount < selectedCount) {
                rows[i].style.display = "";
                rowCount++;
            } else {
                rows[i].style.display = "none";
            }
        }
        // Show the previous and next buttons
        document.getElementById("prevButton").style.display = "block";
        document.getElementById("nextButton").style.display = "block";
    }

    // Update page information
    updatePageInfo();
}




var currentPage = 1; // Initialize current page

// Function to update the number of students displayed per page
function updateStudentCount() {
    currentPage = 1; // Reset current page to 1
    showStudents();
}

// Function to show students for the current page
function showStudents() {
    var countSelect = document.getElementById("studentCountFilter");
    var selectedCount = parseInt(countSelect.value);
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");
    var startIndex = (currentPage - 1) * selectedCount + 1;
    var endIndex = startIndex + selectedCount - 1;

    for (var i = 1; i < rows.length; i++) {
        if (i >= startIndex && i <= endIndex) {
            rows[i].style.display = ""; // Show row
        } else {
            rows[i].style.display = "none"; // Hide row
        }
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        showStudents();
    }
}


function nextPage() {
    var countSelect = document.getElementById("studentCountFilter");
    var selectedCount = parseInt(countSelect.value);
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");
    var totalRows = rows.length - 1;
    var totalPages = Math.ceil(totalRows / selectedCount);

    if (currentPage < totalPages) {
        currentPage++;
        showStudents();
    }
}
function updatePageInfo() {
    var countSelect = document.getElementById("studentCountFilter");
    var selectedCount = countSelect.value;
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");
    var totalRows = rows.length - 1;

    if (selectedCount === "all") {
        document.getElementById("currentPageInfo").innerText = "Showing all " + totalRows + " students";
    } else {
        var totalPages = Math.ceil(totalRows / parseInt(selectedCount));
        document.getElementById("currentPageInfo").innerText = "Page " + currentPage + " of " + totalPages;
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        showStudents();
        updatePageInfo();
    }
}

function nextPage() {
    var countSelect = document.getElementById("studentCountFilter");
    var selectedCount = parseInt(countSelect.value);
    var table = document.getElementById("studentTable");
    var rows = table.getElementsByTagName("tr");
    var totalRows = rows.length - 1; 
    var totalPages = Math.ceil(totalRows / selectedCount);

    if (currentPage < totalPages) {
        currentPage++;
        showStudents();
        updatePageInfo(); 
    }
}

</script>
</body>
</html>

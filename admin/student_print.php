<?php
    include('../connection.php');
    $query = "SELECT * FROM academic_list WHERE default_select = 1";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) > 0) {

      $academic = mysqli_fetch_assoc($result);
      $semester_text = "";
      switch ($academic['semester']) {
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
  } else {

      echo "No academic year found";
      exit();
  }
  $admin_query = "SELECT * FROM admin_list WHERE admin_ID = 1";
$stmt = mysqli_prepare($connection, $admin_query);
mysqli_stmt_execute($stmt);

$admin_info = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($admin_info);


  $search = isset($_GET['search']) ? mysqli_real_escape_string($connection, $_GET['search']) : '';
  $course = isset($_GET['course']) ? mysqli_real_escape_string($connection, $_GET['course']) : '';
  $section = isset($_GET['section']) ? mysqli_real_escape_string($connection, $_GET['section']) : '';
  $section_text = $section;
  switch ($section_text){
    case '':
      $section_text = "All Sections";
  }
  $course_text = "";
  switch ($course) {
    case "BSIS":
      $course_text = "<h4>Course : Bachelor of Science in Information System</h4>";
      break;
    case "BTVTED":
      $course_text = "<h4>Course : Bachelor of Technical-Vocational Teacher Education</h4>";
      break;
    case "BECED":
      $course_text = "<h4>Course : Bachelor of Early Childhood Education</h4>";
      break;
    case "BSE":
      $course_text = "<h4>Course : Bachelor of Science in Entrepreneurship</h4>";
      break;
    case "BSMA":
      $course_text = "<h4>Course : Bachelor of Management Accounting</h4>"; 
      break;
    case "BSTM":
      $course_text = "<h4>Course : Bachelor of Science in Tourism Management</h4>"; 
      break;
    case "BSME":
      $course_text = "<h4>Course : Bachelor of Science in Marine Engineering</h4>"; 
      break;
    case "BSMT":
      $course_text = "<h4>Course : Bachelor of Science in Marine Transporting</h4>";
      break;
    case "BSN":
      $course_text = "<h4>Course : Bachelor of Science in Nursing</h4>";
      break;
    default:
      $course_text = "<h4>All Student List</h4>";
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Print Student List</title>
  <link rel="icon" href="../images/system-logo.png">

  <style>
    @media print {
      @page {
        size: A4;
        margin: 10mm 10mm
      }
      body * {
        visibility: visible;
      }
      .print-area, .print-area * {
        visibility: visible;
      }
      #header, #footer {
        visibility: visible !important;
        
        width: 100%;
        left: 0;
        right: 0;
      }
      #header {
        top: 0;
      }
      #footer {
        bottom: 0;
        position: fixed;
      }
      .print-area {
        margin-top: 0px;
        margin-bottom: 100px;
      }
      
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 3px solid blue;
      padding: 10px 20px;
      background: white;
    }
    .header img {
      width: 100px;
      height: 100px;
      margin-right: -100px;
    }
    .header .title {
      flex-grow: 1;
      text-align: center;
    }
    .header .title p {
      font-family: "Copperplate", fantasy;
      text-align: center;
      margin: 0;
      font-weight:300;
      font-size: 40px;
      
      color: blue;
    }
    .header .title h5 {
      margin: 0;
      font-size: 16px;
    }
    .footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px;
     
      color: black;
      background: none;
      margin-top: 100px;
      bottom: 0px;
      width: 98%;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: small;
      
    }
    table, th, td {
      border: 1px solid black;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .id-column{
      width: 25%;
    }
    .first-name-column{
      width: 30%;
    }
    .middle-name-column{
      width: 10%;
    }
    .department{
      width: 10px;
    }
    .section{
      width: 3%;
    }
    .username{
      width: 45%;
    }
    .below-header{
      text-align: center;
    }
    .table{
      border-style: none;
    }
    .title-info{
      text-decoration: underline;
    }
  </style>
  <script>
    window.onload = function() {
      window.print();
      window.onafterprint = function() {
        window.close();
        
      };
    };
  </script>
</head>
<body>
  <div id="header" class="header">
    <img src="../images/exact logo.png" alt="Left Logo">
    <div class="title">
      <p>EXACT COLLEGES OF ASIA,INC</p>
      <h5>Suclayin, Arayat, Pampanga</h5>
      <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
      <h5>Email address: exact.colleges@yahoo.com</h5>
    </div>
  </div>
  <div class="below-header">

    <h3 class="title-info">STUDENT INFORMATIONS </h3>
    <table>
    
    <h3><?php echo $course_text; ?></h3>
    <h4>Section : <?php echo $section_text; ?></h4>
    <h4>School Year : <?php echo $semester_text; ?> Sem A.Y <?php echo $academic['year']; ?></h4>
    </table>
  </div>
  <div class="print-area">
    
    <table>
      <thead>
        <tr>
          <th class="id-column">Student ID</th>
          <th class="last-name-column">Last Name</th>
          <th class="first-name-column">First Name</th>
          <th class="middle-name-column">Middle Name</th>
          <th class="department">Department</th>
          <th class="section">Section</th>
          <th class="id-column">Username</th>
          <th class="pass-column">Password</th>
        </tr>
      </thead>
      <tbody>
        <?php
          include('../connection.php');


          $query = "SELECT * FROM student_info WHERE 1=1";

          if ($search !== '') {
            $query .= " AND (student_ID LIKE '%$search%' OR student_lastName LIKE '%$search%' OR student_firstName LIKE '%$search%' OR student_middleName LIKE '%$search%')";
          }
          if ($course !== '') {
            $query .= " AND student_COURSE = '$course'";
          }
          if ($section !== '') {
            $query .= " AND student_SECTION = '$section'";
          }

          $query .= " ORDER BY student_lastName";

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
              echo "<td>{$row['student_ID']}</td>";
              echo "<td>{$row['student_PASS']}</td>";
              echo "</tr>";
            }
          } else {
            echo "<tr><td colspan='7'>No students found.</td></tr>";
          }
          mysqli_close($connection);
        ?>
      </tbody>
    </table>
    <div class="footer">
    <span>Prepared by: <?php echo $admin['name'] ?><br> <i><?php  echo $admin['role'] ?> &nbsp;&nbsp;</i></span>
    <span><?php echo "Date Printed: ". date('Y-m-d'); ?></span>
        
    </div>
  </div>

</body>
</html>

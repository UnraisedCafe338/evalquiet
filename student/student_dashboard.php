
<?php
include('../connection.php');
include('../session_detector.php');

if ($connection) {
    
    if(isset($_SESSION['student_id'])) {
        $student_ID = $_SESSION['student_id'];
  
        $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
        $result = mysqli_query($connection, $query);
   
        if(mysqli_num_rows($result) > 0) {

            $student = mysqli_fetch_assoc($result);
        } else {

            echo "No student found with ID: $student_ID Hi";
            exit();
        }
    } else {
     
        echo "Student ID not providedSSSS.. $student_ID .HIII";
        exit();
    }
} else {
    
    echo "Failed to connect to the database.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  
  <meta charset="UTF-8" />
  <title>Student | Dashboard</title>
  <link rel="stylesheet" href="styles.css">
<link rel="icon" href="../images/system-logo.png">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<style>
  .content{
    margin-right: 0px!important;
    display:flex;
    flex-direction: column;
    height: auto!important;
    width: 950px!important;
  }
.dashboard-button {
    background-color: darkblue;
    min-width: 120px; 
    margin-right: 0;
    margin-left: -10px;
    padding-left: 15px;
    border-radius: 10px;
}

.table-container {
    margin-top: 10px;
}
.eval-info{
  padding-bottom: 5px;
}
table {
    width: 100%;
    border-collapse: collapse;
}

table .eval-info, th, td {
    border: 1px solid black;
    padding: 8px;
    text-align: center;
}

table .stud-info td {
    border: 1px solid black;
    padding: 8px;
    text-align: left !important;
}

th {
    background-color: #f2f2f2;
}

.box-body {
    width: 110%!important;
    margin-bottom: 0px;
    margin-top: 10px!important;
    overflow-y: auto;
    margin-right: -110px;
}



.box-header2 {

 

    height: 180px!important;
    width: 600px!important;
    border-top-right-radius: 10px;
    border-top-left-radius: 0;
    text-align: center;
    padding: 0px 20px 20px 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    margin-top: 0px;
    border: 3px solid #a0a0a0;
    border-top: 10px solid #ffee00;

 
}

.box-header3{
    height: 160px!important;
    width: 600px!important;
    border-top-right-radius: 0px;
    border-top-left-radius: 10px;
    text-align: center;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    margin-top: 0px;
    border: 3px solid #a0a0a0;
    border-top: 10px solid #ffee00;
    
   

}

.flex-container {
    display: flex;
    height: auto;
    overflow: hidden;
    width: 1090px!important;
   
}

</style>

<body>
<?php include 'sidebar.php'; ?>
<div class="content">
  <br>
  <br>
  
  <h1>DASHBOARD</h1><br><br>
  <div class="flex-container">
  <div class="box-header3">
    
  <?php
    include('../connection.php');

    if (!$connection) {
      die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM academic_list WHERE default_select = 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
      $academic_year = mysqli_fetch_assoc($result);
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
          $evaluation_status = "Closed";
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

      echo "<h3>Academic Year: {$academic_year['year']} $semester_text Semester</h3>";
      echo "<h3>Evaluation Status: $evaluation_status</h3>";
    } else {
      echo "<h3>No current academic year is selected by the admin<br>Please coordinate with your instructor for more details.</h3>";
    }
    ?>
  </div>
  <div class="box-header2">
  <h2>Student Information</h2>
  <div class="table-container">

    <table class="stud-info">
      <tbody>
        <?php
   
        if(isset($student)) {
            echo "<tr><td><strong>Student ID:</strong></td><td>{$student['student_ID']}</td></tr>";
            echo "<tr><td><strong>Name:</strong></td><td>{$student['student_firstName']} {$student['student_middleName']} {$student['student_lastName']}</td></tr>";
            echo "<tr><td><strong>Course and Section:</strong></td><td>{$student['student_COURSE']} - {$student['student_SECTION']}</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  </div>
  </div>
  <div class="box-body">
  <div class="table-container">
  <h2>How to Evaluate a Teacher</h2>
    <table class="eval-info">
      <thead>
        <tr>
          <th>Step</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>

        <tr>
          <td>1</td>
          <td>Go to the "Evaluate Teacher" section in the dashboard.</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Select the teacher you want to evaluate from the list.</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Provide ratings and feedback to the teacher.</td>
        </tr>
        <tr>
          <td>4</td>
          <td>Submit the evaluation form.</td>
        </tr>
        <tr>
          <td>5</td>
          <td>Then please don't forget to log out after evaluating all teachers.<br></td>
        </tr>
      </tbody>
    </table>
  </div>
  </div>


</div>
</body>
</html>

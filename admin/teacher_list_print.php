<?php
include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$academic_year_id = isset($_GET['academic_year_id']) ? $_GET['academic_year_id'] : null;

if ($academic_year_id === null) {
    echo "Invalid academic year ID.";
    exit;
}

$query = "SELECT * FROM academic_list WHERE id = $academic_year_id";
$result = mysqli_query($connection, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "Academic year not found.";
    exit;
}
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_academic_year'])) {
    $year = $_POST['year'];
    $semester = $_POST['semester'];
    $status = $_POST['status'];

    $update_query = "UPDATE academic_list SET year='$year', semester='$semester', status='$status' WHERE id = $academic_year_id";
    if (mysqli_query($connection, $update_query)) {
        echo "<p class='below-title'>Updating academic year details...</p>";
        header("Refresh:1");
    } else {
        echo "<p class='below-title'>Error updating academic year details: " . mysqli_error($connection) . "</p>";
    }
}

$order_by = "";
$selected_filter = $_GET['score_filter'];
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset( $_GET['score_filter'])) {
    $score_filter = $_GET['score_filter'];
    $selected_filter = $score_filter;
    if ($score_filter == 'highest') {
        $order_by = "ORDER BY overall_rating DESC";
    } elseif ($score_filter == 'lowest') {
        $order_by = "ORDER BY overall_rating ASC";
    }
}
$admin_query = "SELECT * FROM admin_list WHERE admin_ID = 1";
$stmt = mysqli_prepare($connection, $admin_query);
mysqli_stmt_execute($stmt);

$admin_info = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($admin_info);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Taecher List Print</title>
  <link rel="icon" href="../images/system-logo.png">

    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid blue;
            padding-bottom: 10px;
        }

        .header img {
            margin-right: -100px;
            width: 100px;
            height: 100px;
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
        .eval-report {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-decoration: underline;
        }

        .summary-info {
            margin-bottom: 20px;
        }

        .section-summary-table {
            
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .section-summary-table th,
        .section-summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .section-summary-table th {
            background-color: #f2f2f2;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            position: relative;
            color: black;
            bottom: 0px;
            
            z-index: 2;
        }
        .footer span {
            text-align: right;
        }
        table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 30px;
  table-layout: auto;
  
}
th, td {
  border: 3px solid #a4a4a4;
  padding: 8px;
  text-align: center;
  width: auto;
}
th {
  background-color: #cfcfcf;
}
tr:hover {
  background-color: #f2f2f2;
}
.section-summary{
    text-align: center;
}
.datetime{
    margin-top: 0px;
    color: gray;
}
@media print {
      @page {
        size: A4;
        margin: 5mm 5mm;
      }
      body * {
        visibility: visible;
      }
      .print-area, .print-area * {
        visibility: visible;
      }
      #header, #footer {
        visibility: visible !important;
        margin-bottom: -10px;
        width: 98%;
        left: 0;
        right: 0;
      }
      #header {
        top: 0;
      }
      #footer {
        margin-top: 100px;
        bottom: -10px;
        width: 95%;
      }
      .print-area {
        margin-top: 0px;
        margin-bottom: 100px;
      }
      
      
    }
    .footer i{
        color: gray;
    }
    </style>
<body onload="window.print()">
        <div class="header" id="header">
        <img src="../images/exact logo.png" alt="Left Logo">
        <div class="title">
            <p>EXACT COLLEGES OF ASIA, INC</p>
            <h5>Suclayin, Arayat, Pampanga</h5>
            <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
            <h5>Email address: exact.colleges@yahoo.com</h5>
        </div>
    </div>
    <h4 style="text-align:center"class="edit">Academic Year: <?php echo $academic_year['year']; ?>&nbsp;&nbsp;<?php echo $semester_text; ?>&nbsp;Semester </h4>
    
    <h4 style="text-align:center">Faculty List</h4>
          

            <table border="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>No. of Students Evaluated / Total Enrolled</th>
                        <th>Evaluation Percentage</th>
                        <th>Overall Rating Score</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$faculty_query = "
SELECT 
    facultymembers.*, 
    (SELECT AVG(question_score) 
        FROM evaluation_table 
        WHERE FacultyID = facultymembers.FacultyID 
        AND academic_year = ?) AS overall_rating,
    (SELECT COUNT(DISTINCT student_ID) 
        FROM evaluation_table 
        WHERE FacultyID = facultymembers.FacultyID 
        AND academic_year = ?) AS student_count,
    (SELECT COUNT(DISTINCT enrollments.student_ID) 
        FROM enrollments 
        JOIN subjects ON enrollments.subject_code = subjects.subject_code
        WHERE subjects.FacultyID = facultymembers.FacultyID 
        AND enrollments.academic_year = ?) AS total_enrolled
FROM facultymembers
JOIN acadfaculty ON facultymembers.FacultyID = acadfaculty.FacultyID
JOIN academic_list ON acadfaculty.academic_year = academic_list.id
WHERE acadfaculty.academic_year = ?
$order_by
";

$stmt = $connection->prepare($faculty_query);
$stmt->bind_param('iiii', $academic_year_id, $academic_year_id, $academic_year_id, $academic_year_id);
$stmt->execute();
$faculty_result = $stmt->get_result();

                    if ($faculty_result && mysqli_num_rows($faculty_result) > 0) {
                        while ($row = mysqli_fetch_assoc($faculty_result)) {
                            $overall_rating = $row['overall_rating'] / 5;
                            $percentage_score = ($overall_rating) * 100;

                            $performance = "";
                            if ($percentage_score >= 92) {
                                $performance = "Excellent";
                            } elseif ($percentage_score >= 74) {
                                $performance = "Very Good";
                            } elseif ($percentage_score >= 56) {
                                $performance = "Good";
                            } elseif ($percentage_score >= 38) {
                                $performance = "Fair";
                            } else {
                                $performance = "Needs Improvement";
                            }

                            $faculty_id = $row['FacultyID'];
                            $check_query = "SELECT COUNT(*) AS num_rows FROM evaluation_summary WHERE faculty_id = ? AND academic_id = ?";
                            $stmt_check = mysqli_prepare($connection, $check_query);
                            mysqli_stmt_bind_param($stmt_check, "ss", $faculty_id, $academic_year_id);
                            mysqli_stmt_execute($stmt_check);
                            $result_check = mysqli_stmt_get_result($stmt_check);
                            $row_check = mysqli_fetch_assoc($result_check);
                            $num_rows = $row_check['num_rows'];

                            if ($num_rows > 0) {
                                $update_query = "UPDATE evaluation_summary SET avg_rating = ? WHERE faculty_id = ? AND academic_id = ?";
                                $stmt_update = mysqli_prepare($connection, $update_query);
                                mysqli_stmt_bind_param($stmt_update, "dss", $percentage_score, $faculty_id, $academic_year_id);
                                mysqli_stmt_execute($stmt_update);
                            } else {
                                $insert_query = "INSERT INTO evaluation_summary (faculty_id, academic_id, avg_rating) VALUES (?, ?, ?)";
                                $stmt_insert = mysqli_prepare($connection, $insert_query);
                                mysqli_stmt_bind_param($stmt_insert, "ssd", $faculty_id, $academic_year_id, $percentage_score);
                                mysqli_stmt_execute($stmt_insert);
                            }

                            echo "<tr>";
                            echo  "<td>".$row['Name'] . "</td>";
                            echo "<td class='table-row-student'>" . $row['student_count'] . " / " . $row['total_enrolled'] . "</td>";
                            if ($row['total_enrolled'] != 0) {
                                $percentage = round($row['student_count'] / $row['total_enrolled'] * 100, 2);
                            } else {
                                $percentage = 0; 
                            }
                            echo "<td class='table-row-student'>" . $percentage . "%</td>";
                            
                            echo "<td class='table-row-ratings'>" . round($overall_rating * 100, 2) . "</td>";
                            echo "<td class='table-row-ratings'>" . $performance . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No faculty members found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="footer" id="footer">
    <span>Prepared by: <?php echo $admin['name'] ?><br> <i><?php  echo $admin['role'] ?> &nbsp;&nbsp;</i></span>
    <span>Date Printed: <p class="datetime" id="dateTime"></p></span>
    </div>
</body>
</html>
<script>
function updateDate() {
    const now = new Date();
    const dateString = now.toLocaleDateString();
    document.getElementById('dateTime').textContent = dateString;
}

updateDate();
setInterval(updateDate, 1000);

  </script>

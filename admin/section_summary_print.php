<?php
include('../connection.php');


$facultyID = $_GET['FacultyID'] ?? null;
$academicYear = $_GET['academic_year'] ?? null;
$academic_year_id = $_GET['academic_year_id'] ?? null;
$semester = $_GET['semester'] ?? null;
$semester2 = ($_GET['semester'] == "1") ? "1st" : (($_GET['semester'] == "2") ? "2nd" : "3rd");

if ($facultyID === null || $academicYear === null || $semester === null) {
    echo "Invalid Faculty ID, Academic Year, or Semester.";
    exit;
}


$faculty_query = "SELECT Name FROM facultymembers WHERE FacultyID = ?";
$stmt_faculty = mysqli_prepare($connection, $faculty_query);
mysqli_stmt_bind_param($stmt_faculty, "s", $facultyID);
mysqli_stmt_execute($stmt_faculty);
$faculty_result = mysqli_stmt_get_result($stmt_faculty);

if (!$faculty_result || mysqli_num_rows($faculty_result) === 0) {
    echo "Faculty member not found.";
    exit;
}
$faculty = mysqli_fetch_assoc($faculty_result);


$academic_year_query = "SELECT year FROM academic_list WHERE id = ?";
$stmt_academic_year = mysqli_prepare($connection, $academic_year_query);
mysqli_stmt_bind_param($stmt_academic_year, "s", $academic_year_id);
mysqli_stmt_execute($stmt_academic_year);
$academic_year_result = mysqli_stmt_get_result($stmt_academic_year);

if (!$academic_year_result || mysqli_num_rows($academic_year_result) === 0) {
    echo "Academic year not found.";
    exit;
}
$academic_year_data = mysqli_fetch_assoc($academic_year_result);
$academicYearName = $academic_year_data['year'];

$selected_section = $_GET['section'] ?? 'all';

if ($selected_section === 'all') {
    $section_summary_query = "
        SELECT student_SECTION, 
               COUNT(DISTINCT evaluation_table.student_ID) AS total_students_evaluated, 
               AVG(question_score) AS average_score
        FROM evaluation_table
        INNER JOIN student_info ON evaluation_table.student_ID = student_info.student_ID
        WHERE evaluation_table.academic_year = ?
        GROUP BY student_SECTION
    ";
    $stmt_summary = mysqli_prepare($connection, $section_summary_query);
    mysqli_stmt_bind_param($stmt_summary, "s", $academicYear);
    mysqli_stmt_execute($stmt_summary);
    $section_summary_result = mysqli_stmt_get_result($stmt_summary);
} else {
    $section_summary_query = "
        SELECT 
            COUNT(DISTINCT e.student_ID) AS total_evaluations,
            AVG(e.question_score) AS average_score
        FROM evaluation_table e
        INNER JOIN student_info si ON e.student_ID = si.student_ID
        WHERE e.academic_year = ? AND si.student_SECTION = ?
    ";
    $stmt_summary = mysqli_prepare($connection, $section_summary_query);
    mysqli_stmt_bind_param($stmt_summary, "ss", $academicYear, $selected_section);
    mysqli_stmt_execute($stmt_summary);
    $section_summary_result = mysqli_stmt_get_result($stmt_summary);
}
$section_query = "SELECT DISTINCT CONCAT(student_COURSE, '-', student_SECTION) AS course_section FROM student_info";
      $section_result = mysqli_query($connection, $section_query);

      if ($section_result && mysqli_num_rows($section_result) > 0) {
          while ($row = mysqli_fetch_assoc($section_result)) {
              $course_section = htmlspecialchars($row['course_section']);
              $section_name = explode('-', $course_section)[1];
              $selected = ($_GET['section'] ?? 'all') === $section_name ? 'selected' : '';
              
          }
      } else {
          echo "<option value=''>No sections found</option>";
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
    <title>Section Summary Print</title>
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
</head>

<body onload="window.print()">
    <div class="container">
        <div class="header" id="header">
            <img src="../images/exact logo.png" alt="Left Logo">
            <div class="title">
                <p>EXACT COLLEGES OF ASIA, INC</h5>
                <h5>Suclayin, Arayat, Pampanga</h5>
                <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
                <h5>Email address: exact.colleges@yahoo.com</h5>
            </div>
        </div>

        <h3 class="eval-report">PER SECTION SUMMARY REPORT</h3>

        <div class="summary-info">
            <p><strong>Academic Year:</strong> <?php echo htmlspecialchars($academicYearName); ?>, <?php echo htmlspecialchars($semester2); ?> Semester</p>
            <p><strong>Faculty:</strong> <?php echo htmlspecialchars($faculty['Name']); ?></p>
        </div>

        <div class="section-summary">
 
 <?php
if (!$connection) {
 die("Connection failed: " . mysqli_connect_error());
}

$selected_section = $_GET['section'] ?? 'all';
$selected_academic_year = $academic_year_id;
$semester = $_GET['semester'] ?? null;
$facultyID = $_GET['FacultyID'] ?? null;

if ($selected_section === 'all') {
 $section_summary_query = "
 SELECT student_SECTION, COUNT(DISTINCT evaluation_table.student_ID) AS total_students_evaluated, AVG(question_score) AS average_score
 FROM evaluation_table
 INNER JOIN student_info ON evaluation_table.student_ID = student_info.student_ID
 WHERE evaluation_table.academic_year = ?
 GROUP BY student_SECTION
 ";
 $stmt = mysqli_prepare($connection, $section_summary_query);
 mysqli_stmt_bind_param($stmt, "s", $academic_year_id);
 mysqli_stmt_execute($stmt);
 $section_summary_result = mysqli_stmt_get_result($stmt);
 
 if ($section_summary_result && mysqli_num_rows($section_summary_result) > 0) {
     echo "<h2>Section Summary</h2>";
     echo "<table border='1'>";
     echo "<thead><tr><th>Section</th><th>Total Students Evaluated</th><th>Total Enrolled</th><th>Completion Percentage</th><th>Average Score</th></tr></thead>";
     echo "<tbody>";
 
     
         $total_students_query = "
SELECT 
 student_info.student_SECTION AS section,
 COUNT(DISTINCT CASE WHEN evaluation_table.academic_year = ? THEN evaluation_table.student_ID END) AS total_students_evaluated,
 COUNT(DISTINCT enrollments.student_ID) AS total_enrolled,
 AVG(CASE WHEN evaluation_table.academic_year = ? AND evaluation_table.FacultyID = ? THEN evaluation_table.question_score ELSE NULL END) AS average_score
FROM 
 student_info
LEFT JOIN 
 evaluation_table ON student_info.student_ID = evaluation_table.student_ID
LEFT JOIN 
 enrollments ON student_info.student_ID = enrollments.student_ID
LEFT JOIN 
 subjects ON enrollments.subject_code = subjects.subject_code
WHERE
 subjects.FacultyID = ? 
GROUP BY 
 student_info.student_SECTION;
";
 
         $stmt_total = mysqli_prepare($connection, $total_students_query);
         mysqli_stmt_bind_param($stmt_total, "ssss", $academic_year_id,$academic_year_id,$facultyID,$facultyID);
         mysqli_stmt_execute($stmt_total);
         $total_students_result = mysqli_stmt_get_result($stmt_total);
 

     
         while ($row = mysqli_fetch_assoc($total_students_result)) {
             $section = $row['section'];
             $total_students_evaluated = $row['total_students_evaluated'];
             $total_enrolled = $row['total_enrolled'];
             $average_score = round($row['average_score'] / 5 * 100, 2);
 
             echo "<tr><td>$section</td><td>$total_students_evaluated</td><td>$total_enrolled</td><td>". round($total_students_evaluated / $total_enrolled * 100, 2) ."%</td><td>$average_score%</td></tr>";
         }
 
         echo "</tbody></table>";
     
 } else {
     echo "<h2>No data available for the selected academic year.</h2>";
 }
 } else {
     $section_query = "
         SELECT COUNT(DISTINCT e.student_ID) AS total_evaluations, AVG(e.question_score) AS average_score
         FROM evaluation_table e
         INNER JOIN student_info si ON e.student_ID = si.student_ID
         WHERE e.academic_year = ? AND si.student_SECTION = ?
     ";
     $stmt = mysqli_prepare($connection, $section_query);
     mysqli_stmt_bind_param($stmt, "ss", $selected_academic_year, $selected_section);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
 
     if ($result && mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_assoc($result);
         $total_evaluations = $row['total_evaluations'];
         $average_score = round($row['average_score'] / 5 * 100, 2);
 
         $total_students_query = "
             SELECT COUNT(DISTINCT student_info.student_ID) AS total_students
             FROM enrollments
             INNER JOIN subjects ON enrollments.subject_code = subjects.subject_code
             INNER JOIN student_info ON enrollments.student_ID = student_info.student_ID
             WHERE subjects.FacultyID = ? AND student_info.student_SECTION = ?
         ";
 
         $stmt_total = mysqli_prepare($connection, $total_students_query);
 
         $subject_code = get_subject_code_for_section($connection, $selected_section);
 
         mysqli_stmt_bind_param($stmt_total, "ss", $facultyID, $selected_section);
         mysqli_stmt_execute($stmt_total);
         $total_students_result = mysqli_stmt_get_result($stmt_total);
         $total_students_row = mysqli_fetch_assoc($total_students_result);
         $total_students = $total_students_row['total_students'];
 
         echo "<h3>Total Students Evaluated : $total_evaluations / $total_students (". round($total_evaluations / $total_students * 100, 2) ."%)<br> For Department & Section: $course_section</h3><h3>Total Average Score: $average_score%</h3>";
     }
 
     $criteria_query = "
         SELECT DISTINCT c.criteria_name
         FROM evaluation_table e
         INNER JOIN question_list q ON e.question_id = q.question_id
         INNER JOIN criteria c ON q.criteria_id = c.criteria_id
         WHERE e.academic_year = ? AND e.student_ID IN (
             SELECT student_ID FROM student_info WHERE student_SECTION = ?
         )
         ORDER BY c.criteria_order
     ";
     $stmt = mysqli_prepare($connection, $criteria_query);
     mysqli_stmt_bind_param($stmt, "ss", $selected_academic_year, $selected_section);
     mysqli_stmt_execute($stmt);
     $criteria_result = mysqli_stmt_get_result($stmt);
 
     while ($criteria_row = mysqli_fetch_assoc($criteria_result)) {
         $criteria_name = htmlspecialchars($criteria_row['criteria_name']);
         echo "<h4>$criteria_name</h4>";
 
         $questions_query = "
             SELECT 
                 q.question_text,
                 SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS score_5,
                 SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS score_4,
             SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS score_3,
             SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS score_2,
             SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS score_1,
             AVG(e.question_score) AS average_score
         FROM evaluation_table e
         INNER JOIN question_list q ON e.question_id = q.question_id
         INNER JOIN criteria c ON q.criteria_id = c.criteria_id
         WHERE e.academic_year = ? AND e.student_ID IN (
             SELECT student_ID FROM student_info WHERE student_SECTION = ?
         ) AND c.criteria_name = ?
         GROUP BY q.question_text
     ";
     $stmt = mysqli_prepare($connection, $questions_query);
     mysqli_stmt_bind_param($stmt, "sss", $selected_academic_year, $selected_section, $criteria_name);
     mysqli_stmt_execute($stmt);
     $questions_result = mysqli_stmt_get_result($stmt);

     echo "<table border='1'>";
     echo "<thead><tr><th class='question-list'>Question</th><th>Rating 5 (%)</th><th>Rating 4 (%)</th><th>Rating 3 (%)</th><th>Rating 2 (%)</th><th>Rating 1 (%)</th><th>Average Rating</th></tr></thead>";
     echo "<tbody>";
     while ($question_row = mysqli_fetch_assoc($questions_result)) {
         $question_text = htmlspecialchars($question_row['question_text']);
         $score_5 = $question_row['score_5'];
         $score_4 = $question_row['score_4'];
         $score_3 = $question_row['score_3'];
         $score_2 = $question_row['score_2'];
         $score_1 = $question_row['score_1'];
         $average_score = round($question_row['average_score'],2);

         $total_responses = $score_5 + $score_4 + $score_3 + $score_2 + $score_1;
         $percent_5 = ($total_responses > 0) ? round(($score_5 / $total_responses) * 100, 2) : 0;
         $percent_4 = ($total_responses > 0) ? round(($score_4 / $total_responses) * 100, 2) : 0;
         $percent_3 = ($total_responses > 0) ? round(($score_3 / $total_responses) * 100, 2) : 0;
         $percent_2 = ($total_responses > 0) ? round(($score_2 / $total_responses) * 100, 2) : 0;
         $percent_1 = ($total_responses > 0) ? round(($score_1 / $total_responses) * 100, 2) : 0;

         echo "<tr><td>$question_text</td><td>$percent_5%</td><td>$percent_4%</td><td>$percent_3%</td><td>$percent_2%</td><td>$percent_1%</td><td>$average_score</td></tr>";
     }
     echo "</tbody></table>";
 }
}
mysqli_close($connection);

function get_subject_code_for_section($connection, $section) {
 
 $query = "SELECT DISTINCT subject_code FROM subjects WHERE subject_code LIKE CONCAT('%', ?, '%')";
 $stmt = mysqli_prepare($connection, $query);
 mysqli_stmt_bind_param($stmt, "s", $section);
 mysqli_stmt_execute($stmt);
 $result = mysqli_stmt_get_result($stmt);
 $row = mysqli_fetch_assoc($result);
 return $row ? $row['subject_code'] : null;
}
?>
        </div>

        <div class="footer" id="footer">
        <span>Prepared by: <?php echo $admin['name'] ?><br> <i><?php  echo $admin['role'] ?> &nbsp;&nbsp;</i></span>
        <span>Date Printed: <p class="datetime" id="dateTime"></p></span>
        </div>
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


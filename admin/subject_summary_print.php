<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Summary Print</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid blue;
            padding: 10px 20px;
            margin-bottom: 20px;
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
            margin: 5px 0;
            font-size: 14px;
        }
        .header {
            margin-bottom: 20px;
        }
        .header h3 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-align: center;
        }
        .summary-info {
            margin-bottom: 20px;
            text-align: center;
        }
        .summary-info h3 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }
        .summary-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        table {
            width: 98%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        table th {
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
            font-size: 14px;
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
.datetime{
    margin-top: 0px;
    color: gray;
}
@media print {
      @page {
        size: A4;
        margin: 5mm 10mm;
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
    </style>
</head>
<body onload="window.print()">
<?php
        include('../connection.php');
        if (!$connection) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $facultyID = $_GET['FacultyID'] ?? null;
        $academicYear = $_GET['academic_year'] ?? null;
        $academic_year_id = $_GET['academic_year_id'] ?? null;
        $semester = $_GET['semester'] ?? null;
        $semester2 = ($_GET['semester'] == "1") ? "1st" : (($_GET['semester'] == "2") ? "2nd" : "3rd");
        if ($facultyID === null || $academicYear === null || $academic_year_id === null || $semester === null) {
            echo "Invalid Faculty ID, Academic Year, or Academic Year ID.";
            exit;
        }
        $admin_query = "SELECT * FROM admin_list WHERE admin_ID = 1";
        $stmt = mysqli_prepare($connection, $admin_query);
        mysqli_stmt_execute($stmt);
        
        $admin_info = mysqli_stmt_get_result($stmt);
        $admin = mysqli_fetch_assoc($admin_info);
        
        $faculty_query = "SELECT Name FROM facultymembers WHERE FacultyID = ?";
        $stmt = mysqli_prepare($connection, $faculty_query);
        mysqli_stmt_bind_param($stmt, "s", $facultyID);
        mysqli_stmt_execute($stmt);
        $faculty_result = mysqli_stmt_get_result($stmt);

        if (!$faculty_result || mysqli_num_rows($faculty_result) === 0) {
            echo "Faculty member not found.";
            exit;
        }
        $total_students_query = "
        SELECT COUNT(DISTINCT student_id) AS total_enrolled
            FROM enrollments JOIN subjects ON enrollments.subject_code = subjects.subject_code
            WHERE FacultyID = ? AND academic_year = ?
        ";
        $stmt = mysqli_prepare($connection, $total_students_query);
        mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
        mysqli_stmt_execute($stmt);
        $total_count_result = mysqli_stmt_get_result($stmt);
        $total_count = mysqli_fetch_assoc($total_count_result)['total_enrolled'];

        $faculty = mysqli_fetch_assoc($faculty_result);

        $subjects_query = "SELECT DISTINCT s.Name AS subject_name
        FROM evaluation_table e
        JOIN subjects s ON e.subject_code = s.subject_code
        WHERE e.FacultyID = ?
        AND e.academic_year = ?";
        $stmt = mysqli_prepare($connection, $subjects_query);
        mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
        mysqli_stmt_execute($stmt);
        $subjects_result = mysqli_stmt_get_result($stmt);

        $selected_subject = $_GET['subject'] ?? 'all';
        ?>

        <?php
        $student_count_query = "
        SELECT COUNT(DISTINCT student_id) AS student_count
        FROM evaluation_table
        WHERE FacultyID = ? AND academic_year = ?";
        $stmt = mysqli_prepare($connection, $student_count_query);
        mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
        mysqli_stmt_execute($stmt);
        $student_count_result = mysqli_stmt_get_result($stmt);
        $student_count = mysqli_fetch_assoc($student_count_result)['student_count'];

        $overall_avg_score = 0;
        $total_scores_sum = 0;
        $total_responses_sum = 0;

        if ($selected_subject !== 'all') {
            $query = "
                SELECT 
                    s.Name AS subject_name,
                    c.criteria_name AS criteria,
                    q.question_text,
                    AVG(e.question_score) AS avg_score,
                    SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS count_5,
                    SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS count_4,
                    SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS count_3,
                    SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS count_2,
                    SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS count_1,
                    COUNT(e.question_score) AS total_responses
                FROM evaluation_table e
                JOIN question_list q ON e.question_id = q.question_id
                JOIN criteria c ON q.criteria_id = c.criteria_id
                JOIN subjects s ON e.subject_code = s.subject_code
                WHERE e.FacultyID = ? AND e.academic_year = ? AND s.Name = ?
                GROUP BY s.Name, c.criteria_name, q.question_text";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "sss", $facultyID, $academic_year_id, $selected_subject);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (!$result || mysqli_num_rows($result) === 0) {
                echo "No evaluation data found for this teacher in the selected academic year and subject.";
                echo "<a href='manage_academic.php?id=" . $academic_year_id . "' class='back'><i class='fas fa-arrow-left'></i>Back</a><br><br>";
                exit;
            }
        } else {
            $query = "
                SELECT 
                    s.Name AS subject_name,
                    c.criteria_name AS criteria,
                    q.question_text,
                    AVG(e.question_score) AS avg_score,
                    SUM(CASE WHEN e.question_score = 5 THEN 1 ELSE 0 END) AS count_5,
                    SUM(CASE WHEN e.question_score = 4 THEN 1 ELSE 0 END) AS count_4,
                    SUM(CASE WHEN e.question_score = 3 THEN 1 ELSE 0 END) AS count_3,
                    SUM(CASE WHEN e.question_score = 2 THEN 1 ELSE 0 END) AS count_2,
                    SUM(CASE WHEN e.question_score = 1 THEN 1 ELSE 0 END) AS count_1,
                    COUNT(e.question_score) AS total_responses
                FROM evaluation_table e
                JOIN question_list q ON e.question_id = q.question_id
                JOIN criteria c ON q.criteria_id = c.criteria_id
                JOIN subjects s ON e.subject_code = s.subject_code
                WHERE e.FacultyID = ? AND e.academic_year = ?
                GROUP BY s.Name, c.criteria_name, q.question_text";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (!$result || mysqli_num_rows($result) === 0) {
                echo "No evaluation data found for this teacher in the selected academic year.";
                echo "<a href='manage_academic.php?id=" . $academic_year_id . "' class='back'><i class='fas fa-arrow-left'></i>Back</a><br><br>";
                exit;
            }
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $total_scores_sum += $row['avg_score'] * $row['total_responses'];
            $total_responses_sum += $row['total_responses'];
        }
        $overall_avg_score = $total_scores_sum / $total_responses_sum / 5 * 100;

        $performance = '';
        if ($overall_avg_score >= 92) {
            $performance = 'Excellent';
        } elseif ($overall_avg_score >= 74) {
            $performance = 'Very Good';
        } elseif ($overall_avg_score >= 56) {
            $performance = 'Good';
        } elseif ($overall_avg_score >= 38) {
            $performance = 'Fair';
        } else {
            $performance = 'Needs Improvement';
        }
        $total_avg_rating_query = "
            SELECT avg_rating 
            FROM evaluation_summary 
            WHERE faculty_id = ? AND academic_id = ?";
        $stmt = mysqli_prepare($connection, $total_avg_rating_query);
        mysqli_stmt_bind_param($stmt, "ss", $facultyID, $academic_year_id);
        mysqli_stmt_execute($stmt);
        $total_avg_rating_result = mysqli_stmt_get_result($stmt);
        $total_avg_rating = mysqli_fetch_assoc($total_avg_rating_result)['avg_rating'];
        ?>
                    
 
        <div class='box-body'>
        <div id="header" class="header">
    <img src="../images/exact logo.png" alt="Left Logo">
    <div class="title">
      <p>EXACT COLLEGES OF ASIA,INC</p>
      <h5>Suclayin, Arayat, Pampanga</h5>
      <h5>Cel No. 0925-870-1013; 0917-324-7803</h5>
      <h5>Email address: exact.colleges@yahoo.com</h5>
    </div>
  </div>
            <h3 class="eval-report" style="text-align: center; text-decoration:underline">SUBJECT SUMMARY REPORT</h3>

            <h3> Academic Year: &nbsp;<?php echo htmlspecialchars($academicYear);?>, <?php echo htmlspecialchars($semester2); ?> Semester</h3>
            <h3>Total Students Evaluated:&nbsp;&nbsp; <?php echo $student_count; ?> / <?php echo $total_count; ?> (<?php echo round($student_count / $total_count * 100, 2); ?>%)</h3>
            <h3>Evaluation Results for: <?php echo ($selected_subject === 'all') ? 'All Subjects' : htmlspecialchars($selected_subject); ?><br><br> Faculty: <?php echo htmlspecialchars($faculty['Name']); ?></h3>
            <?php if ($selected_subject !== 'all'): ?>
                <?php
                $subject_query = "
                    SELECT AVG(avg_score) AS overall_avg
                    FROM (
                        SELECT AVG(e.question_score) AS avg_score
                        FROM evaluation_table e
                        JOIN subjects s ON e.subject_code = s.subject_code
                        WHERE e.FacultyID = ? AND e.academic_year = ? AND s.Name = ?
                        GROUP BY e.subject_code
                    ) AS subject_avg
                ";
                $stmt = mysqli_prepare($connection, $subject_query);
                mysqli_stmt_bind_param($stmt, "sss", $facultyID, $academic_year_id, $selected_subject);
                mysqli_stmt_execute($stmt);
                $subject_result = mysqli_stmt_get_result($stmt);
                $subject_row = mysqli_fetch_assoc($subject_result);
                $subject_avg_score = $subject_row['overall_avg'] / 5 * 100;

                $subject_performance = '';
                if ($subject_avg_score >= 92) {
                    $subject_performance = 'Excellent';
                } elseif ($subject_avg_score >= 74) {
                    $subject_performance = 'Very Good';
                } elseif ($subject_avg_score >= 56) {
                    $subject_performance = 'Good';
                } elseif ($subject_avg_score >= 38) {
                    $subject_performance = 'Fair';
                } else {
                    $subject_performance = 'Needs Improvement';
                }
                ?>
                <div class="overall-rating">
                    <h3>Subject Total Score: <?php echo round($subject_avg_score, 2); ?>% (<?php echo $subject_performance; ?>)</h3>
                </div>
                <br>
            <?php endif; ?>
            <?php if ($selected_subject === 'all'): ?>
                <div class="overall-rating">
                    <h3>Overall Subject Score: <?php echo round($total_avg_rating, 2); ?>% (<?php echo $performance; ?>)</h3>
                </div>
            <?php endif; ?>
            <table border='1'>
                <thead>
                    <tr>
                        <th class="question-list">Question</th>
                        <th>Rating 5 (%)</th>
                        <th>Rating 4 (%)</th>
                        <th>Rating 3 (%)</th>
                        <th>Rating 2 (%)</th>
                        <th>Rating 1 (%)</th>
                        <th>Average Rating</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Function to calculate performance rating
                    function getPerformance2($criteria_avg_score) {
                        if ($criteria_avg_score >= 4.6) {
                            return 'Excellent';
                        } elseif ($criteria_avg_score >= 3.8) {
                            return 'Very Good';
                        } elseif ($criteria_avg_score >= 2.8) {
                            return 'Good';
                        } elseif ($criteria_avg_score >= 1.8) {
                            return 'Fair';
                        } else {
                            return 'Needs Improvement';
                        }
                    }

                    // Function to convert score to percentage
                    function scoreToPercentage($score) {
                        return ($score / 5) * 100;
                    }

                    $current_subject = null;
                    $current_criteria = null;
                    $current_criteria_avg_score = 0;
                    $current_criteria_total_responses = 0;
                    $current_questions = array();

                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        if ($current_subject !== $row['subject_name']) {
                            if ($current_criteria !== null) {
                                echo "<tr>";
                                echo "<th class='criteria-head' colspan='8'>" . htmlspecialchars($current_criteria) . " - " . round(scoreToPercentage($current_criteria_avg_score / $current_criteria_total_responses), 2) . "% (" . getPerformance2($current_criteria_avg_score / $current_criteria_total_responses) . ")</th>";
                                echo "</tr>";
                                foreach ($current_questions as $question) {
                                    echo "<tr>";
                                    echo "<td class='questions'>" . htmlspecialchars($question['question_text']) . "</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_5'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_4'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_3'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_2'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_1'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round($question['avg_score'], 2) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            echo "<tr>";
                            echo "<th class='subject-head' colspan='8'>" . htmlspecialchars($row['subject_name']) . "</th>";
                            echo "</tr>";
                            $current_subject = $row['subject_name'];
                            $current_criteria = null;
                            $current_criteria_avg_score = 0;
                            $current_criteria_total_responses = 0;
                            $current_questions = array();
                        }

                        if ($current_criteria !== $row['criteria']) {
                            if ($current_criteria !== null) {
                                echo "<tr>";
                                echo "<th class='criteria-head' colspan='8'>" . htmlspecialchars($current_criteria) . " - " . round(scoreToPercentage($current_criteria_avg_score / $current_criteria_total_responses), 2) . "% (" . getPerformance2($current_criteria_avg_score / $current_criteria_total_responses) . ")</th>";
                                echo "</tr>";
                                foreach ($current_questions as $question) {
                                    echo "<tr>";
                                    echo "<td class='questions'>" . htmlspecialchars($question['question_text']) . "</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_5'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_4'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_3'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_2'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round(scoreToPercentage($question['count_1'] / $question['total_responses']) * 5, 2) . "%</td>";
                                    echo "<td>" . round($question['avg_score'], 2) . "</td>";
                                    echo "</tr>";
                                }
                            }
                            $current_criteria = $row['criteria'];
                            $current_criteria_avg_score = 0;
                            $current_criteria_total_responses = 0;
                            $current_questions = array();
                        }

                        $current_criteria_avg_score += $row['avg_score'] * $row['total_responses'];
                        $current_criteria_total_responses += $row['total_responses'];

                        $current_questions[] = $row;
                    }
                    if ($current_criteria !== null) {
                        echo "<tr>";
                        echo "<th class='criteria-head' colspan='8'>" . htmlspecialchars($current_criteria) . " - " . round(scoreToPercentage($current_criteria_avg_score / $current_criteria_total_responses), 2) . "% (" . getPerformance2($current_criteria_avg_score / $current_criteria_total_responses) . ")</th>";
                        echo "</tr>";
                        foreach ($current_questions as $question) {
                            echo "<tr>";
                            echo "<td class='questions'>" . htmlspecialchars($question['question_text']) . "</td>";
                            echo "<td>" . round(scoreToPercentage($question['count_5'] / $question['total_responses']) * 5, 2) . "%</td>";
                            echo "<td>" . round(scoreToPercentage($question['count_4'] / $question['total_responses']) * 5, 2) . "%</td>";
                            echo "<td>" . round(scoreToPercentage($question['count_3'] / $question['total_responses']) * 5, 2) . "%</td>";
                            echo "<td>" . round(scoreToPercentage($question['count_2'] / $question['total_responses']) * 5, 2) . "%</td>";
                            echo "<td>" . round(scoreToPercentage($question['count_1'] / $question['total_responses']) * 5, 2) . "%</td>";
                            echo "<td>" . round($question['avg_score'], 2) . "</td>";
                            echo "</tr>";
                        }
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

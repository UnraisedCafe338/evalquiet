<?php
 session_start();
?>
<?php
include('../connection.php');

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$academic_year_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($academic_year_id === null) {
    echo "Invalid academic year ID.";
    exit;
}

$query = "SELECT * FROM academic_list WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param('i', $academic_year_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo "Academic year not found.";
    exit;
}
$academic_year = $result->fetch_assoc();
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

    $update_query = "UPDATE academic_list SET year=?, semester=?, status=? WHERE id = ?";
    $update_stmt = $connection->prepare($update_query);
    $update_stmt->bind_param('siii', $year, $semester, $status, $academic_year_id);
    if ($update_stmt->execute()) {
        echo "<p class='below-title'>Updating academic year details...</p>";
        header("Refresh:1");
    } else {
        echo "<p class='below-title'>Error updating academic year details: " . $connection->error . "</p>";
    }
}

$order_by = "";
$selected_filter = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['apply_filter'])) {
    $score_filter = $_POST['score_filter'];
    $selected_filter = $score_filter;
    if ($score_filter == 'highest') {
        $order_by = "ORDER BY overall_rating DESC";
    } elseif ($score_filter == 'lowest') {
        $order_by = "ORDER BY overall_rating ASC";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Manage Academic Year</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<style>
    .below-title {
        background: linear-gradient(to top, rgb(66, 78, 255), rgb(49, 0, 208));
        width: 1245px;
        padding: 10px;
        position: fixed;
        margin-top: 68px;
        margin-left: 260px;
        color: white;
        top: 30px;
        border-bottom: 3px solid #002594;
    }

    .academic-button {
        background-color: darkblue;
        min-width: 120px;
        margin-right: 0px;
        margin-left: -10px;
        padding-left: 15px;
        border-radius: 10px;
    }
    .box-header{
        margin-top: 15px!important;
        width: 1200px!important;
        text-align: left!important;
        margin-right: -70px;
    }
    .box-body{
        width: 1200px!important;
        margin-right: -70px;
        margin-bottom: 40px!important;
    }
    .table-row-name{
        width: 20%;
    }
    table{
        width: 100%!important;
    }
    .table-row-student{
        width: 20%;
    }
    table td{
        height: 50px;
    }
    .table-row-count{
        width: 20%;
    }
    .back-academic{
        margin-left: 20px;
        background-color: #0055ff;
        color: rgb(255, 255, 255);
        padding: 15px 20px;
        text-align: center;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        text-decoration: none;
        height: 20px;
    }
    .edit{
        width: 600px;
    }
    .head{
        display: flex;
    }
    .print-button{
        padding: 15px 20px;
        height: 50px;
        margin-left: 420px;
    }
</style>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="content">
        <h1>Manage Academic Year</h1><br><br><br>
        
        <div class="box-header">
            <div class="head">
            <h2 class="edit">Edit Academic Year: <?php echo $academic_year['year']; ?>&nbsp;&nbsp;<?php echo $semester_text; ?>&nbsp;Semester </h2><button class="print-button" onclick="navigateToPrintPage()">Print</button><a class="back-academic" href="academic_year.php"><i class='fas fa-arrow-left'></i>Back</a>
            </div>
            <form method="post">
                <label for="year">Year:</label>
                <input type="text" id="year" name="year" value="<?php echo $academic_year['year']; ?>">&nbsp;&nbsp;
                <label for="semester">Semester:</label>
                <select id="semester" name="semester">
                    <option value="1" <?php if ($academic_year['semester'] == 1) echo "selected"; ?>>1st</option>
                    <option value="2" <?php if ($academic_year['semester'] == 2) echo "selected"; ?>>2nd</option>
                    <option value="3" <?php if ($academic_year['semester'] == 3) echo "selected"; ?>>3rd</option>
                </select>&nbsp;&nbsp;
                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="0" <?php if ($academic_year['status'] == 0) echo "selected"; ?>>Finished</option>
                    <option value="2" <?php if ($academic_year['status'] == 2) echo "selected"; ?>>Start</option>
                </select>&nbsp;&nbsp;
                <input type="hidden" name="update_academic_year" value="1">
                <input type="submit" value="Update">
            </form>
        </div>

        <div class="box-body">
            <h2>Faculty List</h2>
            <div class="batch-upload-container">
            <form action="batch_upload_faculty.php?id=<?php echo $academic_year_id; ?>" method="post" enctype="multipart/form-data">
    <label for="file">Upload CSV file:</label>
    <input type="file" name="file" id="file" accept=".csv" required>
    <button type="submit" name="upload">Upload</button>
</form>

            </div>
            <form id="filterForm" method="post">
                <label for="score_filter">Filter by Score:</label>
                <select id="score_filter" name="score_filter" onchange="submitForm()">
                    <option value="">Default</option>
                    <option value="highest" <?php if ($selected_filter == 'highest') echo "selected"; ?>>Highest Score</option>
                    <option value="lowest" <?php if ($selected_filter == 'lowest') echo "selected"; ?>>Lowest Score</option>
                </select>
                <input type="hidden" name="apply_filter" value="1">
            </form>
            <br>

            <table border="1">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>No. of Students Evaluated / Total Enrolled</th>
                        <th>Evaluation Percentage</th>
                        <th>Overall Rating Score</th>
                        <th>Performance</th>
                        <th>Action</th>
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

if ($faculty_result && $faculty_result->num_rows > 0) {
    while ($row = $faculty_result->fetch_assoc()) {
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
        $stmt_check = $connection->prepare($check_query);
        $stmt_check->bind_param("ss", $faculty_id, $academic_year_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $num_rows = $row_check['num_rows'];

        if ($num_rows > 0) {
            $update_query = "UPDATE evaluation_summary SET avg_rating = ? WHERE faculty_id = ? AND academic_id = ?";
            $stmt_update = $connection->prepare($update_query);
            $stmt_update->bind_param("dss", $percentage_score, $faculty_id, $academic_year_id);
            $stmt_update->execute();
        } else {
            $insert_query = "INSERT INTO evaluation_summary (faculty_id, academic_id, avg_rating) VALUES (?, ?, ?)";
            $stmt_insert = $connection->prepare($insert_query);
            $stmt_insert->bind_param("ssd", $faculty_id, $academic_year_id, $percentage_score);
            $stmt_insert->execute();
        }

        echo "<tr>";
        echo "<td class='table-row-name'>" . $row['Name'] . "</td>";
        echo "<td class='table-row-student'>" . $row['student_count'] . " / " . $row['total_enrolled'] . "</td>";
        if ($row['total_enrolled'] != 0) {
            $percentage = round($row['student_count'] / $row['total_enrolled'] * 100, 2);
        } else {
            $percentage = 0;
        }
        echo "<td class='table-row-student'>" . $percentage . "%</td>";
        echo "<td class='table-row-ratings'>" . round($overall_rating * 100, 2) . "</td>";
        echo "<td class='table-row-ratings'>" . $performance . "</td>";
        echo "<td><a class='button' href='teacher_summary.php?FacultyID=" . $row['FacultyID'] . "&academic_year_id=" . $academic_year_id .
            "&academic_year=" . urlencode($academic_year['year']) . "&semester=" . urlencode($academic_year['semester']) . "'><i class='fas fa-chart-bar'></i></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No faculty members found.</td></tr>";
}
?>

<script>
        function submitForm() {
            document.getElementById('filterForm').submit();
        }

        function navigateToPrintPage() {
            const academicYearId = "<?php echo $academic_year_id; ?>";
            const scoreFilter = document.getElementById('score_filter').value;
            window.location.href = `teacher_list_print.php?academic_year_id=${academicYearId}&score_filter=${scoreFilter}`;
        }
    </script>
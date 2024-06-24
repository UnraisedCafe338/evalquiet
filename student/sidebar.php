<body>
<style>
    .box-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .box-footer span {
        text-align: right; 
    }
    .acc-info{
        position: fixed;
  left: 70%;
  bottom: 93%;
  color: white;
  z-index: 1001;
  font-size: large;
    }
    .acc-info i{
        font-size: 25px;
    }
</style>
<?php
include('../connection.php');

if ($connection) {
    
    if(isset($_SESSION['student_id'])) {
        $student_ID = $_SESSION['student_id'];
  
        $query = "SELECT * FROM student_info WHERE student_ID = '$student_ID'";
    
        $result = mysqli_query($connection, $query);
   
        if(mysqli_num_rows($result) > 0) {

            $student = mysqli_fetch_assoc($result);
        } else {

            echo "No student found with ID: $student_ID";
            exit();
        }
    } else {
        
  
        echo "Student ID not provided.ssss$student_ID";
        exit();
    }
} else {
    
    echo "Failed to connect to the database.";
    exit();
}
?>
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="sidebar-container">
    <div class="sidebar">
        <img src="../images/exact logo.png" alt="Logo" class="logo">
        <h1 class="admin-title">STUDENT PANEL</h1>
        <ul class="menu"><br><br>
            <li class="dashboard-button"><a href="student_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li class="evaluation-button"><a href="evaluation_menu.php"><i class="fas fas fa-tasks"></i>Evaluate Teachers</a></li>
            <li class="password-button"><a href="manage_password.php"><i class="fas fa-user-cog"></i>Manage Password</a></li>
            <li class="subject-button"><a href="subject_list.php"><i class="fas fas fa-list"></i>Subjects</a></li>
        </ul>
    </div>
</div>
<div class="acc-info">
    <?php
echo "<i class='fas fa-user'></i> </strong></td><td>{$student['student_firstName']} {$student['student_middleName']} {$student['student_lastName']}";
?>
</div>
<section>
    <button class="show-modal"><i class="fa fa-sign-in-alt">&nbsp;&nbsp;&nbsp;Log out</i></button>
    <input type="hidden" name="student_ID" value="<?php echo $_SESSION['student_id']; ?>">

    <div class="modal-box">
        <i class="fas fa-exclamation-triangle"></i>
        <h2>Are you sure you wanna log out?</h2>
        <div class="buttons">
        <form action="logout.php?studentId=<?php echo $_SESSION['student_id']; ?>" method="post">

                <button type="submit" class="sign-out">Yes</button>
                <input type="hidden" name="student_ID" value="<?php echo $_SESSION['student_id']; ?>">
                <button type="button" class="close-btn">No</button>
        </div>
        
            </form>
            
    </div>
    <span class="overlay"></span>
</section>

<div class="box-footer">
    <span>2024 | Copyright Team Quiet</span>
    <span>OSA Faculty Evaluation Management System</span>
</div>

<script>
const section = document.querySelector("section");
const overlay = document.querySelector(".overlay");
const showBtn = document.querySelector(".show-modal");
const closeBtn = document.querySelector(".close-btn");

showBtn.addEventListener("click", () => section.classList.add("active"));
closeBtn.addEventListener("click", () => section.classList.remove("active"));
</script>
</body>

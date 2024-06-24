
<?php
   
include('../connection.php');
include('../session_detector2.php');

if ($connection) {
    
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
  
        $query = "SELECT * FROM admin_list WHERE admin_name = '$username'";
    
        $result = mysqli_query($connection, $query);
   
        if(mysqli_num_rows($result) > 0) {

            $student = mysqli_fetch_assoc($result);
        } else {

            echo "No username found with ID: $username";
            exit();
        }
    } else {
        
  
        echo "Username not provided.$username";
        exit();
    }
} else {
    
    echo "Failed to connect to the database.";
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" href="../images/system-logo.png">
</head>
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
        .buttons-yn{
            display: flex;
        }
    </style>
    
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div class="sidebar-container">
    <div class="sidebar">
    <img src="../images/exact logo.png" alt="Logo" class="logo">
        <p class="admin-title">ADMIN PANEL</p>
        
        <ul class="menu">
            <li class="dashboard-button"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li class="academic-button"><a href="academic_year.php"><i class="fas fa-graduation-cap"></i>Academic Year</a></li>
            <li class="question-button"><a href="criteria_list.php"><i class="fas fa-comment-dots"></i>Questionnaire</a></li>
            <li class="students-button"><a href="student_list.php"><i class="fas fa-user"></i>Students</a></li>
            <li class="session-button"><a href="session_list.php"><i class="fas fa-user-clock"></i>Active Sessions</a></li>
            <li class="settings-button"><a href="settings.php"><i class="fas fa-cog"></i>Settings</a></li>
            
        </ul>
    </div>
</div>

<div class="box-footer">
        <span>2024 | Copyright Team Quiet</span>
        <span>OSA Faculty Evaluation Management System</span>
    </div>
<section>

<button class="show-modal"><i class="fa fa-sign-in-alt">&nbsp;&nbsp;&nbsp;Log out</i></button>


<div class="modal-box">
 <i class="fas fa-exclamation-triangle"></i>
 <h2>Are you sure you wanna log out?</h2>
 
 
 <div class="buttons-yn">
 <form action="logout.php?username=<?php echo $_SESSION['username']; ?>" method="post">

    <button class="sign-out" onclick="backToLoginPage()"> Yes</button>
    <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
    </form> 
    <button class="no-btn">No</button>

    </div>
    
 </div>
 <span class="overlay"></span>

</section>


<script>
const section = document.querySelector("section");
const overlay = document.querySelector(".overlay");
const showBtn = document.querySelector(".show-modal");
const closeBtn = document.querySelector(".no-btn");

showBtn.addEventListener("click", () => section.classList.add("active"));
closeBtn.addEventListener("click", () => section.classList.remove("active"));
function backToLoginPage() {
    window.location.href = 'admin_login.php'; 
    session_destroy();
}
</script>
</body>
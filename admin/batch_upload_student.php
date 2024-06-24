<?php

include('../connection.php');

$uploaded_student_ids = [];

if (isset($_POST['upload'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            fgetcsv($handle, 1000, ',');

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $yearStmt = $pdo->prepare("SELECT id FROM academic_list WHERE default_select = 1 AND status = '2' LIMIT 1");
                $yearStmt->execute();
                $academic_info = $yearStmt->fetch(PDO::FETCH_ASSOC);

                if (!$academic_info) {
                    throw new Exception("No current academic year set.");
                }

                $current_academic_year_id = $academic_info['id'];

                $stmt = $pdo->prepare("INSERT INTO student_info (student_ID, student_lastName, student_firstName, student_middleName, student_COURSE, student_SECTION, student_PASS) 
                                      VALUES (:student_ID, :student_lastName, :student_firstName, :student_middleName, :student_COURSE, :student_SECTION, :student_PASS)");

                $updateStmt = $pdo->prepare("UPDATE student_info SET student_lastName = :student_lastName, student_firstName = :student_firstName, student_middleName = :student_middleName, student_COURSE = :student_COURSE, student_SECTION = :student_SECTION WHERE student_ID = :student_ID");

                $enrollStmt = $pdo->prepare("INSERT INTO enrollments (student_ID, subject_code, academic_year) VALUES (:student_ID, :subject_code, :academic_year)");

                $checkSubjectStmt = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE subject_code = :subject_code");

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $student_ID = $data[0];
                    $student_lastNAME = $data[1];
                    $student_firstNAME = $data[2];
                    $student_middleNAME = $data[3];
                    $student_COURSE = $data[4];
                    $student_SECTION = $data[5];

                    $check_query = "SELECT student_ID FROM student_info WHERE student_ID = :student_ID";
                    $check_stmt = $pdo->prepare($check_query);
                    $check_stmt->bindParam(':student_ID', $student_ID);
                    $check_stmt->execute();

                    if ($check_stmt->rowCount() > 0) {
                        
                        $updateStmt->bindParam(':student_ID', $student_ID);
                        $updateStmt->bindParam(':student_lastName', $student_lastNAME);
                        $updateStmt->bindParam(':student_firstName', $student_firstNAME);
                        $updateStmt->bindParam(':student_middleName', $student_middleNAME);
                        $updateStmt->bindParam(':student_COURSE', $student_COURSE);
                        $updateStmt->bindParam(':student_SECTION', $student_SECTION);
                        $updateStmt->execute();

                        
                        for ($i = 6; $i <= 12; $i++) {
                            if (!empty($data[$i])) {
                                
                                $checkSubjectStmt->bindParam(':subject_code', $data[$i]);
                                $checkSubjectStmt->execute();
                                $subject_exists = $checkSubjectStmt->fetchColumn();

                                if ($subject_exists) {
                                    $enrollStmt->bindParam(':student_ID', $student_ID);
                                    $enrollStmt->bindParam(':subject_code', $data[$i]);
                                    $enrollStmt->bindParam(':academic_year', $current_academic_year_id);
                                    $enrollStmt->execute();
                                }
                            }
                        }
                        $uploaded_student_ids[] = $student_ID;
                        continue;
                    }

                   
                    $student_PASS = generateRandomPassword();
                    $stmt->bindParam(':student_ID', $student_ID);
                    $stmt->bindParam(':student_lastName', $student_lastNAME);
                    $stmt->bindParam(':student_firstName', $student_firstNAME);
                    $stmt->bindParam(':student_middleName', $student_middleNAME);
                    $stmt->bindParam(':student_COURSE', $student_COURSE);
                    $stmt->bindParam(':student_SECTION', $student_SECTION);
                    $stmt->bindParam(':student_PASS', $student_PASS);

                    if ($stmt->execute()) {
                        $uploaded_student_ids[] = $student_ID;

                        for ($i = 6; $i <= 12; $i++) {
                            if (!empty($data[$i])) {
                                
                                $checkSubjectStmt->bindParam(':subject_code', $data[$i]);
                                $checkSubjectStmt->execute();
                                $subject_exists = $checkSubjectStmt->fetchColumn();

                                if ($subject_exists) {
                                    $enrollStmt->bindParam(':student_ID', $student_ID);
                                    $enrollStmt->bindParam(':subject_code', $data[$i]);
                                    $enrollStmt->bindParam(':academic_year', $current_academic_year_id);
                                    $enrollStmt->execute();
                                }
                            }
                        }
                    }
                }

                echo "Batch upload successful.";
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }

            fclose($handle);
        } else {
            echo "Error opening the file.";
        }
    } else {
        echo "Error uploading the file.";
    }
}

if (!empty($uploaded_student_ids)) {
    echo "<h2>Uploaded Students</h2>";
    echo "<table border='1'>";
    echo "<thead><tr><th>Student ID</th><th>Last Name</th><th>First Name</th><th>Middle Name</th><th>Course</th><th>Section</th><th>Password</th></tr></thead>";
    echo "<tbody>";

    foreach ($uploaded_student_ids as $student_id) {
        $query = "SELECT * FROM student_info WHERE student_ID = :student_ID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':student_ID', $student_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<tr>";
            echo "<td>{$row['student_ID']}</td>";
            echo "<td>{$row['student_lastName']}</td>";
            echo "<td>{$row['student_firstName']}</td>";
            echo "<td>{$row['student_middleName']}</td>";
            echo "<td>{$row['student_COURSE']}</td>";
            echo "<td>{$row['student_SECTION']}</td>";
            echo "<td>{$row['student_PASS']}</td>";
            echo "</tr>";
        }
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "No students uploaded.";
}

function generateRandomPassword($length = 8) {
    $characters = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';
    $password = '';
    $maxIndex = strlen($characters) - 1;
    for ($i = 0; $length > $i; $i++) {
        $password .= $characters[mt_rand(0, $maxIndex)];
    }
    return $password;
}
?>

<button onclick="window.location.href='student_list.php';">Go to Student List</button>

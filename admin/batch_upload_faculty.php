<?php

include('../connection.php');

$uploaded_faculty_ids = [];

if (isset($_POST['upload'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $file = $_FILES['file']['tmp_name'];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            fgetcsv($handle, 1000, ',');

            try {
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if (!isset($_GET['id'])) {
                    throw new Exception("Invalid academic year ID.");
                }

                $current_academic_year_id = $_GET['id'];

                $facultyStmt = $pdo->prepare("INSERT INTO facultymembers (FacultyID, Name) VALUES (:FacultyID, :Name)");
                $subjectStmt = $pdo->prepare("INSERT INTO subjects (Name, subject_code, FacultyID) VALUES (:Name, :subject_code, :FacultyID)");
                $acadFacultyStmt = $pdo->prepare("
                    INSERT INTO acadfaculty (FacultyID, academic_year) 
                    SELECT :FacultyID, :academic_year 
                    FROM DUAL
                    WHERE NOT EXISTS (
                        SELECT 1 FROM acadfaculty 
                        WHERE FacultyID = :FacultyID AND academic_year = :academic_year
                    )
                ");
                $checkFacultyStmt = $pdo->prepare("SELECT FacultyID FROM facultymembers WHERE Name = :Name");
                $checkSubjectStmt = $pdo->prepare("SELECT * FROM subjects WHERE subject_code = :subject_code AND FacultyID = :FacultyID");

                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $facultyID = $data[0];
                    $facultyName = $data[1];

                    $checkFacultyStmt->bindParam(':Name', $facultyName);
                    $checkFacultyStmt->execute();

                    if ($checkFacultyStmt->rowCount() == 0) {
                        $facultyStmt->bindParam(':FacultyID', $facultyID);
                        $facultyStmt->bindParam(':Name', $facultyName);
                        $facultyStmt->execute();
                    } else {
                        $existingFaculty = $checkFacultyStmt->fetch(PDO::FETCH_ASSOC);
                        $facultyID = $existingFaculty['FacultyID'];
                    }

                    for ($i = 2; $i < count($data); $i += 2) {
                        if (!empty($data[$i]) && !empty($data[$i + 1])) {
                            $subjectName = $data[$i];
                            $subjectCode = $data[$i + 1];

                            $checkSubjectStmt->bindParam(':subject_code', $subjectCode);
                            $checkSubjectStmt->bindParam(':FacultyID', $facultyID);
                            $checkSubjectStmt->execute();

                            if ($checkSubjectStmt->rowCount() == 0) {
                                $subjectStmt->bindParam(':Name', $subjectName);
                                $subjectStmt->bindParam(':subject_code', $subjectCode);
                                $subjectStmt->bindParam(':FacultyID', $facultyID);
                                $subjectStmt->execute();
                            }
                        }
                    }

                    $acadFacultyStmt->bindParam(':FacultyID', $facultyID);
                    $acadFacultyStmt->bindParam(':academic_year', $current_academic_year_id);
                    $acadFacultyStmt->execute();

                    $uploaded_faculty_ids[] = $facultyID; // Track the uploaded faculty ID
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

if (!empty($uploaded_faculty_ids)) {
    echo "<h2>Uploaded Faculty Members</h2>";
    echo "<table border='1'>";
    echo "<thead><tr><th>Faculty ID</th><th>Name</th></tr></thead>";
    echo "<tbody>";

    foreach ($uploaded_faculty_ids as $facultyID) {
        $query = "SELECT * FROM facultymembers WHERE FacultyID = :FacultyID";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':FacultyID', $facultyID);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<tr>";
            echo "<td>{$row['FacultyID']}</td>";
            echo "<td>{$row['Name']}</td>";
            echo "</tr>";
        }
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "No faculty members uploaded.";
}
?>

<button onclick="window.location.href='manage_academic.php?id=<?php echo $current_academic_year_id; ?>';">Go to Faculty List</button>

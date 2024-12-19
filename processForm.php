<?php
// Include database connection
include 'dbconn.php';

// Start session
session_start();

// Check if form_id is passed
if (!isset($_POST['form_id'])) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Invalid request: Form ID is missing.'
                }).then(function() {
                    window.history.back();
                });
            });
          </script>";
    exit;
}

$form_id = $_POST['form_id'];

try {
    // Start a transaction
    $conn->begin_transaction();

    // 1. Insert Subjects and Map to Faculty
    $subjectCodes = $_POST['subjectCode'] ?? [];
    $subjectNames = $_POST['subjectName'] ?? [];
    $employeeIDs = $_POST['employeeID'] ?? [];
    $section = $_POST['section'];

    $subjectIds = []; // To store inserted subject IDs for mapping

    foreach ($subjectCodes as $index => $subjectCode) {
        $subjectName = $subjectNames[$index];
        $employeeID = $employeeIDs[$index];

        // Directly insert the subject
        $insertSubjectStmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, form_id, section) VALUES (?, ?, ?, ?)");
        $insertSubjectStmt->bind_param('ssis', $subjectCode, $subjectName, $form_id, $section);
        $insertSubjectStmt->execute();
        $subjectId = $insertSubjectStmt->insert_id;

        // Save the inserted subject ID
        $subjectIds[] = $subjectId;

        // Map subject to faculty
        $facultyStmt = $conn->prepare("INSERT INTO subject_faculty_mapping (subject_id, employee_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE employee_id = ?");
        $facultyStmt->bind_param('iss', $subjectId, $employeeID, $employeeID);
        $facultyStmt->execute();
    }

    // 2. Insert Mentoring Activities
    $staticMentoringOptions = [
        'Student Achievements (IELTS / BEC / Foreign Language / Workshop / Conference / SIH / Publication etc.)',
        'NASSCOM Certification',
        'Course Exit Survey',
        'AICTE 360 Feedback',
        'Mentor Mentee Meeting',
        'NPTEL Certificate',
        'Soft Skills',
        'Skill Oriented Course'
    ];

    foreach ($staticMentoringOptions as $activity) {
        $mentoringOptionStmt = $conn->prepare("INSERT INTO mentoring_options (form_id, option_name) VALUES (?, ?) 
                                            ON DUPLICATE KEY UPDATE option_name = option_name");
        $mentoringOptionStmt->bind_param('is', $form_id, $activity);
        $mentoringOptionStmt->execute();
    }

    $mentoringActivities = $_POST['mentoringActivities'] ?? [];
    foreach ($mentoringActivities as $activity) {
        $mentoringOptionStmt = $conn->prepare("INSERT INTO mentoring_options (form_id, option_name) VALUES (?, ?) 
                                            ON DUPLICATE KEY UPDATE option_name = option_name");
        $mentoringOptionStmt->bind_param('is', $form_id, $activity);
        $mentoringOptionStmt->execute();
    }

    // 3. Insert Students and Map to Data
    if (isset($_FILES['studentListFile']) && $_FILES['studentListFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['studentListFile']['tmp_name'];

        require_once 'vendor/autoload.php';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        $firstRow = true;

        foreach ($worksheet->getRowIterator() as $row) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $rollNo = $worksheet->getCell('A' . $row->getRowIndex())->getValue();
            $studentName = $worksheet->getCell('B' . $row->getRowIndex())->getValue();
            $mentorId = $worksheet->getCell('C' . $row->getRowIndex())->getValue();

            $stmt = $conn->prepare("INSERT INTO students (roll_number, name, mentor_id, form_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('sssi', $rollNo, $studentName, $mentorId, $form_id);
            $stmt->execute();
            $studentId = $stmt->insert_id;

            foreach ($subjectIds as $index => $subjectId) {
                $employeeID = $employeeIDs[$index];

                $mappingStmt = $conn->prepare("INSERT INTO student_subject_mapping (student_id, subject_id, employee_id) VALUES (?, ?, ?)");
                $mappingStmt->bind_param('iis', $studentId, $subjectId, $employeeID);
                $mappingStmt->execute();

                $assignmentStmt = $conn->prepare("INSERT INTO student_assignments (student_id, subject_id, assignment_1_status, assignment_2_status) VALUES (?, ?, 0, 0)");
                $assignmentStmt->bind_param('ii', $studentId, $subjectId);
                $assignmentStmt->execute();
            }

            $mentoringOptionsStmt = $conn->prepare("SELECT option_name FROM mentoring_options WHERE form_id = ?");
            $mentoringOptionsStmt->bind_param('i', $form_id);
            $mentoringOptionsStmt->execute();
            $mentoringOptionsResult = $mentoringOptionsStmt->get_result();

            while ($row = $mentoringOptionsResult->fetch_assoc()) {
                $mentoringStmt = $conn->prepare("INSERT INTO student_mentoring (student_id, activity_type, completion_status) VALUES (?, ?, NULL)");
                $mentoringStmt->bind_param('is', $studentId, $row['option_name']);
                $mentoringStmt->execute();
            }
        }
    } else {
        throw new Exception("Error: Student data file is missing or invalid.");
    }

    $conn->commit();

    // SweetAlert success notification
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Data successfully mapped and inserted.'
                }).then(function() {
                    window.location.href = 'NoDueFormList.php';
                });
            });
          </script>";
} catch (Exception $e) {
    $conn->rollback();

    // SweetAlert error notification
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred: " . addslashes($e->getMessage()) . "'
                }).then(function() {
                    window.history.back();
                });
            });
          </script>";
    exit;
}

$conn->close();
?>

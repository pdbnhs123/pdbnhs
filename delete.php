<?php
session_start();

// Database connection
require_once 'db.php'; // This should define $conn as the mysqli connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Security token mismatch";
        header("Location: students_info.php");
        exit();
    }

    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    
    if (!$student_id) {
        $_SESSION['error_message'] = "Invalid student ID";
        header("Location: students_info.php");
        exit();
    }

    // Escape and validate the ID
    $student_id = mysqli_real_escape_string($conn, $student_id);

    // Check if student exists
    $checkQuery = "SELECT * FROM student_info WHERE Student_ID = $student_id";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) === 0) {
        $_SESSION['error_message'] = "Student not found";
        header("Location: students_info.php");
        exit();
    }

    // Delete student record
    $deleteQuery = "DELETE FROM student_info WHERE Student_ID = $student_id";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult && mysqli_affected_rows($conn) > 0) {
        $_SESSION['success_message'] = "Student record deleted successfully";
    } else {
        $_SESSION['error_message'] = "Failed to delete student record";
    }

    header("Location: students_info.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request method";
    header("Location: students_info.php");
    exit();
}

<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "Security token mismatch";
        header("Location: students_info.php");
        exit();
    }

    if (!isset($_POST['student_ids']) || !is_array($_POST['student_ids'])) {
        $_SESSION['error_message'] = "No students selected";
        header("Location: students_info.php");
        exit();
    }

    $ids = array_map('intval', $_POST['student_ids']);
    $id_list = implode(",", $ids);

    $query = "DELETE FROM student_info WHERE student_id IN ($id_list)";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success_message'] = "Selected students deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete selected students.";
    }

    header("Location: students_info.php");
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request";
    header("Location: students_info.php");
    exit();
}

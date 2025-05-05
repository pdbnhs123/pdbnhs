<?php
include 'db.php';
$_SESSION['user_id'] = $user['id']; // for example
$_SESSION['username'] = $user['username']; // optional: store more info
if (!isset($_SESSION['admin_logged_in'])){
    header('Location: login.php');
    exit();
}

// You can add additional checks here if needed
// For example, verify the admin still exists in database
?>

<!-- Include this file at the top of all admin pages -->
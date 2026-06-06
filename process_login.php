<?php
session_start();
include "config/database.php";
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_POST['student_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($_POST['password'], $user['password'])) {
        $_SESSION['student_id'] = $user['student_id'];
        header("Location: home.html"); exit();
    }
}
header("Location: login.php"); exit();
?>

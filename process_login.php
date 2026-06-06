<?php

session_start();

include "config/database.php";

$email = $_POST['email'];

$password = $_POST['password'];

$sql =

"SELECT *
FROM students
WHERE email=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s",$email);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows > 0){

    $user = $result->fetch_assoc();

    if(password_verify(
        $password,
        $user['password']
    )){

        $_SESSION['student_id']
        =
        $user['student_id'];

        header("Location: dashboard.php");

        exit();

    }

}

header("Location: login.php");

exit();

?>
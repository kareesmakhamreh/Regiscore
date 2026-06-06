<?php

include
"includes/auth_check.php";

include
"config/database.php";

$id =
$_SESSION['student_id'];

$sql = "

SELECT

students.*,

majors.major_name

FROM students

JOIN majors

ON students.major_id
=
majors.major_id

WHERE
student_id
=
$id

";

$result =
mysqli_query(
$conn,
$sql
);

$student =
mysqli_fetch_assoc(
$result
);

?>

<!DOCTYPE html>

<html>

<head>

<title>

Dashboard

</title>

<link
rel="stylesheet"
href="assets/style.css"
>

</head>

<body>

<div class="dashboard-layout">

<?php
include
"includes/sidebar.php";
?>

<div class="dashboard-main">

<h1>

Welcome

<?= $student['first_name'] ?>

</h1>

<br>

<div class="card">

<h2>

Academic Summary

</h2>

<p>

Major:
<?= $student['major_name'] ?>

</p>

<p>

GPA:
<?= $student['gpa'] ?>

</p>

<p>

Credits Completed:
<?= $student['credits_completed'] ?>

</p>

<p>

Year:
<?= $student['year_level'] ?>

</p>

</div>

</div>

</div>

</body>

</html>
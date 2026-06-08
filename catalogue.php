<?php

require "includes/auth_check.php";

require "config/database.php";

$id = $_SESSION['student_id'];

$result = mysqli_query(
    $conn,
    "SELECT students.*, majors.major_name
     FROM students
     JOIN majors ON students.major_id = majors.major_id
     WHERE student_id = $id"
);

$student = mysqli_fetch_assoc($result);

$courses = $conn->query("SELECT * FROM courses ORDER BY FIELD(category,'University','College','Department','Department Elective'), code");

?>
<!-- COURSE CATALOGUE PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Course Catalogue</title>

  <link rel="stylesheet" href="style.css">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >

</head>

<body>

<div class="dashboard-layout">

  <!-- SIDEBAR -->

  <aside class="dashboard-sidebar">

    <div>

      <!-- PROFILE -->

      <div class="student-profile">

        <img
          src="pfp.png"
          class="student-pfp"
        >

        <div class="student-info">

          <h2><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></h2>

          <p><?= htmlspecialchars($student['major_name']) ?></p>

          <span><?= htmlspecialchars($student['year_level']) ?></span>

        </div>

      </div>

      <!-- MAIN -->

      <div class="sidebar-section">

        <h4>MAIN</h4>

        <a href="home.html">
          <i class="fa-solid fa-house"></i>
          Dashboard
        </a>

        <a href="myprogress.html">
          <i class="fa-solid fa-chart-line"></i>
          My Progress
        </a>

        <a href="advisor.html">
          <i class="fa-solid fa-robot"></i>
          Registration Advisor
        </a>

        <a
          href="catalogue.php"
          class="active-link"
        >
          <i class="fa-solid fa-book"></i>
          Course Catalogue
        </a>

      </div>

      <!-- SCHEDULE -->

      <div class="sidebar-section">

        <h4>SCHEDULE</h4>

        <a href="calendar.html">
          <i class="fa-solid fa-calendar"></i>
          Shared Calendar
        </a>

        <a href="formative.html">
          <i class="fa-solid fa-users"></i>
          Formative Sessions
        </a>

      </div>

      <!-- OTHER -->

      <div class="sidebar-section">

        <h4>OTHER</h4>

        <a href="notifications.html">
          <i class="fa-solid fa-bell"></i>
          Notifications
        </a>

        <a href="help.html">
          <i class="fa-solid fa-circle-question"></i>
          Help & FAQ
        </a>

      </div>

    </div>

    <!-- LOGO -->

    <div class="sidebar-logo">

      <h1>RegisCore</h1>

      <p>Spring 2026</p>

    </div>

  </aside>

  <!-- MAIN -->

  <main class="dashboard-main">

    <!-- TOP BOX -->

    <div class="welcome-box">

      <div>

        <h1>Course Catalogue</h1>

        <p>
          Bachelor of Computer Science • Fall 2026
        </p>

      </div>

      <div class="notification-area">

        <div class="main-alert">
          <?= htmlspecialchars($courses->num_rows . " Courses") ?>
        </div>

      </div>

    </div>

    <!-- STUDENT DETAILS -->

    <div class="catalogue-student-box">

      <div class="catalogue-student-left">

        <img
          src="pfp.png"
          class="catalogue-pfp"
        >

        <div>

          <h2><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></h2>

          <p><?= htmlspecialchars($student['major_name'] . " Major") ?></p>

          <span>
            Student ID: <?= htmlspecialchars($student['student_id']) ?> • <?= htmlspecialchars($student['year_level']) ?>
          </span>

        </div>

      </div>

      <div class="catalogue-student-right">

        <div class="catalogue-mini-stat">

          <h3><?= htmlspecialchars($student['credits_completed']) ?></h3>

          <p>Credits Completed</p>

        </div>

        <div class="catalogue-mini-stat">

          <h3><?= htmlspecialchars(135 - $student['credits_completed']) ?></h3>

          <p>Credits Remaining</p>

        </div>

      </div>

    </div>

    <!-- COURSE LIST -->

    <div class="catalogue-wrapper">

      <?php while ($course = $courses->fetch_assoc()): ?>

      <!-- COURSE CARD -->

      <div class="course-catalogue-card">

        <div class="course-card-top">

          <div>

            <h2><?= htmlspecialchars($course['name']) ?></h2>

            <p><?= htmlspecialchars($course['code'] . " • " . $course['credit_hours'] . " Credit Hours") ?></p>

          </div>

          <div class="course-tag">
            <?= $course['category'] === "Department Elective" ? "Elective" : "Required" ?>
          </div>

        </div>

        <div class="catalogue-grid">

          <div class="catalogue-info-box">

            <h4>Course Code</h4>

            <p><?= htmlspecialchars($course['code']) ?></p>

          </div>

          <div class="catalogue-info-box">

            <h4>Credit Hours</h4>

            <p><?= htmlspecialchars($course['credit_hours']) ?></p>

          </div>

          <div class="catalogue-info-box">

            <h4>Category</h4>

            <p><?= htmlspecialchars($course['category']) ?></p>

          </div>

          <div class="catalogue-info-box">

            <h4>Prerequisites</h4>

            <p><?= htmlspecialchars($course['prerequisites']) ?></p>

          </div>

        </div>

      </div>

      <?php endwhile; ?>

    </div>

  </main>

</div>

</body>
</html>

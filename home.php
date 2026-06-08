<?php

require "includes/auth_check.php";

require "config/database.php";

$id = $_SESSION['student_id'];

// Logged-in student (same join as dashboard.php)
$result = mysqli_query(
    $conn,
    "SELECT students.*, majors.major_name
     FROM students
     JOIN majors ON students.major_id = majors.major_id
     WHERE student_id = $id"
);
$student = mysqli_fetch_assoc($result);

// GPA scale (same as myprogress.php)
function gradePoints($grade) {
    $scale = [
        "A" => 4.0, "A-" => 3.7, "B+" => 3.3, "B" => 3.0, "B-" => 2.7,
        "C+" => 2.3, "C" => 2.0, "C-" => 1.7, "D+" => 1.3, "D" => 1.0,
        "D-" => 0.7, "F" => 0.0,
    ];
    return $scale[$grade] ?? 0.0;
}

// Completed courses with code + category (same LEFT JOIN as myprogress.php)
$stmt = $conn->prepare(
    "SELECT cc.course_name, cc.grade, cc.credits, c.code, c.category
     FROM completed_courses cc
     LEFT JOIN courses c ON cc.course_name = c.name
     WHERE cc.student_id = ?"
);
$stmt->bind_param("s", $id);
$stmt->execute();
$res = $stmt->get_result();
$completed = [];
while ($row = $res->fetch_assoc()) { $completed[] = $row; }

// Totals + GPA
$totalCompleted = 0;
$qualityPoints = 0;
foreach ($completed as $c) {
    $totalCompleted += $c['credits'];
    $qualityPoints += gradePoints($c['grade']) * $c['credits'];
}
$gpa = $totalCompleted > 0 ? $qualityPoints / $totalCompleted : 0;
$pct = round($totalCompleted / 135 * 100);

// Completed credits per category
$uni = 0;
$col = 0;
$dept = 0;
foreach ($completed as $c) {
    if ($c['category'] === 'University') {
        $uni += $c['credits'];
    } elseif ($c['category'] === 'College') {
        $col += $c['credits'];
    } elseif ($c['category'] === 'Department' || $c['category'] === 'Department Elective') {
        $dept += $c['credits'];
    }
}

$completedCount = count($completed);

$totalCourses = (int) $conn->query("SELECT COUNT(*) AS c FROM courses")->fetch_assoc()['c'];
$remainingCount = $totalCourses - $completedCount;

// 5 most recently completed
$rstmt = $conn->prepare("SELECT course_name, grade FROM completed_courses WHERE student_id = ? ORDER BY id DESC LIMIT 5");
$rstmt->bind_param("s", $id);
$rstmt->execute();
$rres = $rstmt->get_result();
$recent = [];
while ($row = $rres->fetch_assoc()) { $recent[] = $row; }

?>
<!-- HOME PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Dashboard</title>


  <link rel="stylesheet" href="style.css">

  <!-- ICONS -->

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

        <a
          href="home.php"
          class="active-link"
        >
          <i class="fa-solid fa-house"></i>
          Dashboard
        </a>

        <a href="myprogress.php">
          <i class="fa-solid fa-chart-line"></i>
          My Progress
        </a>

        <a href="advisor.html">
          <i class="fa-solid fa-robot"></i>
          Registration Advisor
        </a>

        <a href="catalogue.php">
          <i class="fa-solid fa-book"></i>
          Course Catalogue
        </a>

      </div>

      <!-- SCHEDULE -->

      <div class="sidebar-section">

        <h4>SCHEDULE</h4>

        <a href="calendar.php">
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

    <!-- WELCOME -->

    <div class="welcome-box">

      <div>

        <h1>Good Morning, <?= htmlspecialchars($student['first_name']) ?></h1>

        <p><?= htmlspecialchars(date('l, F j Y')) ?></p>

      </div>

      <div class="notification-area">

        <div class="main-alert">
          Registration closes in 3 days
        </div>

        <a
          href="notifications.html"
          class="notification-icon"
        >
          <i class="fa-solid fa-bell"></i>
        </a>

      </div>

    </div>

    <!-- STATS -->

    <div class="stats-grid">

      <!-- CARD -->

      <div class="stat-card">

        <div>

          <h3>Credits Completed</h3>

          <p><?= htmlspecialchars($totalCompleted) ?></p>

          <div class="stat-progress-info">
            out of 135 required
          </div>

        </div>

        <div class="stat-progress-bg">

          <div
            class="stat-progress-fill"
            style="width:<?= $pct ?>%"
          ></div>

        </div>

      </div>

      <!-- CARD -->

      <div class="stat-card">

        <div>

          <h3>Current GPA</h3>

          <p><?= htmlspecialchars(number_format($gpa, 2)) ?></p>

          <div class="stat-progress-info">
            out of 4.0 cumulative
          </div>

        </div>

        <div class="stat-progress-bg">

          <div
            class="stat-progress-fill"
            style="width:<?= round($gpa / 4 * 100) ?>%"
          ></div>

        </div>

      </div>

      <!-- CARD -->

      <div class="stat-card">

        <div>

          <h3>Courses Completed</h3>

          <p><?= htmlspecialchars($completedCount) ?></p>

          <div class="stat-progress-info">
            <?= htmlspecialchars($totalCompleted . " credit hours earned") ?>
          </div>

        </div>

        <div class="stat-progress-bg">

          <div
            class="stat-progress-fill"
            style="width:<?= $pct ?>%"
          ></div>

        </div>

      </div>

      <!-- CARD -->

      <div class="stat-card">

        <div>

          <h3>Courses Remaining</h3>

          <p><?= htmlspecialchars($remainingCount) ?></p>

          <div class="stat-progress-info">
            to finish your plan
          </div>

        </div>

        <div class="stat-progress-bg">

          <div
            class="stat-progress-fill"
            style="width:<?= $totalCourses > 0 ? round($remainingCount / $totalCourses * 100) : 0 ?>%"
          ></div>

        </div>

      </div>

    </div>

    <!-- LOWER GRID -->

    <div class="bottom-grid">

      <!-- RECENTLY COMPLETED -->

      <div class="large-card">

        <h2>Recently Completed</h2>

        <?php foreach ($recent as $r): ?>
        <!-- SESSION -->

        <div class="session-row">

          <div class="session-left">

            <div class="session-time-box">
              <?= htmlspecialchars($r['grade']) ?>
            </div>

            <div>

              <h3><?= htmlspecialchars($r['course_name']) ?></h3>

              <span>Completed</span>

            </div>

          </div>

        </div>
        <?php endforeach; ?>

      </div>

      <!-- PROGRESS -->

      <div class="large-card">

        <h2>Degree Progress</h2>

        <div class="progress-layout">

          <!-- LEFT -->

          <div class="progress-circle">

            <div class="circle-inner">
              <?= htmlspecialchars($pct . "%") ?>
            </div>

          </div>

          <!-- RIGHT -->

          <div class="progress-bars">

            <!-- ITEM -->

            <div class="progress-item">

              <div class="progress-title">
                University
              </div>

              <div class="progress-bar-bg">

                <div
                  class="progress-fill"
                  style="width:<?= round($uni / 27 * 100) ?>%"
                ></div>

              </div>

            </div>

            <!-- ITEM -->

            <div class="progress-item">

              <div class="progress-title">
                College
              </div>

              <div class="progress-bar-bg">

                <div
                  class="progress-fill"
                  style="width:<?= round($col / 21 * 100) ?>%"
                ></div>

              </div>

            </div>

            <!-- ITEM -->

            <div class="progress-item">

              <div class="progress-title">
                Department
              </div>

              <div class="progress-bar-bg">

                <div
                  class="progress-fill"
                  style="width:<?= round($dept / 87 * 100) ?>%"
                ></div>

              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </main>

</div>

</body>
</html>

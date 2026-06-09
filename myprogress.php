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

// Completed courses with their code + category
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

// GPA scale
function gradePoints($grade) {
    $scale = [
        "A" => 4.0, "A-" => 3.7, "B+" => 3.3, "B" => 3.0, "B-" => 2.7,
        "C+" => 2.3, "C" => 2.0, "C-" => 1.7, "D+" => 1.3, "D" => 1.0,
        "D-" => 0.7, "F" => 0.0,
    ];
    return $scale[$grade] ?? 0.0;
}

// Totals + GPA
$totalCompleted = 0;
$qualityPoints = 0;
foreach ($completed as $c) {
    $totalCompleted += $c['credits'];
    $qualityPoints += gradePoints($c['grade']) * $c['credits'];
}
$gpa = $totalCompleted > 0 ? $qualityPoints / $totalCompleted : 0;
$pct = round($totalCompleted / 135 * 100);

// Codes + names already completed
$completedCodes = [];
$completedNames = [];
foreach ($completed as $c) {
    if ($c['code'] !== null) { $completedCodes[] = $c['code']; }
    $completedNames[] = $c['course_name'];
}

// Completed credits per category
$uniDone = 0;
$colDone = 0;
$deptDone = 0;
foreach ($completed as $c) {
    if ($c['category'] === 'University') {
        $uniDone += $c['credits'];
    } elseif ($c['category'] === 'College') {
        $colDone += $c['credits'];
    } elseif ($c['category'] === 'Department' || $c['category'] === 'Department Elective') {
        $deptDone += $c['credits'];
    }
}
$uniReq = 27;
$colReq = 21;
$deptReq = 87;

// Sync the dashboard with the computed figures
$upd = $conn->prepare("UPDATE students SET gpa=?, credits_completed=? WHERE student_id=?");
$upd->bind_param("dis", $gpa, $totalCompleted, $id);
$upd->execute();

// Eligibility for a remaining course based on its prerequisites text
function courseStatus($prereq, $totalCompleted, $completedCodes) {
    if ($prereq === 'none') {
        return "Eligible";
    } elseif (stripos($prereq, 'approval') !== false) {
        return "Needs approval";
    } elseif (preg_match('/(\d+)\+?\s*completed CH/', $prereq, $m)) {
        return $totalCompleted >= (int) $m[1] ? "Eligible" : "Not eligible";
    } else {
        preg_match_all('/\d{10}|UNIELEC\d/', $prereq, $codes);
        foreach ($codes[0] as $req) {
            if (!in_array($req, $completedCodes)) {
                return "Not eligible";
            }
        }
        return "Eligible";
    }
}

// All courses -> the ones still to take
$allRes = $conn->query("SELECT * FROM courses");
$remaining = [];
while ($course = $allRes->fetch_assoc()) {
    if (!in_array($course['name'], $completedNames)) {
        $course['status'] = courseStatus($course['prerequisites'], $totalCompleted, $completedCodes);
        $remaining[] = $course;
    }
}

?>
<!-- MY PROGRESS PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>My Progress</title>

  <link rel="stylesheet" href="style.css">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >

  <!-- SCREEN: hide the print-only header on the normal page -->
  <style>
    .print-only-header { display: none; }
  </style>

  <!-- PRINT / PDF ONLY: never affects the on-screen page -->
  <style media="print">
    @page { margin: 1.5cm; }
    .dashboard-sidebar, .red-btn, .export-btn, .progress-mini-sidebar { display: none !important; }
    .progress-main { padding: 0 !important; }
    .dashboard-layout { display: block !important; }
    .progress-content-card { display: block !important; page-break-inside: avoid; margin-bottom: 20px; }
    .print-only-header { display: block !important; text-align: center; margin-bottom: 20px; }
    .progress-table tr { page-break-inside: avoid; }
  </style>

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

        <a href="home.php">
          <i class="fa-solid fa-house"></i>
          Dashboard
        </a>

        <a
          href="myprogress.php"
          class="active-link"
        >
          <i class="fa-solid fa-chart-line"></i>
          My Progress
        </a>

        <a href="advisor.php">
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

        <a href="schedule.php">
          <i class="fa-solid fa-calendar-plus"></i>
          Schedule Builder
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

  <main class="progress-main">

    <!-- PRINT-ONLY HEADER -->

    <div class="print-only-header">

      <h1><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></h1>

      <h2>Academic Progress Report</h2>

      <p>Student ID: <?= htmlspecialchars($student['student_id']) ?></p>

      <p>GPA: <?= htmlspecialchars(number_format($gpa, 2)) ?></p>

      <p>Credits: <?= htmlspecialchars($totalCompleted . "/135") ?></p>

      <p><?= htmlspecialchars(date('F j, Y')) ?></p>

    </div>

    <!-- TOP HEADER -->

    <div class="progress-header">

      <h1>My Progress</h1>

      <button class="red-btn" onclick="window.print()">
        Export Progress
      </button>

    </div>

    <!-- OVERALL PROGRESS -->

    <div class="overall-progress-box">

      <div class="overall-progress-bar">

        <div class="progress-segment done-segment" style="width: <?= $pct ?>%"></div>

        <div class="progress-segment enrolled-segment" style="width: 0"></div>

        <div class="progress-segment failed-segment" style="width: 0"></div>

        <div class="progress-segment unregistered-segment" style="width: <?= 100 - $pct ?>%"></div>

      </div>

      <div class="overall-progress-info">

        <div class="legend-group">

          <div class="legend-item">
            <span class="legend-color done-color"></span>
            Done
          </div>

          <div class="legend-item">
            <span class="legend-color enrolled-color"></span>
            Enrolled
          </div>

          <div class="legend-item">
            <span class="legend-color failed-color"></span>
            Failed
          </div>

          <div class="legend-item">
            <span class="legend-color unregistered-color"></span>
            Unregistered
          </div>

        </div>

        <h2><?= htmlspecialchars($pct . "%") ?></h2>

      </div>

    </div>

    <!-- CONTENT SECTION -->

    <div class="progress-content-layout">

      <!-- MINI SIDEBAR -->

      <div class="progress-mini-sidebar">

        <button class="progress-tab active-progress-tab">
          Hours Passed
        </button>

        <button class="progress-tab">
          Done Courses
        </button>

        <button class="progress-tab">
          Courses To Take
        </button>

      </div>

      <!-- CONTENT AREA -->

      <div class="progress-content-area">

        <!-- HOURS PASSED -->

        <div class="progress-content-card">

          <div class="hours-layout">

            <!-- PIE -->

            <div class="hours-pie-chart">

              <div class="hours-pie-inner">
                <?= htmlspecialchars($pct . "%") ?>
              </div>

            </div>

            <!-- SUMMARY -->

            <div class="hours-summary">

              <div class="hours-summary-item">

                <h3>Completed Hours</h3>

                <p><?= htmlspecialchars($totalCompleted . " / 135") ?></p>

              </div>

              <div class="hours-summary-item">

                <h3>Remaining Hours</h3>

                <p><?= htmlspecialchars(135 - $totalCompleted) ?></p>

              </div>

              <div class="hours-summary-item">

                <h3>Current GPA</h3>

                <p><?= htmlspecialchars(number_format($gpa, 2)) ?></p>

              </div>

            </div>

          </div>

          <!-- TABLE -->

          <table class="progress-table">

            <thead>

              <tr>

                <th>Category</th>
                <th>Completed</th>
                <th>Required</th>
                <th>Status</th>

              </tr>

            </thead>

            <tbody>

              <tr>

                <td>University</td>
                <td><?= htmlspecialchars($uniDone) ?></td>
                <td><?= htmlspecialchars($uniReq) ?></td>
                <td><?= $uniDone >= $uniReq ? "Completed" : "In Progress" ?></td>

              </tr>

              <tr>

                <td>College</td>
                <td><?= htmlspecialchars($colDone) ?></td>
                <td><?= htmlspecialchars($colReq) ?></td>
                <td><?= $colDone >= $colReq ? "Completed" : "In Progress" ?></td>

              </tr>

              <tr>

                <td>Department</td>
                <td><?= htmlspecialchars($deptDone) ?></td>
                <td><?= htmlspecialchars($deptReq) ?></td>
                <td><?= $deptDone >= $deptReq ? "Completed" : "In Progress" ?></td>

              </tr>

            </tbody>

          </table>

        </div>

        <!-- DONE COURSES -->

        <div class="progress-content-card">

          <div class="table-top-bar">

            <h2>Completed Courses</h2>

            <input
              type="text"
              placeholder="Search Courses"
              class="table-search"
            >

          </div>

          <table class="progress-table">

            <thead>

              <tr>

                <th>Course</th>
                <th>Grade</th>
                <th>Credits</th>

              </tr>

            </thead>

            <tbody>

              <?php foreach ($completed as $c): ?>
              <tr>

                <td><?= htmlspecialchars($c['course_name']) ?></td>
                <td><?= htmlspecialchars($c['grade']) ?></td>
                <td><?= htmlspecialchars($c['credits']) ?></td>

              </tr>
              <?php endforeach; ?>

            </tbody>

          </table>

          <button class="red-btn export-btn" onclick="window.print()">
            Export PDF
          </button>

        </div>

        <!-- COURSES TO TAKE -->

        <div class="progress-content-card">

          <div class="table-top-bar">

            <h2>Courses To Take</h2>

            <input
              type="text"
              placeholder="Search Courses"
              class="table-search"
            >

          </div>

          <table class="progress-table">

            <thead>

              <tr>

                <th>Course</th>
                <th>Credit Hours</th>
                <th>Prerequisites</th>
                <th>Status</th>

              </tr>

            </thead>

            <tbody>

              <?php foreach ($remaining as $course): ?>
              <tr>

                <td><?= htmlspecialchars($course['name']) ?></td>
                <td><?= htmlspecialchars($course['credit_hours']) ?></td>
                <td><?= htmlspecialchars($course['prerequisites']) ?></td>
                <td><?= htmlspecialchars($course['status']) ?></td>

              </tr>
              <?php endforeach; ?>

            </tbody>

          </table>

          <button class="red-btn export-btn" onclick="window.print()">
            Export PDF
          </button>

        </div>

      </div>

    </div>

  </main>

</div>

</body>
</html>

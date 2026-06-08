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

// Current week (Sunday start)
$today = new DateTime();
$dow = (int) $today->format('w');                // 0 = Sunday
$weekStart = (clone $today)->modify("-$dow days");
$days = [];
for ($i = 0; $i < 7; $i++) {
    $days[$i] = (clone $weekStart)->modify("+$i days");
}

// This week's events grouped by date
$start = $weekStart->format('Y-m-d');
$end = (clone $weekStart)->modify('+6 days')->format('Y-m-d');

$stmt = $conn->prepare("SELECT * FROM events WHERE event_date BETWEEN ? AND ? ORDER BY start_time");
$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$res = $stmt->get_result();
$eventsByDate = [];
while ($row = $res->fetch_assoc()) {
    $eventsByDate[$row['event_date']][] = $row;
}

?>
<!-- SHARED CALENDAR PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Shared Calendar</title>

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

        <a href="home.php">
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

        <a
          href="calendar.php"
          class="active-link"
        >
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

  <main class="calendar-main">

    <!-- TOP BAR -->

    <div class="calendar-header">

      <div>

        <h1>Shared Calendar</h1>

        <p><?= htmlspecialchars($weekStart->format('F j') . " - " . $days[6]->format('F j, Y')) ?></p>

      </div>

      <div class="calendar-top-actions">

        <button class="today-btn">
          Today
        </button>

        <div class="calendar-view-switch">

          <button class="active-calendar-view">
            Day
          </button>

          <button>
            Week
          </button>

          <button>
            Month
          </button>

          <button>
            Year
          </button>

        </div>

      </div>

    </div>

    <!-- FILTERS -->

    <div class="calendar-filters">

      <span class="filter-title">
        Show:
      </span>

      <div class="filter-tag lecture-tag">
        Lectures
      </div>

      <div class="filter-tag holiday-tag">
        Holidays
      </div>

      <div class="filter-tag deadline-tag">
        Deadlines
      </div>

      <div class="filter-tag office-tag">
        Office Hours
      </div>

      <div class="filter-tag oral-tag">
        Oral Sessions
      </div>

    </div>

    <!-- CALENDAR -->

    <div class="calendar-container">

      <!-- DAYS -->

      <div class="calendar-days">

        <div>Sunday</div>
        <div>Monday</div>
        <div>Tuesday</div>
        <div>Wednesday</div>
        <div>Thursday</div>
        <div>Friday</div>
        <div>Saturday</div>

      </div>

      <!-- GRID -->

      <div class="calendar-grid">

        <?php for ($i = 0; $i < 7; $i++): ?>

        <!-- DAY -->

        <div class="calendar-cell">

          <div class="calendar-date">
            <?= htmlspecialchars($days[$i]->format('j')) ?>
          </div>

          <?php
          $key = $days[$i]->format('Y-m-d');
          if (isset($eventsByDate[$key])):
              foreach ($eventsByDate[$key] as $ev):
          ?>
          <div class="calendar-event <?= htmlspecialchars($ev['type']) ?>-event" data-type="<?= htmlspecialchars($ev['type']) ?>">
            <?= htmlspecialchars($ev['title']) ?>
          </div>
          <?php
              endforeach;
          endif;
          ?>

        </div>

        <?php endfor; ?>

      </div>

    </div>

  </main>

</div>

<script>
  const filterMap = {
    "lecture-tag": "lecture",
    "holiday-tag": "holiday",
    "deadline-tag": "deadline",
    "office-tag": "office",
    "oral-tag": "oral"
  };
  for (const cls in filterMap) {
    const tag = document.querySelector("." + cls);
    if (!tag) continue;
    tag.addEventListener("click", () => {
      const type = filterMap[cls];
      tag.classList.toggle("filter-off");
      const hidden = tag.classList.contains("filter-off");
      document.querySelectorAll('.calendar-event[data-type="' + type + '"]').forEach(ev => {
        ev.style.display = hidden ? "none" : "";
      });
    });
  }
</script>

</body>
</html>

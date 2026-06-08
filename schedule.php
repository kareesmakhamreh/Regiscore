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

// Offered sections joined to their course
$sql = "SELECT s.id, s.course_code, s.section_number, s.instructor, s.days,
               s.start_time, s.end_time, s.room, s.seats,
               c.name AS course_name, c.credit_hours
        FROM sections s JOIN courses c ON s.course_code = c.code
        ORDER BY c.name, s.section_number";
$secRes = $conn->query($sql);

$coursesArr = [];
while ($row = $secRes->fetch_assoc()) {
    $code = $row['course_code'];
    if (!isset($coursesArr[$code])) {
        $coursesArr[$code] = [
            'code' => $row['course_code'],
            'name' => $row['course_name'],
            'credits' => (int) $row['credit_hours'],
            'sections' => [],
        ];
    }
    $coursesArr[$code]['sections'][] = [
        'id' => (int) $row['id'],
        'section_number' => $row['section_number'],
        'instructor' => $row['instructor'],
        'days' => $row['days'],
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'room' => $row['room'],
        'seats' => (int) $row['seats'],
    ];
}

?>
<!-- SCHEDULE BUILDER PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Schedule Builder</title>

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

        <a href="calendar.php">
          <i class="fa-solid fa-calendar"></i>
          Shared Calendar
        </a>

        <a href="formative.html">
          <i class="fa-solid fa-users"></i>
          Formative Sessions
        </a>

        <a
          href="schedule.php"
          class="active-link"
        >
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

  <main class="dashboard-main">

    <!-- HEADER -->

    <div class="welcome-box">

      <div>

        <h1>Schedule Builder</h1>

        <p>Pick a section for each course and build a conflict-free week</p>

      </div>

      <div class="notification-area">

        <div class="main-alert"><span id="creditTotal">0</span> Credits</div>

        <div class="main-alert"><span id="conflictTotal">0</span> Conflicts</div>

      </div>

    </div>

    <!-- TWO-COLUMN LAYOUT -->

    <div style="display:grid; grid-template-columns: 340px 1fr; gap:22px;">

      <!-- LEFT: AVAILABLE COURSES -->

      <div class="large-card">

        <h2>Available Courses</h2>

        <div id="availableList"></div>

      </div>

      <!-- RIGHT: WEEKLY SCHEDULE -->

      <div class="large-card">

        <h2>Weekly Schedule</h2>

        <div id="timetable" style="display:flex; gap:8px; margin-top:12px;"></div>

        <h2 style="margin-top:22px;">Selected Courses</h2>

        <div id="selectedList"></div>

      </div>

    </div>

  </main>

</div>

<script>
  const COURSES = <?php echo json_encode(array_values($coursesArr)); ?>;
</script>

<script>
  const selected = {}; // course_code -> section_id

  function toMinutes(t) {
    const [h, m] = t.split(":").map(Number);
    return h * 60 + m;
  }

  function parseDays(d) {
    return d.split(",").map(s => s.trim());
  }

  function getCourse(code) {
    return COURSES.find(c => c.code === code);
  }

  function getSection(code, id) {
    const c = getCourse(code);
    return c ? c.sections.find(s => s.id === id) || null : null;
  }

  function selectedList() {
    const list = [];
    for (const code in selected) {
      const c = getCourse(code);
      const s = getSection(code, selected[code]);
      if (c && s) list.push({ code, course: c, section: s });
    }
    return list;
  }

  function computeConflicts() {
    const list = selectedList();
    const conflicting = new Set();
    for (let i = 0; i < list.length; i++) {
      for (let j = i + 1; j < list.length; j++) {
        const a = list[i].section, b = list[j].section;
        const shareDay = parseDays(a.days).some(d => parseDays(b.days).includes(d));
        if (!shareDay) continue;
        const aStart = toMinutes(a.start_time), aEnd = toMinutes(a.end_time);
        const bStart = toMinutes(b.start_time), bEnd = toMinutes(b.end_time);
        if (aStart < bEnd && bStart < aEnd) {
          conflicting.add(a.id);
          conflicting.add(b.id);
        }
      }
    }
    return conflicting;
  }

  function addCourse(code) {
    const sel = document.getElementById("sec-" + code);
    selected[code] = sel ? Number(sel.value) : getCourse(code).sections[0].id;
    render();
  }

  function removeCourse(code) {
    delete selected[code];
    render();
  }

  function changeSection(code, sectionId) {
    selected[code] = Number(sectionId);
    render();
  }

  function esc(s) {
    return String(s)
      .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
  }

  function optionText(s) {
    return "Sec " + s.section_number + " · " + s.days + " " + s.start_time + "-" + s.end_time +
           " · " + s.instructor + " · " + s.room;
  }

  const DAYS = [["Sun", "Sunday"], ["Mon", "Monday"], ["Tue", "Tuesday"], ["Wed", "Wednesday"], ["Thu", "Thursday"]];

  function render() {
    const conflicting = computeConflicts();
    const list = selectedList();

    // 1. AVAILABLE COURSES
    const avail = document.getElementById("availableList");
    avail.innerHTML = "";
    COURSES.forEach(c => {
      const isAdded = c.code in selected;
      const options = c.sections.map(s => `<option value="${s.id}">${esc(optionText(s))}</option>`).join("");
      const row = document.createElement("div");
      row.style.cssText = "border-bottom:1px solid #ececec; padding:12px 0;";
      row.innerHTML =
        `<div style="font-weight:600;">${esc(c.name)}</div>` +
        `<div style="font-size:12px; color:#666; margin-bottom:6px;">${esc(c.code)} · ${c.credits} Credit Hours</div>` +
        `<select id="sec-${esc(c.code)}" style="width:100%; margin-bottom:8px; padding:6px;" ${isAdded ? "disabled" : ""}>${options}</select>` +
        (isAdded
          ? `<button class="red-btn" disabled>Added</button>`
          : `<button class="red-btn" onclick="addCourse('${c.code}')">Add</button>`);
      avail.appendChild(row);
      if (isAdded) {
        const sel = row.querySelector("select");
        if (sel) sel.value = selected[c.code];
      }
    });

    // 2. TIMETABLE
    const tt = document.getElementById("timetable");
    tt.innerHTML = "";
    DAYS.forEach(([abbr, full]) => {
      const col = document.createElement("div");
      col.style.cssText = "flex:1;";
      const header = document.createElement("div");
      header.style.cssText = "font-weight:600; text-align:center; padding-bottom:8px; border-bottom:1px solid #ececec; margin-bottom:8px;";
      header.textContent = full;
      col.appendChild(header);

      const blocks = list
        .filter(item => parseDays(item.section.days).includes(abbr))
        .sort((a, b) => toMinutes(a.section.start_time) - toMinutes(b.section.start_time));

      blocks.forEach(item => {
        const s = item.section;
        const conf = conflicting.has(s.id);
        const block = document.createElement("div");
        block.style.cssText = conf
          ? "background:#fdeaea; border:1px solid #e5e7eb; border-left:4px solid #c41515; border-radius:10px; padding:8px; margin-bottom:8px; font-size:12px; color:#991111;"
          : "background:#fff; border:1px solid #e5e7eb; border-left:4px solid #c41515; border-radius:10px; padding:8px; margin-bottom:8px; font-size:12px;";
        block.innerHTML =
          `<div style="font-weight:700;">${esc(item.course.name)}</div>` +
          `<div>${esc(s.start_time)}-${esc(s.end_time)}</div>` +
          `<div>${esc(s.room)}</div>` +
          `<div>Sec ${esc(s.section_number)}</div>`;
        col.appendChild(block);
      });
      tt.appendChild(col);
    });

    // 3. SELECTED COURSES LIST
    const selList = document.getElementById("selectedList");
    selList.innerHTML = "";
    if (list.length === 0) {
      selList.innerHTML = `<p style="color:#666; font-size:13px; margin-top:10px;">No courses selected yet.</p>`;
    }
    list.forEach(item => {
      const c = item.course;
      const options = c.sections.map(s =>
        `<option value="${s.id}" ${s.id === item.section.id ? "selected" : ""}>${esc(optionText(s))}</option>`).join("");
      const row = document.createElement("div");
      row.style.cssText = "display:flex; align-items:center; gap:10px; border-bottom:1px solid #ececec; padding:10px 0;";
      row.innerHTML =
        `<div style="flex:1;"><div style="font-weight:600;">${esc(c.name)}</div>` +
        `<div style="font-size:12px; color:#666;">${c.credits} Credit Hours</div></div>` +
        `<select onchange="changeSection('${c.code}', this.value)" style="flex:1; padding:6px;">${options}</select>` +
        `<button class="red-btn" onclick="removeCourse('${c.code}')">Remove</button>`;
      selList.appendChild(row);
    });

    // 4. TOTALS
    let credits = 0;
    list.forEach(item => credits += item.course.credits);
    document.getElementById("creditTotal").textContent = credits;
    document.getElementById("conflictTotal").textContent = conflicting.size;
  }

  render();
</script>

</body>
</html>

<?php
require "includes/auth_check.php";
require "config/database.php";
$id = $_SESSION['student_id'];
$stmt = $conn->prepare("SELECT students.*, majors.major_name FROM students JOIN majors ON students.major_id = majors.major_id WHERE student_id = ?");
$stmt->bind_param("s", $id); $stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>
<!-- REGISTRATION ADVISOR PAGE -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Registration Advisor</title>

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

        <img src="pfp.png" class="student-pfp">

        <div class="student-info">

          <h2><?= htmlspecialchars($student['first_name'] . " " . $student['last_name']) ?></h2>

          <p><?= htmlspecialchars($student['major_name']) ?></p>

          <span><?= htmlspecialchars($student['year_level']) ?></span>

        </div>

      </div>

      <!-- MENU -->

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

        <a href="advisor.php" class="active-link">
          <i class="fa-solid fa-robot"></i>
          Registration Advisor
        </a>

        <a href="catalogue.php">
          <i class="fa-solid fa-book"></i>
          Course Catalogue
        </a>

      </div>

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

  <main class="advisor-main">

    <!-- TOP HEADER -->

    <div class="advisor-header">

      <div>

        <h1>Registration Advisor</h1>

        <p>Automated Course Planning for Fall 2026</p>

      </div>

      <div class="advisor-top-stats">

        <div class="advisor-mini-box">
          <span>Credits Done</span>
          <h3>78</h3>
        </div>

        <div class="advisor-mini-box">
          <span>Credits Remaining</span>
          <h3>54</h3>
        </div>

        <div class="advisor-mini-box">
          <span>Registration Closes</span>
          <h3>3 Days</h3>
        </div>

      </div>

    </div>

    <!-- CONTENT -->

    <div class="advisor-content">

      <!-- LEFT CHAT -->

      <div class="chat-container">

        <div class="chat-header">

          <div class="chat-profile">

            <div class="chat-avatar">
              <i class="fa-solid fa-robot"></i>
            </div>

            <div>

              <h3>Advisor AI</h3>

              <span>Online</span>

            </div>

          </div>

        </div>

        <div class="chat-body">

        </div>

        <div class="chat-input-area">

          <input
            type="text"
            placeholder="Ask Advisor..."
          >

          <button>
            <i class="fa-solid fa-paper-plane"></i>
          </button>

        </div>

      </div>

      <!-- RIGHT SIDE -->

      <div class="generated-section">

        <!-- PLAN HEADER -->

        <div class="generated-top-box">

          <div>

            <h2>Generated Plan - Fall 2026</h2>

            <p>5 courses . 14 credit hours . 0 conflicts</p>

          </div>

          <div class="generated-buttons">

            <button class="outline-btn">
              Export PDF
            </button>

            <button class="red-btn">
              Edit Manually
            </button>

          </div>

        </div>

        <!-- SUMMARY BOXES -->

        <div class="summary-grid">

          <div class="summary-box">

            <span>Credit Hours</span>

            <h2>14</h2>

            <p>Recommended 13-16</p>

          </div>

          <div class="summary-box">

            <span>Prerequisites Met</span>

            <h2>5/5</h2>

            <p>All courses eligible</p>

          </div>

          <div class="summary-box">

            <span>Schedule Conflict</span>

            <h2>0</h2>

            <p>No empty time</p>

          </div>

        </div>

        <!-- GENERATED COURSES -->

        <div class="schedule-box">

          <div class="schedule-top">

            <div>

              <h2>Fall 2026 - Selected Courses</h2>

              <p>Mon - Thu only (All morning slots)</p>

            </div>

          </div>

          <!-- COURSE -->

          <div class="course-row">

            <div class="course-main">

              <h3>Computer Networks</h3>

              <span>Sun/Wed 9:00-10:30 AM · S-208</span>

            </div>

            <div class="course-right">

              <div class="course-tag">
                Required
              </div>

              <div class="credits-box">
                3 CH
              </div>

            </div>

          </div>

          <!-- COURSE -->

          <div class="course-row">

            <div class="course-main">

              <h3>AI Ethics</h3>

              <span>Mon/Thu 11:00-12:30 PM · IT-104</span>

            </div>

            <div class="course-right">

              <div class="course-tag">
                Elective
              </div>

              <div class="credits-box">
                3 CH
              </div>

            </div>

          </div>

          <!-- COURSE -->

          <div class="course-row">

            <div class="course-main">

              <h3>Software Engineering II</h3>

              <span>Sun/Wed 1:00-2:30 PM · S-110</span>

            </div>

            <div class="course-right">

              <div class="course-tag">
                Required
              </div>

              <div class="credits-box">
                3 CH
              </div>

            </div>

          </div>

          <!-- COURSE -->

          <div class="course-row">

            <div class="course-main">

              <h3>Machine Learning</h3>

              <span>Tue/Thu 9:00-10:30 AM · LAB-2</span>

            </div>

            <div class="course-right">

              <div class="course-tag">
                Thesis Track
              </div>

              <div class="credits-box">
                3 CH
              </div>

            </div>

          </div>

          <!-- COURSE -->

          <div class="course-row">

            <div class="course-main">

              <h3>Advanced Databases</h3>

              <span>Mon/Wed 2:00-3:30 PM · IT-302</span>

            </div>

            <div class="course-right">

              <div class="course-tag">
                Elective
              </div>

              <div class="credits-box">
                2 CH
              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  </main>

</div>

<script>
  const input = document.querySelector(".chat-input-area input");
  const sendBtn = document.querySelector(".chat-input-area button");
  const chatBody = document.querySelector(".chat-body");
  function escapeHtml(s) {
    return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }
  function inlineMd(s) {
    return s
      .replace(/`([^`]+)`/g, "<code>$1</code>")
      .replace(/\*\*([^*]+)\*\*/g, "<strong>$1</strong>")
      .replace(/(^|[^*])\*([^*\n]+)\*/g, "$1<em>$2</em>");
  }
  function renderMarkdown(md) {
    const lines = escapeHtml(md).split("\n");
    let html = "", inList = false, listTag = "ul";
    const closeList = () => { if (inList) { html += "</" + listTag + ">"; inList = false; } };
    for (const line of lines) {
      const ol = line.match(/^\s*\d+\.\s+(.*)$/);
      const ul = line.match(/^\s*[-*]\s+(.*)$/);
      const h  = line.match(/^\s*#{1,6}\s+(.*)$/);
      if (ol) {
        if (!inList || listTag !== "ol") { closeList(); html += '<ol style="margin:6px 0; padding-left:22px;">'; inList = true; listTag = "ol"; }
        html += "<li>" + inlineMd(ol[1]) + "</li>";
      } else if (ul) {
        if (!inList || listTag !== "ul") { closeList(); html += '<ul style="margin:6px 0; padding-left:22px;">'; inList = true; listTag = "ul"; }
        html += "<li>" + inlineMd(ul[1]) + "</li>";
      } else if (h) {
        closeList(); html += "<strong>" + inlineMd(h[1]) + "</strong><br>";
      } else if (line.trim() === "") {
        closeList(); html += "<br>";
      } else {
        closeList(); html += inlineMd(line) + "<br>";
      }
    }
    closeList();
    return html;
  }
  function addMessage(text, cls) {
    const div = document.createElement("div");
    div.className = cls;
    if (cls === "bot-message") {
      div.innerHTML = renderMarkdown(text);
    } else {
      div.textContent = text;
    }
    chatBody.appendChild(div); chatBody.scrollTop = chatBody.scrollHeight;
  }
  async function send() {
    const text = input.value.trim(); if (!text) return;
    addMessage(text, "user-message"); input.value = "";
    try {
      const res = await fetch("advisor_api.php", {
        method: "POST", headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ message: text })
      });
      const data = await res.json();
      addMessage(data.reply || "Error: no reply", "bot-message");
    } catch (e) { addMessage("Connection error.", "bot-message"); }
  }
  sendBtn.addEventListener("click", send);
  input.addEventListener("keydown", (e) => { if (e.key === "Enter") send(); });
</script>

</body>
</html>
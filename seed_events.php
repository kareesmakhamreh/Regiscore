<?php
require "config/database.php";

$today = new DateTime();
$dow = (int)$today->format('w');               // 0 = Sunday
$weekStart = (clone $today)->modify("-$dow days");

$conn->query("DROP TABLE IF EXISTS events");
$conn->query("CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120),
  type VARCHAR(20),
  event_date DATE,
  start_time VARCHAR(20),
  end_time VARCHAR(20),
  location VARCHAR(60)
)");

$events = [
  ['Operating Systems Lecture','lecture',0,'09:00','10:30','S-208'],
  ['Software Development Lifecycles','lecture',0,'11:00','12:30','IT-104'],
  ['Application Development Oral','oral',0,'14:00','14:30','IT-302'],
  ['Registration Deadline','deadline',1,'23:59','','Online'],
  ['Database Design Lecture','lecture',2,'09:00','10:30','LAB-2'],
  ['Networking Lecture','lecture',2,'13:00','14:30','S-110'],
  ['Advisor Office Hours','office',3,'10:00','11:00','C-402'],
  ['University Holiday','holiday',4,'','','—'],
];

$stmt = $conn->prepare("INSERT INTO events (title,type,event_date,start_time,end_time,location) VALUES (?,?,?,?,?,?)");
foreach ($events as $e) {
  $date = (clone $weekStart)->modify("+{$e[2]} days")->format('Y-m-d');
  $stmt->bind_param("ssssss", $e[0], $e[1], $date, $e[3], $e[4], $e[5]);
  $stmt->execute();
}

echo "Done. Seeded " . count($events) . " events for the week of " . $weekStart->format('M j, Y') . ". Delete this file.";
?>

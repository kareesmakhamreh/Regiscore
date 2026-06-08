<?php
require "config/database.php";

$conn->query("DROP TABLE IF EXISTS sections");
$conn->query("CREATE TABLE sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  course_code VARCHAR(20),
  section_number VARCHAR(10),
  instructor VARCHAR(60),
  days VARCHAR(20),
  start_time VARCHAR(10),
  end_time VARCHAR(10),
  room VARCHAR(30),
  seats INT
)");

$rows = [
  ['0040201320','01','Dr. Omar Hasan','Sun,Tue','09:30','11:00','IT-302',20],
  ['0040201320','02','Dr. Omar Hasan','Mon,Wed','13:00','14:30','IT-305',18],
  ['0040201430','01','Dr. Lina Hammad','Sun,Tue','11:00','12:30','LAB-2',22],
  ['0040201430','02','Dr. Lina Hammad','Mon,Wed','09:30','11:00','LAB-3',22],
  ['0040201440','01','Dr. Ahmad Khalil','Sun,Tue','09:30','11:00','S-110',15],
  ['0040201440','02','Dr. Ahmad Khalil','Thu','13:00','16:00','S-110',15],
  ['0040201450','01','Dr. Noor Ibrahim','Mon,Wed','11:00','12:30','C-201',25],
  ['0040201450','02','Dr. Noor Ibrahim','Sun,Tue','13:00','14:30','C-202',25],
  ['0040201441','01','Dr. Samir Adel','Mon,Wed','09:30','11:00','C-203',20],
  ['0010203380','01','Dr. Rana Saleh','Sun,Tue','08:00','09:30','S-208',30],
  ['0010203380','02','Dr. Rana Saleh','Mon,Wed','14:30','16:00','S-208',30],
  ['0040201362','01','Dr. Khaled Nasser','Thu','09:00','12:00','LAB-4',16],
  ['0040201461','01','Dr. Noor Ibrahim','Mon,Wed','11:00','12:30','IT-104',20],
  ['0040201461','02','Dr. Noor Ibrahim','Sun,Tue','14:30','16:00','IT-104',20],
  ['0040201442','01','Dr. Ahmad Khalil','Sun,Tue','11:00','12:30','S-112',18],
  ['0040201460','01','Dr. Lina Hammad','Mon,Wed','13:00','14:30','IT-301',20],
];

$stmt = $conn->prepare("INSERT INTO sections (course_code,section_number,instructor,days,start_time,end_time,room,seats) VALUES (?,?,?,?,?,?,?,?)");
foreach ($rows as $r) {
  $stmt->bind_param("sssssssi", $r[0],$r[1],$r[2],$r[3],$r[4],$r[5],$r[6],$r[7]);
  $stmt->execute();
}
echo "Done. Seeded " . count($rows) . " sections. Delete this file.";
?>

<?php
session_start();
require "config/database.php";
header("Content-Type: application/json");
if (!isset($_SESSION['student_id'])) { http_response_code(401); echo json_encode(["ok"=>false]); exit; }
$conn->query("CREATE TABLE IF NOT EXISTS schedule_selections (id INT AUTO_INCREMENT PRIMARY KEY, student_id VARCHAR(20), section_id INT)");
$input = json_decode(file_get_contents("php://input"), true);
$sections = $input["sections"] ?? [];
$sid = $_SESSION['student_id'];
$del = $conn->prepare("DELETE FROM schedule_selections WHERE student_id = ?");
$del->bind_param("s", $sid); $del->execute();
$ins = $conn->prepare("INSERT INTO schedule_selections (student_id, section_id) VALUES (?, ?)");
foreach ($sections as $secId) { $secId = (int)$secId; $ins->bind_param("si", $sid, $secId); $ins->execute(); }
echo json_encode(["ok"=>true, "count"=>count($sections)]);
?>

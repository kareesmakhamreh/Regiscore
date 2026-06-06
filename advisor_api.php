<?php
session_start();
require "config/database.php";
$input = json_decode(file_get_contents("php://input"), true);
$userMessage = $input["message"] ?? "";
$context = "";
if (isset($_SESSION['student_id'])) {
    $stmt = $conn->prepare("SELECT first_name, gpa, credits_completed FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $_SESSION['student_id']); $stmt->execute();
    $s = $stmt->get_result()->fetch_assoc();
    $stmt2 = $conn->prepare("SELECT course_name, grade FROM completed_courses WHERE student_id = ?");
    $stmt2->bind_param("s", $_SESSION['student_id']); $stmt2->execute();
    $rows = $stmt2->get_result();
    $done = [];
    while ($c = $rows->fetch_assoc()) { $done[] = $c['course_name'] . " (" . $c['grade'] . ")"; }
    if ($s) {
        $context = "Student: {$s['first_name']}, GPA {$s['gpa']}, {$s['credits_completed']} credits completed. "
                 . "Completed courses: " . (implode(", ", $done) ?: "none") . ".";
    }
}
$systemPrompt = "You are RegisCore's academic registration advisor for university students. "
    . "Help them plan course schedules, check prerequisites, and avoid time conflicts. "
    . "Keep replies short, clear, and friendly. " . $context;
$payload = json_encode([
    "systemInstruction" => ["parts" => [["text" => $systemPrompt]]],
    "contents" => [["parts" => [["text" => $userMessage]]]],
]);
$ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json", "x-goog-api-key: " . getenv("GEMINI_API_KEY")],
    CURLOPT_POSTFIELDS => $payload,
]);
$res = curl_exec($ch);
curl_close($ch);
$data = json_decode($res, true);
$reply = $data["candidates"][0]["content"]["parts"][0]["text"] ?? "Sorry, I couldn't generate a response.";
header("Content-Type: application/json");
echo json_encode(["reply" => $reply]);
?>

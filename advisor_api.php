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
$curriculum = <<<'CURRICULUM'
HTU COMPUTER SCIENCE — OFFICIAL BSc PROGRAM (School of Computing and Informatics)
Pearson BTEC-based. Each course is an HNC (Higher National Certificate), HND
(Higher National Diploma), or HTU unit.

DEGREE RULES
- Total to graduate: 135 credit hours = 27 University + 21 College + 87 Department
  (the 87 includes 9 CH of department electives).
- Normal load is 15-19 credit hours per semester.
- A course may be taken only when ALL its prerequisites have been passed.
- "Corequisite" means it may be taken in the SAME semester as the linked course.
- HNC Training (12 CH) requires >= 85 completed credit hours. HND Training (6 CH)
  is taken together with HNC Training (corequisite). These are the year-long
  industry apprenticeship.
- Capstone Project I (1 CH) requires >= 90 completed CH including core courses;
  Capstone Project II (2 CH) requires Capstone Project I.
- The student chooses 3 department electives (9 CH total) from the elective list.

UNIVERSITY REQUIREMENTS (27 CH) — Code | Course | CH | Prerequisite
0030301121 | English Pre-Intermediate Intensive + Lab | 4 | English Elementary (0030301120)
0030301122 | English Intermediate | 3 | English Pre-Intermediate (0030301121)
0030301123 | English Upper-Intermediate | 3 | English Intermediate (0030301122)
0030301124 | English Advanced | 3 | English Upper-Intermediate (0030301123)
0040302111 | Professional Skills | 1 | none
0040302211 | Professional Practice | 3 | Professional Skills (0040302111) or Soft Skills I; and English Intermediate (0030301122)
0040302231 | Entrepreneurship Bootcamp | 4 | English Upper-Intermediate (0030301123) and Professional Practice (0040302211)
0030301111 | Arabic Language & Communication Skills | 1 | Remedial Arabic (0030301110)
0030302129 | Military Science | 1 | none
0030302232 | Leadership Camp | 1 | Entrepreneurship Bootcamp (0040302231)
University Elective I / II / III | 1 each | none

COLLEGE REQUIREMENTS (21 CH)
0030303111 | Functional Math | 3 | Remedial Math (0030303110)
0040303121 | Maths for Computing | 3 | Functional Math (0030303111)
0040303130 | Fundamentals of Computing | 4 | none
0040201100 | Programming | 3 | Fundamentals of Computing (0040303130)
0040303221 | Discrete Maths | 3 | Maths for Computing (0040303121); corequisite Data Structures & Algorithms (0040201201)
0040201290 | Planning a Computing Project | 4 | Professional Practice (0040302211)
0030303121 | STEM Lab I | 1 | none

DEPARTMENT REQUIREMENTS (87 CH, including 9 CH electives)
0010203180 | Networking | 3 | Fundamentals of Computing (0040303130)
0040201260 | Website Design & Development | 3 | Programming (0040201100)
0040201201 | Data Structures & Algorithms | 3 | Programming (0040201100)
0000203280 | Security | 3 | Fundamentals of Computing (0040303130)
0040201200 | Advanced Programming | 3 | Programming (0040201100)
0040201220 | Software Development Lifecycles | 3 | Programming (0040201100)
0040201261 | Prototyping | 3 | Software Development Lifecycles (0040201220)
0040201360 | Application Development | 3 | Prototyping (0040201261)
0010204282 | Database Design & Development | 3 | Programming (0040201100)
0010204312 | Business Intelligence | 3 | Database Design & Development (0010204282)
0040201321 | Systems Analysis & Design | 3 | Software Development Lifecycles (0040201220)
0010203380 | Computer Organization and Design | 3 | Programming (0040201100); corequisite Discrete Maths (0040303221)
0040201341 | Operating Systems | 3 | Data Structures & Algorithms (0040201201)
0040201362 | Games Engine & Scripting | 3 | Programming (0040201100)
0040201320 | ERP Systems | 3 | Application Development (0040201360)
0040201430 | Database Programming | 3 | Database Design & Development (0010204282) and Data Structures & Algorithms (0040201201)
0040201440 | Systems Programming | 3 | Operating Systems (0040201341)
0000201391 | Computing Research Project | 6 | Planning a Computing Project (0040201290)
0040201491 | Capstone Project I | 1 | >= 90 completed CH including core courses
0040201492 | Capstone Project II | 2 | Capstone Project I (0040201491)
0040201390 | HNC Training | 12 | >= 85 completed CH
0040201490 | HND Training | 6 | corequisite HNC Training (0040201390)
(plus 3 Department Electives, 3 CH each, from the list below)

DEPARTMENT ELECTIVES (choose 3 = 9 CH)
0040201441 | Internet of Things | 3 | Networking (0010203180) and Operating Systems (0040201341)
0040201450 | Cloud Computing | 3 | Networking (0010203180) and Operating Systems (0040201341)
0040201462 | Virtual & Augmented Reality Development | 3 | Games Engine & Scripting (0040201362)
0040201460 | E-Commerce | 3 | Website Design & Development (0040201260)
0040201442 | Real Time Systems | 3 | Operating Systems (0040201341)
0040201461 | Mobile Application Development | 3 | Application Development (0040201360)
0000204414 | Data Analytics for IT Professionals | 3 | Business Intelligence (0010204312)
0000204456 | Artificial Intelligence for IT Professionals | 3 | Business Intelligence (0010204312)
0040201470 | Special Topics | 3 | Department approval
CURRICULUM;

$systemPrompt = "You are RegisCore's academic advisor for Computer Science students at "
    . "Al-Hussein Technical University (HTU), a Pearson BTEC-based program. Use ONLY the official "
    . "HTU CS program reference below. When the student asks what to take next: recommend only "
    . "courses whose prerequisites they have already passed, prioritise required courses over "
    . "electives, and keep a normal 15-19 credit-hour semester. If they are not yet eligible for a "
    . "course, name the exact missing prerequisite. Remember HNC Training needs >= 85 completed "
    . "hours, HND Training is taken with it, and Capstone Project I needs >= 90 hours including "
    . "core courses. Never invent courses or codes that are not in the reference. Be concise, "
    . "specific, and friendly.\n\n" . $curriculum . "\n\n"
    . "THIS STUDENT (from their records):\n" . $context;
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

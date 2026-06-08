<?php
require "config/database.php";

$sql = <<<'SQL'
DROP TABLE IF EXISTS courses;
CREATE TABLE courses (
  code          VARCHAR(20) PRIMARY KEY,
  name          VARCHAR(120),
  credit_hours  INT,
  category      VARCHAR(30),
  prerequisites VARCHAR(255)
);
INSERT INTO courses (code, name, credit_hours, category, prerequisites) VALUES
('0030301121', 'English Pre-Intermediate Intensive + Lab', 4, 'University', 'English Elementary (0030301120)'),
('0030301122', 'English Intermediate', 3, 'University', 'English Pre-Intermediate (0030301121)'),
('0030301123', 'English Upper-Intermediate', 3, 'University', 'English Intermediate (0030301122)'),
('0030301124', 'English Advanced', 3, 'University', 'English Upper-Intermediate (0030301123)'),
('0040302111', 'Professional Skills', 1, 'University', 'none'),
('0040302211', 'Professional Practice', 3, 'University', 'Professional Skills (0040302111) or Soft Skills I; and English Intermediate (0030301122)'),
('0040302231', 'Entrepreneurship Bootcamp', 4, 'University', 'English Upper-Intermediate (0030301123) and Professional Practice (0040302211)'),
('0030301111', 'Arabic Language & Communication Skills', 1, 'University', 'Remedial Arabic (0030301110)'),
('0030302129', 'Military Science', 1, 'University', 'none'),
('0030302232', 'Leadership Camp', 1, 'University', 'Entrepreneurship Bootcamp (0040302231)'),
('UNIELEC1', 'University Elective I', 1, 'University', 'none'),
('UNIELEC2', 'University Elective II', 1, 'University', 'none'),
('UNIELEC3', 'University Elective III', 1, 'University', 'none'),
('0030303111', 'Functional Math', 3, 'College', 'Remedial Math (0030303110)'),
('0040303121', 'Maths for Computing', 3, 'College', 'Functional Math (0030303111)'),
('0040303130', 'Fundamentals of Computing', 4, 'College', 'none'),
('0040201100', 'Programming', 3, 'College', 'Fundamentals of Computing (0040303130)'),
('0040303221', 'Discrete Maths', 3, 'College', 'Maths for Computing (0040303121); corequisite Data Structures & Algorithms (0040201201)'),
('0040201290', 'Planning a Computing Project', 4, 'College', 'Professional Practice (0040302211)'),
('0030303121', 'STEM Lab I', 1, 'College', 'none'),
('0010203180', 'Networking', 3, 'Department', 'Fundamentals of Computing (0040303130)'),
('0040201260', 'Website Design & Development', 3, 'Department', 'Programming (0040201100)'),
('0040201201', 'Data Structures & Algorithms', 3, 'Department', 'Programming (0040201100)'),
('0000203280', 'Security', 3, 'Department', 'Fundamentals of Computing (0040303130)'),
('0040201200', 'Advanced Programming', 3, 'Department', 'Programming (0040201100)'),
('0040201220', 'Software Development Lifecycles', 3, 'Department', 'Programming (0040201100)'),
('0040201261', 'Prototyping', 3, 'Department', 'Software Development Lifecycles (0040201220)'),
('0040201360', 'Application Development', 3, 'Department', 'Prototyping (0040201261)'),
('0010204282', 'Database Design & Development', 3, 'Department', 'Programming (0040201100)'),
('0010204312', 'Business Intelligence', 3, 'Department', 'Database Design & Development (0010204282)'),
('0040201321', 'Systems Analysis & Design', 3, 'Department', 'Software Development Lifecycles (0040201220)'),
('0010203380', 'Computer Organization and Design', 3, 'Department', 'Programming (0040201100); corequisite Discrete Maths (0040303221)'),
('0040201341', 'Operating Systems', 3, 'Department', 'Data Structures & Algorithms (0040201201)'),
('0040201362', 'Games Engine & Scripting', 3, 'Department', 'Programming (0040201100)'),
('0040201320', 'ERP Systems', 3, 'Department', 'Application Development (0040201360)'),
('0040201430', 'Database Programming', 3, 'Department', 'Database Design & Development (0010204282) and Data Structures & Algorithms (0040201201)'),
('0040201440', 'Systems Programming', 3, 'Department', 'Operating Systems (0040201341)'),
('0000201391', 'Computing Research Project', 6, 'Department', 'Planning a Computing Project (0040201290)'),
('0040201491', 'Capstone Project I', 1, 'Department', '>= 90 completed CH including core courses'),
('0040201492', 'Capstone Project II', 2, 'Department', 'Capstone Project I (0040201491)'),
('0040201390', 'HNC Training', 12, 'Department', '>= 85 completed CH'),
('0040201490', 'HND Training', 6, 'Department', 'corequisite HNC Training (0040201390)'),
('0040201441', 'Internet of Things', 3, 'Department Elective', 'Networking (0010203180) and Operating Systems (0040201341)'),
('0040201450', 'Cloud Computing', 3, 'Department Elective', 'Networking (0010203180) and Operating Systems (0040201341)'),
('0040201462', 'Virtual & Augmented Reality Development', 3, 'Department Elective', 'Games Engine & Scripting (0040201362)'),
('0040201460', 'E-Commerce', 3, 'Department Elective', 'Website Design & Development (0040201260)'),
('0040201442', 'Real Time Systems', 3, 'Department Elective', 'Operating Systems (0040201341)'),
('0040201461', 'Mobile Application Development', 3, 'Department Elective', 'Application Development (0040201360)'),
('0000204414', 'Data Analytics for IT Professionals', 3, 'Department Elective', 'Business Intelligence (0010204312)'),
('0000204456', 'Artificial Intelligence for IT Professionals', 3, 'Department Elective', 'Business Intelligence (0010204312)'),
('0040201470', 'Special Topics', 3, 'Department Elective', 'Department approval');

DELETE FROM completed_courses WHERE student_id = '202201245';

INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'English Pre-Intermediate Intensive + Lab';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'English Intermediate';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'English Upper-Intermediate';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B',  credit_hours FROM courses WHERE name = 'English Advanced';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Functional Math';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B',  credit_hours FROM courses WHERE name = 'Maths for Computing';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'Fundamentals of Computing';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'Programming';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Discrete Maths';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'Planning a Computing Project';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'STEM Lab I';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'Professional Skills';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'Professional Practice';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'Arabic Language & Communication Skills';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'Military Science';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'University Elective I';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A',  credit_hours FROM courses WHERE name = 'University Elective II';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Networking';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'Website Design & Development';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Data Structures & Algorithms';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B',  credit_hours FROM courses WHERE name = 'Security';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'Advanced Programming';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Software Development Lifecycles';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'A-', credit_hours FROM courses WHERE name = 'Database Design & Development';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Prototyping';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B',  credit_hours FROM courses WHERE name = 'Operating Systems';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Business Intelligence';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B',  credit_hours FROM courses WHERE name = 'Systems Analysis & Design';
INSERT INTO completed_courses (student_id, course_name, grade, credits) SELECT '202201245', name, 'B+', credit_hours FROM courses WHERE name = 'Application Development';
SQL;

if ($conn->multi_query($sql)) {
    do {
        if ($res = $conn->store_result()) { $res->free(); }
    } while ($conn->more_results() && $conn->next_result());
    $count = $conn->query("SELECT COUNT(*) AS c FROM courses")->fetch_assoc()["c"];
    echo "Done. Seeded " . $count . " courses.";
} else {
    echo "Error: " . $conn->error;
}
?>

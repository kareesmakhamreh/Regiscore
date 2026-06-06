CREATE TABLE majors (
  major_id INT AUTO_INCREMENT PRIMARY KEY,
  major_name VARCHAR(100) NOT NULL
);
CREATE TABLE students (
  student_id VARCHAR(20) PRIMARY KEY,
  first_name VARCHAR(50), last_name VARCHAR(50),
  email VARCHAR(100), password VARCHAR(255),
  major_id INT, year_level VARCHAR(20),
  gpa DECIMAL(3,2), credits_completed INT,
  FOREIGN KEY (major_id) REFERENCES majors(major_id)
);
CREATE TABLE completed_courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(20), course_name VARCHAR(100),
  grade VARCHAR(5), credits INT,
  FOREIGN KEY (student_id) REFERENCES students(student_id)
);
INSERT INTO majors (major_name) VALUES ('Computer Science');
INSERT INTO students (student_id, first_name, last_name, email, password, major_id, year_level, gpa, credits_completed)
VALUES ('202201245', 'Sarah', 'Ahmad', 'sarah@htu.edu.jo', '$2y$10$Bl0JTRpMjXBqT70/vlEhbe2BiG/LDEdj1kqG7gDU7UHxa.VeKsmwq', 1, '3rd Year', 3.42, 78);
INSERT INTO completed_courses (student_id, course_name, grade, credits) VALUES
('202201245', 'Programming I', 'A', 3),
('202201245', 'Database Systems', 'B+', 3),
('202201245', 'Software Engineering', 'A-', 3);

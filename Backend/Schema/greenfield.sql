CREATE DATABASE greenfield;
USE greenfield;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'student'
);

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100),
    course_code VARCHAR(20),
    slots INT
);

CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    UNIQUE(student_id, course_id),
    FOREIGN KEY(student_id) REFERENCES users(id),
    FOREIGN KEY(course_id) REFERENCES courses(id)
);

INSERT INTO courses(course_name, course_code, slots)
VALUES
('Web Development', 'WD101', 30),
('Database Systems', 'DB202', 25),
('Networking Fundamentals', 'NT303', 20),
('Graphic Design', 'GD401', 30),
('Cyber Security', 'CS402', 30),
('Machine Learning', 'ML403', 30),
('Software Development', 'SD404', 30);


CREATE TABLE admin(
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(20) DEFAULT 'student'
)
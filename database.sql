-- Fresh-install schema for the BUBT Student Portal.
-- Database name matches config.php: student_admin_portal
-- For an existing installation, do NOT re-import this file; use the specific migration
-- files only when their columns/tables are missing.

CREATE DATABASE IF NOT EXISTS student_admin_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_admin_portal;

CREATE TABLE IF NOT EXISTS student (
  id VARCHAR(30) NOT NULL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  dept VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user` (
  id VARCHAR(30) NOT NULL UNIQUE,
  phn_num VARCHAR(20) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  CONSTRAINT fk_user_student FOREIGN KEY (id) REFERENCES student(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  admin_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(30) NOT NULL DEFAULT 'Admin',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teachers (
  teacher_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  department VARCHAR(100) NOT NULL,
  designation VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) NOT NULL UNIQUE,
  address VARCHAR(255) DEFAULT NULL,
  password VARCHAR(255) DEFAULT NULL,
  account_status VARCHAR(20) NOT NULL DEFAULT 'approved',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_teacher_status (account_status),
  INDEX idx_teacher_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS class_routines (
  routine_id INT AUTO_INCREMENT PRIMARY KEY,
  department VARCHAR(100) NOT NULL,
  semester VARCHAR(50) NOT NULL,
  intake_no SMALLINT UNSIGNED NULL,
  section_no TINYINT UNSIGNED NULL,
  class_day VARCHAR(20) NOT NULL,
  course_code VARCHAR(30) NOT NULL,
  course_name VARCHAR(150) NOT NULL,
  teacher_name VARCHAR(100) NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  room_no VARCHAR(30) NOT NULL,
  created_by INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_routine_lookup (department, semester, class_day),
  INDEX idx_routine_section_slot (department, intake_no, section_no, class_day, start_time),
  INDEX idx_routine_room_slot (room_no, class_day, start_time),
  INDEX idx_routine_teacher_slot (teacher_name, class_day, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS routine_scans (
  scan_id INT AUTO_INCREMENT PRIMARY KEY,
  image_path VARCHAR(255) NOT NULL,
  extracted_text MEDIUMTEXT,
  uploaded_by INT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bubt_departments (
  department_id INT AUTO_INCREMENT PRIMARY KEY,
  department_name VARCHAR(150) NOT NULL UNIQUE,
  faculty_name VARCHAR(150) NULL,
  official_url VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bubt_teacher_directory (
  directory_id INT AUTO_INCREMENT PRIMARY KEY,
  teacher_name VARCHAR(150) NOT NULL,
  designation VARCHAR(120) NULL,
  department_name VARCHAR(150) NOT NULL,
  directory_role VARCHAR(80) NOT NULL DEFAULT 'Faculty',
  source_url VARCHAR(255) NULL,
  verified_on DATE NULL,
  UNIQUE KEY uq_teacher_dept_role (teacher_name, department_name, directory_role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS student_course_results (
  record_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id VARCHAR(30) NOT NULL,
  department VARCHAR(100) NOT NULL,
  course_code VARCHAR(30) NOT NULL,
  course_name VARCHAR(150) NOT NULL,
  semester VARCHAR(50) NOT NULL,
  academic_year VARCHAR(20) NOT NULL,
  semester_start DATE NULL,
  semester_end DATE NULL,
  ct1_marks DECIMAL(6,2) NULL,
  ct2_marks DECIMAL(6,2) NULL,
  mid_marks DECIMAL(6,2) NULL,
  final_marks DECIMAL(6,2) NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_course_term (student_id, course_code, semester, academic_year),
  INDEX idx_student_term (student_id, academic_year, semester),
  CONSTRAINT fk_scr_student FOREIGN KEY (student_id) REFERENCES student(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

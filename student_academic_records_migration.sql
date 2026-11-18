-- Run once in phpMyAdmin after selecting student_admin_portal.
-- One row represents one student's course in one semester/year.
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

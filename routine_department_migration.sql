-- Run this ONCE in phpMyAdmin after selecting student_admin_portal.
-- Adds separate Intake/Batch and Section/Group fields.
-- Actual values vary by BUBT program and semester; do not assume a fixed count.
ALTER TABLE class_routines
  ADD COLUMN intake_no SMALLINT UNSIGNED NULL AFTER semester,
  ADD COLUMN section_no TINYINT UNSIGNED NULL AFTER intake_no,
  ADD INDEX idx_routine_section_slot (department, intake_no, section_no, class_day, start_time),
  ADD INDEX idx_routine_room_slot (room_no, class_day, start_time),
  ADD INDEX idx_routine_teacher_slot (teacher_name, class_day, start_time);

-- Existing records remain intact. New/edit entries will save intake_no and section_no.

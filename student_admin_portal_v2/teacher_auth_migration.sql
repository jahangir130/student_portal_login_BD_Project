USE student_admin_portal;
ALTER TABLE teachers
  ADD COLUMN password VARCHAR(255) NULL AFTER address;

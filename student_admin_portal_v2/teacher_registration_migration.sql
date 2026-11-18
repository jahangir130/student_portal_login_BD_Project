USE student_admin_portal;
ALTER TABLE teachers
  ADD COLUMN account_status VARCHAR(20) NOT NULL DEFAULT 'approved';

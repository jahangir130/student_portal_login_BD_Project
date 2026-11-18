STUDENT ADMIN PORTAL V2 — PHP + MySQL

NEW FEATURES
- Multiple admin accounts: create more accounts from Admin Panel > Add Admin.
- Admin role labels: Admin, Routine Manager, Super Admin. These are labels only in this
  version; all signed-in admin accounts currently share the same access.
- Class Routine: add, search, edit and delete weekly timetable entries.
- Smart Routine Scanner: upload JPG/PNG/WEBP timetable image; Tesseract.js OCR extracts
  text in the browser. Review the OCR draft before adding structured class entries.
- Student routine lookup: view_routine.php lets students choose department and intake.

DATABASE
Import database.sql. It retains the original student, user, admins and teachers tables
and adds class_routines and routine_scans.

RUN WITH XAMPP
1. Extract student_admin_portal_v2 into C:\xampp\htdocs\
2. Start Apache and MySQL.
3. Open http://localhost/phpmyadmin and import this package's database.sql.
   If the earlier student_portal database exists, CREATE TABLE IF NOT EXISTS adds the
   new tables without deleting existing records.
4. Check MySQL credentials in config.php.
5. First time only, open http://localhost/student_admin_portal_v2/setup_admin.php
6. Create the first Super Admin, then DELETE setup_admin.php.
7. Login: http://localhost/student_admin_portal_v2/admin_login.php
8. Add further admins from Admin Panel > Add Admin.
9. Class Routine is available in the admin sidebar.
10. Student routine lookup: http://localhost/student_admin_portal_v2/view_routine.php

OCR NOTES
- Tesseract.js loads from a CDN, so internet access is needed for the OCR library.
- Use a clear, straight, high-resolution image with printed text.
- OCR extracts text but may not preserve timetable rows and columns correctly.
- Verify all extracted information, then add each class through Add Class.
- Uploaded images are stored in uploads/routines.

SECURITY
Educational project. Add granular role-based authorization, HTTPS, secure cookie settings,
rate limiting, audit logs and backups before production use.


STUDENT AUTH PAGES (V2 UI UPDATE)
- Student login: student_login.php
- Student registration: student_register.php
- Login page links to registration; registration links back to login.
- The uploaded photo's badge area is cropped and used as the portal logo in assets/portal-logo.jpg.
- Student registration inserts records into the existing student and user tables.
- Student dashboard: student_dashboard.php
- Student logout: student_logout.php

ADMIN LOGIN REMAINS SEPARATE
- Admin login: admin_login.php
- New public student registration does not create administrator accounts.

CSE ROUTINE STRUCTURE UPDATE
- CSE Intake selector supports 1–55; Section selector supports 1–10 (550 section groups).
- Admin must run routine_department_migration.sql ONCE in phpMyAdmin after selecting
  the existing student_admin_portal database. Do not re-import database.sql over an
  existing database and do not run this migration a second time.
- Each class entry now stores intake_no and section_no separately. The public student
  routine page filters by Department + Intake + Section.
- Before saving, the app checks room, teacher and exact intake/section conflicts for
  the selected day and period. It also blocks more than 3 consecutive student periods.
- Room inventory is intentionally not hard-coded because the actual room count was
  not supplied. Admin enters the real room number; the system prevents simultaneous
  double-booking of that room.
- The schedule is still admin-entered; it is not a fully automatic optimizer that
  allocates every course/teacher/room without course-load and availability data.


UNIVERSITY-WIDE ROUTINE UPDATE
- Routine entry supports all BUBT departments (choose a suggested department or type another).
- Intake/Batch range: 1-200; Section/Group range: 1-30. These are software capacity limits, not claims about actual BUBT intake counts.
- Room and teacher conflicts are checked across all departments for the same day and period.
- Student-group conflicts are checked within the same department, intake and section.
- Daily fixed periods: 08:15-09:45, 09:45-11:15, 11:15-12:45, 13:15-14:45, 14:45-16:15, 16:15-17:45.


DEMO STUDENT DATA:
See DEMO_STUDENT_DATA_GUIDE.txt and import demo_student_data_migration.sql into student_admin_portal.

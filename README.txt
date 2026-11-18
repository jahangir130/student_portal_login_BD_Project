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

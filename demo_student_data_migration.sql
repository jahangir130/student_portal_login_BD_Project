-- DEMO DATA ONLY. Fictional test accounts, not real BUBT students.
-- Target database: student_admin_portal (matches config.php).
-- Run after the existing student and user tables have been created.
-- Login password for every demo account: Demo@12345
-- Phone values are dummy placeholders, not real contact numbers.
USE student_admin_portal;

INSERT IGNORE INTO student (id, name, dept) VALUES
('DEMO-001', 'Demo Student 01', 'Computer Science and Engineering'),
('DEMO-002', 'Demo Student 02', 'Computer Science and Engineering'),
('DEMO-003', 'Demo Student 03', 'Electrical & Electronics Engineering'),
('DEMO-004', 'Demo Student 04', 'Electrical & Electronics Engineering'),
('DEMO-005', 'Demo Student 05', 'Civil Engineering'),
('DEMO-006', 'Demo Student 06', 'Civil Engineering'),
('DEMO-007', 'Demo Student 07', 'Textile Engineering'),
('DEMO-008', 'Demo Student 08', 'Textile Engineering'),
('DEMO-009', 'Demo Student 09', 'Data Science & Engineering'),
('DEMO-010', 'Demo Student 10', 'Data Science & Engineering'),
('DEMO-011', 'Demo Student 11', 'Mathematics & Statistics'),
('DEMO-012', 'Demo Student 12', 'Mathematics & Statistics'),
('DEMO-013', 'Demo Student 13', 'English'),
('DEMO-014', 'Demo Student 14', 'English'),
('DEMO-015', 'Demo Student 15', 'Economics'),
('DEMO-016', 'Demo Student 16', 'Economics'),
('DEMO-017', 'Demo Student 17', 'Accounting'),
('DEMO-018', 'Demo Student 18', 'Accounting'),
('DEMO-019', 'Demo Student 19', 'Finance'),
('DEMO-020', 'Demo Student 20', 'Finance'),
('DEMO-021', 'Demo Student 21', 'Marketing'),
('DEMO-022', 'Demo Student 22', 'Marketing'),
('DEMO-023', 'Demo Student 23', 'Management'),
('DEMO-024', 'Demo Student 24', 'Management'),
('DEMO-025', 'Demo Student 25', 'Law and Justice'),
('DEMO-026', 'Demo Student 26', 'Law and Justice');

INSERT IGNORE INTO `user` (id, phn_num, password) VALUES
('DEMO-001', '00000000001', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-002', '00000000002', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-003', '00000000003', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-004', '00000000004', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-005', '00000000005', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-006', '00000000006', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-007', '00000000007', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-008', '00000000008', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-009', '00000000009', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-010', '00000000010', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-011', '00000000011', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-012', '00000000012', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-013', '00000000013', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-014', '00000000014', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-015', '00000000015', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-016', '00000000016', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-017', '00000000017', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-018', '00000000018', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-019', '00000000019', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-020', '00000000020', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-021', '00000000021', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-022', '00000000022', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-023', '00000000023', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-024', '00000000024', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-025', '00000000025', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O'),
('DEMO-026', '00000000026', '$2y$12$PZbZQOb5A5F0Y6O5o.uvze352CxVgez2FPH0IxfULCYjchiftWk8O');

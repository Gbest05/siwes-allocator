-- Seed Data for SIWES Allocation Management System
-- Password for all seed users: password123 (bcrypt: $2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2)

-- 1. Departments
INSERT INTO departments (id, name, code, description, is_active) VALUES
(1, 'Computer Science', 'COM', 'Department of Computer Science & Software Development', 1),
(2, 'Electrical & Electronic Engineering', 'EEE', 'Department of Electrical & Electronics Engineering', 1),
(3, 'Information Technology', 'IFT', 'Department of Information & Communications Technology', 1),
(4, 'Statistics & Data Analytics', 'STA', 'Department of Applied Mathematics and Data Science', 1),
(5, 'Computer Engineering', 'CPE', 'Department of Computer Systems and Hardware Engineering', 1)
ON CONFLICT (id) DO UPDATE SET is_active = EXCLUDED.is_active;

-- 2. Academic Programmes & Levels
INSERT INTO academic_programmes (id, code, name, is_active) VALUES
(1, 'ND', 'National Diploma (ND)', 1),
(2, 'HND', 'Higher National Diploma (HND)', 1),
(3, 'BSc', 'Bachelor of Science (B.Sc)', 1),
(4, 'BEng', 'Bachelor of Engineering (B.Eng)', 1)
ON CONFLICT (id) DO UPDATE SET is_active = EXCLUDED.is_active;

INSERT INTO academic_levels (id, code, name, is_active) VALUES
(1, 'ND1', 'ND 1', 1),
(2, 'ND2', 'ND 2', 1),
(3, 'HND1', 'HND 1', 1),
(4, 'HND2', 'HND 2', 1),
(5, '200L', '200 Level', 1),
(6, '300L', '300 Level', 1),
(7, '400L', '400 Level', 1)
ON CONFLICT (id) DO UPDATE SET is_active = EXCLUDED.is_active;

-- 3. Users (Admin, Coordinator, 3 Supervisors, 10 Students)
-- Password: password123
INSERT INTO users (id, full_name, email, password_hash, role) VALUES
(1, 'Prof. Emmanuel Okafor', 'admin@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'admin'),
(2, 'Dr. Mrs. Amina Yusuf', 'coordinator@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'coordinator'),
(3, 'Chidubem Chukwuma', 'student@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(4, 'Fatima Abubakar', 'fatima@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(5, 'Oluwaseun Adebayo', 'seun@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(6, 'Blessing Danjuma', 'blessing@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(7, 'Engr. Tunde Bakare', 'supervisor1@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'supervisor'),
(8, 'Dr. Mrs. Nkechi Egwu', 'supervisor2@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'supervisor'),
(9, 'Dr. Farouk Umar', 'supervisor3@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'supervisor'),
(10, 'Emmanuel Bassey', 'emmanuel@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(11, 'Zainab Bello', 'zainab@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(12, 'Kelechi Okonkwo', 'kelechi@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(13, 'Amina Mohammed', 'amina.m@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(14, 'David Adeleke', 'david@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student'),
(15, 'Maryam Sani', 'maryam@siwesp.edu.ng', '$2y$10$.faAMhHU8HSTlnEmvSd5weQMSgvTdbgf0dmwA5B.qG7VL7BWe3Mi2', 'student')
ON CONFLICT (id) DO UPDATE SET password_hash = EXCLUDED.password_hash, full_name = EXCLUDED.full_name, role = EXCLUDED.role;

-- 4. Supervisors Profiles
INSERT INTO supervisors (id, user_id, department_id, staff_id, phone, designation, max_capacity) VALUES
(1, 7, 1, 'SUP/COM/001', '08031234567', 'Senior Lecturer & SIWES Supervisor', 20),
(2, 8, 2, 'SUP/EEE/002', '08029876543', 'Principal Supervisor', 20),
(3, 9, 3, 'SUP/IFT/003', '08055512345', 'SIWES Academic Supervisor', 20)
ON CONFLICT (id) DO UPDATE SET department_id = EXCLUDED.department_id, staff_id = EXCLUDED.staff_id, phone = EXCLUDED.phone, designation = EXCLUDED.designation;

-- 5. Companies
INSERT INTO companies (id, name, reg_number, address, state, city, industry, contact_person, phone, email, total_capacity, available_slots, status) VALUES
(1, 'Technovate Solutions Ltd', 'RC-1049281', '45 Victoria Island Expressway', 'Lagos', 'Lagos Island', 'Software Development & IT', 'Tunde Bakare', '08031234567', 'info@technovate.ng', 15, 12, 'active'),
(2, 'MainOne Cable Telecommunications', 'RC-8492019', '12 Marina Promenade', 'Lagos', 'Lagos Island', 'Telecommunications & Cloud', 'Nkechi Egwu', '08029876543', 'careers@mainone.net', 20, 15, 'active'),
(3, 'National Space Research Agency (NASRDA)', 'FG-002931', 'Airport Road, Lugbe', 'Abuja', 'Abuja Central', 'Research & Hardware Systems', 'Dr. Farouk Umar', '08055512345', 'siwes@nasrda.gov.ng', 10, 6, 'active'),
(4, 'Interswitch Group Tech Hub', 'RC-9930219', '1642 Oko Awo Street, VI', 'Lagos', 'Victoria Island', 'Fintech & Cyber Security', 'Ayo Balogun', '08091112233', 'siwes@interswitchgroup.com', 25, 18, 'active'),
(5, 'Shell Petroleum Dev Co (SPDC)', 'RC-001293', 'Industrial Area, Rumuobiokani', 'Rivers', 'Port Harcourt', 'Energy & Embedded Control', 'Engr. Victor Ibim', '08033334444', 'siwes@shell.ng', 12, 8, 'active'),
(6, 'Ibadan Electricity Distribution Co (IBEDC)', 'RC-402910', 'Capitall Building, Ring Road', 'Oyo', 'Ibadan', 'Power & Electrical Grid', 'Engr. Modupe Alabi', '08077778888', 'trainees@ibedc.com', 15, 14, 'active')
ON CONFLICT (id) DO NOTHING;

-- 6. Students (10 Demo Students with Supervisor Assignments)
INSERT INTO students (id, user_id, matric_number, department_id, programme, level, phone, address, supervisor_id) VALUES
(1, 3, 'F/ND/22/3210001', 1, 'ND', 'ND2', '08123456789', 'Block C, Campus Hostel 2, Yaba', 7),
(2, 4, 'F/HND/22/3210002', 1, 'HND', 'HND2', '08139876543', '14 Herbert Macaulay Way, Yaba, Lagos', 8),
(3, 5, 'F/ND/22/3210003', 2, 'ND', 'ND2', '08098761234', '7 University Road, Akoka, Lagos', 7),
(4, 6, 'F/HND/22/3210004', 3, 'HND', 'HND1', '08145556677', '22 Garki Phase 2, Abuja', 8),
(5, 10, 'F/ND/22/3210007', 1, 'ND', 'ND2', '08021112233', 'Campus Hall of Residence', 7),
(6, 11, 'F/HND/22/3210008', 4, 'HND', 'HND2', '08034445566', 'Campus Hall of Residence', 8),
(7, 12, 'F/ND/22/3210009', 5, 'ND', 'ND1', '08057778899', 'Campus Hall of Residence', 9),
(8, 13, 'F/HND/22/3210010', 1, 'HND', 'HND2', '08089990011', 'Campus Hall of Residence', 7),
(9, 14, 'F/ND/22/3210011', 2, 'ND', 'ND2', '08091114455', 'Campus Hall of Residence', 8),
(10, 15, 'F/HND/22/3210012', 3, 'HND', 'HND1', '08132225566', 'Campus Hall of Residence', 9)
ON CONFLICT (id) DO UPDATE SET supervisor_id = EXCLUDED.supervisor_id, department_id = EXCLUDED.department_id, matric_number = EXCLUDED.matric_number;

-- 7. SIWES Applications
INSERT INTO siwes_applications (id, student_id, preferred_industry, preferred_location, status, notes) VALUES
(1, 1, 'Software Development & IT', 'Lagos', 'Allocated', 'Student expresses strong passion for full-stack web development.'),
(2, 2, 'Fintech & Cyber Security', 'Lagos', 'Allocated', 'Student completed ND with distinction and requests high-impact fintech placement.'),
(3, 3, 'Power & Electrical Grid', 'Oyo', 'Submitted', 'Interested in power systems distribution and automation controls.'),
(4, 4, 'Telecommunications & Cloud', 'Abuja', 'Under Review', 'Seeking networking and cloud infrastructure placement in FCT.'),
(5, 5, 'Software Development & IT', 'Lagos', 'Approved', 'Ready for supervisor and placement allocation.'),
(6, 6, 'Data Analytics & AI', 'Lagos', 'Approved', 'Ready for supervisor and placement allocation.'),
(7, 7, 'Hardware & Embedded Systems', 'Rivers', 'Approved', 'Ready for supervisor and placement allocation.'),
(8, 8, 'Fintech & Cyber Security', 'Lagos', 'Approved', 'Ready for supervisor and placement allocation.'),
(9, 9, 'Renewable Energy & Power', 'Lagos', 'Approved', 'Ready for supervisor and placement allocation.'),
(10, 10, 'Cloud Systems & Networking', 'Abuja', 'Approved', 'Ready for supervisor and placement allocation.')
ON CONFLICT (id) DO UPDATE SET status = EXCLUDED.status;

-- 8. Allocations
INSERT INTO allocations (id, application_id, student_id, company_id, compatibility_score, match_reasons, status, allocated_by, start_date, end_date) VALUES
(1, 2, 2, 4, 95, '{"dept_match":30, "industry_match":30, "location_match":20, "slot_match":15}', 'Allocated', 2, '2026-09-01', '2027-02-28'),
(2, 1, 1, 4, 90, '{"dept_match":30, "industry_match":20, "location_match":20, "slot_match":20}', 'Allocated', 7, '2026-10-17', '2027-04-17')
ON CONFLICT (id) DO NOTHING;

-- 9. Documents
INSERT INTO documents (id, student_id, doc_type, file_path, file_name, file_size, status) VALUES
(1, 1, 'SIWES Application Letter', 'uploads/siwes_letter_3210001.pdf', 'SIWES_Request_Letter_Chidubem.pdf', 245000, 'Approved'),
(2, 1, 'Student Identification Card', 'uploads/id_card_3210001.png', 'Student_ID_Chidubem.png', 512000, 'Approved'),
(3, 2, 'SIWES Application Letter', 'uploads/siwes_letter_3210002.pdf', 'SIWES_Letter_Fatima.pdf', 198000, 'Approved'),
(4, 3, 'SIWES Application Letter', 'uploads/siwes_letter_3210003.pdf', 'SIWES_Letter_Oluwaseun.pdf', 310000, 'Pending')
ON CONFLICT (id) DO NOTHING;

-- 10. Notifications
INSERT INTO notifications (id, user_id, title, message, type, is_read) VALUES
(1, 3, 'SIWES Application Approved', 'Your SIWES application has been reviewed and approved by the department coordinator.', 'success', 0),
(2, 4, 'Company Allocation Completed!', 'Congratulations! You have been successfully allocated to Interswitch Group Tech Hub (Lagos) for your SIWES attachment.', 'success', 1),
(3, 1, 'New Student Registrations', '4 new student applications submitted today require coordinator review.', 'info', 0)
ON CONFLICT (id) DO NOTHING;

-- 11. System Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('institution_name', 'School of Technology & Applied Sciences'),
('academic_session', '2025/2026 Academic Session'),
('siwes_duration_months', '6 Months'),
('max_file_size_mb', '5'),
('allocation_mode', 'Smart Automated Engine with Manual Override')
ON CONFLICT (setting_key) DO NOTHING;

-- 12. Synchronize all PostgreSQL sequences to prevent duplicate key conflicts
SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE(MAX(id), 1)) FROM users;
SELECT setval(pg_get_serial_sequence('departments', 'id'), COALESCE(MAX(id), 1)) FROM departments;
SELECT setval(pg_get_serial_sequence('academic_programmes', 'id'), COALESCE(MAX(id), 1)) FROM academic_programmes;
SELECT setval(pg_get_serial_sequence('academic_levels', 'id'), COALESCE(MAX(id), 1)) FROM academic_levels;
SELECT setval(pg_get_serial_sequence('supervisors', 'id'), COALESCE(MAX(id), 1)) FROM supervisors;
SELECT setval(pg_get_serial_sequence('companies', 'id'), COALESCE(MAX(id), 1)) FROM companies;
SELECT setval(pg_get_serial_sequence('students', 'id'), COALESCE(MAX(id), 1)) FROM students;
SELECT setval(pg_get_serial_sequence('siwes_applications', 'id'), COALESCE(MAX(id), 1)) FROM siwes_applications;
SELECT setval(pg_get_serial_sequence('allocations', 'id'), COALESCE(MAX(id), 1)) FROM allocations;
SELECT setval(pg_get_serial_sequence('documents', 'id'), COALESCE(MAX(id), 1)) FROM documents;
SELECT setval(pg_get_serial_sequence('notifications', 'id'), COALESCE(MAX(id), 1)) FROM notifications;

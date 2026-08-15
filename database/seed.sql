-- Seed Data for SIWES Allocation Management System
-- Password for all seed users: password123 (bcrypt: $2y$10$4.61eZgHvg1o1G1O1z.h/eDkL5BfKjT3uJkL5M1N2O3P4Q5R6S7T8)

-- 1. Departments
INSERT INTO departments (id, name, code, description) VALUES
(1, 'Computer Science', 'COM', 'Department of Computer Science & Software Development'),
(2, 'Electrical & Electronic Engineering', 'EEE', 'Department of Electrical & Electronics Engineering'),
(3, 'Information Technology', 'IFT', 'Department of Information & Communications Technology'),
(4, 'Statistics & Data Analytics', 'STA', 'Department of Applied Mathematics and Data Science'),
(5, 'Computer Engineering', 'CPE', 'Department of Computer Systems and Hardware Engineering')
ON CONFLICT (id) DO NOTHING;

-- 2. Users (Admin, Coordinator, Students, Supervisors)
-- Password: password123
INSERT INTO users (id, full_name, email, password_hash, role) VALUES
(1, 'Prof. Emmanuel Okafor', 'admin@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'admin'),
(2, 'Dr. Mrs. Amina Yusuf', 'coordinator@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'coordinator'),
(3, 'Chidubem Chukwuma', 'student@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'student'),
(4, 'Fatima Abubakar', 'fatima@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'student'),
(5, 'Oluwaseun Adebayo', 'seun@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'student'),
(6, 'Blessing Danjuma', 'blessing@siwesp.edu.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'student'),
(7, 'Tunde Bakare', 'supervisor@technovate.ng', '$2y$10$r.7gB.K4a0G.1YqJ2pU5E.Z7R3L2N4P5Q6R7S8T9U0V1W2X3Y4Z5a', 'supervisor')
ON CONFLICT (id) DO NOTHING;

-- 3. Companies
INSERT INTO companies (id, name, reg_number, address, state, city, industry, contact_person, phone, email, total_capacity, available_slots, status) VALUES
(1, 'Technovate Solutions Ltd', 'RC-1049281', '45 Victoria Island Expressway', 'Lagos', 'Lagos Island', 'Software Development & IT', 'Tunde Bakare', '08031234567', 'info@technovate.ng', 15, 12, 'active'),
(2, 'MainOne Cable Telecommunications', 'RC-8492019', '12 Marina Promenade', 'Lagos', 'Lagos Island', 'Telecommunications & Cloud', 'Nkechi Egwu', '08029876543', 'careers@mainone.net', 20, 15, 'active'),
(3, 'National Space Research Agency (NASRDA)', 'FG-002931', 'Airport Road, Lugbe', 'Abuja', 'Abuja Central', 'Research & Hardware Systems', 'Dr. Farouk Umar', '08055512345', 'siwes@nasrda.gov.ng', 10, 6, 'active'),
(4, 'Interswitch Group Tech Hub', 'RC-9930219', '1642 Oko Awo Street, VI', 'Lagos', 'Victoria Island', 'Fintech & Cyber Security', 'Ayo Balogun', '08091112233', 'siwes@interswitchgroup.com', 25, 18, 'active'),
(5, 'Shell Petroleum Dev Co (SPDC)', 'RC-001293', 'Industrial Area, Rumuobiokani', 'Rivers', 'Port Harcourt', 'Energy & Embedded Control', 'Engr. Victor Ibim', '08033334444', 'siwes@shell.ng', 12, 8, 'active'),
(6, 'Ibadan Electricity Distribution Co (IBEDC)', 'RC-402910', 'Capitall Building, Ring Road', 'Oyo', 'Ibadan', 'Power & Electrical Grid', 'Engr. Modupe Alabi', '08077778888', 'trainees@ibedc.com', 15, 14, 'active')
ON CONFLICT (id) DO NOTHING;

-- 4. Students
INSERT INTO students (id, user_id, matric_number, department_id, programme, level, phone, address) VALUES
(1, 3, 'F/ND/22/3210001', 1, 'ND', 'ND2', '08123456789', 'Block C, Campus Hostel 2, Yaba'),
(2, 4, 'F/HND/22/3210002', 1, 'HND', 'HND2', '08139876543', '14 Herbert Macaulay Way, Yaba, Lagos'),
(3, 5, 'F/ND/22/3210003', 2, 'ND', 'ND2', '08098761234', '7 University Road, Akoka, Lagos'),
(4, 6, 'F/HND/22/3210004', 3, 'HND', 'HND1', '08145556677', '22 Garki Phase 2, Abuja')
ON CONFLICT (id) DO NOTHING;

-- 5. SIWES Applications
INSERT INTO siwes_applications (id, student_id, preferred_industry, preferred_location, status, notes) VALUES
(1, 1, 'Software Development & IT', 'Lagos', 'Approved', 'Student expresses strong passion for full-stack web development and database management.'),
(2, 2, 'Fintech & Cyber Security', 'Lagos', 'Allocated', 'Student completed ND with distinction and requests high-impact fintech placement.'),
(3, 3, 'Power & Electrical Grid', 'Oyo', 'Submitted', 'Interested in power systems distribution and automation controls.'),
(4, 4, 'Telecommunications & Cloud', 'Abuja', 'Under Review', 'Seeking networking and cloud infrastructure placement in FCT.')
ON CONFLICT (id) DO NOTHING;

-- 6. Allocations
INSERT INTO allocations (id, application_id, student_id, company_id, compatibility_score, match_reasons, status, allocated_by, start_date, end_date) VALUES
(1, 2, 2, 4, 95, '{"dept_match":30, "industry_match":30, "location_match":20, "slot_match":15}', 'Allocated', 2, '2026-09-01', '2027-02-28')
ON CONFLICT (id) DO NOTHING;

-- 7. Documents
INSERT INTO documents (id, student_id, doc_type, file_path, file_name, file_size, status) VALUES
(1, 1, 'SIWES Application Letter', 'uploads/siwes_letter_3210001.pdf', 'SIWES_Request_Letter_Chidubem.pdf', 245000, 'Approved'),
(2, 1, 'Student Identification Card', 'uploads/id_card_3210001.png', 'Student_ID_Chidubem.png', 512000, 'Approved'),
(3, 2, 'SIWES Application Letter', 'uploads/siwes_letter_3210002.pdf', 'SIWES_Letter_Fatima.pdf', 198000, 'Approved'),
(4, 3, 'SIWES Application Letter', 'uploads/siwes_letter_3210003.pdf', 'SIWES_Letter_Oluwaseun.pdf', 310000, 'Pending')
ON CONFLICT (id) DO NOTHING;

-- 8. Notifications
INSERT INTO notifications (id, user_id, title, message, type, is_read) VALUES
(1, 3, 'SIWES Application Approved', 'Your SIWES application has been reviewed and approved by the department coordinator. You are currently pending company allocation.', 'success', false),
(2, 4, 'Company Allocation Completed!', 'Congratulations! You have been successfully allocated to Interswitch Group Tech Hub (Lagos) for your SIWES attachment.', 'success', true),
(3, 1, 'New Student Registrations', '4 new student applications submitted today require coordinator review.', 'info', false)
ON CONFLICT (id) DO NOTHING;

-- 9. System Settings
INSERT INTO settings (setting_key, setting_value) VALUES
('institution_name', 'School of Technology & Applied Sciences'),
('academic_session', '2025/2026 Academic Session'),
('siwes_duration_months', '6 Months'),
('max_file_size_mb', '5'),
('allocation_mode', 'Smart Automated Engine with Manual Override')
ON CONFLICT (setting_key) DO NOTHING;

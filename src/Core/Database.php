<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            
            try {
                // Primary Connection Attempt: PostgreSQL PDO
                if ($config['driver'] === 'pgsql') {
                    $ssl = !empty($config['sslmode']) ? ";sslmode={$config['sslmode']}" : "";
                    $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}{$ssl}";
                    self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                    self::initializePostgresSchema(self::$instance);
                } else {
                    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                    self::$instance = new PDO($dsn, $config['username'], $config['password'], $config['options']);
                }
            } catch (PDOException $e) {
                // Lightweight Fallback Driver (SQLite file storage) if local PostgreSQL service is uninitialized
                try {
                    $dbFile = __DIR__ . '/../../database/siwes_local.sqlite';
                    $dsn = "sqlite:" . $dbFile;
                    self::$instance = new PDO($dsn, null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                    self::initializeSQLiteSchema(self::$instance);
                } catch (PDOException $ex) {
                    die("Database Connection Error: " . $ex->getMessage());
                }
            }

            self::ensureSupervisorSchema(self::$instance);
        }
        return self::$instance;
    }

    /**
     * Automatic SQLite Schema & Seed Loader for immediate out-of-the-box demo testing
     */
    private static function initializeSQLiteSchema(PDO $db): void {
        $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetchAll();
        if (empty($tables)) {
            $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL,
                avatar_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS departments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT UNIQUE NOT NULL,
                code TEXT UNIQUE NOT NULL,
                description TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS companies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                reg_number TEXT UNIQUE NOT NULL,
                address TEXT NOT NULL,
                state TEXT NOT NULL,
                city TEXT NOT NULL,
                industry TEXT NOT NULL,
                contact_person TEXT NOT NULL,
                phone TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                total_capacity INTEGER DEFAULT 10,
                available_slots INTEGER DEFAULT 10,
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS students (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER UNIQUE REFERENCES users(id) ON DELETE CASCADE,
                matric_number TEXT UNIQUE NOT NULL,
                department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
                programme TEXT NOT NULL,
                level TEXT NOT NULL,
                phone TEXT NOT NULL,
                address TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS siwes_applications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER UNIQUE REFERENCES students(id) ON DELETE CASCADE,
                preferred_industry TEXT NOT NULL,
                preferred_location TEXT NOT NULL,
                status TEXT DEFAULT 'Submitted',
                notes TEXT,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS allocations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                application_id INTEGER UNIQUE REFERENCES siwes_applications(id) ON DELETE CASCADE,
                student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
                company_id INTEGER REFERENCES companies(id) ON DELETE CASCADE,
                compatibility_score INTEGER DEFAULT 0,
                match_reasons TEXT DEFAULT '{}',
                status TEXT DEFAULT 'Allocated',
                allocated_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
                start_date DATE,
                end_date DATE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
                doc_type TEXT NOT NULL,
                file_path TEXT NOT NULL,
                file_name TEXT NOT NULL,
                file_size INTEGER NOT NULL,
                status TEXT DEFAULT 'Pending',
                uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
                title TEXT NOT NULL,
                message TEXT NOT NULL,
                type TEXT DEFAULT 'info',
                is_read INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                setting_key TEXT UNIQUE NOT NULL,
                setting_value TEXT,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            ";
            $db->exec($sql);

            // Populate Seed Data
            $hash = password_hash('password123', PASSWORD_BCRYPT);
            
            $db->exec("INSERT OR IGNORE INTO departments (id, name, code, description) VALUES
                (1, 'Computer Science', 'COM', 'Department of Computer Science & Software Development'),
                (2, 'Electrical & Electronic Engineering', 'EEE', 'Department of Electrical & Electronics Engineering'),
                (3, 'Information Technology', 'IFT', 'Department of Information & Communications Technology'),
                (4, 'Statistics & Data Analytics', 'STA', 'Department of Applied Mathematics and Data Science'),
                (5, 'Computer Engineering', 'CPE', 'Department of Computer Systems and Hardware Engineering');");

            $db->exec("INSERT OR IGNORE INTO users (id, full_name, email, password_hash, role) VALUES
                (1, 'Prof. Emmanuel Okafor', 'admin@siwesp.edu.ng', '{$hash}', 'admin'),
                (2, 'Dr. Mrs. Amina Yusuf', 'coordinator@siwesp.edu.ng', '{$hash}', 'coordinator'),
                (3, 'Chidubem Chukwuma', 'student@siwesp.edu.ng', '{$hash}', 'student'),
                (4, 'Fatima Abubakar', 'fatima@siwesp.edu.ng', '{$hash}', 'student'),
                (5, 'Oluwaseun Adebayo', 'seun@siwesp.edu.ng', '{$hash}', 'student'),
                (6, 'Blessing Danjuma', 'blessing@siwesp.edu.ng', '{$hash}', 'student');");

            $db->exec("INSERT OR IGNORE INTO companies (id, name, reg_number, address, state, city, industry, contact_person, phone, email, total_capacity, available_slots, status) VALUES
                (1, 'Technovate Solutions Ltd', 'RC-1049281', '45 Victoria Island Expressway', 'Lagos', 'Lagos Island', 'Software Development & IT', 'Tunde Bakare', '08031234567', 'info@technovate.ng', 15, 12, 'active'),
                (2, 'MainOne Cable Telecommunications', 'RC-8492019', '12 Marina Promenade', 'Lagos', 'Lagos Island', 'Telecommunications & Cloud', 'Nkechi Egwu', '08029876543', 'careers@mainone.net', 20, 15, 'active'),
                (3, 'National Space Research Agency (NASRDA)', 'FG-002931', 'Airport Road, Lugbe', 'Abuja', 'Abuja Central', 'Research & Hardware Systems', 'Dr. Farouk Umar', '08055512345', 'siwes@nasrda.gov.ng', 10, 6, 'active'),
                (4, 'Interswitch Group Tech Hub', 'RC-9930219', '1642 Oko Awo Street, VI', 'Lagos', 'Victoria Island', 'Fintech & Cyber Security', 'Ayo Balogun', '08091112233', 'siwes@interswitchgroup.com', 25, 18, 'active'),
                (5, 'Shell Petroleum Dev Co (SPDC)', 'RC-001293', 'Industrial Area, Rumuobiokani', 'Rivers', 'Port Harcourt', 'Energy & Embedded Control', 'Engr. Victor Ibim', '08033334444', 'siwes@shell.ng', 12, 8, 'active'),
                (6, 'Ibadan Electricity Distribution Co (IBEDC)', 'RC-402910', 'Capitall Building, Ring Road', 'Oyo', 'Ibadan', 'Power & Electrical Grid', 'Engr. Modupe Alabi', '08077778888', 'trainees@ibedc.com', 15, 14, 'active');");

            $db->exec("INSERT OR IGNORE INTO students (id, user_id, matric_number, department_id, programme, level, phone, address) VALUES
                (1, 3, 'F/ND/22/3210001', 1, 'ND', 'ND2', '08123456789', 'Block C, Campus Hostel 2, Yaba'),
                (2, 4, 'F/HND/22/3210002', 1, 'HND', 'HND2', '08139876543', '14 Herbert Macaulay Way, Yaba, Lagos'),
                (3, 5, 'F/ND/22/3210003', 2, 'ND', 'ND2', '08098761234', '7 University Road, Akoka, Lagos'),
                (4, 6, 'F/HND/22/3210004', 3, 'HND', 'HND1', '08145556677', '22 Garki Phase 2, Abuja');");

            $db->exec("INSERT OR IGNORE INTO siwes_applications (id, student_id, preferred_industry, preferred_location, status, notes) VALUES
                (1, 1, 'Software Development & IT', 'Lagos', 'Approved', 'Student expresses strong passion for full-stack web development.'),
                (2, 2, 'Fintech & Cyber Security', 'Lagos', 'Allocated', 'Student completed ND with distinction and requests high-impact placement.'),
                (3, 3, 'Power & Electrical Grid', 'Oyo', 'Submitted', 'Interested in power systems distribution.'),
                (4, 4, 'Telecommunications & Cloud', 'Abuja', 'Under Review', 'Seeking networking placement.');");

            $db->exec("INSERT OR IGNORE INTO allocations (id, application_id, student_id, company_id, compatibility_score, status, allocated_by, start_date, end_date) VALUES
                (1, 2, 2, 4, 95, 'Allocated', 2, '2026-09-01', '2027-02-28');");

            $db->exec("INSERT OR IGNORE INTO documents (id, student_id, doc_type, file_path, file_name, file_size, status) VALUES
                (1, 1, 'SIWES Application Letter', 'uploads/siwes_letter_3210001.pdf', 'SIWES_Request_Letter_Chidubem.pdf', 245000, 'Approved'),
                (2, 1, 'Student Identification Card', 'uploads/id_card_3210001.png', 'Student_ID_Chidubem.png', 512000, 'Approved'),
                (3, 2, 'SIWES Application Letter', 'uploads/siwes_letter_3210002.pdf', 'SIWES_Letter_Fatima.pdf', 198000, 'Approved');");

            $db->exec("INSERT OR IGNORE INTO notifications (id, user_id, title, message, type, is_read) VALUES
                (1, 3, 'SIWES Application Approved', 'Your SIWES application has been reviewed and approved by the department coordinator.', 'success', 0),
                (2, 4, 'Company Allocation Completed!', 'Congratulations! You have been successfully allocated to Interswitch Group Tech Hub.', 'success', 1);");
        }
    }

    /**
     * Automatic PostgreSQL Schema & Seed Loader
     */
    private static function initializePostgresSchema(PDO $db): void {
        try {
            $stmt = $db->query("SELECT to_regclass('public.users')");
            $exists = $stmt ? $stmt->fetchColumn() : null;
            if (!$exists) {
                $schemaFile = __DIR__ . '/../../database/schema.sql';
                $seedFile = __DIR__ . '/../../database/seed.sql';
                if (file_exists($schemaFile)) {
                    $db->exec(file_get_contents($schemaFile));
                }
                if (file_exists($seedFile)) {
                    $db->exec(file_get_contents($seedFile));
                }
            }
        } catch (PDOException $e) {
            // Silently continue if tables already exist or permission boundary prevents to_regclass
        }
    }

    /**
     * Automatic Supervisor Schema Migration & Demo Seeder
     */
    private static function ensureSupervisorSchema(PDO $db): void {
        try {
            // 1. Create supervisors table if not exists
            $db->exec("
                CREATE TABLE IF NOT EXISTS supervisors (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER UNIQUE REFERENCES users(id) ON DELETE CASCADE,
                    department_id INTEGER REFERENCES departments(id) ON DELETE SET NULL,
                    staff_id TEXT UNIQUE,
                    phone TEXT,
                    designation TEXT DEFAULT 'Institution Supervisor',
                    max_capacity INTEGER DEFAULT 20,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ");

            // 2. Add supervisor columns to students table if missing
            $driver = $db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $cols = $db->query("PRAGMA table_info(students)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('supervisor_id', $cols)) {
                    $db->exec("ALTER TABLE students ADD COLUMN supervisor_id INTEGER REFERENCES users(id) ON DELETE SET NULL");
                    $db->exec("ALTER TABLE students ADD COLUMN supervisor_assigned_at DATETIME DEFAULT NULL");
                    $db->exec("ALTER TABLE students ADD COLUMN supervisor_assigned_by INTEGER REFERENCES users(id) ON DELETE SET NULL");
                }
            } else {
                // PostgreSQL
                $db->exec("
                    ALTER TABLE students ADD COLUMN IF NOT EXISTS supervisor_id INT REFERENCES users(id) ON DELETE SET NULL;
                    ALTER TABLE students ADD COLUMN IF NOT EXISTS supervisor_assigned_at TIMESTAMP WITH TIME ZONE DEFAULT NULL;
                    ALTER TABLE students ADD COLUMN IF NOT EXISTS supervisor_assigned_by INT REFERENCES users(id) ON DELETE SET NULL;
                ");
            }

            // 3. Seed demo supervisors if none exist
            $supCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'")->fetchColumn();
            $hash = password_hash('password123', PASSWORD_BCRYPT);

            if ($supCount === 0) {
                // Supervisor 1: Engr. Tunde Bakare
                $db->exec("INSERT INTO users (full_name, email, password_hash, role) VALUES 
                    ('Engr. Tunde Bakare', 'supervisor1@siwesp.edu.ng', '{$hash}', 'supervisor')");
                $sup1UserId = (int)$db->lastInsertId();
                $db->exec("INSERT INTO supervisors (user_id, department_id, staff_id, phone, designation) VALUES 
                    ({$sup1UserId}, 1, 'SUP/COM/001', '08031234567', 'Senior Lecturer & SIWES Supervisor')");

                // Supervisor 2: Dr. Mrs. Nkechi Egwu
                $db->exec("INSERT INTO users (full_name, email, password_hash, role) VALUES 
                    ('Dr. Mrs. Nkechi Egwu', 'supervisor2@siwesp.edu.ng', '{$hash}', 'supervisor')");
                $sup2UserId = (int)$db->lastInsertId();
                $db->exec("INSERT INTO supervisors (user_id, department_id, staff_id, phone, designation) VALUES 
                    ({$sup2UserId}, 2, 'SUP/EEE/002', '08029876543', 'Principal Supervisor')");

                // Supervisor 3: Dr. Farouk Umar
                $db->exec("INSERT INTO users (full_name, email, password_hash, role) VALUES 
                    ('Dr. Farouk Umar', 'supervisor3@siwesp.edu.ng', '{$hash}', 'supervisor')");
                $sup3UserId = (int)$db->lastInsertId();
                $db->exec("INSERT INTO supervisors (user_id, department_id, staff_id, phone, designation) VALUES 
                    ({$sup3UserId}, 3, 'SUP/IFT/003', '08055512345', 'SIWES Academic Supervisor')");
            }

            // 4. Ensure there are at least 10 sample students for robust equal-distribution testing
            $studentCount = (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
            if ($studentCount < 10) {
                $additionalStudents = [
                    ['name' => 'Oluwaseun Adebayo', 'email' => 'seun@siwesp.edu.ng', 'matric' => 'F/ND/22/3210005', 'dept' => 2, 'prog' => 'ND', 'level' => 'ND2', 'phone' => '08098761234', 'industry' => 'Power & Electrical Grid', 'location' => 'Oyo'],
                    ['name' => 'Blessing Danjuma', 'email' => 'blessing@siwesp.edu.ng', 'matric' => 'F/HND/22/3210006', 'dept' => 3, 'prog' => 'HND', 'level' => 'HND1', 'phone' => '08145556677', 'industry' => 'Telecommunications & Cloud', 'location' => 'Abuja'],
                    ['name' => 'Emmanuel Bassey', 'email' => 'emmanuel@siwesp.edu.ng', 'matric' => 'F/ND/22/3210007', 'dept' => 1, 'prog' => 'ND', 'level' => 'ND2', 'phone' => '08021112233', 'industry' => 'Software Development & IT', 'location' => 'Lagos'],
                    ['name' => 'Zainab Bello', 'email' => 'zainab@siwesp.edu.ng', 'matric' => 'F/HND/22/3210008', 'dept' => 4, 'prog' => 'HND', 'level' => 'HND2', 'phone' => '08034445566', 'industry' => 'Data Analytics & AI', 'location' => 'Lagos'],
                    ['name' => 'Kelechi Okonkwo', 'email' => 'kelechi@siwesp.edu.ng', 'matric' => 'F/ND/22/3210009', 'dept' => 5, 'prog' => 'ND', 'level' => 'ND1', 'phone' => '08057778899', 'industry' => 'Hardware & Embedded Systems', 'location' => 'Rivers'],
                    ['name' => 'Amina Mohammed', 'email' => 'amina.m@siwesp.edu.ng', 'matric' => 'F/HND/22/3210010', 'dept' => 1, 'prog' => 'HND', 'level' => 'HND2', 'phone' => '08089990011', 'industry' => 'Fintech & Cyber Security', 'location' => 'Lagos'],
                    ['name' => 'David Adeleke', 'email' => 'david@siwesp.edu.ng', 'matric' => 'F/ND/22/3210011', 'dept' => 2, 'prog' => 'ND', 'level' => 'ND2', 'phone' => '08091114455', 'industry' => 'Renewable Energy & Power', 'location' => 'Lagos'],
                    ['name' => 'Maryam Sani', 'email' => 'maryam@siwesp.edu.ng', 'matric' => 'F/HND/22/3210012', 'dept' => 3, 'prog' => 'HND', 'level' => 'HND1', 'phone' => '08132225566', 'industry' => 'Cloud Systems & Networking', 'location' => 'Abuja'],
                ];

                foreach ($additionalStudents as $st) {
                    $exists = $db->query("SELECT id FROM users WHERE email = '{$st['email']}'")->fetch();
                    if (!$exists) {
                        $db->exec("INSERT INTO users (full_name, email, password_hash, role) VALUES ('{$st['name']}', '{$st['email']}', '{$hash}', 'student')");
                        $uId = (int)$db->lastInsertId();
                        $db->exec("INSERT INTO students (user_id, matric_number, department_id, programme, level, phone, address) VALUES ({$uId}, '{$st['matric']}', {$st['dept']}, '{$st['prog']}', '{$st['level']}', '{$st['phone']}', 'Campus Hall of Residence')");
                        $sId = (int)$db->lastInsertId();
                        $db->exec("INSERT INTO siwes_applications (student_id, preferred_industry, preferred_location, status, notes) VALUES ({$sId}, '{$st['industry']}', '{$st['location']}', 'Approved', 'Ready for supervisor and placement allocation.')");
                    }
                }
            }

            // 5. Ensure Registration Controls Schema (Departments is_active, Academic Programmes & Levels)
            if ($driver === 'sqlite') {
                $deptCols = $db->query("PRAGMA table_info(departments)")->fetchAll(PDO::FETCH_COLUMN, 1);
                if (!in_array('is_active', $deptCols)) {
                    $db->exec("ALTER TABLE departments ADD COLUMN is_active INTEGER DEFAULT 1");
                }

                $db->exec("
                    CREATE TABLE IF NOT EXISTS academic_programmes (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        code TEXT UNIQUE NOT NULL,
                        name TEXT NOT NULL,
                        is_active INTEGER DEFAULT 1,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    );
                    CREATE TABLE IF NOT EXISTS academic_levels (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        code TEXT UNIQUE NOT NULL,
                        name TEXT NOT NULL,
                        is_active INTEGER DEFAULT 1,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    );
                ");
            } else {
                $db->exec("
                    ALTER TABLE departments ADD COLUMN IF NOT EXISTS is_active INT DEFAULT 1;
                    CREATE TABLE IF NOT EXISTS academic_programmes (
                        id SERIAL PRIMARY KEY,
                        code VARCHAR(50) UNIQUE NOT NULL,
                        name VARCHAR(150) NOT NULL,
                        is_active INT DEFAULT 1,
                        created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
                    );
                    CREATE TABLE IF NOT EXISTS academic_levels (
                        id SERIAL PRIMARY KEY,
                        code VARCHAR(50) UNIQUE NOT NULL,
                        name VARCHAR(150) NOT NULL,
                        is_active INT DEFAULT 1,
                        created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
                    );
                ");
            }

            // Seed default programmes if empty
            $progCount = (int)$db->query("SELECT COUNT(*) FROM academic_programmes")->fetchColumn();
            if ($progCount === 0) {
                $defaultProgrammes = [
                    ['code' => 'ND', 'name' => 'National Diploma (ND)'],
                    ['code' => 'HND', 'name' => 'Higher National Diploma (HND)'],
                    ['code' => 'BSc', 'name' => 'Bachelor of Science (B.Sc)'],
                    ['code' => 'BEng', 'name' => 'Bachelor of Engineering (B.Eng)'],
                ];
                $pStmt = $db->prepare("INSERT INTO academic_programmes (code, name, is_active) VALUES (:c, :n, 1)");
                foreach ($defaultProgrammes as $p) {
                    $pStmt->execute(['c' => $p['code'], 'n' => $p['name']]);
                }
            }

            // Seed default levels if empty
            $levelCount = (int)$db->query("SELECT COUNT(*) FROM academic_levels")->fetchColumn();
            if ($levelCount === 0) {
                $defaultLevels = [
                    ['code' => 'ND1', 'name' => 'ND 1'],
                    ['code' => 'ND2', 'name' => 'ND 2'],
                    ['code' => 'HND1', 'name' => 'HND 1'],
                    ['code' => 'HND2', 'name' => 'HND 2'],
                    ['code' => '200L', 'name' => '200 Level'],
                    ['code' => '300L', 'name' => '300 Level'],
                    ['code' => '400L', 'name' => '400 Level'],
                ];
                $lStmt = $db->prepare("INSERT INTO academic_levels (code, name, is_active) VALUES (:c, :n, 1)");
                foreach ($defaultLevels as $l) {
                    $lStmt->execute(['c' => $l['code'], 'n' => $l['name']]);
                }
            }

        } catch (\Exception $e) {
            error_log("Supervisor/Registration schema migration notice: " . $e->getMessage());
        }
    }
}


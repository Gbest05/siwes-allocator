-- PostgreSQL Database Schema for SIWES Allocation Management System

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL CHECK (role IN ('admin', 'coordinator', 'student', 'supervisor')),
    avatar_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS departments (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) UNIQUE NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS companies (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    reg_number VARCHAR(100) UNIQUE NOT NULL,
    address TEXT NOT NULL,
    state VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    industry VARCHAR(100) NOT NULL,
    contact_person VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    total_capacity INT DEFAULT 10,
    available_slots INT DEFAULT 10,
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    id SERIAL PRIMARY KEY,
    user_id INT UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    matric_number VARCHAR(50) UNIQUE NOT NULL,
    department_id INT REFERENCES departments(id) ON DELETE SET NULL,
    programme VARCHAR(50) NOT NULL CHECK (programme IN ('ND', 'HND')),
    level VARCHAR(20) NOT NULL CHECK (level IN ('ND1', 'ND2', 'HND1', 'HND2')),
    phone VARCHAR(50) NOT NULL,
    address TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS siwes_applications (
    id SERIAL PRIMARY KEY,
    student_id INT UNIQUE REFERENCES students(id) ON DELETE CASCADE,
    preferred_industry VARCHAR(100) NOT NULL,
    preferred_location VARCHAR(100) NOT NULL,
    status VARCHAR(30) DEFAULT 'Submitted' CHECK (status IN ('Draft', 'Submitted', 'Under Review', 'Approved', 'Allocated', 'Rejected', 'Completed')),
    notes TEXT,
    submitted_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS allocations (
    id SERIAL PRIMARY KEY,
    application_id INT UNIQUE REFERENCES siwes_applications(id) ON DELETE CASCADE,
    student_id INT REFERENCES students(id) ON DELETE CASCADE,
    company_id INT REFERENCES companies(id) ON DELETE CASCADE,
    compatibility_score INT DEFAULT 0,
    match_reasons JSONB DEFAULT '{}'::jsonb,
    status VARCHAR(30) DEFAULT 'Allocated' CHECK (status IN ('Allocated', 'Reassigned', 'Confirmed', 'Cancelled', 'Completed')),
    allocated_by INT REFERENCES users(id) ON DELETE SET NULL,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS documents (
    id SERIAL PRIMARY KEY,
    student_id INT REFERENCES students(id) ON DELETE CASCADE,
    doc_type VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    status VARCHAR(30) DEFAULT 'Pending' CHECK (status IN ('Pending', 'Approved', 'Rejected')),
    uploaded_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) DEFAULT 'info' CHECK (type IN ('info', 'success', 'warning', 'danger')),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS supervisors (
    id SERIAL PRIMARY KEY,
    user_id INT UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    company_id INT REFERENCES companies(id) ON DELETE CASCADE,
    designation VARCHAR(100) NOT NULL,
    phone VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS placement_reviews (
    id SERIAL PRIMARY KEY,
    allocation_id INT REFERENCES allocations(id) ON DELETE CASCADE,
    supervisor_id INT REFERENCES supervisors(id) ON DELETE CASCADE,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    evaluation_date TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INT REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
    id SERIAL PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for optimal PostgreSQL performance
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_students_matric ON students(matric_number);
CREATE INDEX IF NOT EXISTS idx_companies_industry ON companies(industry);
CREATE INDEX IF NOT EXISTS idx_companies_state ON companies(state);
CREATE INDEX IF NOT EXISTS idx_applications_status ON siwes_applications(status);
CREATE INDEX IF NOT EXISTS idx_allocations_status ON allocations(status);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id, is_read);

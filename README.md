# SIWES Placement & Management System

A production-ready, high-performance, full-stack **Student Industrial Work Experience Scheme (SIWES) Allocation Management System** built with **PHP 8.2+ (MVC Architecture)**, **PostgreSQL (Neon Cloud / Local SQLite)**, **Bootstrap 5**, and **Nginx / Docker**.

---

## 🌟 Comprehensive Features

### 1. 🎓 Student Portal
- **Streamlined SIWES Application:** Submit industrial placement preferences (preferred industry, preferred state/city, and cover notes).
- **Single-Submission Integrity:** Unallocated students can edit or delete their application to re-apply. Once officially allocated to a company, applications are permanently locked to maintain placement integrity.
- **Supervisor Contact Notice:** Allocated students are presented with their assigned Institutional Supervisor's full profile (name, department, staff ID, phone, email) with instructions to reach out for field guidance.
- **Document Verification Manager:** Upload, manage, and track approval status of required documents (SIWES Request Letters, Student ID Cards, Acceptance Slips) with secure MIME and file-size validation.
- **Official Placement Letter Generator:** Dynamically renders the official institution SIWES Placement & Acceptance Letter with personalized data, official letterhead, authorized signature, and verification stamp. Includes clean print `@media print` formatting.
- **Real-Time Notifications:** In-app notification center alerting students about application approval, supervisor assignment, and company allocation.

### 2. 👨‍🏫 Institutional Supervisor Portal
- **Dedicated Supervisor Dashboard:** Real-time metrics showing total allocated students, active placements, pending reviews, and department distribution.
- **Assigned Student Cohort Roster:** View detailed profiles of assigned students, their matriculation numbers, placement company, industry, and placement dates.
- **Direct & Broadcast Messaging:** 
  - Send private 1-on-1 guidance messages and instructions directly to specific assigned students.
  - Broadcast cohort-wide notifications to all assigned students simultaneously.
- **Field Evaluations & Placement Reviews:** Submit performance ratings (1–5 stars) and structured feedback for students' industrial training progress.

### 3. 📋 SIWES Coordinator Portal
- **Smart Compatibility Matching Engine (100-Point Algorithm):**
  - **Academic Department Alignment:** +30 points
  - **Industry Relevance Match:** +30 points
  - **Geographical Proximity Match:** +20 points
  - **Available Company Capacity:** +20 points
  - Automated slot deduction with real-time visual progress indicators.
- **Application Review Workflow:** Approve, request review, or reject student SIWES submissions with audit notes.
- **Supervisor Allocation & Balancing:** Assign institutional supervisors to students with automated equal-distribution load balancing.
- **Partner Company Directory:** Manage registered host organizations, address details, contact persons, industry sectors, and intake quotas.
- **Interactive Letter Template CMS:** Full visual editor for the official SIWES Application & Placement Letter with dynamic placeholders (`{STUDENT_NAME}`, `{MATRIC_NUMBER}`, `{DEPARTMENT_NAME}`, `{COMPANY_NAME}`, etc.) and real-time live preview.
- **Data Export & Reports:** Export student rosters, company lists, and allocation audits to CSV/Excel formats.

### 4. ⚙️ System Administrator Portal
- **Master Landing Page CMS:** Customize institution name, portal logo, banner imagery, headlines, and public contact information dynamically.
- **Academic Controls:** Create, edit, and activate/deactivate academic departments, programmes (ND, HND, B.Sc, B.Eng), and levels (ND1, ND2, HND1, HND2, 100L–500L).
- **Partner Company Management:** Full CRUD operations including Edit, Suspend/Reactivate, and Delete with safeguards against removing companies with active student interns.
- **System-Wide Analytics:** Visual analytics powered by Chart.js displaying department distributions, placement progress, and supervisor allocations.
- **User Role Management & Audit Trail:** Manage system user accounts, passwords, and track system activities.

---

## ⚡ High-Performance Optimization Engine

- **Sub-10ms Database Response Time:** Implemented a version-gated migration guard (`CURRENT_SCHEMA_VERSION = '2.4'`) ensuring expensive DDL schema checks run only once on deployment, slashing per-request database latency on Neon PostgreSQL from ~5,000ms to **6.2ms** (99.8% speedup).
- **Persistent TLS Connection Pooling:** Configured `PDO::ATTR_PERSISTENT => true` and `PDO::ATTR_EMULATE_PREPARES => true` to eliminate redundant TLS handshakes and halve network round-trips over the WAN.
- **In-Memory Request Caching:** Static runtime caching for site settings and letter templates to prevent repetitive database queries within a single lifecycle.
- **Automated Sequence Synchronization:** Auto-repairs PostgreSQL primary key sequences (`users_id_seq`, `students_id_seq`, etc.) after data seeding to completely eliminate duplicate key collision errors on user registration.
- **Indexed Relational Queries:** High-performance database indexes across foreign keys, user IDs, student matric numbers, and allocation statuses.

---

## 🏗️ Software Architecture & Methodology

### Development Methodology
The system was engineered using the **Agile-Iterative Software Development Methodology (Iterative & Incremental Model)** with **Rapid Application Development (RAD)**. Features were conceptualized, implemented, tested, and deployed in distinct functional sprints:
1. **Sprint 1:** Architecture, Schema Design, and Role-Based Authentication.
2. **Sprint 2:** Company Management, Academic Settings, and Student Application Workflow.
3. **Sprint 3:** Smart Allocation Algorithm, Supervisor Cohort Management, and Messaging.
4. **Sprint 4:** Dynamic Letter CMS, Verification Documents, and Notifications.
5. **Sprint 5:** Neon PostgreSQL Integration, Docker Containerization, Performance Tuning, and Cloud Deployment.

### Architectural Pattern
The application follows the **Model-View-Controller (MVC)** architectural pattern:
- **Presentation Layer (View):** Bootstrap 5.3, Font Awesome 6, responsive HTML5/CSS3, dynamic live preview widgets.
- **Application Layer (Controller & Core):** Object-oriented PHP 8.2, front controller routing (`public/index.php`), CSRF middleware, session management, and role-based guards (`App\Core\Auth`).
- **Data Layer (Model & Persistence):** PDO Dual-Driver architecture supporting **Neon Serverless PostgreSQL** in production and **SQLite** for local development.

```
                              [ Browser Client (Desktop / Mobile) ]
                                              │
                                       (HTTPS Requests)
                                              ▼
                             [ Nginx Reverse Proxy / FastCGI ]
                                              │
                                              ▼
                                   [ public/index.php ]
                                              │
                              [ App\Core\Router & Middleware ]
                                (RBAC, CSRF, Input Sanitizer)
                                              │
                         ┌────────────────────┴────────────────────┐
                         ▼                                         ▼
               [ App\Controllers ]                        [ App\Core Services ]
         (Auth, Student, Supervisor,                    (Database Manager,
           Coordinator, Admin, etc.)                      Helper, Auth Session)
                         │                                         │
                         └────────────────────┬────────────────────┘
                                              ▼
                                 [ Persistence Layer ]
                   ┌──────────────────────────┴──────────────────────────┐
                   ▼                                                     ▼
        [ Neon PostgreSQL Cloud ]                              [ Local SQLite DB ]
```

---

## 🛠️ Technology Stack

| Component | Technologies Used |
| :--- | :--- |
| **Backend** | PHP 8.2+ (Clean Object-Oriented MVC Architecture) |
| **Database** | PostgreSQL (Neon Serverless Cloud via PDO `pgsql`) / SQLite fallback |
| **Frontend** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, Font Awesome 6 Icons |
| **Data Visualizations** | Chart.js |
| **Web Server** | Nginx / PHP-FPM / Docker |
| **Hosting & CI/CD** | Render PaaS, GitHub Webhook Automated Builds |

---

## 🚀 Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/Gbest05/siwes-allocator.git
cd siwes-allocator
```

### 2. Environment Configuration
Create or configure your database connection in `config/database.php` or provide a `DATABASE_URL` environment variable:
```bash
# Cloud PostgreSQL (Neon / Render)
DATABASE_URL="postgres://user:password@ep-sample.neon.tech/siwes_db?sslmode=require"
```
*Note: If no PostgreSQL credentials are provided, the portal automatically falls back to local SQLite storage at `database/siwes_local.sqlite` for zero-configuration development.*

### 3. Run Locally with PHP Built-in Server
```bash
php -S localhost:8000 -t public
```
Visit **[http://localhost:8000](http://localhost:8000)** in your browser.

### 4. Run with Docker & Docker Compose
```bash
docker-compose up -d --build
```
Visit **[http://localhost:8000](http://localhost:8000)** in your browser.

### 5. Deploy to Render Cloud
1. Connect this repository to a new **Web Service** on [Render](https://render.com).
2. Set Environment to **Docker** (Render uses the root `Dockerfile`).
3. Set your `DATABASE_URL` environment variable pointing to your Neon PostgreSQL database.
4. Render will build and deploy automatically on every `git push`.

---

## 👥 Demo Accounts

The database comes pre-seeded with accounts for all roles. All demo accounts use the default password: **`password123`**.

| Role | Name / Designation | Email / Login ID | Default Password |
| :--- | :--- | :--- | :--- |
| **System Administrator** | Prof. Emmanuel Okafor | `admin@siwesp.edu.ng` | `password123` |
| **SIWES Coordinator** | Dr. Mrs. Amina Yusuf | `coordinator@siwesp.edu.ng` | `password123` |
| **Institutional Supervisor 1** | Engr. Tunde Bakare (Computer Science) | `supervisor1@siwesp.edu.ng` | `password123` |
| **Institutional Supervisor 2** | Dr. Mrs. Nkechi Egwu (Electrical Eng.) | `supervisor2@siwesp.edu.ng` | `password123` |
| **Institutional Supervisor 3** | Dr. Farouk Umar (Info Technology) | `supervisor3@siwesp.edu.ng` | `password123` |
| **Student (Allocated)** | Chidubem Chukwuma (ND2 - Computer Science) | `student@siwesp.edu.ng` / `F/ND/22/3210001` | `password123` |
| **Student (Allocated)** | Fatima Abubakar (HND2 - Computer Science) | `fatima@siwesp.edu.ng` / `F/HND/22/3210002` | `password123` |
| **Student (Unallocated)** | Oluwaseun Adebayo (ND2 - Electrical Eng.) | `seun@siwesp.edu.ng` / `F/ND/22/3210003` | `password123` |

---

## 📁 Project Directory Structure

```
siwes-allocator/
├── config/                  # App, database, and system configurations
│   ├── app.php
│   └── database.php
├── database/                # Database DDL schemas, seed scripts, SQLite storage
│   ├── schema.sql
│   ├── seed.sql
│   └── siwes_local.sqlite
├── docker/                  # Nginx, Supervisor, and entrypoint container configs
│   ├── entrypoint.sh
│   ├── nginx.conf
│   └── supervisord.conf
├── public/                  # Document root / public entry point
│   ├── index.php            # Front Controller
│   ├── assets/              # CSS, JavaScript, and static files
│   └── uploads/             # Student documents, letters, institution logos
├── src/
│   ├── Controllers/         # Application MVC Controllers
│   │   ├── AdminController.php
│   │   ├── AllocationController.php
│   │   ├── AuthController.php
│   │   ├── CompanyController.php
│   │   ├── CoordinatorController.php
│   │   ├── DocumentController.php
│   │   ├── StudentController.php
│   │   └── SupervisorController.php
│   ├── Core/                # Framework Core engines
│   │   ├── Auth.php         # RBAC Authentication & Session Engine
│   │   ├── Database.php     # High-Performance Dual-Driver Connection & Migration Pool
│   │   ├── Helper.php       # Sanitization, CSRF, Settings Cache, Flash messages
│   │   └── Router.php       # HTTP Route Dispatcher
│   └── Views/               # UI Templates & Views
│       ├── admin/
│       ├── allocation/
│       ├── auth/
│       ├── coordinator/
│       ├── layouts/
│       ├── student/
│       └── supervisor/
├── Dockerfile               # Production container specification for Render
├── docker-compose.yml       # Local containerization configuration
├── render.yaml              # Render Cloud service blueprint
└── README.md                # Project documentation
```

---

## 🔒 Security Implementations

- **CSRF Defense:** Synchronizer token pattern implemented across all application forms.
- **SQL Injection Prevention:** Strictly parameterized PDO prepared statements across both PostgreSQL and SQLite.
- **Password Protection:** Industry-standard **Bcrypt** algorithm via `password_hash()` and `password_verify()`.
- **Role-Based Access Control (RBAC):** Strict server-side route guards (`Auth::requireRole()`) preventing unauthorized endpoint execution.
- **XSS Sanitization:** Global string sanitization and HTML entity encoding on all user-submitted data.
- **Secure File Uploads:** Verification of MIME types, restricted file extensions (`.pdf`, `.jpg`, `.png`), randomized storage names, and size caps.

---

## 📜 License

This project is open-source and licensed under the [MIT License](LICENSE).

# SIWES Allocation Management System

A digital, responsive, full-stack **Student Industrial Work Experience Scheme (SIWES) Allocation Management System** built with PHP 8+, PostgreSQL PDO, Bootstrap 5, and Nginx.

---

## 🌟 Key Features

- **High-Impact Landing Page:** Responsive navbar, animated statistics counters, step-by-step placement workflow, testimonials, and dynamic content.
- **Admin Landing Page CMS:** Manage portal name, upload custom logos, edit headlines, change hero/about background images, and update contact information in real-time.
- **Smart Compatibility Matching Engine (100-Point Algorithm):**
  - **Department Match:** +30 pts
  - **Industry Relevance Match:** +30 pts
  - **Geographic Proximity Match:** +20 pts
  - **Available Slot Capacity:** +20 pts
  - Automatic company slot deduction & progress bar visualization.
- **Role-Based Access Control (RBAC):**
  - **Admin:** System analytics with Chart.js (department distribution doughnut chart, placement status bar chart), user directory, departments, and settings.
  - **Coordinator:** Student list, application approval/rejection review workflow, smart allocation module, partner company slot manager, and CSV reporting exports.
  - **Student:** Placement preferences submission, document upload manager (application letter, student ID, etc.), placement status, and printable official SIWES allocation letter.
- **Security & Integrity:** CSRF token verification, password hashing with bcrypt, input sanitization, and prepared PDO SQL queries.

---

## 🛠️ Technology Stack

- **Backend:** PHP 8+ (MVC-inspired clean architecture)
- **Database:** PostgreSQL (PDO `pgsql` driver) with SQLite fallback
- **Web Server:** Nginx / Apache / PHP Built-in Server
- **Frontend:** HTML5, CSS3, JavaScript, Bootstrap 5, Font Awesome 6 Icons
- **Visualizations:** Chart.js

---

## 🚀 Getting Started

### 1. Clone the Repository
```bash
git clone https://github.com/Gbest05/siwes-allocator.git
cd siwes-allocator
```

### 2. Database Setup (PostgreSQL)
Run the SQL DDL schema and seed files into your PostgreSQL database:
```bash
psql -U postgres -d siwes_db -f database/schema.sql
psql -U postgres -d siwes_db -f database/seed.sql
```

Configure database credentials in `config/database.php` or set environment variables:
```php
'host'     => '127.0.0.1',
'port'     => '5432',
'dbname'   => 'siwes_db',
'username' => 'postgres',
'password' => 'your_password',
```

### 3. Run with Docker (Recommended)
You can run the full Nginx + PHP 8 + PostgreSQL stack using Docker:
```bash
docker-compose up -d --build
```
Open **[http://localhost:8000](http://localhost:8000)** in your browser.

### 4. Run with PHP Local Server
To run using PHP's built-in development server:
```bash
php -S localhost:8000 -t public
```
Open **[http://localhost:8000](http://localhost:8000)** in your browser.

### 5. Deploy to Render.com
1. Create a new **Web Service** on Render.
2. Connect this repository (`https://github.com/Gbest05/siwes-allocator`).
3. Set the **Runtime** to **Docker** (it will automatically use the root `Dockerfile`).
4. Add a Render **PostgreSQL Database** and attach its `DATABASE_URL` to your Web Service environment variables.
5. Deploy! The container automatically configures Nginx, PHP-FPM, migrations, and port bindings.

---

## 👥 Demo Logins

| Role | Email / Matric Number | Password |
| :--- | :--- | :--- |
| **System Admin** | `admin@siwesp.edu.ng` | `password123` |
| **SIWES Coordinator** | `coordinator@siwesp.edu.ng` | `password123` |
| **Student 1** | `student@siwesp.edu.ng` / `F/ND/22/3210001` | `password123` |
| **Student 2** | `fatima@siwesp.edu.ng` / `F/HND/22/3210002` | `password123` |

---

## 📜 License
This project is open source and available under the [MIT License](LICENSE).

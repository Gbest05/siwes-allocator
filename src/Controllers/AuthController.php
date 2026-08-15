<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class AuthController {
    
    public function showLogin(): void {
        if (Auth::check()) {
            Helper::redirect('dashboard');
        }
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void {
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('login');
        }

        $emailOrMatric = Helper::sanitize($_POST['login_id'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($emailOrMatric) || empty($password)) {
            Helper::setFlash('warning', 'Please enter your email/matric number and password.');
            Helper::redirect('login');
        }

        $db = Database::getInstance();
        
        // Search user by email or by student's matric number
        $stmt = $db->prepare("
            SELECT u.*, s.matric_number 
            FROM users u 
            LEFT JOIN students s ON u.id = s.user_id 
            WHERE u.email = :id OR s.matric_number = :id
        ");
        $stmt->execute(['id' => $emailOrMatric]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            Auth::login($user);
            Helper::setFlash('success', "Welcome back, {$user['full_name']}!");

            // Role-based Redirect
            switch ($user['role']) {
                case 'admin':
                    Helper::redirect('admin/dashboard');
                    break;
                case 'coordinator':
                    Helper::redirect('coordinator/dashboard');
                    break;
                default:
                    Helper::redirect('dashboard');
                    break;
            }
        } else {
            Helper::setFlash('danger', 'Invalid email/matric number or password.');
            Helper::redirect('login');
        }
    }

    public function showRegister(): void {
        if (Auth::check()) {
            Helper::redirect('dashboard');
        }
        $db = Database::getInstance();
        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();
        require __DIR__ . '/../Views/auth/register.php';
    }

    public function register(): void {
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('register');
        }

        $fullName = Helper::sanitize($_POST['full_name'] ?? '');
        $matricNumber = strtoupper(Helper::sanitize($_POST['matric_number'] ?? ''));
        $email = Helper::sanitize($_POST['email'] ?? '');
        $phone = Helper::sanitize($_POST['phone'] ?? '');
        $deptId = (int)($_POST['department_id'] ?? 0);
        $programme = Helper::sanitize($_POST['programme'] ?? 'ND');
        $level = Helper::sanitize($_POST['level'] ?? 'ND2');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($fullName) || empty($matricNumber) || empty($email) || empty($password)) {
            Helper::setFlash('warning', 'Please fill in all required fields.');
            Helper::redirect('register');
        }

        if ($password !== $confirmPassword) {
            Helper::setFlash('warning', 'Passwords do not match.');
            Helper::redirect('register');
        }

        $db = Database::getInstance();

        // Check if email or matric number already exists
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetch()) {
            Helper::setFlash('danger', 'An account with this email address already exists.');
            Helper::redirect('register');
        }

        $matStmt = $db->prepare("SELECT id FROM students WHERE matric_number = :matric");
        $matStmt->execute(['matric' => $matricNumber]);
        if ($matStmt->fetch()) {
            Helper::setFlash('danger', 'A student with this Matriculation Number already exists.');
            Helper::redirect('register');
        }

        try {
            $db->beginTransaction();

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $userStmt = $db->prepare("
                INSERT INTO users (full_name, email, password_hash, role) 
                VALUES (:name, :email, :hash, 'student')
            ");
            $userStmt->execute([
                'name' => $fullName,
                'email' => $email,
                'hash' => $passwordHash
            ]);
            $userId = (int)$db->lastInsertId();

            $studentStmt = $db->prepare("
                INSERT INTO students (user_id, matric_number, department_id, programme, level, phone) 
                VALUES (:user_id, :matric, :dept_id, :prog, :level, :phone)
            ");
            $studentStmt->execute([
                'user_id' => $userId,
                'matric' => $matricNumber,
                'dept_id' => $deptId,
                'prog' => $programme,
                'level' => $level,
                'phone' => $phone
            ]);

            // Add Welcome Notification
            $notifStmt = $db->prepare("
                INSERT INTO notifications (user_id, title, message, type) 
                VALUES (:user_id, 'Registration Successful', 'Welcome to the SIWES Portal! Please complete your SIWES application and upload your documents.', 'success')
            ");
            $notifStmt->execute(['user_id' => $userId]);

            $db->commit();

            // Auto Login
            Auth::login([
                'id' => $userId,
                'full_name' => $fullName,
                'email' => $email,
                'role' => 'student'
            ]);

            Helper::setFlash('success', 'Registration successful! Welcome to your SIWES dashboard.');
            Helper::redirect('dashboard');

        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Registration failed: ' . $e->getMessage());
            Helper::redirect('register');
        }
    }

    public function logout(): void {
        Auth::logout();
        Helper::setFlash('info', 'You have been logged out successfully.');
        Helper::redirect('login');
    }
}

<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class StudentController {

    public function dashboard(): void {
        Auth::requireAuth();
        
        // Redirect non-students to their respective dashboards
        if (Auth::role() === 'admin') {
            Helper::redirect('admin/dashboard');
        } elseif (Auth::role() === 'coordinator') {
            Helper::redirect('coordinator/dashboard');
        }

        $userId = Auth::id();
        $db = Database::getInstance();

        // Fetch Student Record
        $studentStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS department_name, d.code AS department_code 
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN departments d ON s.department_id = d.id 
            WHERE s.user_id = :uid
        ");
        $studentStmt->execute(['uid' => $userId]);
        $student = $studentStmt->fetch();

        if (!$student) {
            Helper::setFlash('danger', 'Student record not found.');
            Helper::redirect('logout');
        }

        // Fetch Application
        $appStmt = $db->prepare("SELECT * FROM siwes_applications WHERE student_id = :sid");
        $appStmt->execute(['sid' => $student['id']]);
        $application = $appStmt->fetch();

        // Fetch Allocation & Company Details
        $allocStmt = $db->prepare("
            SELECT a.*, c.name AS company_name, c.address AS company_address, c.state AS company_state, 
                   c.city AS company_city, c.industry AS company_industry, c.contact_person, c.phone AS company_phone, c.email AS company_email 
            FROM allocations a 
            JOIN companies c ON a.company_id = c.id 
            WHERE a.student_id = :sid AND a.status != 'Cancelled'
        ");
        $allocStmt->execute(['sid' => $student['id']]);
        $allocation = $allocStmt->fetch();

        // Fetch Recent Notifications
        $notifStmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5");
        $notifStmt->execute(['uid' => $userId]);
        $notifications = $notifStmt->fetchAll();

        // Fetch Uploaded Documents
        $docStmt = $db->prepare("SELECT * FROM documents WHERE student_id = :sid");
        $docStmt->execute(['sid' => $student['id']]);
        $documents = $docStmt->fetchAll();

        require __DIR__ . '/../Views/student/dashboard.php';
    }

    public function application(): void {
        Auth::requireRole('student');
        $userId = Auth::id();
        $db = Database::getInstance();

        $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();
        $application = $db->query("SELECT * FROM siwes_applications WHERE student_id = {$student['id']}")->fetch();

        require __DIR__ . '/../Views/student/application.php';
    }

    public function submitApplication(): void {
        Auth::requireRole('student');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('student/application');
        }

        $userId = Auth::id();
        $db = Database::getInstance();
        $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();

        $industryPref = Helper::sanitize($_POST['preferred_industry'] ?? '');
        $locationPref = Helper::sanitize($_POST['preferred_location'] ?? '');
        $notes = Helper::sanitize($_POST['notes'] ?? '');

        if (empty($industryPref) || empty($locationPref)) {
            Helper::setFlash('warning', 'Please select both preferred industry and location.');
            Helper::redirect('student/application');
        }

        // Upsert Application
        $stmt = $db->prepare("
            INSERT INTO siwes_applications (student_id, preferred_industry, preferred_location, notes, status, updated_at) 
            VALUES (:sid, :ind, :loc, :notes, 'Submitted', CURRENT_TIMESTAMP)
            ON CONFLICT (student_id) DO UPDATE SET 
                preferred_industry = EXCLUDED.preferred_industry,
                preferred_location = EXCLUDED.preferred_location,
                notes = EXCLUDED.notes,
                status = 'Submitted',
                updated_at = CURRENT_TIMESTAMP
        ");
        
        try {
            $stmt->execute([
                'sid' => $student['id'],
                'ind' => $industryPref,
                'loc' => $locationPref,
                'notes' => $notes
            ]);
            Helper::setFlash('success', 'SIWES Application submitted successfully!');
            Helper::redirect('dashboard');
        } catch (\Exception $e) {
            Helper::setFlash('danger', 'Error submitting application: ' . $e->getMessage());
            Helper::redirect('student/application');
        }
    }

    public function viewAllocation(): void {
        Auth::requireRole('student');
        $userId = Auth::id();
        $db = Database::getInstance();

        $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();
        $allocStmt = $db->prepare("
            SELECT a.*, c.name AS company_name, c.address AS company_address, c.state AS company_state, 
                   c.city AS company_city, c.industry AS company_industry, c.contact_person, c.phone AS company_phone, c.email AS company_email 
            FROM allocations a 
            JOIN companies c ON a.company_id = c.id 
            WHERE a.student_id = :sid AND a.status != 'Cancelled'
        ");
        $allocStmt->execute(['sid' => $student['id']]);
        $allocation = $allocStmt->fetch();

        require __DIR__ . '/../Views/student/allocation.php';
    }

    public function downloadLetter(): void {
        Auth::requireRole('student');
        $userId = Auth::id();
        $db = Database::getInstance();

        $studentStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS department_name 
            FROM students s 
            JOIN users u ON s.user_id = u.id 
            LEFT JOIN departments d ON s.department_id = d.id 
            WHERE s.user_id = :uid
        ");
        $studentStmt->execute(['uid' => $userId]);
        $student = $studentStmt->fetch();

        $allocStmt = $db->prepare("
            SELECT a.*, c.name AS company_name, c.address AS company_address, c.state AS company_state, c.city AS company_city, c.contact_person 
            FROM allocations a 
            JOIN companies c ON a.company_id = c.id 
            WHERE a.student_id = :sid AND a.status != 'Cancelled'
        ");
        $allocStmt->execute(['sid' => $student['id']]);
        $allocation = $allocStmt->fetch();

        if (!$allocation) {
            Helper::setFlash('warning', 'You have not been allocated to an organization yet.');
            Helper::redirect('dashboard');
        }

        require __DIR__ . '/../Views/student/allocation_letter.php';
    }
}

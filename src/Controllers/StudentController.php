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
        } elseif (Auth::role() === 'supervisor') {
            Helper::redirect('supervisor/dashboard');
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

        // Fetch Assigned Supervisor Details
        $supervisor = null;
        if (!empty($student['supervisor_id'])) {
            $supStmt = $db->prepare("
                SELECT u.full_name AS supervisor_name, u.email AS supervisor_email,
                       sup.phone AS supervisor_phone, sup.designation, sup.staff_id,
                       d.name AS supervisor_department,
                       s.supervisor_assigned_at
                FROM students s
                JOIN users u ON s.supervisor_id = u.id
                LEFT JOIN supervisors sup ON u.id = sup.user_id
                LEFT JOIN departments d ON sup.department_id = d.id
                WHERE s.id = :sid
            ");
            $supStmt->execute(['sid' => $student['id']]);
            $supervisor = $supStmt->fetch() ?: null;
        }

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

        $studentStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS department_name, d.code AS department_code
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.user_id = :uid
        ");
        $studentStmt->execute(['uid' => $userId]);
        $student = $studentStmt->fetch();

        $application = $db->query("SELECT * FROM siwes_applications WHERE student_id = {$student['id']}")->fetch();
        $documents = $db->query("SELECT * FROM documents WHERE student_id = {$student['id']} ORDER BY uploaded_at DESC")->fetchAll();

        // Check if student has already been allocated
        $allocStmt = $db->prepare("
            SELECT a.*, c.name AS company_name, c.address AS company_address, c.city AS company_city,
                   c.state AS company_state, c.industry AS company_industry, c.contact_person, c.phone AS company_phone,
                   c.email AS company_email
            FROM allocations a
            JOIN companies c ON a.company_id = c.id
            WHERE a.student_id = :sid AND a.status != 'Cancelled'
            LIMIT 1
        ");
        $allocStmt->execute(['sid' => $student['id']]);
        $allocation = $allocStmt->fetch() ?: null;

        // Supervisor details if attached
        $supervisor = null;
        if (!empty($student['supervisor_id'])) {
            $supStmt = $db->prepare("
                SELECT u.full_name AS supervisor_name, u.email AS supervisor_email,
                       sup.phone AS supervisor_phone, sup.designation, sup.staff_id,
                       d.name AS supervisor_department
                FROM users u
                LEFT JOIN supervisors sup ON u.id = sup.user_id
                LEFT JOIN departments d ON sup.department_id = d.id
                WHERE u.id = :sup_id
            ");
            $supStmt->execute(['sup_id' => $student['supervisor_id']]);
            $supervisor = $supStmt->fetch() ?: null;
        }

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

        $studentStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS department_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.user_id = :uid
        ");
        $studentStmt->execute(['uid' => $userId]);
        $student = $studentStmt->fetch();

        // Check if student has already been allocated to a company
        $allocCheck = $db->query("SELECT id FROM allocations WHERE student_id = {$student['id']} AND status != 'Cancelled'")->fetch();
        $app = $db->query("SELECT * FROM siwes_applications WHERE student_id = {$student['id']}")->fetch();
        $existingStatus = $app['status'] ?? 'Draft';

        if ($allocCheck || ($app && $existingStatus === 'Allocated')) {
            Helper::setFlash('danger', 'Your SIWES placement has already been officially allocated. You cannot modify or re-submit your application. Please contact your institutional supervisor for any complaints or adjustments.');
            Helper::redirect('student/application');
        }

        $action = Helper::sanitize($_POST['action'] ?? 'final_submit');
        $industryPref = Helper::sanitize($_POST['preferred_industry'] ?? '');
        $locationPref = Helper::sanitize($_POST['preferred_location'] ?? '');
        $notes = Helper::sanitize($_POST['notes'] ?? '');

        // Rule: Student can only drop SIWES application once. If active application exists and student hasn't deleted it, block re-submission.
        if ($action !== 'save_preferences' && $app && in_array($existingStatus, ['Submitted', 'Approved'], true)) {
            Helper::setFlash('warning', 'You have already submitted an active SIWES application. You can only submit once. If you need to make changes, please delete your previous application below to re-submit before placement allocation.');
            Helper::redirect('student/application');
        }

        if (empty($industryPref) || empty($locationPref)) {
            // Check if existing application has preferences
            if (!empty($app['preferred_industry']) && !empty($app['preferred_location'])) {
                $industryPref = $app['preferred_industry'];
                $locationPref = $app['preferred_location'];
                $notes = !empty($notes) ? $notes : ($app['notes'] ?? '');
            } else {
                Helper::setFlash('warning', 'Please select both preferred industry and location.');
                Helper::redirect('student/application?step=1');
            }
        }

        if ($action === 'save_preferences') {
            // Save preferences as draft and advance to document uploads
            $targetStatus = ($existingStatus === 'Submitted' || $existingStatus === 'Approved' || $existingStatus === 'Allocated') ? $existingStatus : 'Draft';
            $stmt = $db->prepare("
                INSERT INTO siwes_applications (student_id, preferred_industry, preferred_location, notes, status, updated_at) 
                VALUES (:sid, :ind, :loc, :notes, :st, CURRENT_TIMESTAMP)
                ON CONFLICT (student_id) DO UPDATE SET 
                    preferred_industry = EXCLUDED.preferred_industry,
                    preferred_location = EXCLUDED.preferred_location,
                    notes = EXCLUDED.notes,
                    updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([
                'sid'   => $student['id'],
                'ind'   => $industryPref,
                'loc'   => $locationPref,
                'notes' => $notes,
                'st'    => $targetStatus
            ]);

            Helper::setFlash('success', 'Preferences saved! Please upload your required documents in Step 2.');
            Helper::redirect('student/application?step=2');
        }

        // Final Submission Flow
        // 1. Verify that the student has uploaded at least one document
        $docCount = (int)$db->query("SELECT COUNT(*) FROM documents WHERE student_id = {$student['id']}")->fetchColumn();
        if ($docCount === 0) {
            Helper::setFlash('warning', 'Please upload at least one required document (e.g. SIWES Application Letter or Student ID Card) before submitting your application.');
            Helper::redirect('student/application?step=2');
        }

        // 2. Update Application Status to 'Submitted'
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
        $stmt->execute([
            'sid'   => $student['id'],
            'ind'   => $industryPref,
            'loc'   => $locationPref,
            'notes' => $notes
        ]);

        // 3. Notify Attached Supervisor if present
        $supervisorName = 'your institutional supervisor';
        if (!empty($student['supervisor_id'])) {
            $supUser = $db->query("SELECT full_name FROM users WHERE id = {$student['supervisor_id']}")->fetch();
            if ($supUser) {
                $supervisorName = $supUser['full_name'];
            }

            $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'Student Application & Documents Submitted', :msg, 'info')
            ")->execute([
                'uid' => $student['supervisor_id'],
                'msg' => "Student {$student['full_name']} ({$student['matric_number']}) from {$student['department_name']} has submitted their SIWES application and uploaded documents for review and placement allocation."
            ]);
        }

        // 4. Notify All Coordinators & Administrators
        $coords = $db->query("SELECT id FROM users WHERE role = 'coordinator' OR role = 'admin'")->fetchAll();
        foreach ($coords as $c) {
            $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'New SIWES Application & Documents', :msg, 'info')
            ")->execute([
                'uid' => $c['id'],
                'msg' => "New SIWES application and documents submitted by {$student['full_name']} ({$student['matric_number']}) - Department: {$student['department_name']}."
            ]);
        }

        // 5. Confirmation Notification to Student
        $db->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (:uid, 'Application Submitted Successfully', :msg, 'success')
        ")->execute([
            'uid' => $userId,
            'msg' => "Your SIWES application and uploaded documents have been forwarded to {$supervisorName} and the coordinator for review and placement allocation."
        ]);

        Helper::setFlash('success', "Your SIWES application and documents have been submitted successfully to {$supervisorName} and the coordinator!");
        Helper::redirect('student/application');
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

    public function deleteApplication(): void {
        Auth::requireRole('student');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('student/application');
        }

        $userId = Auth::id();
        $db = Database::getInstance();
        $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();
        if (!$student) {
            Helper::setFlash('danger', 'Student record not found.');
            Helper::redirect('dashboard');
        }

        // Check if student has already been allocated
        $alloc = $db->query("SELECT id FROM allocations WHERE student_id = {$student['id']} AND status != 'Cancelled'")->fetch();
        $app = $db->query("SELECT id, status FROM siwes_applications WHERE student_id = {$student['id']}")->fetch();

        if ($alloc || ($app && ($app['status'] ?? '') === 'Allocated')) {
            Helper::setFlash('danger', 'Action Denied: Your placement has already been officially allocated to a partner organization. You cannot drop or delete your application. If you have any inquiries or complaints, please contact your institutional supervisor.');
            Helper::redirect('student/application');
        }

        if ($app) {
            $db->prepare("DELETE FROM siwes_applications WHERE id = :id")->execute(['id' => $app['id']]);
            Helper::setFlash('success', 'Your previous SIWES application has been removed. You can now submit a fresh application with your updated placement preferences and documents.');
        } else {
            Helper::setFlash('info', 'No active application found to delete.');
        }

        Helper::redirect('student/application');
    }
}

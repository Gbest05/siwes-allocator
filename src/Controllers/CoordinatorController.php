<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class CoordinatorController {

    public function dashboard(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        $stats = [
            'total_students' => (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'total_companies' => (int)$db->query("SELECT COUNT(*) FROM companies")->fetchColumn(),
            'pending_apps' => (int)$db->query("SELECT COUNT(*) FROM siwes_applications WHERE status = 'Submitted' OR status = 'Under Review'")->fetchColumn(),
            'total_allocated' => (int)$db->query("SELECT COUNT(*) FROM allocations WHERE status = 'Allocated'")->fetchColumn(),
            'unallocated' => (int)$db->query("SELECT COUNT(*) FROM siwes_applications sa LEFT JOIN allocations a ON sa.student_id = a.student_id WHERE sa.status = 'Approved' AND a.id IS NULL")->fetchColumn()
        ];

        // Fetch recent student applications needing allocation
        $pendingAllocations = $db->query("
            SELECT sa.id AS app_id, sa.preferred_industry, sa.preferred_location, sa.status AS app_status,
                   s.id AS student_id, s.matric_number, s.programme, s.level, u.full_name, d.name AS dept_name
            FROM siwes_applications sa
            JOIN students s ON sa.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN allocations a ON sa.student_id = a.student_id AND a.status != 'Cancelled'
            WHERE sa.status = 'Approved' AND a.id IS NULL
            ORDER BY sa.submitted_at DESC
        ")->fetchAll();

        // Fetch active companies with slot capacity
        $companies = $db->query("SELECT * FROM companies WHERE status = 'active' ORDER BY name ASC")->fetchAll();

        require __DIR__ . '/../Views/coordinator/dashboard.php';
    }

    public function students(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        $search = Helper::sanitize($_GET['q'] ?? '');
        $deptId = (int)($_GET['dept_id'] ?? 0);

        $sql = "
            SELECT s.*, u.full_name, u.email, d.name AS dept_name,
                   sa.status AS app_status, a.status AS alloc_status, c.name AS company_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN siwes_applications sa ON s.id = sa.student_id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            LEFT JOIN companies c ON a.company_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :q OR s.matric_number LIKE :q OR u.email LIKE :q)";
            $params['q'] = "%{$search}%";
        }
        if ($deptId > 0) {
            $sql .= " AND s.department_id = :dept_id";
            $params['dept_id'] = $deptId;
        }

        $sql .= " ORDER BY s.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();

        require __DIR__ . '/../Views/coordinator/students.php';
    }

    public function applications(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        $applications = $db->query("
            SELECT sa.*, s.id AS student_id, s.matric_number, s.programme, s.level, u.full_name, u.email, d.name AS dept_name
            FROM siwes_applications sa
            JOIN students s ON sa.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            ORDER BY sa.submitted_at DESC
        ")->fetchAll();

        require __DIR__ . '/../Views/coordinator/applications.php';
    }

    public function updateApplicationStatus(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/applications');
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $newStatus = Helper::sanitize($_POST['status'] ?? '');
        $notes = Helper::sanitize($_POST['notes'] ?? '');

        if ($appId <= 0 || empty($newStatus)) {
            Helper::setFlash('warning', 'Invalid request parameters.');
            Helper::redirect('coordinator/applications');
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE siwes_applications SET status = :status, notes = :notes, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $stmt->execute(['status' => $newStatus, 'notes' => $notes, 'id' => $appId]);

        // Send notification to student
        $app = $db->query("SELECT student_id FROM siwes_applications WHERE id = {$appId}")->fetch();
        if ($app) {
            $student = $db->query("SELECT user_id FROM students WHERE id = {$app['student_id']}")->fetch();
            if ($student) {
                $notifStmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type)
                    VALUES (:uid, :title, :msg, :type)
                ");
                $notifStmt->execute([
                    'uid' => $student['user_id'],
                    'title' => "SIWES Application Status: {$newStatus}",
                    'msg' => "Your SIWES application has been updated to: '{$newStatus}'. Note: {$notes}",
                    'type' => $newStatus === 'Approved' ? 'success' : ($newStatus === 'Rejected' ? 'danger' : 'info')
                ]);
            }
        }

        Helper::setFlash('success', "Application status updated to {$newStatus}.");
        Helper::redirect('coordinator/applications');
    }
}

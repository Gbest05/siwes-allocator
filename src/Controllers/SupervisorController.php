<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class SupervisorController {

    public function dashboard(): void {
        Auth::requireRole('supervisor');
        $userId = Auth::id();
        $db = Database::getInstance();

        // 1. Fetch Supervisor Profile
        $stmt = $db->prepare("
            SELECT u.*, sup.staff_id, sup.phone AS supervisor_phone, sup.designation,
                   d.name AS department_name, d.code AS department_code
            FROM users u
            LEFT JOIN supervisors sup ON u.id = sup.user_id
            LEFT JOIN departments d ON sup.department_id = d.id
            WHERE u.id = :uid
        ");
        $stmt->execute(['uid' => $userId]);
        $supervisor = $stmt->fetch();

        // 2. Compute Supervisor Metrics
        $totalAssigned = (int)$db->query("
            SELECT COUNT(*) FROM students WHERE supervisor_id = {$userId}
        ")->fetchColumn();

        $totalPlaced = (int)$db->query("
            SELECT COUNT(DISTINCT s.id)
            FROM students s
            JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            WHERE s.supervisor_id = {$userId}
        ")->fetchColumn();

        $pendingPlacement = max(0, $totalAssigned - $totalPlaced);

        $distinctDepts = (int)$db->query("
            SELECT COUNT(DISTINCT department_id) FROM students WHERE supervisor_id = {$userId}
        ")->fetchColumn();

        $stats = [
            'total_assigned'   => $totalAssigned,
            'total_placed'     => $totalPlaced,
            'pending_placement'=> $pendingPlacement,
            'distinct_depts'   => $distinctDepts
        ];

        // 3. Fetch Assigned Students with Full Contact and Placement Information
        $studentsStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS dept_name, d.code AS dept_code,
                   sa.status AS application_status, sa.preferred_industry, sa.preferred_location,
                   a.status AS allocation_status, a.compatibility_score, a.start_date, a.end_date,
                   c.name AS company_name, c.state AS company_state, c.city AS company_city,
                   c.industry AS company_industry, c.contact_person, c.phone AS company_phone, c.email AS company_email
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN siwes_applications sa ON s.id = sa.student_id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            LEFT JOIN companies c ON a.company_id = c.id
            WHERE s.supervisor_id = :uid
            ORDER BY u.full_name ASC
        ");
        $studentsStmt->execute(['uid' => $userId]);
        $assignedStudents = $studentsStmt->fetchAll();

        // 4. Fetch Recent Notifications
        $notifStmt = $db->prepare("
            SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 5
        ");
        $notifStmt->execute(['uid' => $userId]);
        $notifications = $notifStmt->fetchAll();

        require __DIR__ . '/../Views/supervisor/dashboard.php';
    }

    public function students(): void {
        Auth::requireRole('supervisor');
        $userId = Auth::id();
        $db = Database::getInstance();

        $search = Helper::sanitize($_GET['q'] ?? '');
        $statusFilter = Helper::sanitize($_GET['status'] ?? '');

        $sql = "
            SELECT s.*, u.full_name, u.email, d.name AS dept_name, d.code AS dept_code,
                   sa.status AS application_status, sa.preferred_industry, sa.preferred_location,
                   a.status AS allocation_status, a.compatibility_score, a.start_date, a.end_date,
                   c.name AS company_name, c.state AS company_state, c.city AS company_city,
                   c.industry AS company_industry, c.contact_person, c.phone AS company_phone, c.email AS company_email
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN siwes_applications sa ON s.id = sa.student_id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            LEFT JOIN companies c ON a.company_id = c.id
            WHERE s.supervisor_id = :uid
        ";
        $params = ['uid' => $userId];

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :q OR s.matric_number LIKE :q OR u.email LIKE :q OR c.name LIKE :q)";
            $params['q'] = "%{$search}%";
        }

        if ($statusFilter === 'placed') {
            $sql .= " AND a.status = 'Allocated'";
        } elseif ($statusFilter === 'pending') {
            $sql .= " AND (a.id IS NULL OR a.status != 'Allocated')";
        }

        $sql .= " ORDER BY u.full_name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        require __DIR__ . '/../Views/supervisor/students.php';
    }
}

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

    /**
     * Coordinator Supervisor Management & Assignment Roster
     */
    public function supervisors(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        // 1. Fetch All Supervisors with Assigned Student Count
        $supervisors = $db->query("
            SELECT u.id, u.full_name, u.email, sup.staff_id, sup.phone, sup.designation, sup.max_capacity,
                   d.name AS dept_name, d.code AS dept_code,
                   (SELECT COUNT(*) FROM students WHERE supervisor_id = u.id) AS assigned_student_count
            FROM users u
            LEFT JOIN supervisors sup ON u.id = sup.user_id
            LEFT JOIN departments d ON sup.department_id = d.id
            WHERE u.role = 'supervisor'
            ORDER BY u.full_name ASC
        ")->fetchAll();

        // 2. Fetch Students with Current Supervisor Assignment Status
        $search = Helper::sanitize($_GET['q'] ?? '');
        $statusFilter = Helper::sanitize($_GET['status'] ?? '');
        $supFilter = (int)($_GET['supervisor_id'] ?? 0);

        $sql = "
            SELECT s.*, u.full_name AS student_name, u.email AS student_email,
                   d.name AS dept_name, d.code AS dept_code,
                   sup_user.id AS supervisor_user_id, sup_user.full_name AS supervisor_name,
                   c.name AS company_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN users sup_user ON s.supervisor_id = sup_user.id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            LEFT JOIN companies c ON a.company_id = c.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :q OR s.matric_number LIKE :q OR u.email LIKE :q)";
            $params['q'] = "%{$search}%";
        }

        if ($statusFilter === 'assigned') {
            $sql .= " AND s.supervisor_id IS NOT NULL";
        } elseif ($statusFilter === 'unassigned') {
            $sql .= " AND s.supervisor_id IS NULL";
        }

        if ($supFilter > 0) {
            $sql .= " AND s.supervisor_id = :sup_filter";
            $params['sup_filter'] = $supFilter;
        }

        $sql .= " ORDER BY s.id ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();

        // 3. Overall Statistics
        $totalStudents = (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $assignedStudents = (int)$db->query("SELECT COUNT(*) FROM students WHERE supervisor_id IS NOT NULL")->fetchColumn();
        $unassignedStudents = max(0, $totalStudents - $assignedStudents);
        $totalSupervisors = count($supervisors);

        $stats = [
            'total_supervisors'   => $totalSupervisors,
            'total_students'      => $totalStudents,
            'assigned_students'   => $assignedStudents,
            'unassigned_students' => $unassignedStudents
        ];

        $departments = $db->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll();

        require __DIR__ . '/../Views/coordinator/supervisors.php';
    }

    /**
     * Manually Assign a Single Student or Multiple Students to a Chosen Supervisor
     */
    public function assignSupervisor(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/supervisors');
        }

        $supervisorId = (int)($_POST['supervisor_id'] ?? 0);
        $studentId = (int)($_POST['student_id'] ?? 0);
        $studentIds = $_POST['student_ids'] ?? [];

        if ($supervisorId <= 0) {
            Helper::setFlash('warning', 'Please select a valid supervisor.');
            Helper::redirect('coordinator/supervisors');
        }

        $db = Database::getInstance();

        // Verify supervisor exists and has role 'supervisor'
        $supStmt = $db->prepare("SELECT id, full_name, email FROM users WHERE id = :id AND role = 'supervisor'");
        $supStmt->execute(['id' => $supervisorId]);
        $supervisor = $supStmt->fetch();

        if (!$supervisor) {
            Helper::setFlash('danger', 'Selected supervisor was not found.');
            Helper::redirect('coordinator/supervisors');
        }

        // Normalize student list (single vs bulk)
        $targets = [];
        if (!empty($studentIds) && is_array($studentIds)) {
            $targets = array_map('intval', $studentIds);
        } elseif ($studentId > 0) {
            $targets = [$studentId];
        }

        if (empty($targets)) {
            Helper::setFlash('warning', 'Please select at least one student to assign.');
            Helper::redirect('coordinator/supervisors');
        }

        try {
            $db->beginTransaction();
            $upStmt = $db->prepare("
                UPDATE students
                SET supervisor_id = :sup_id,
                    supervisor_assigned_at = CURRENT_TIMESTAMP,
                    supervisor_assigned_by = :coord_id
                WHERE id = :st_id
            ");

            $notifStudentStmt = $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'Institutional Supervisor Assigned', :msg, 'success')
            ");

            $count = 0;
            foreach ($targets as $stId) {
                $upStmt->execute([
                    'sup_id'   => $supervisorId,
                    'coord_id' => Auth::id(),
                    'st_id'    => $stId
                ]);
                $count++;

                // Notify student
                $stUser = $db->query("SELECT user_id FROM students WHERE id = {$stId}")->fetch();
                if ($stUser) {
                    $notifStudentStmt->execute([
                        'uid' => $stUser['user_id'],
                        'msg' => "Institutional Supervisor {$supervisor['full_name']} has been assigned to you by the department coordinator."
                    ]);
                }
            }

            // Notify supervisor
            $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'New Students Assigned', :msg, 'info')
            ")->execute([
                'uid' => $supervisorId,
                'msg' => "The coordinator has manually assigned {$count} student(s) to your supervision roster."
            ]);

            $db->commit();
            Helper::setFlash('success', "Successfully assigned {$count} student(s) to {$supervisor['full_name']}.");
        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Assignment failed: ' . $e->getMessage());
        }

        Helper::redirect('coordinator/supervisors');
    }

    /**
     * Automatically Distribute Students to Available Supervisors Equally
     */
    public function autoDistributeSupervisors(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/supervisors');
        }

        $mode = Helper::sanitize($_POST['mode'] ?? 'all'); // 'all' or 'unassigned'
        $db = Database::getInstance();

        // 1. Fetch All Active Supervisors
        $supervisors = $db->query("
            SELECT u.id, u.full_name, u.email
            FROM users u
            WHERE u.role = 'supervisor'
            ORDER BY u.id ASC
        ")->fetchAll();

        $numSupervisors = count($supervisors);
        if ($numSupervisors === 0) {
            Helper::setFlash('danger', 'No supervisors found in the system. Please add or register supervisors first.');
            Helper::redirect('coordinator/supervisors');
        }

        // 2. Fetch Eligible Students
        if ($mode === 'unassigned') {
            $students = $db->query("
                SELECT id, user_id FROM students
                WHERE supervisor_id IS NULL
                ORDER BY department_id ASC, matric_number ASC
            ")->fetchAll();
        } else {
            // Rebalance all students
            $students = $db->query("
                SELECT id, user_id FROM students
                ORDER BY department_id ASC, matric_number ASC
            ")->fetchAll();
        }

        $numStudents = count($students);
        if ($numStudents === 0) {
            Helper::setFlash('info', 'No eligible students available to distribute.');
            Helper::redirect('coordinator/supervisors');
        }

        try {
            $db->beginTransaction();

            $upStmt = $db->prepare("
                UPDATE students
                SET supervisor_id = :sup_id,
                    supervisor_assigned_at = CURRENT_TIMESTAMP,
                    supervisor_assigned_by = :coord_id
                WHERE id = :st_id
            ");

            $notifStudentStmt = $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'Supervisor Assigned (Auto)', :msg, 'success')
            ");

            $assignedCounts = [];
            foreach ($supervisors as $sup) {
                $assignedCounts[$sup['id']] = 0;
            }

            // Equal Fair Round-Robin Distribution
            foreach ($students as $index => $st) {
                $assignedSupervisor = $supervisors[$index % $numSupervisors];
                $supId = (int)$assignedSupervisor['id'];
                $assignedCounts[$supId]++;

                $upStmt->execute([
                    'sup_id'   => $supId,
                    'coord_id' => Auth::id(),
                    'st_id'    => $st['id']
                ]);

                // Student notification
                $notifStudentStmt->execute([
                    'uid' => $st['user_id'],
                    'msg' => "You have been assigned to institutional supervisor {$assignedSupervisor['full_name']} for your SIWES supervision."
                ]);
            }

            // Supervisor summary notifications
            foreach ($supervisors as $sup) {
                $count = $assignedCounts[$sup['id']] ?? 0;
                if ($count > 0) {
                    $db->prepare("
                        INSERT INTO notifications (user_id, title, message, type)
                        VALUES (:uid, 'Students Assigned by Coordinator', :msg, 'info')
                    ")->execute([
                        'uid' => $sup['id'],
                        'msg' => "The coordinator has assigned {$count} student(s) to your supervision roster via automatic equal distribution."
                    ]);
                }
            }

            $db->commit();

            // Calculate per supervisor share description
            $shareText = [];
            foreach ($supervisors as $sup) {
                $c = $assignedCounts[$sup['id']] ?? 0;
                $shareText[] = "{$sup['full_name']}: {$c}";
            }

            $summary = implode(', ', $shareText);
            Helper::setFlash('success', "Equal Distribution Successful! Distributed {$numStudents} students across {$numSupervisors} supervisors ({$summary}).");

        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Automatic distribution failed: ' . $e->getMessage());
        }

        Helper::redirect('coordinator/supervisors');
    }

    /**
     * Unassign a Student from Their Supervisor
     */
    public function unassignSupervisor(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/supervisors');
        }

        $studentId = (int)($_POST['student_id'] ?? 0);
        if ($studentId <= 0) {
            Helper::setFlash('warning', 'Invalid student ID.');
            Helper::redirect('coordinator/supervisors');
        }

        $db = Database::getInstance();
        $st = $db->query("SELECT user_id, supervisor_id FROM students WHERE id = {$studentId}")->fetch();
        if ($st) {
            $db->prepare("
                UPDATE students 
                SET supervisor_id = NULL, supervisor_assigned_at = NULL, supervisor_assigned_by = NULL
                WHERE id = :id
            ")->execute(['id' => $studentId]);

            // Notify student
            $db->prepare("
                INSERT INTO notifications (user_id, title, message, type)
                VALUES (:uid, 'Supervisor Unassigned', 'Your institutional supervisor assignment has been reset by the coordinator.', 'warning')
            ")->execute(['uid' => $st['user_id']]);

            Helper::setFlash('info', 'Student supervisor assignment has been removed.');
        }

        Helper::redirect('coordinator/supervisors');
    }

    /**
     * Register a New Supervisor Account from Coordinator Portal
     */
    public function createSupervisor(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/supervisors');
        }

        $fullName = Helper::sanitize($_POST['full_name'] ?? '');
        $email = Helper::sanitize($_POST['email'] ?? '');
        $phone = Helper::sanitize($_POST['phone'] ?? '');
        $staffId = strtoupper(Helper::sanitize($_POST['staff_id'] ?? ''));
        $designation = Helper::sanitize($_POST['designation'] ?? 'Institution Supervisor');
        $deptId = (int)($_POST['department_id'] ?? 0);
        $password = $_POST['password'] ?? 'password123';

        if (empty($fullName) || empty($email)) {
            Helper::setFlash('warning', 'Full Name and Email are required.');
            Helper::redirect('coordinator/supervisors');
        }

        $db = Database::getInstance();

        // Check if email already exists
        $check = $db->prepare("SELECT id FROM users WHERE email = :email");
        $check->execute(['email' => $email]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'A user account with this email address already exists.');
            Helper::redirect('coordinator/supervisors');
        }

        try {
            $db->beginTransaction();
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $userStmt = $db->prepare("
                INSERT INTO users (full_name, email, password_hash, role)
                VALUES (:name, :email, :hash, 'supervisor')
            ");
            $userStmt->execute([
                'name'  => $fullName,
                'email' => $email,
                'hash'  => $hash
            ]);
            $userId = (int)$db->lastInsertId();

            $supStmt = $db->prepare("
                INSERT INTO supervisors (user_id, department_id, staff_id, phone, designation)
                VALUES (:uid, :dept, :staff, :phone, :desig)
            ");
            $supStmt->execute([
                'uid'   => $userId,
                'dept'  => $deptId > 0 ? $deptId : null,
                'staff' => !empty($staffId) ? $staffId : 'SUP/' . rand(100, 999),
                'phone' => $phone,
                'desig' => $designation
            ]);

            $db->commit();
            Helper::setFlash('success', "Supervisor {$fullName} created successfully with default password: password123");
        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Failed to create supervisor: ' . $e->getMessage());
        }

        Helper::redirect('coordinator/supervisors');
    }

    /**
     * View and Edit Official SIWES Application & Placement Letter Content Template
     */
    public function letterTemplate(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $settings = Helper::getAllSettings();
        require __DIR__ . '/../Views/coordinator/letter_template.php';
    }

    /**
     * Save Updates to Official SIWES Application & Placement Letter Content Template
     */
    public function updateLetterTemplate(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/letter-template');
        }

        $fields = [
            'letter_institution_name',
            'letter_directorate_title',
            'letter_contact_info',
            'letter_ref_prefix',
            'letter_title',
            'letter_salutation',
            'letter_body_intro',
            'letter_body_training',
            'letter_body_conclusion',
            'letter_closing',
            'letter_signatory_name',
            'letter_signatory_title',
            'letter_seal_text'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                Helper::setSetting($field, trim($_POST[$field]));
            }
        }

        Helper::setFlash('success', 'Official SIWES Application & Placement Letter content updated successfully!');
        $redirectRoute = Auth::role() === 'admin' && str_contains($_SERVER['REQUEST_URI'] ?? '', 'admin/letter-template')
            ? 'admin/letter-template'
            : 'coordinator/letter-template';
        Helper::redirect($redirectRoute);
    }
}



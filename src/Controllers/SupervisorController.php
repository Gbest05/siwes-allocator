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

        // 3. Fetch Assigned Students with Full Contact, Application and Placement Information
        $studentsStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS dept_name, d.code AS dept_code,
                   sa.id AS app_id, sa.status AS application_status, sa.preferred_industry, sa.preferred_location,
                   sa.notes AS app_notes, sa.submitted_at AS app_submitted_at,
                   a.id AS allocation_id, a.status AS allocation_status, a.compatibility_score, a.start_date, a.end_date,
                   c.id AS current_company_id, c.name AS company_name, c.state AS company_state, c.city AS company_city,
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

        // 4. Fetch Uploaded Documents for Assigned Students
        $studentDocs = [];
        if (!empty($assignedStudents)) {
            $studentIds = array_column($assignedStudents, 'id');
            $inClause = implode(',', array_map('intval', $studentIds));
            $docs = $db->query("SELECT * FROM documents WHERE student_id IN ({$inClause}) ORDER BY uploaded_at DESC")->fetchAll();
            foreach ($docs as $doc) {
                $studentDocs[$doc['student_id']][] = $doc;
            }
        }

        // 5. Fetch Active Companies with Available Slots
        $companies = $db->query("
            SELECT * FROM companies 
            WHERE status = 'active' AND available_slots > 0 
            ORDER BY name ASC
        ")->fetchAll();

        // 6. Fetch Recent Notifications
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
                   sa.id AS app_id, sa.status AS application_status, sa.preferred_industry, sa.preferred_location,
                   sa.notes AS app_notes, sa.submitted_at AS app_submitted_at,
                   a.id AS allocation_id, a.status AS allocation_status, a.compatibility_score, a.start_date, a.end_date,
                   c.id AS current_company_id, c.name AS company_name, c.state AS company_state, c.city AS company_city,
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

        // Uploaded documents
        $studentDocs = [];
        if (!empty($students)) {
            $studentIds = array_column($students, 'id');
            $inClause = implode(',', array_map('intval', $studentIds));
            $docs = $db->query("SELECT * FROM documents WHERE student_id IN ({$inClause}) ORDER BY uploaded_at DESC")->fetchAll();
            foreach ($docs as $doc) {
                $studentDocs[$doc['student_id']][] = $doc;
            }
        }

        // Active companies with slots
        $companies = $db->query("
            SELECT * FROM companies 
            WHERE status = 'active' AND available_slots > 0 
            ORDER BY name ASC
        ")->fetchAll();

        require __DIR__ . '/../Views/supervisor/students.php';
    }

    /**
     * Supervisor: Review and Approve/Reject Student SIWES Application
     */
    public function reviewApplication(): void {
        Auth::requireRole('supervisor');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('supervisor/dashboard');
        }

        $supervisorId = Auth::id();
        $studentId = (int)($_POST['student_id'] ?? 0);
        $appId = (int)($_POST['application_id'] ?? 0);
        $action = Helper::sanitize($_POST['action'] ?? ''); // 'Approved' or 'Rejected'
        $notes = Helper::sanitize($_POST['notes'] ?? '');
        $redirect = Helper::sanitize($_POST['redirect_to'] ?? 'supervisor/dashboard');

        if ($studentId <= 0 || !in_array($action, ['Approved', 'Rejected'], true)) {
            Helper::setFlash('warning', 'Invalid review request parameters.');
            Helper::redirect($redirect);
        }

        $db = Database::getInstance();

        // Verify student is assigned to this supervisor
        $stStmt = $db->prepare("SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = :sid AND s.supervisor_id = :sup_id");
        $stStmt->execute(['sid' => $studentId, 'sup_id' => $supervisorId]);
        $student = $stStmt->fetch();

        if (!$student) {
            Helper::setFlash('danger', 'Access denied. This student is not in your assigned roster.');
            Helper::redirect($redirect);
        }

        // Ensure application exists
        if ($appId <= 0) {
            $app = $db->query("SELECT id FROM siwes_applications WHERE student_id = {$studentId} ORDER BY id DESC LIMIT 1")->fetch();
            $appId = $app ? (int)$app['id'] : 0;
        }

        if ($appId > 0) {
            $stmt = $db->prepare("
                UPDATE siwes_applications 
                SET status = :status, notes = :notes, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id
            ");
            $stmt->execute([
                'status' => $action,
                'notes'  => $notes,
                'id'     => $appId
            ]);
        }

        // Fetch supervisor full name
        $supUser = $db->query("SELECT full_name FROM users WHERE id = {$supervisorId}")->fetch();
        $supName = $supUser['full_name'] ?? 'Supervisor';

        // 1. Notify Student
        $notifStmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, :title, :msg, :type)");
        $notifStmt->execute([
            'uid'   => $student['user_id'],
            'title' => "SIWES Application " . ($action === 'Approved' ? 'Approved by Supervisor' : 'Reviewed by Supervisor'),
            'msg'   => "Institutional supervisor {$supName} has marked your SIWES application as '{$action}'." . (!empty($notes) ? " Note: {$notes}" : ""),
            'type'  => $action === 'Approved' ? 'success' : 'warning'
        ]);

        // 2. Notify Coordinator(s)
        $coords = $db->query("SELECT id FROM users WHERE role IN ('coordinator', 'admin')")->fetchAll();
        foreach ($coords as $coord) {
            $notifStmt->execute([
                'uid'   => $coord['id'],
                'title' => "Application {$action} by Supervisor",
                'msg'   => "Supervisor {$supName} marked SIWES application for student {$student['full_name']} ({$student['matric_number']}) as '{$action}'.",
                'type'  => 'info'
            ]);
        }

        Helper::setFlash('success', "Student application marked as '{$action}' successfully. Notifications sent to student and coordinator.");
        Helper::redirect($redirect);
    }

    /**
     * Supervisor: Manually Assign a Partner Company Placement to Student
     */
    public function allocatePlacement(): void {
        Auth::requireRole('supervisor');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('supervisor/dashboard');
        }

        $supervisorId = Auth::id();
        $studentId = (int)($_POST['student_id'] ?? 0);
        $companyId = (int)($_POST['company_id'] ?? 0);
        $startDate = Helper::sanitize($_POST['start_date'] ?? date('Y-m-d'));
        $endDate = Helper::sanitize($_POST['end_date'] ?? date('Y-m-d', strtotime('+6 months')));
        $redirect = Helper::sanitize($_POST['redirect_to'] ?? 'supervisor/dashboard');

        if ($studentId <= 0 || $companyId <= 0) {
            Helper::setFlash('warning', 'Please select a valid student and partner company.');
            Helper::redirect($redirect);
        }

        $db = Database::getInstance();

        // Verify student is assigned to this supervisor
        $stStmt = $db->prepare("
            SELECT s.*, u.full_name, u.email, d.name AS dept_name
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE s.id = :sid AND s.supervisor_id = :sup_id
        ");
        $stStmt->execute(['sid' => $studentId, 'sup_id' => $supervisorId]);
        $student = $stStmt->fetch();

        if (!$student) {
            Helper::setFlash('danger', 'Access denied. This student is not assigned to your supervision.');
            Helper::redirect($redirect);
        }

        // Verify company has available slots
        $cmpStmt = $db->prepare("SELECT * FROM companies WHERE id = :cid AND status = 'active'");
        $cmpStmt->execute(['cid' => $companyId]);
        $company = $cmpStmt->fetch();

        if (!$company || (int)$company['available_slots'] <= 0) {
            Helper::setFlash('danger', 'The selected partner company has no available placement slots.');
            Helper::redirect($redirect);
        }

        // Fetch application details
        $appStmt = $db->prepare("SELECT * FROM siwes_applications WHERE student_id = :sid ORDER BY id DESC LIMIT 1");
        $appStmt->execute(['sid' => $studentId]);
        $app = $appStmt->fetch();

        $appId = $app ? (int)$app['id'] : null;
        $prefIndustry = $app['preferred_industry'] ?? 'Information Technology';
        $prefLocation = $app['preferred_location'] ?? 'Lagos';

        // Calculate Compatibility Match Score
        $matchResult = Helper::calculateMatchScore(
            $student['dept_name'] ?? 'General',
            $prefIndustry,
            $prefLocation,
            $company['industry'],
            $company['state'],
            $company['city'],
            (int)$company['available_slots']
        );

        try {
            $db->beginTransaction();

            // Check if student already has an active allocation
            $existingAlloc = $db->query("SELECT * FROM allocations WHERE student_id = {$studentId} AND status != 'Cancelled'")->fetch();

            if ($existingAlloc) {
                // If company changed, return slot to previous company and take slot from new company
                if ((int)$existingAlloc['company_id'] !== $companyId) {
                    $db->exec("UPDATE companies SET available_slots = available_slots + 1 WHERE id = {$existingAlloc['company_id']}");
                    $db->exec("UPDATE companies SET available_slots = available_slots - 1 WHERE id = {$companyId}");
                }
                $allocStmt = $db->prepare("
                    UPDATE allocations 
                    SET company_id = :cid, application_id = :aid, compatibility_score = :score,
                        match_reasons = :reasons, status = 'Allocated', allocated_by = :by,
                        start_date = :sdate, end_date = :edate, updated_at = CURRENT_TIMESTAMP
                    WHERE id = :id
                ");
                $allocStmt->execute([
                    'cid'     => $companyId,
                    'aid'     => $appId,
                    'score'   => $matchResult['total_score'],
                    'reasons' => json_encode($matchResult),
                    'by'      => $supervisorId,
                    'sdate'   => $startDate,
                    'edate'   => $endDate,
                    'id'      => $existingAlloc['id']
                ]);
            } else {
                // Deduct slot from company
                $db->exec("UPDATE companies SET available_slots = available_slots - 1 WHERE id = {$companyId}");

                // Insert new allocation
                $allocStmt = $db->prepare("
                    INSERT INTO allocations (application_id, student_id, company_id, compatibility_score, match_reasons, status, allocated_by, start_date, end_date)
                    VALUES (:aid, :sid, :cid, :score, :reasons, 'Allocated', :by, :sdate, :edate)
                ");
                $allocStmt->execute([
                    'aid'     => $appId,
                    'sid'     => $studentId,
                    'cid'     => $companyId,
                    'score'   => $matchResult['total_score'],
                    'reasons' => json_encode($matchResult),
                    'by'      => $supervisorId,
                    'sdate'   => $startDate,
                    'edate'   => $endDate
                ]);
            }

            // Update application status
            if ($appId) {
                $db->exec("UPDATE siwes_applications SET status = 'Allocated', updated_at = CURRENT_TIMESTAMP WHERE id = {$appId}");
            }

            $supUser = $db->query("SELECT full_name FROM users WHERE id = {$supervisorId}")->fetch();
            $supName = $supUser['full_name'] ?? 'Supervisor';

            // Notify Student
            $notifStmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, :title, :msg, :type)");
            $notifStmt->execute([
                'uid'   => $student['user_id'],
                'title' => 'SIWES Placement Allocated!',
                'msg'   => "Congratulations! Your supervisor {$supName} has assigned your SIWES placement to {$company['name']} ({$company['state']}). Compatibility Match Score: {$matchResult['total_score']}%.",
                'type'  => 'success'
            ]);

            // Notify Coordinators
            $coords = $db->query("SELECT id FROM users WHERE role IN ('coordinator', 'admin')")->fetchAll();
            foreach ($coords as $coord) {
                $notifStmt->execute([
                    'uid'   => $coord['id'],
                    'title' => 'Supervisor Assigned Placement',
                    'msg'   => "Supervisor {$supName} has assigned student {$student['full_name']} ({$student['matric_number']}) to {$company['name']} ({$matchResult['total_score']}% match).",
                    'type'  => 'info'
                ]);
            }

            $db->commit();
            Helper::setFlash('success', "Student {$student['full_name']} successfully placed at {$company['name']} with {$matchResult['total_score']}% compatibility match score!");
        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Placement allocation failed: ' . $e->getMessage());
        }

        Helper::redirect($redirect);
    }

    /**
     * Supervisor: Automatically Assign Placements to Unplaced Assigned Students
     */
    public function autoAssignPlacements(): void {
        Auth::requireRole('supervisor');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('supervisor/dashboard');
        }

        $supervisorId = Auth::id();
        $studentId = (int)($_POST['student_id'] ?? 0); // single student ID or 0 for all
        $redirect = Helper::sanitize($_POST['redirect_to'] ?? 'supervisor/dashboard');
        $db = Database::getInstance();

        // 1. Fetch unplaced students assigned to this supervisor
        $sql = "
            SELECT s.*, u.full_name, u.email, d.name AS dept_name,
                   sa.id AS app_id, sa.preferred_industry, sa.preferred_location
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN siwes_applications sa ON s.id = sa.student_id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            WHERE s.supervisor_id = :sup_id AND a.id IS NULL
        ";
        $params = ['sup_id' => $supervisorId];

        if ($studentId > 0) {
            $sql .= " AND s.id = :sid";
            $params['sid'] = $studentId;
        }

        $sql .= " ORDER BY s.id ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $unplacedStudents = $stmt->fetchAll();

        if (empty($unplacedStudents)) {
            Helper::setFlash('info', 'No unplaced students found awaiting placement.');
            Helper::redirect($redirect);
        }

        // 2. Fetch active partner companies with available slots
        $companies = $db->query("SELECT * FROM companies WHERE status = 'active' AND available_slots > 0 ORDER BY available_slots DESC")->fetchAll();

        if (empty($companies)) {
            Helper::setFlash('danger', 'No partner companies with available placement slots exist in the system.');
            Helper::redirect($redirect);
        }

        $supUser = $db->query("SELECT full_name FROM users WHERE id = {$supervisorId}")->fetch();
        $supName = $supUser['full_name'] ?? 'Supervisor';

        $placedCount = 0;
        $skippedCount = 0;

        try {
            $db->beginTransaction();

            $allocInsert = $db->prepare("
                INSERT INTO allocations (application_id, student_id, company_id, compatibility_score, match_reasons, status, allocated_by, start_date, end_date)
                VALUES (:aid, :sid, :cid, :score, :reasons, 'Allocated', :by, :sdate, :edate)
            ");

            $appUpdate = $db->prepare("UPDATE siwes_applications SET status = 'Allocated', updated_at = CURRENT_TIMESTAMP WHERE id = :aid");
            $companySlotUpdate = $db->prepare("UPDATE companies SET available_slots = available_slots - 1 WHERE id = :cid");
            $notifStmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (:uid, :title, :msg, :type)");

            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+6 months'));

            foreach ($unplacedStudents as $st) {
                // Find best matching company with remaining slots > 0
                $bestCompanyKey = null;
                $bestScore = -1;
                $bestMatchResult = null;

                foreach ($companies as $key => $cmp) {
                    if ($cmp['available_slots'] <= 0) {
                        continue;
                    }

                    $res = Helper::calculateMatchScore(
                        $st['dept_name'] ?? 'General',
                        $st['preferred_industry'] ?? 'Information Technology',
                        $st['preferred_location'] ?? 'Lagos',
                        $cmp['industry'],
                        $cmp['state'],
                        $cmp['city'],
                        $cmp['available_slots']
                    );

                    if ($res['total_score'] > $bestScore) {
                        $bestScore = $res['total_score'];
                        $bestMatchResult = $res;
                        $bestCompanyKey = $key;
                    }
                }

                if ($bestCompanyKey !== null) {
                    $chosenCompany = &$companies[$bestCompanyKey];

                    // Assign student to this best matching company
                    $allocInsert->execute([
                        'aid'     => $st['app_id'] ?: null,
                        'sid'     => $st['id'],
                        'cid'     => $chosenCompany['id'],
                        'score'   => $bestMatchResult['total_score'],
                        'reasons' => json_encode($bestMatchResult),
                        'by'      => $supervisorId,
                        'sdate'   => $startDate,
                        'edate'   => $endDate
                    ]);

                    if ($st['app_id']) {
                        $appUpdate->execute(['aid' => $st['app_id']]);
                    }

                    $companySlotUpdate->execute(['cid' => $chosenCompany['id']]);
                    $chosenCompany['available_slots']--; // Decrement in local tracking

                    // Notify Student
                    $notifStmt->execute([
                        'uid'   => $st['user_id'],
                        'title' => 'SIWES Placement Auto-Allocated!',
                        'msg'   => "Congratulations! You have been auto-matched and placed at {$chosenCompany['name']} ({$chosenCompany['state']}) by Supervisor {$supName}. Compatibility Match Score: {$bestMatchResult['total_score']}%.",
                        'type'  => 'success'
                    ]);

                    // Notify Coordinators
                    $coords = $db->query("SELECT id FROM users WHERE role IN ('coordinator', 'admin')")->fetchAll();
                    foreach ($coords as $coord) {
                        $notifStmt->execute([
                            'uid'   => $coord['id'],
                            'title' => 'Supervisor Auto-Allocated Placement',
                            'msg'   => "Supervisor {$supName} auto-matched student {$st['full_name']} ({$st['matric_number']}) to {$chosenCompany['name']} ({$bestMatchResult['total_score']}% match).",
                            'type'  => 'info'
                        ]);
                    }

                    $placedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $db->commit();

            if ($placedCount > 0) {
                Helper::setFlash('success', "Auto-placement completed! Successfully matched and placed {$placedCount} student(s) to partner organizations." . ($skippedCount > 0 ? " ({$skippedCount} students could not be placed due to company capacity limits)" : ""));
            } else {
                Helper::setFlash('warning', "Could not auto-place students. No companies with available slots match the placement requirements.");
            }
        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Automatic placement failed: ' . $e->getMessage());
        }

        Helper::redirect($redirect);
    }
}

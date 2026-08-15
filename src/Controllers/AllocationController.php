<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class AllocationController {

    public function index(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        // 1. Fetch all current allocations
        $allocations = $db->query("
            SELECT a.*, s.matric_number, u.full_name AS student_name, d.name AS dept_name,
                   c.name AS company_name, c.state AS company_state, c.city AS company_city
            FROM allocations a
            JOIN students s ON a.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            JOIN companies c ON a.company_id = c.id
            ORDER BY a.created_at DESC
        ")->fetchAll();

        // 2. Fetch approved applications needing allocation
        $approvedApps = $db->query("
            SELECT sa.*, s.id AS student_id, s.matric_number, s.programme, s.level, u.full_name, d.name AS dept_name
            FROM siwes_applications sa
            JOIN students s ON sa.student_id = s.id
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN allocations a ON sa.student_id = a.student_id AND a.status != 'Cancelled'
            WHERE sa.status = 'Approved' AND a.id IS NULL
            ORDER BY sa.submitted_at ASC
        ")->fetchAll();

        // 3. Fetch active companies
        $companies = $db->query("SELECT * FROM companies WHERE status = 'active' AND available_slots > 0 ORDER BY name ASC")->fetchAll();

        require __DIR__ . '/../Views/coordinator/allocation.php';
    }

    public function allocate(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/allocation');
        }

        $appId = (int)($_POST['application_id'] ?? 0);
        $studentId = (int)($_POST['student_id'] ?? 0);
        $companyId = (int)($_POST['company_id'] ?? 0);

        if ($appId <= 0 || $studentId <= 0 || $companyId <= 0) {
            Helper::setFlash('warning', 'Please select a valid company for allocation.');
            Helper::redirect('coordinator/allocation');
        }

        $db = Database::getInstance();

        // Fetch company info
        $companyStmt = $db->prepare("SELECT * FROM companies WHERE id = :cid AND available_slots > 0");
        $companyStmt->execute(['cid' => $companyId]);
        $company = $companyStmt->fetch();

        if (!$company) {
            Helper::setFlash('danger', 'Selected company has no available placement slots.');
            Helper::redirect('coordinator/allocation');
        }

        // Fetch student & application details for smart scoring
        $appStmt = $db->prepare("
            SELECT sa.*, s.department_id, d.name AS dept_name
            FROM siwes_applications sa
            JOIN students s ON sa.student_id = s.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE sa.id = :aid
        ");
        $appStmt->execute(['aid' => $appId]);
        $appData = $appStmt->fetch();

        $matchResult = Helper::calculateMatchScore(
            $appData['dept_name'] ?? 'Computer Science',
            $appData['preferred_industry'],
            $appData['preferred_location'],
            $company['industry'],
            $company['state'],
            $company['city'],
            $company['available_slots']
        );

        try {
            $db->beginTransaction();

            // Insert Allocation
            $allocStmt = $db->prepare("
                INSERT INTO allocations (application_id, student_id, company_id, compatibility_score, match_reasons, status, allocated_by, start_date, end_date)
                VALUES (:aid, :sid, :cid, :score, :reasons, 'Allocated', :by, CURRENT_DATE, CURRENT_DATE + INTERVAL '6 months')
            ");
            $allocStmt->execute([
                'aid' => $appId,
                'sid' => $studentId,
                'cid' => $companyId,
                'score' => $matchResult['total_score'],
                'reasons' => json_encode($matchResult),
                'by' => Auth::id()
            ]);

            // Update Application Status
            $db->exec("UPDATE siwes_applications SET status = 'Allocated' WHERE id = {$appId}");

            // Deduct available slot from company
            $db->exec("UPDATE companies SET available_slots = available_slots - 1 WHERE id = {$companyId}");

            // Notify Student
            $student = $db->query("SELECT user_id FROM students WHERE id = {$studentId}")->fetch();
            if ($student) {
                $notifStmt = $db->prepare("
                    INSERT INTO notifications (user_id, title, message, type)
                    VALUES (:uid, 'SIWES Placement Allocated!', :msg, 'success')
                ");
                $notifStmt->execute([
                    'uid' => $student['user_id'],
                    'msg' => "Congratulations! You have been allocated to {$company['name']} ({$company['state']}). Compatibility Match Score: {$matchResult['total_score']}%."
                ]);
            }

            $db->commit();
            Helper::setFlash('success', "Student successfully allocated to {$company['name']} with {$matchResult['total_score']}% match score!");
            Helper::redirect('coordinator/allocation');

        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Allocation error: ' . $e->getMessage());
            Helper::redirect('coordinator/allocation');
        }
    }

    public function reassign(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/allocation');
        }

        $allocId = (int)($_POST['allocation_id'] ?? 0);
        $newCompanyId = (int)($_POST['new_company_id'] ?? 0);

        if ($allocId <= 0 || $newCompanyId <= 0) {
            Helper::setFlash('warning', 'Invalid re-assignment request.');
            Helper::redirect('coordinator/allocation');
        }

        $db = Database::getInstance();
        $alloc = $db->query("SELECT * FROM allocations WHERE id = {$allocId}")->fetch();
        if (!$alloc) {
            Helper::setFlash('danger', 'Allocation record not found.');
            Helper::redirect('coordinator/allocation');
        }

        try {
            $db->beginTransaction();

            // Return slot to old company
            $db->exec("UPDATE companies SET available_slots = available_slots + 1 WHERE id = {$alloc['company_id']}");

            // Deduct slot from new company
            $db->exec("UPDATE companies SET available_slots = available_slots - 1 WHERE id = {$newCompanyId}");

            // Update Allocation
            $stmt = $db->prepare("UPDATE allocations SET company_id = :cid, status = 'Reassigned', updated_at = CURRENT_TIMESTAMP WHERE id = :aid");
            $stmt->execute(['cid' => $newCompanyId, 'aid' => $allocId]);

            $db->commit();
            Helper::setFlash('success', 'Student successfully reassigned to new organization.');
            Helper::redirect('coordinator/allocation');
        } catch (\Exception $e) {
            $db->rollBack();
            Helper::setFlash('danger', 'Reassignment error: ' . $e->getMessage());
            Helper::redirect('coordinator/allocation');
        }
    }
}

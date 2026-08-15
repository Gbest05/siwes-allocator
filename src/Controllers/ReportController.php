<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class ReportController {

    public function index(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        $format = $_GET['export'] ?? null;

        $reportData = $db->query("
            SELECT s.matric_number, u.full_name AS student_name, u.email, d.name AS dept_name,
                   sa.status AS app_status, a.status AS alloc_status, a.compatibility_score,
                   c.name AS company_name, c.state AS company_state, a.start_date, a.end_date
            FROM students s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN siwes_applications sa ON s.id = sa.student_id
            LEFT JOIN allocations a ON s.id = a.student_id AND a.status != 'Cancelled'
            LEFT JOIN companies c ON a.company_id = c.id
            ORDER BY u.full_name ASC
        ")->fetchAll();

        // CSV Export Trigger
        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="SIWES_Allocation_Report_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Matric Number', 'Student Name', 'Department', 'Email', 'Application Status', 'Allocation Status', 'Company Allocated', 'Location', 'Match Score']);
            
            foreach ($reportData as $row) {
                fputcsv($output, [
                    $row['matric_number'],
                    $row['student_name'],
                    $row['dept_name'] ?? 'N/A',
                    $row['email'],
                    $row['app_status'] ?? 'None',
                    $row['alloc_status'] ?? 'Unallocated',
                    $row['company_name'] ?? 'Not Allocated',
                    $row['company_state'] ?? 'N/A',
                    ($row['compatibility_score'] ?? 0) . '%'
                ]);
            }
            fclose($output);
            exit;
        }

        require __DIR__ . '/../Views/reports/index.php';
    }
}

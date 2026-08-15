<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Helper;

class LandingController {
    public function index(): void {
        $db = Database::getInstance();
        
        // Fetch live stats for landing page counters
        $totalStudents = (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn();
        $totalCompanies = (int)$db->query("SELECT COUNT(*) FROM companies")->fetchColumn();
        $totalAllocated = (int)$db->query("SELECT COUNT(*) FROM allocations WHERE status = 'Allocated'")->fetchColumn();
        $pendingApps = (int)$db->query("SELECT COUNT(*) FROM siwes_applications WHERE status = 'Submitted' OR status = 'Under Review'")->fetchColumn();

        $settings = Helper::getAllSettings();

        require __DIR__ . '/../Views/landing.php';
    }
}

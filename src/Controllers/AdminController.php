<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class AdminController {

    public function dashboard(): void {
        Auth::requireRole('admin');
        $db = Database::getInstance();

        $stats = [
            'total_students'  => (int)$db->query("SELECT COUNT(*) FROM students")->fetchColumn(),
            'total_companies' => (int)$db->query("SELECT COUNT(*) FROM companies")->fetchColumn(),
            'pending_apps'    => (int)$db->query("SELECT COUNT(*) FROM siwes_applications WHERE status = 'Submitted' OR status = 'Under Review'")->fetchColumn(),
            'completed_alloc' => (int)$db->query("SELECT COUNT(*) FROM allocations WHERE status = 'Allocated'")->fetchColumn(),
            'unallocated'     => (int)$db->query("SELECT COUNT(*) FROM siwes_applications sa LEFT JOIN allocations a ON sa.student_id = a.student_id WHERE sa.status = 'Approved' AND a.id IS NULL")->fetchColumn()
        ];

        $recentUsers = $db->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 6")->fetchAll();

        require __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function users(): void {
        Auth::requireRole('admin');
        $db = Database::getInstance();

        $users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
        require __DIR__ . '/../Views/admin/users.php';
    }

    public function departments(): void {
        Auth::requireRole('admin');
        $db = Database::getInstance();

        $departments = $db->query("
            SELECT d.*, COUNT(s.id) AS student_count
            FROM departments d
            LEFT JOIN students s ON d.id = s.department_id
            GROUP BY d.id
            ORDER BY d.name ASC
        ")->fetchAll();

        require __DIR__ . '/../Views/admin/departments.php';
    }

    public function settings(): void {
        Auth::requireRole('admin');
        $settings = Helper::getAllSettings();
        require __DIR__ . '/../Views/admin/settings.php';
    }

    public function updateSettings(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/settings');
        }

        // Text fields to update
        $fields = [
            'site_name',
            'institution_name',
            'hero_badge',
            'hero_title',
            'hero_description',
            'about_badge',
            'about_title',
            'about_description_1',
            'about_description_2',
            'cta_title',
            'cta_description',
            'footer_description',
            'contact_address',
            'contact_email',
            'contact_phone',
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                Helper::setSetting($field, trim($_POST[$field]));
            }
        }

        // Handle File Uploads (Logo, Hero Image, About Image)
        $uploadDir = __DIR__ . '/../../public/uploads/settings/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageFields = ['site_logo', 'hero_image', 'about_image'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];

        foreach ($imageFields as $imgField) {
            if (isset($_FILES[$imgField]) && $_FILES[$imgField]['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES[$imgField];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (in_array($ext, $allowedExtensions, true) && $file['size'] <= 8 * 1024 * 1024) {
                    $newFileName = $imgField . '_' . time() . '.' . $ext;
                    $targetPath = $uploadDir . $newFileName;
                    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                        Helper::setSetting($imgField, 'uploads/settings/' . $newFileName);
                    }
                }
            }
        }

        Helper::setFlash('success', 'Landing page branding, text, and images updated successfully!');
        Helper::redirect('admin/settings');
    }
}

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

    /**
     * Admin: Control Academic Departments, Programmes, and Levels
     */
    public function departments(): void {
        Auth::requireRole('admin');
        $db = Database::getInstance();

        // 1. Departments with Enrolled Student Count
        $departments = $db->query("
            SELECT d.*, COUNT(s.id) AS student_count
            FROM departments d
            LEFT JOIN students s ON d.id = s.department_id
            GROUP BY d.id
            ORDER BY d.name ASC
        ")->fetchAll();

        // 2. Programmes with Enrolled Student Count
        $programmes = $db->query("
            SELECT p.*, COUNT(s.id) AS student_count
            FROM academic_programmes p
            LEFT JOIN students s ON p.code = s.programme
            GROUP BY p.id
            ORDER BY p.id ASC
        ")->fetchAll();

        // 3. Levels with Enrolled Student Count
        $levels = $db->query("
            SELECT l.*, COUNT(s.id) AS student_count
            FROM academic_levels l
            LEFT JOIN students s ON l.code = s.level
            GROUP BY l.id
            ORDER BY l.id ASC
        ")->fetchAll();

        $activeTab = Helper::sanitize($_GET['tab'] ?? 'departments');

        require __DIR__ . '/../Views/admin/departments.php';
    }

    // ==========================================
    // DEPARTMENT CRUD & REGISTRATION CONTROL
    // ==========================================

    public function createDepartment(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $name = Helper::sanitize($_POST['name'] ?? '');
        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $description = Helper::sanitize($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($code)) {
            Helper::setFlash('warning', 'Department name and code are required.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $db = Database::getInstance();

        // Check uniqueness
        $check = $db->prepare("SELECT id FROM departments WHERE code = :code OR name = :name");
        $check->execute(['code' => $code, 'name' => $name]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'A department with this name or code already exists.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $stmt = $db->prepare("
            INSERT INTO departments (name, code, description, is_active)
            VALUES (:name, :code, :description, :is_active)
        ");
        $stmt->execute([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'is_active' => $isActive
        ]);

        Helper::setFlash('success', "Department '{$name}' created successfully.");
        Helper::redirect('admin/departments?tab=departments');
    }

    public function updateDepartment(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $id = (int)($_POST['id'] ?? 0);
        $name = Helper::sanitize($_POST['name'] ?? '');
        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $description = Helper::sanitize($_POST['description'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($name) || empty($code)) {
            Helper::setFlash('warning', 'Department ID, name and code are required.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $db = Database::getInstance();

        // Check duplicate code on another department
        $check = $db->prepare("SELECT id FROM departments WHERE (code = :code OR name = :name) AND id != :id");
        $check->execute(['code' => $code, 'name' => $name, 'id' => $id]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'Another department with this name or code already exists.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $stmt = $db->prepare("
            UPDATE departments
            SET name = :name, code = :code, description = :description, is_active = :is_active
            WHERE id = :id
        ");
        $stmt->execute([
            'name' => $name,
            'code' => $code,
            'description' => $description,
            'is_active' => $isActive,
            'id' => $id
        ]);

        Helper::setFlash('success', "Department '{$name}' updated successfully.");
        Helper::redirect('admin/departments?tab=departments');
    }

    public function deleteDepartment(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Helper::setFlash('warning', 'Invalid department ID.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $db = Database::getInstance();

        // Check if students are currently enrolled in this department
        $studentCount = (int)$db->query("SELECT COUNT(*) FROM students WHERE department_id = {$id}")->fetchColumn();
        if ($studentCount > 0) {
            Helper::setFlash('danger', "Cannot delete department: {$studentCount} student(s) are currently enrolled in it. You can deactivate it instead to hide it from the registration page.");
            Helper::redirect('admin/departments?tab=departments');
        }

        $db->prepare("DELETE FROM departments WHERE id = :id")->execute(['id' => $id]);
        Helper::setFlash('success', 'Department deleted successfully.');
        Helper::redirect('admin/departments?tab=departments');
    }

    public function toggleDepartment(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=departments');
        }

        $id = (int)($_POST['id'] ?? 0);
        $db = Database::getInstance();
        $dept = $db->query("SELECT name, is_active FROM departments WHERE id = {$id}")->fetch();
        if ($dept) {
            $newStatus = ((int)$dept['is_active'] === 1) ? 0 : 1;
            $db->prepare("UPDATE departments SET is_active = :status WHERE id = :id")->execute(['status' => $newStatus, 'id' => $id]);
            $statusText = $newStatus === 1 ? 'Activated (Visible on student registration)' : 'Deactivated (Hidden from student registration)';
            Helper::setFlash('success', "Department '{$dept['name']}' {$statusText}.");
        }

        Helper::redirect('admin/departments?tab=departments');
    }

    // ==========================================
    // PROGRAMME CRUD & REGISTRATION CONTROL
    // ==========================================

    public function createProgramme(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $name = Helper::sanitize($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($code) || empty($name)) {
            Helper::setFlash('warning', 'Programme code and full name are required.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $db = Database::getInstance();
        $check = $db->prepare("SELECT id FROM academic_programmes WHERE code = :code");
        $check->execute(['code' => $code]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'A programme with this code already exists.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $stmt = $db->prepare("
            INSERT INTO academic_programmes (code, name, is_active)
            VALUES (:code, :name, :is_active)
        ");
        $stmt->execute([
            'code' => $code,
            'name' => $name,
            'is_active' => $isActive
        ]);

        Helper::setFlash('success', "Academic programme '{$name}' created successfully.");
        Helper::redirect('admin/departments?tab=programmes');
    }

    public function updateProgramme(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $name = Helper::sanitize($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($code) || empty($name)) {
            Helper::setFlash('warning', 'Invalid programme details.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $db = Database::getInstance();
        $check = $db->prepare("SELECT id FROM academic_programmes WHERE code = :code AND id != :id");
        $check->execute(['code' => $code, 'id' => $id]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'Another programme with this code already exists.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $stmt = $db->prepare("
            UPDATE academic_programmes
            SET code = :code, name = :name, is_active = :is_active
            WHERE id = :id
        ");
        $stmt->execute([
            'code' => $code,
            'name' => $name,
            'is_active' => $isActive,
            'id' => $id
        ]);

        Helper::setFlash('success', "Programme '{$name}' updated successfully.");
        Helper::redirect('admin/departments?tab=programmes');
    }

    public function deleteProgramme(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Helper::setFlash('warning', 'Invalid programme ID.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $db = Database::getInstance();
        $prog = $db->query("SELECT code, name FROM academic_programmes WHERE id = {$id}")->fetch();
        if (!$prog) {
            Helper::setFlash('danger', 'Programme not found.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $studentCount = (int)$db->prepare("SELECT COUNT(*) FROM students WHERE programme = :code");
        $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE programme = :code");
        $stmt->execute(['code' => $prog['code']]);
        $studentCount = (int)$stmt->fetchColumn();

        if ($studentCount > 0) {
            Helper::setFlash('danger', "Cannot delete programme: {$studentCount} student(s) currently belong to '{$prog['name']}'. You can deactivate it instead to hide it from registration.");
            Helper::redirect('admin/departments?tab=programmes');
        }

        $db->prepare("DELETE FROM academic_programmes WHERE id = :id")->execute(['id' => $id]);
        Helper::setFlash('success', "Programme '{$prog['name']}' deleted successfully.");
        Helper::redirect('admin/departments?tab=programmes');
    }

    public function toggleProgramme(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=programmes');
        }

        $id = (int)($_POST['id'] ?? 0);
        $db = Database::getInstance();
        $prog = $db->query("SELECT name, is_active FROM academic_programmes WHERE id = {$id}")->fetch();
        if ($prog) {
            $newStatus = ((int)$prog['is_active'] === 1) ? 0 : 1;
            $db->prepare("UPDATE academic_programmes SET is_active = :status WHERE id = :id")->execute(['status' => $newStatus, 'id' => $id]);
            $statusText = $newStatus === 1 ? 'Activated (Visible on student registration)' : 'Deactivated (Hidden from student registration)';
            Helper::setFlash('success', "Programme '{$prog['name']}' {$statusText}.");
        }

        Helper::redirect('admin/departments?tab=programmes');
    }

    // ==========================================
    // LEVEL CRUD & REGISTRATION CONTROL
    // ==========================================

    public function createLevel(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $name = Helper::sanitize($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($code) || empty($name)) {
            Helper::setFlash('warning', 'Level code and full name are required.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $db = Database::getInstance();
        $check = $db->prepare("SELECT id FROM academic_levels WHERE code = :code");
        $check->execute(['code' => $code]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'An academic level with this code already exists.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $stmt = $db->prepare("
            INSERT INTO academic_levels (code, name, is_active)
            VALUES (:code, :name, :is_active)
        ");
        $stmt->execute([
            'code' => $code,
            'name' => $name,
            'is_active' => $isActive
        ]);

        Helper::setFlash('success', "Academic level '{$name}' created successfully.");
        Helper::redirect('admin/departments?tab=levels');
    }

    public function updateLevel(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $id = (int)($_POST['id'] ?? 0);
        $code = strtoupper(Helper::sanitize($_POST['code'] ?? ''));
        $name = Helper::sanitize($_POST['name'] ?? '');
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if ($id <= 0 || empty($code) || empty($name)) {
            Helper::setFlash('warning', 'Invalid academic level details.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $db = Database::getInstance();
        $check = $db->prepare("SELECT id FROM academic_levels WHERE code = :code AND id != :id");
        $check->execute(['code' => $code, 'id' => $id]);
        if ($check->fetch()) {
            Helper::setFlash('danger', 'Another academic level with this code already exists.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $stmt = $db->prepare("
            UPDATE academic_levels
            SET code = :code, name = :name, is_active = :is_active
            WHERE id = :id
        ");
        $stmt->execute([
            'code' => $code,
            'name' => $name,
            'is_active' => $isActive,
            'id' => $id
        ]);

        Helper::setFlash('success', "Academic level '{$name}' updated successfully.");
        Helper::redirect('admin/departments?tab=levels');
    }

    public function deleteLevel(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            Helper::setFlash('warning', 'Invalid level ID.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $db = Database::getInstance();
        $lvl = $db->query("SELECT code, name FROM academic_levels WHERE id = {$id}")->fetch();
        if (!$lvl) {
            Helper::setFlash('danger', 'Academic level not found.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $stmt = $db->prepare("SELECT COUNT(*) FROM students WHERE level = :code");
        $stmt->execute(['code' => $lvl['code']]);
        $studentCount = (int)$stmt->fetchColumn();

        if ($studentCount > 0) {
            Helper::setFlash('danger', "Cannot delete level: {$studentCount} student(s) currently belong to level '{$lvl['name']}'. You can deactivate it instead to hide it from registration.");
            Helper::redirect('admin/departments?tab=levels');
        }

        $db->prepare("DELETE FROM academic_levels WHERE id = :id")->execute(['id' => $id]);
        Helper::setFlash('success', "Academic level '{$lvl['name']}' deleted successfully.");
        Helper::redirect('admin/departments?tab=levels');
    }

    public function toggleLevel(): void {
        Auth::requireRole('admin');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('admin/departments?tab=levels');
        }

        $id = (int)($_POST['id'] ?? 0);
        $db = Database::getInstance();
        $lvl = $db->query("SELECT name, is_active FROM academic_levels WHERE id = {$id}")->fetch();
        if ($lvl) {
            $newStatus = ((int)$lvl['is_active'] === 1) ? 0 : 1;
            $db->prepare("UPDATE academic_levels SET is_active = :status WHERE id = :id")->execute(['status' => $newStatus, 'id' => $id]);
            $statusText = $newStatus === 1 ? 'Activated (Visible on student registration)' : 'Deactivated (Hidden from student registration)';
            Helper::setFlash('success', "Academic level '{$lvl['name']}' {$statusText}.");
        }

        Helper::redirect('admin/departments?tab=levels');
    }

    // ==========================================
    // LANDING PAGE & PORTAL SETTINGS
    // ==========================================

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

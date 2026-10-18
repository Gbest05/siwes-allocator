<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class CompanyController {

    public function index(): void {
        Auth::requireRole(['coordinator', 'admin']);
        $db = Database::getInstance();

        $companies = $db->query("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM allocations WHERE company_id = c.id AND status != 'Cancelled') AS allocated_count
            FROM companies c
            ORDER BY c.name ASC
        ")->fetchAll();

        require __DIR__ . '/../Views/coordinator/companies.php';
    }

    public function store(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/companies');
        }

        $name = Helper::sanitize($_POST['name'] ?? '');
        $regNumber = Helper::sanitize($_POST['reg_number'] ?? '');
        $address = Helper::sanitize($_POST['address'] ?? '');
        $state = Helper::sanitize($_POST['state'] ?? '');
        $city = Helper::sanitize($_POST['city'] ?? '');
        $industry = Helper::sanitize($_POST['industry'] ?? '');
        $contactPerson = Helper::sanitize($_POST['contact_person'] ?? '');
        $phone = Helper::sanitize($_POST['phone'] ?? '');
        $email = Helper::sanitize($_POST['email'] ?? '');
        $capacity = (int)($_POST['total_capacity'] ?? 10);

        if (empty($name) || empty($regNumber) || empty($email)) {
            Helper::setFlash('warning', 'Please fill in company name, registration number, and email.');
            Helper::redirect('coordinator/companies');
        }

        $db = Database::getInstance();

        try {
            $stmt = $db->prepare("
                INSERT INTO companies (name, reg_number, address, state, city, industry, contact_person, phone, email, total_capacity, available_slots, status)
                VALUES (:name, :reg, :addr, :state, :city, :ind, :contact, :phone, :email, :cap, :cap, 'active')
            ");
            $stmt->execute([
                'name'    => $name,
                'reg'     => $regNumber,
                'addr'    => $address,
                'state'   => $state,
                'city'    => $city,
                'ind'     => $industry,
                'contact' => $contactPerson,
                'phone'   => $phone,
                'email'   => $email,
                'cap'     => $capacity
            ]);

            Helper::setFlash('success', "Company '{$name}' registered successfully!");
        } catch (\Exception $e) {
            Helper::setFlash('danger', 'Error adding company: ' . $e->getMessage());
        }

        Helper::redirect('coordinator/companies');
    }

    public function update(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/companies');
        }

        $id = (int)($_POST['company_id'] ?? 0);
        $name = Helper::sanitize($_POST['name'] ?? '');
        $regNumber = Helper::sanitize($_POST['reg_number'] ?? '');
        $address = Helper::sanitize($_POST['address'] ?? '');
        $state = Helper::sanitize($_POST['state'] ?? '');
        $city = Helper::sanitize($_POST['city'] ?? '');
        $industry = Helper::sanitize($_POST['industry'] ?? '');
        $contactPerson = Helper::sanitize($_POST['contact_person'] ?? '');
        $phone = Helper::sanitize($_POST['phone'] ?? '');
        $email = Helper::sanitize($_POST['email'] ?? '');
        $totalCapacity = (int)($_POST['total_capacity'] ?? 10);
        $availableSlots = (int)($_POST['available_slots'] ?? $totalCapacity);
        $status = Helper::sanitize($_POST['status'] ?? 'active');

        if ($id <= 0 || empty($name) || empty($regNumber)) {
            Helper::setFlash('warning', 'Please provide valid company details.');
            Helper::redirect('coordinator/companies');
        }

        $db = Database::getInstance();

        try {
            $stmt = $db->prepare("
                UPDATE companies
                SET name = :name,
                    reg_number = :reg,
                    address = :addr,
                    state = :state,
                    city = :city,
                    industry = :ind,
                    contact_person = :contact,
                    phone = :phone,
                    email = :email,
                    total_capacity = :cap,
                    available_slots = :slots,
                    status = :status
                WHERE id = :id
            ");
            $stmt->execute([
                'name'    => $name,
                'reg'     => $regNumber,
                'addr'    => $address,
                'state'   => $state,
                'city'    => $city,
                'ind'     => $industry,
                'contact' => $contactPerson,
                'phone'   => $phone,
                'email'   => $email,
                'cap'     => $totalCapacity,
                'slots'   => $availableSlots,
                'status'  => $status,
                'id'      => $id
            ]);

            Helper::setFlash('success', "Company '{$name}' updated successfully.");
        } catch (\Exception $e) {
            Helper::setFlash('danger', 'Error updating company: ' . $e->getMessage());
        }

        Helper::redirect('coordinator/companies');
    }

    public function toggleStatus(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/companies');
        }

        $id = (int)($_POST['company_id'] ?? 0);
        if ($id <= 0) {
            Helper::setFlash('warning', 'Invalid company ID.');
            Helper::redirect('coordinator/companies');
        }

        $db = Database::getInstance();
        $comp = $db->query("SELECT name, status FROM companies WHERE id = {$id}")->fetch();
        if (!$comp) {
            Helper::setFlash('danger', 'Company not found.');
            Helper::redirect('coordinator/companies');
        }

        $newStatus = ($comp['status'] === 'active') ? 'suspended' : 'active';
        $db->prepare("UPDATE companies SET status = :st WHERE id = :id")->execute([
            'st' => $newStatus,
            'id' => $id
        ]);

        $actionText = ($newStatus === 'suspended') ? 'suspended from new allocations' : 'reactivated successfully';
        Helper::setFlash('success', "Company '{$comp['name']}' has been {$actionText}.");
        Helper::redirect('coordinator/companies');
    }

    public function delete(): void {
        Auth::requireRole(['coordinator', 'admin']);
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('coordinator/companies');
        }

        $id = (int)($_POST['company_id'] ?? 0);
        if ($id <= 0) {
            Helper::setFlash('warning', 'Invalid company ID.');
            Helper::redirect('coordinator/companies');
        }

        $db = Database::getInstance();
        $comp = $db->query("SELECT name FROM companies WHERE id = {$id}")->fetch();
        if (!$comp) {
            Helper::setFlash('danger', 'Company not found.');
            Helper::redirect('coordinator/companies');
        }

        // Check if any student allocations are attached
        $allocatedCount = (int)$db->query("SELECT COUNT(*) FROM allocations WHERE company_id = {$id} AND status != 'Cancelled'")->fetchColumn();
        if ($allocatedCount > 0) {
            Helper::setFlash('danger', "Cannot delete company '{$comp['name']}': {$allocatedCount} student(s) are currently placed here. Please suspend the company instead so no new allocations are made.");
            Helper::redirect('coordinator/companies');
        }

        $db->prepare("DELETE FROM companies WHERE id = :id")->execute(['id' => $id]);
        Helper::setFlash('success', "Company '{$comp['name']}' deleted successfully.");
        Helper::redirect('coordinator/companies');
    }
}

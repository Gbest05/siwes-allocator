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
            SELECT c.*, COUNT(a.id) AS allocated_count
            FROM companies c
            LEFT JOIN allocations a ON c.id = a.company_id AND a.status != 'Cancelled'
            GROUP BY c.id
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
                'name' => $name,
                'reg' => $regNumber,
                'addr' => $address,
                'state' => $state,
                'city' => $city,
                'ind' => $industry,
                'contact' => $contactPerson,
                'phone' => $phone,
                'email' => $email,
                'cap' => $capacity
            ]);

            Helper::setFlash('success', "Company '{$name}' registered successfully!");
            Helper::redirect('coordinator/companies');

        } catch (\Exception $e) {
            Helper::setFlash('danger', 'Error adding company: ' . $e->getMessage());
            Helper::redirect('coordinator/companies');
        }
    }
}

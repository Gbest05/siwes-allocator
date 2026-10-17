<?php
// Start output buffering to prevent headers sent issues
ob_start();

use App\Core\Auth;
use App\Core\Router;

require_once __DIR__ . '/../src/Core/Auth.php';
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Core/Helper.php';
require_once __DIR__ . '/../src/Core/Router.php';

// Initialize session immediately before any view rendering
Auth::initSession();

// Controllers Autoload
require_once __DIR__ . '/../src/Controllers/LandingController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/StudentController.php';
require_once __DIR__ . '/../src/Controllers/CoordinatorController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';
require_once __DIR__ . '/../src/Controllers/AllocationController.php';
require_once __DIR__ . '/../src/Controllers/CompanyController.php';
require_once __DIR__ . '/../src/Controllers/DocumentController.php';
require_once __DIR__ . '/../src/Controllers/NotificationController.php';
require_once __DIR__ . '/../src/Controllers/ReportController.php';
require_once __DIR__ . '/../src/Controllers/SupervisorController.php';

$router = new Router();

// 1. Landing & Auth Routes
$router->get('home', [App\Controllers\LandingController::class, 'index']);
$router->get('login', [App\Controllers\AuthController::class, 'showLogin']);
$router->post('login', [App\Controllers\AuthController::class, 'login']);
$router->get('register', [App\Controllers\AuthController::class, 'showRegister']);
$router->post('register', [App\Controllers\AuthController::class, 'register']);
$router->get('logout', [App\Controllers\AuthController::class, 'logout']);

// 2. Dashboards Routing
$router->get('dashboard', [App\Controllers\StudentController::class, 'dashboard']);
$router->get('coordinator/dashboard', [App\Controllers\CoordinatorController::class, 'dashboard']);
$router->get('admin/dashboard', [App\Controllers\AdminController::class, 'dashboard']);

// 3. Student Workflow Routes
$router->get('student/application', [App\Controllers\StudentController::class, 'application']);
$router->post('student/application', [App\Controllers\StudentController::class, 'submitApplication']);
$router->post('student/application/delete', [App\Controllers\StudentController::class, 'deleteApplication']);
$router->get('student/documents', [App\Controllers\DocumentController::class, 'index']);
$router->post('student/documents', [App\Controllers\DocumentController::class, 'upload']);
$router->post('student/documents/delete', [App\Controllers\DocumentController::class, 'delete']);
$router->get('student/allocation', [App\Controllers\StudentController::class, 'viewAllocation']);
$router->get('allocation-letter', [App\Controllers\StudentController::class, 'downloadLetter']);

// 4. Coordinator & Allocation Routes
$router->get('coordinator/students', [App\Controllers\CoordinatorController::class, 'students']);
$router->get('coordinator/applications', [App\Controllers\CoordinatorController::class, 'applications']);
$router->post('coordinator/application-status', [App\Controllers\CoordinatorController::class, 'updateApplicationStatus']);
$router->get('coordinator/allocation', [App\Controllers\AllocationController::class, 'index']);
$router->post('coordinator/allocate', [App\Controllers\AllocationController::class, 'allocate']);
$router->post('coordinator/reassign', [App\Controllers\AllocationController::class, 'reassign']);
$router->get('coordinator/companies', [App\Controllers\CompanyController::class, 'index']);
$router->post('coordinator/companies', [App\Controllers\CompanyController::class, 'store']);
$router->post('coordinator/companies/update', [App\Controllers\CompanyController::class, 'update']);
$router->post('coordinator/companies/toggle-status', [App\Controllers\CompanyController::class, 'toggleStatus']);
$router->post('coordinator/companies/delete', [App\Controllers\CompanyController::class, 'delete']);

$router->get('admin/companies', [App\Controllers\CompanyController::class, 'index']);
$router->post('admin/companies/update', [App\Controllers\CompanyController::class, 'update']);
$router->post('admin/companies/toggle-status', [App\Controllers\CompanyController::class, 'toggleStatus']);
$router->post('admin/companies/delete', [App\Controllers\CompanyController::class, 'delete']);
$router->get('coordinator/supervisors', [App\Controllers\CoordinatorController::class, 'supervisors']);
$router->post('coordinator/assign-supervisor', [App\Controllers\CoordinatorController::class, 'assignSupervisor']);
$router->post('coordinator/auto-distribute-supervisors', [App\Controllers\CoordinatorController::class, 'autoDistributeSupervisors']);
$router->post('coordinator/unassign-supervisor', [App\Controllers\CoordinatorController::class, 'unassignSupervisor']);
$router->post('coordinator/create-supervisor', [App\Controllers\CoordinatorController::class, 'createSupervisor']);

// 5. Supervisor Routes
$router->get('supervisor/dashboard', [App\Controllers\SupervisorController::class, 'dashboard']);
$router->get('supervisor/students', [App\Controllers\SupervisorController::class, 'students']);
$router->post('supervisor/review-application', [App\Controllers\SupervisorController::class, 'reviewApplication']);
$router->post('supervisor/allocate-placement', [App\Controllers\SupervisorController::class, 'allocatePlacement']);
$router->post('supervisor/auto-assign-placements', [App\Controllers\SupervisorController::class, 'autoAssignPlacements']);
$router->post('supervisor/send-notification', [App\Controllers\SupervisorController::class, 'sendNotification']);

// 6. Admin & Management Routes
$router->get('admin/users', [App\Controllers\AdminController::class, 'users']);
$router->get('admin/departments', [App\Controllers\AdminController::class, 'departments']);
$router->post('admin/departments/create', [App\Controllers\AdminController::class, 'createDepartment']);
$router->post('admin/departments/update', [App\Controllers\AdminController::class, 'updateDepartment']);
$router->post('admin/departments/delete', [App\Controllers\AdminController::class, 'deleteDepartment']);
$router->post('admin/departments/toggle', [App\Controllers\AdminController::class, 'toggleDepartment']);
$router->post('admin/programmes/create', [App\Controllers\AdminController::class, 'createProgramme']);
$router->post('admin/programmes/update', [App\Controllers\AdminController::class, 'updateProgramme']);
$router->post('admin/programmes/delete', [App\Controllers\AdminController::class, 'deleteProgramme']);
$router->post('admin/programmes/toggle', [App\Controllers\AdminController::class, 'toggleProgramme']);
$router->post('admin/levels/create', [App\Controllers\AdminController::class, 'createLevel']);
$router->post('admin/levels/update', [App\Controllers\AdminController::class, 'updateLevel']);
$router->post('admin/levels/delete', [App\Controllers\AdminController::class, 'deleteLevel']);
$router->post('admin/levels/toggle', [App\Controllers\AdminController::class, 'toggleLevel']);
$router->get('admin/settings', [App\Controllers\AdminController::class, 'settings']);
$router->post('admin/settings', [App\Controllers\AdminController::class, 'updateSettings']);

// 6. Reports & Notifications
$router->get('reports', [App\Controllers\ReportController::class, 'index']);
$router->get('notifications', [App\Controllers\NotificationController::class, 'index']);

// Dispatch request
$router->dispatch();

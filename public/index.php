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
$router->get('student/documents', [App\Controllers\DocumentController::class, 'index']);
$router->post('student/documents', [App\Controllers\DocumentController::class, 'upload']);
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

// 5. Admin & Management Routes
$router->get('admin/users', [App\Controllers\AdminController::class, 'users']);
$router->get('admin/departments', [App\Controllers\AdminController::class, 'departments']);
$router->get('admin/settings', [App\Controllers\AdminController::class, 'settings']);
$router->post('admin/settings', [App\Controllers\AdminController::class, 'updateSettings']);

// 6. Reports & Notifications
$router->get('reports', [App\Controllers\ReportController::class, 'index']);
$router->get('notifications', [App\Controllers\NotificationController::class, 'index']);

// Dispatch request
$router->dispatch();

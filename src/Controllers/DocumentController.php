<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class DocumentController {

    public function index(): void {
        Auth::requireRole(['student', 'coordinator', 'admin']);
        $db = Database::getInstance();
        $userId = Auth::id();

        if (Auth::role() === 'student') {
            $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();
            $documents = $db->query("SELECT * FROM documents WHERE student_id = {$student['id']} ORDER BY uploaded_at DESC")->fetchAll();
        } else {
            $documents = $db->query("
                SELECT d.*, s.matric_number, u.full_name AS student_name
                FROM documents d
                JOIN students s ON d.student_id = s.id
                JOIN users u ON s.user_id = u.id
                ORDER BY d.uploaded_at DESC
            ")->fetchAll();
        }

        require __DIR__ . '/../Views/student/documents.php';
    }

    public function upload(): void {
        Auth::requireRole('student');
        if (!Helper::verifyCsrf($_POST['csrf_token'] ?? '')) {
            Helper::setFlash('danger', 'Invalid security token.');
            Helper::redirect('student/documents');
        }

        $userId = Auth::id();
        $db = Database::getInstance();
        $student = $db->query("SELECT id FROM students WHERE user_id = {$userId}")->fetch();

        $docType = Helper::sanitize($_POST['doc_type'] ?? 'SIWES Application Letter');

        if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
            Helper::setFlash('warning', 'Please select a valid document file to upload.');
            Helper::redirect('student/documents');
        }

        $file = $_FILES['document_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $allowedTypes = ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'];
        if (!in_array($extension, $allowedTypes, true)) {
            Helper::setFlash('danger', 'Invalid file type. Allowed types: PDF, PNG, JPG, DOC, DOCX.');
            Helper::redirect('student/documents');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            Helper::setFlash('danger', 'File size exceeds maximum 5MB limit.');
            Helper::redirect('student/documents');
        }

        $newFileName = 'doc_' . $student['id'] . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/';
        $destination = $uploadDir . $newFileName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $relativePath = 'uploads/' . $newFileName;
            $stmt = $db->prepare("
                INSERT INTO documents (student_id, doc_type, file_path, file_name, file_size, status)
                VALUES (:sid, :type, :path, :fname, :size, 'Pending')
            ");
            $stmt->execute([
                'sid' => $student['id'],
                'type' => $docType,
                'path' => $relativePath,
                'fname' => $file['name'],
                'size' => $file['size']
            ]);

            Helper::setFlash('success', "Document '{$file['name']}' uploaded successfully!");
        } else {
            Helper::setFlash('danger', 'Failed to store uploaded file on server.');
        }

        Helper::redirect('student/documents');
    }
}

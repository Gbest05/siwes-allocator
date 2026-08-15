<?php
/**
 * Global Application Configuration Settings
 */

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
$detectedUrl = rtrim($protocol . $host . $scriptDir, '/');

return [
    'name'        => 'SIWES Allocation Management System',
    'short_name'  => 'SIWES Allocator',
    'base_url'    => getenv('APP_URL') ?: ($detectedUrl ?: 'http://localhost:8000'),
    'upload_dir'  => __DIR__ . '/../public/uploads/',
    'max_upload_size' => 5 * 1024 * 1024, // 5 MB
    'allowed_doc_types' => ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'],
    'session'     => [
        'name'     => 'SIWES_SESS_ID',
        'lifetime' => 86400, // 24 hours
    ]
];

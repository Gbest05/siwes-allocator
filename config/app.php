<?php
/**
 * Global Application Configuration Settings
 */

// Detect HTTPS behind reverse proxies (Render, Cloudflare, AWS, etc.)
$isHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
);

$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
if ($scriptDir === '/' || $scriptDir === '.') {
    $scriptDir = '';
}

// Compute relative and absolute base paths
$detectedUrl = rtrim($protocol . $host . $scriptDir, '/');

// Base URL: In root deployments (like Docker, Render, PHP dev server), use relative base path so CSS/JS never suffer from Mixed Content
$baseUrl = getenv('APP_URL') ?: (empty($scriptDir) ? '' : $scriptDir);

return [
    'name'              => 'SIWES Allocation Management System',
    'short_name'        => 'SIWES Allocator',
    'base_url'          => $baseUrl,
    'full_url'          => $detectedUrl,
    'upload_dir'        => __DIR__ . '/../public/uploads/',
    'max_upload_size'   => 5 * 1024 * 1024, // 5 MB
    'allowed_doc_types' => ['pdf', 'png', 'jpg', 'jpeg', 'doc', 'docx'],
    'session'           => [
        'name'     => 'SIWES_SESS_ID',
        'lifetime' => 86400, // 24 hours
    ]
];

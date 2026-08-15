<?php

namespace App\Core;

class Helper {

    public static function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public static function csrfToken(): string {
        Auth::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(string $token): bool {
        Auth::initSession();
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function setFlash(string $type, string $message): void {
        Auth::initSession();
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public static function getFlash(): ?array {
        Auth::initSession();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function redirect(string $url): void {
        $baseUrl = require __DIR__ . '/../../config/app.php';
        $fullUrl = (strpos($url, 'http') === 0) ? $url : rtrim($baseUrl['base_url'], '/') . '/index.php?route=' . ltrim($url, '/');
        header("Location: {$fullUrl}");
        exit;
    }

    public static function jsonResponse(array $data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Smart SIWES Student-Company Compatibility Score Engine (0-100%)
     * 
     * Criteria:
     * - Department & Programme Match (+30 pts)
     * - Industry Preference Match   (+30 pts)
     * - Location Preference Match   (+20 pts)
     * - Available Capacity Slots    (+20 pts)
     */
    public static function calculateMatchScore(
        string $departmentName,
        string $preferredIndustry,
        string $preferredLocation,
        string $companyIndustry,
        string $companyState,
        string $companyCity,
        int $availableSlots
    ): array {
        $deptScore = 0;
        $industryScore = 0;
        $locationScore = 0;
        $slotScore = 0;

        // 1. Department Match (+30)
        $deptLower = strtolower($departmentName);
        $compIndLower = strtolower($companyIndustry);
        
        if (
            (str_contains($deptLower, 'computer') || str_contains($deptLower, 'information')) &&
            (str_contains($compIndLower, 'software') || str_contains($compIndLower, 'it') || str_contains($compIndLower, 'fintech') || str_contains($compIndLower, 'telecom'))
        ) {
            $deptScore = 30;
        } elseif (
            str_contains($deptLower, 'electrical') &&
            (str_contains($compIndLower, 'power') || str_contains($compIndLower, 'energy') || str_contains($compIndLower, 'hardware') || str_contains($compIndLower, 'telecom'))
        ) {
            $deptScore = 30;
        } else {
            $deptScore = 20; // General engineering/science overlap
        }

        // 2. Industry Match (+30)
        if (strtolower(trim($preferredIndustry)) === strtolower(trim($companyIndustry)) || str_contains(strtolower($companyIndustry), strtolower($preferredIndustry))) {
            $industryScore = 30;
        } elseif (str_contains(strtolower($preferredIndustry), 'it') || str_contains(strtolower($companyIndustry), 'it')) {
            $industryScore = 20;
        } else {
            $industryScore = 10;
        }

        // 3. Location Match (+20)
        $locPrefLower = strtolower(trim($preferredLocation));
        $stateLower = strtolower(trim($companyState));
        $cityLower = strtolower(trim($companyCity));

        if ($locPrefLower === $stateLower || $locPrefLower === $cityLower || str_contains($cityLower, $locPrefLower) || str_contains($stateLower, $locPrefLower)) {
            $locationScore = 20;
        } else {
            $locationScore = 5;
        }

        // 4. Available Slots (+20)
        if ($availableSlots > 10) {
            $slotScore = 20;
        } elseif ($availableSlots >= 5) {
            $slotScore = 15;
        } elseif ($availableSlots > 0) {
            $slotScore = 10;
        } else {
            $slotScore = 0; // No slots left
        }

        $totalScore = $deptScore + $industryScore + $locationScore + $slotScore;

        return [
            'total_score' => $totalScore,
            'dept_score'  => $deptScore,
            'industry_score' => $industryScore,
            'location_score' => $locationScore,
            'slot_score' => $slotScore,
            'badge' => $totalScore >= 85 ? 'Excellent Match' : ($totalScore >= 70 ? 'High Match' : 'Moderate Match')
        ];
    }

    public static function getSetting(string $key, string $default = ''): string {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1");
            $stmt->execute(['key' => $key]);
            $val = $stmt->fetchColumn();
            return ($val !== false && $val !== null && $val !== '') ? $val : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    public static function getAllSettings(): array {
        $defaults = [
            'site_name'            => 'SIWES Allocator',
            'institution_name'     => 'School of Technology & Applied Sciences',
            'site_logo'            => '',
            'hero_badge'           => 'Digital SIWES Management Platform',
            'hero_title'           => 'Simplifying SIWES Placement and Allocation',
            'hero_description'     => 'A smart digital platform for managing student SIWES registration, company placement, allocation, and monitoring efficiently.',
            'hero_image'           => 'images/hero.jpg',
            'about_badge'          => 'About SIWES Portal',
            'about_title'          => 'Modernizing Industrial Training Placement',
            'about_description_1'  => 'The Student Industrial Work Experience Scheme (SIWES) is a mandatory skills training program designed to bridge the gap between theoretical knowledge acquired in institutions and practical industrial work environment experience.',
            'about_description_2'  => 'Our digital platform eliminates manual application delays, paper file loss, and allocation bias through a multi-factor smart matching engine that pairs students with partner organizations based on department relevance, preferred industry, and geographical location.',
            'about_image'          => 'images/about.jpg',
            'cta_title'            => 'Ready to simplify SIWES management?',
            'cta_description'      => 'Experience a faster, paperless, and intelligent student industrial work experience scheme.',
            'footer_description'   => 'An advanced enterprise management system designed for polytechnic and university SIWES units, supporting student registration, company slot management, and intelligent allocation.',
            'contact_address'      => 'SIWES Directorate, Admin Block',
            'contact_email'        => 'siwes@institution.edu.ng',
            'contact_phone'        => '+234 803 123 4567',
        ];

        try {
            $db = Database::getInstance();
            $rows = $db->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
            foreach ($rows as $row) {
                if (!empty($row['setting_key']) && $row['setting_value'] !== null) {
                    $defaults[$row['setting_key']] = $row['setting_value'];
                }
            }
        } catch (\Exception $e) {
            // fallback to defaults
        }

        return $defaults;
    }

    public static function setSetting(string $key, string $value): void {
        $db = Database::getInstance();
        $check = $db->prepare("SELECT id FROM settings WHERE setting_key = :k");
        $check->execute(['k' => $key]);
        if ($check->fetch()) {
            $stmt = $db->prepare("UPDATE settings SET setting_value = :v, updated_at = CURRENT_TIMESTAMP WHERE setting_key = :k");
            $stmt->execute(['v' => $value, 'k' => $key]);
        } else {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)");
            $stmt->execute(['k' => $key, 'v' => $value]);
        }
    }
}

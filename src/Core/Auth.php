<?php

namespace App\Core;

class Auth {

    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('SIWES_SESS_ID');
            session_start();
        }
    }

    public static function user(): ?array {
        self::initSession();
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool {
        return self::user() !== null;
    }

    public static function id(): ?int {
        $user = self::user();
        return $user ? (int)$user['id'] : null;
    }

    public static function role(): ?string {
        $user = self::user();
        return $user ? $user['role'] : null;
    }

    public static function login(array $user): void {
        self::initSession();
        // Prevent session fixation
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar_url' => $user['avatar_url'] ?? null
        ];
    }

    public static function logout(): void {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function requireAuth(): void {
        if (!self::check()) {
            Helper::setFlash('warning', 'Please log in to access this page.');
            Helper::redirect('login');
        }
    }

    public static function requireRole(array|string $roles): void {
        self::requireAuth();
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array(self::role(), $allowed, true)) {
            Helper::setFlash('danger', 'Access denied. You do not have permission to view this section.');
            Helper::redirect('dashboard');
        }
    }
}

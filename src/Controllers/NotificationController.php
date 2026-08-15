<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Helper;

class NotificationController {

    public function index(): void {
        Auth::requireAuth();
        $db = Database::getInstance();
        $userId = Auth::id();

        // Mark all as read when opening notification drawer
        $db->exec("UPDATE notifications SET is_read = TRUE WHERE user_id = {$userId}");

        $notifications = $db->query("SELECT * FROM notifications WHERE user_id = {$userId} ORDER BY created_at DESC")->fetchAll();

        require __DIR__ . '/../Views/notifications.php';
    }
}

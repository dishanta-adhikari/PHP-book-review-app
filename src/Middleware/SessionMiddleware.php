<?php

namespace App\Middleware;

use Exception;

class SessionMiddleware
{
    public static function verifySession(string $role): void
    {

        if (session_status() === PHP_SESSION_NONE) { // Start session if not already started
            session_start();
        }

        $allowedRoles = ['user', 'author'];

        try {
            if (!in_array($role, $allowedRoles, true)) {
                throw new Exception("Role '{$role}' is not allowed.");
            }

            $sessionKey = $role . '_id';

            if (isset($_SESSION[$sessionKey])) {
                header("Location: " . APP_URL . "/src/views/{$role}/dashboard");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . AUTH_URL);
            exit;
        }
    }

    public static function validateLoggedInSession(string $role): void
    {
        $sessionKey = $role . '_id';
        if (!isset($_SESSION[$sessionKey])) {
            header("Location: " . AUTH_URL . "/{$role}/login");
            exit;
        }
    }
}

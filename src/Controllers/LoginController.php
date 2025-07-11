<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Author;
use Exception;

class LoginController
{
    private $User, $Author;

    public function __construct($db)
    {
        $this->User = new User($db);
        $this->Author = new Author($db);
    }

    public function login(array $values) //email,password and role
    {
        try {
            $email = trim($values['email']);
            $password = trim($values['password']);
            $role = trim($values['role'] ?? 'user');

            if (empty($email) || empty($role) || empty($password)) {
                throw new Exception("Required fields are Empty!");
            }

            $allowedRole = ['user', 'author'];

            if (!in_array($role, $allowedRole)) {
                throw new Exception("Invalid role selected.");
            }

            if ($role === 'user') {
                $result = $this->User->getUserByEmail($email);
            } else {
                $result = $this->Author->getAuthorByEmail($email);
            }

            if (!$result) {
                throw new Exception("No account found for this email.");
            }

            if (!password_verify($password, $result['password'])) {
                throw new Exception("Invalid Password!");
            }

            session_regenerate_id(true); //regenerate the id after login

            $_SESSION[$role . '_id'] = $result['id'];
            $_SESSION['role'] = $role;

            $name = ucfirst($result['name']);
            $_SESSION['success'] = "Welcome back {$name} !";
            header("Location: " . APP_URL . "/src/views/{$role}/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: " . AUTH_URL . "/{$role}/login");
            exit;
        }
    }
}

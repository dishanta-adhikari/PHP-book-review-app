<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Author;
use Exception;

class RegisterController
{
    private $User, $Author;

    public function __construct($db)
    {
        $this->User = new User($db);
        $this->Author = new Author($db);
    }

    public function register(array $values)
    {
        try {

            if (
                empty($values['name']) ||
                empty($values['email']) ||
                empty($values['phone']) ||
                empty($values['address']) ||
                empty($values['password']) ||
                empty($values['role'])
            ) {
                throw new Exception("Required fields are empty!");
            }

            $role     = trim($values['role']);
            $name     = trim($values['name']);
            $email    = trim($values['email']);
            $phone    = (int) $values['phone'];
            $address  = trim($values['address']);
            $password = trim($values['password']);

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $values = [
                'name'     => $name,
                'email'   => $email,
                'phone'    =>  $phone,
                'address'  =>  $address,
                'password' => $hashedPassword
            ];

            $verifyEmail = ($role === 'author')     //check duplicate accounts for respected roles
                ? $this->Author->getAuthorByEmail($email)
                : $this->User->getUserByEmail($email);


            if ($verifyEmail) {
                throw new Exception("Email already exists!");
            }

            $created = ($role === 'author')     //create accounts for respective roles
                ? $this->Author->create($values)
                : $this->User->create($values);


            if (!$created) {
                throw new Exception("Registration Failed. Please try again!");
            }

            $_SESSION[$role . '_id'] = $created['id'];
            $_SESSION['user_name'] = $created['name'] ?? $name;
            $_SESSION['role'] = $role;

            session_regenerate_id(true);

            $_SESSION['success'] = "Registration Successfull . Welcome {$name} !";
            header("Location:" . APP_URL . "/src/views/{$role}/dashboard");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location:" . AUTH_URL . "/{$role}/register");
            exit;
        }
    }
}

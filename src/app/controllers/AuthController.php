<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Database;
use Exception;

class AuthController extends BaseController
{
    protected $db;
    protected $errors = [];

    public function __construct()
    {
        parent::__construct();
        $this->db = new Database(require 'src/config/config.php');
    }





    /* LOGIN METHODS */






    // #[Route('/login', 'GET')]
    public function showLogin()
    {
        echo $this->view->renderWithLayout('auth/login.view.php', 'layouts/main.php', [
            'title' => 'Login',
            'errors' => $this->errors,
            'email' => '',
            'emailErr' => '',
            'passErr' => '',
            'invalid' => ''
        ]);
    }

    // #[Route('/login', 'POST')]
    public function handleLogin()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $emailErr = $passErr = $invalid = '';

        // Validation
        if (empty($email)) {
            $emailErr = "Email is required";
        }

        if (empty($password)) {
            $passErr = "Password is required";
        }

        // Only proceed if no validation errors
        if (empty($emailErr) && empty($passErr)) {
            $query = "SELECT * FROM users WHERE email = :email";
            $stmt = $this->db->connection->prepare($query);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: /home");
                exit();
            } else {
                $invalid = "Invalid email or password";
            }
        }

        // If login failed, show form again with errors
        echo $this->view->renderWithLayout('auth/login.view.php', 'layouts/main.php', [
            'title' => 'Login',
            'errors' => $this->errors,
            'emailErr' => $emailErr,
            'passErr' => $passErr,
            'invalid' => $invalid,
            'email' => $email
        ]);
    }

    /* SIGNUP METHODS */

    // #[Route('/signup', 'GET')]
    public function showSignup()
    {
        echo $this->view->renderWithLayout('auth/signup.view.php', 'layouts/main.php', [
            'title' => 'Sign Up',
            'errors' => $this->errors,
            'input' => [
                'name' => '',
                'email' => '',
                'age' => '',
            ],
            'fieldErrors' => [
                'name' => '',
                'email' => '',
                'password' => '',
                'age' => '',
                'image' => ''
            ]
        ]);
    }

    // #[Route('/signup', 'POST')]
    public function handleSignup()
    {
        $input = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'age' => $_POST['age'] ?? '',
        ];

        $fieldErrors = $this->validateSignup($input);

        if (empty(array_filter($fieldErrors))) {
            try {
                $this->createUser($input);
                header("Location: /login");
                exit();
            } catch (Exception $e) {
                $this->errors[] = "Registration failed. Please try again.";
            }
        }

        echo $this->view->renderWithLayout('auth/signup.view.php', 'layouts/main.php', [
            'title' => 'Sign Up',
            'errors' => $this->errors,
            'input' => $input,
            'fieldErrors' => $fieldErrors
        ]);
    }

    protected function validateSignup(array $input): array
    {
        $errors = [
            'name' => '',
            'email' => '',
            'password' => '',
            'age' => '',
            'image' => ''
        ];

        // Name validation
        if (empty($input['name'])) {
            $errors['name'] = "Name is required";
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $input['name'])) {
            $errors['name'] = "Name must contain only letters";
        }

        // Email validation
        if (empty($input['email'])) {
            $errors['email'] = "Email is required";
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email format";
        } else {
            $stmt = $this->db->connection->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $input['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = "Email already registered";
            }
        }

        // Password validation
        if (empty($input['password'])) {
            $errors['password'] = "Password is required";
        } elseif (strlen($input['password']) < 8) {
            $errors['password'] = "Password must be at least 8 characters";
        }

        // Age validation
        if (empty($input['age'])) {
            $errors['age'] = "Age is required";
        } elseif (!is_numeric($input['age']) || $input['age'] < 18 || $input['age'] > 80) {
            $errors['age'] = "Age must be between 18-80";
        }

        // Image validation
        if (!empty($_FILES['fileToUpload']['name'])) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $fileExt = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExt, $allowed)) {
                $errors['image'] = "Only JPG, JPEG, PNG allowed";
            }
        }

        return $errors;
    }

    protected function createUser(array $input): void
    {
        $passwordHash = password_hash($input['password'], PASSWORD_BCRYPT);

        $this->db->connection->beginTransaction();

        try {
            // Insert user
            $stmt = $this->db->connection->prepare(
                "INSERT INTO users (name, age, email, password) VALUES (:name, :age, :email, :password)"
            );
            $stmt->execute([
                'name' => $input['name'],
                'age' => $input['age'],
                'email' => $input['email'],
                'password' => $passwordHash
            ]);

            $userId = $this->db->connection->lastInsertId();

            // Handle image upload if present
            if (!empty($_FILES['fileToUpload']['name'])) {
                $fileExt = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));
                $fileName = uniqid() . '.' . $fileExt;
                $destination = 'uploads/' . $fileName;

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }

                if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $destination)) {
                    $mediaStmt = $this->db->connection->prepare(
                        "INSERT INTO media (user_id, image_path) VALUES (:user_id, :image_path)"
                    );
                    $mediaStmt->execute([
                        'user_id' => $userId,
                        'image_path' => $fileName
                    ]);
                }
            }

            $this->db->connection->commit();
        } catch (Exception $e) {
            $this->db->connection->rollBack();
            throw $e;
        }
    }

    // #[Route('/logout', 'GET')]
    public function logout()
    {
        session_destroy();
        header("Location: /login");
        exit();
    }
}

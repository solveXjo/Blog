<?php

namespace App\Controllers;

use App\Models\UserRepository;
use Exception;
use App\Core\BaseController;
use App\Core\Route;

class ProfileController extends BaseController
{
    private $errors = [];
    private $success = [];
    protected $userRepo;

    public function __construct()
    {
        parent::__construct();
        $this->userRepo = new UserRepository($this->app->db);
    }
    public function show()
    {
        $user = $this->getProfile();
        if (!$user) {
            header("Location: /Login");
            exit();
        }

        echo $this->view->renderWithLayout('profile.view.php', 'layouts/main.php', [
            $this->view->title=($user['name']) . "'s Profile",
            'user' => $user,
            'errors' => $this->errors,
            'success' => $this->success
        ]);
    }

    // #[Route('/profile/update', 'POST')]
    public function handleProfileUpdate()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /Login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        if (isset($_POST['change_password'])) {
            $this->handleChangePassword($userId);
        } elseif (isset($_POST['update_profile'])) {
            $this->handleUpdateProfile($userId);
        } elseif (isset($_POST['delete_account'])) {
            $this->handleDeleteAccount($userId);
        } elseif (isset($_FILES['image'])) {
            $this->handleUploadImage($userId);
        }

        header("Location: /profile");
        exit();
    }

    protected function getProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return $this->userRepo->getUserById($_SESSION['user_id']);
    }

    protected function handleChangePassword($userId)
    {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $this->errors[] = "New passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $this->errors[] = "Password length should be more than 7 characters";
        } elseif (!$this->userRepo->changePassword($userId, $newPassword)) {
            $this->errors[] = "Failed to update password.";
        } else {
            $this->success[] = "Password updated successfully!";
        }
    }

    protected function handleUpdateProfile($userId)
    {
        $name = $_POST['name'] ?? '';
        $title = $_POST['title'] ?? '';
        $age = $_POST['age'] ?? '';
        $email = $_POST['email'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $location = $_POST['location'] ?? 'Palestine';

        if (empty($name) || empty($age) || empty($email)) {
            $this->errors[] = "Name, age, and email are required fields.";
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Invalid email format";
            return;
        }



        if ($age > 80 || $age < 18) {
            $this->errors[] = "The age must be between 18 and 80";
            return;
        }

        if ($this->userRepo->updateUser(
            userId: $userId,
            name: $name,
            title: $title,
            age: $age,
            email: $email,
            bio: $bio,
            location: $location
        )) {
            $this->success[] = "Profile updated successfully!";
        } else {
            $this->errors[] = "Failed to update profile.";
        }
    }

    protected function handleDeleteAccount($userId)
    {
        if ($this->userRepo->deleteUser($userId)) {
            session_destroy();
            header("Location: /Login");
            exit();
        } else {
            $this->errors[] = "Failed to delete account.";
        }
    }

    protected function handleUploadImage($userId)
    {
        try {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $this->errors[] = "File upload error.";
                return;
            }

            $file = $_FILES['image'];
            $fileName = basename($file['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $maxFileSize = 5 * 1024 * 1024;

            if (!in_array($fileExt, $allowedExtensions)) {
                $this->errors[] = "Only JPG, JPEG, PNG files are allowed.";
                return;
            }

            if ($file['size'] > $maxFileSize) {
                $this->errors[] = "File size must be less than 5MB.";
                return;
            }

            $newFileName = uniqid('', true) . '.' . $fileExt;
            $uploadDir = 'uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], "{$uploadDir}{$newFileName}")) {
                $oldImage = $this->userRepo->getUserImage($userId);
                if ($oldImage && $oldImage !== 'default.png' && file_exists("{$uploadDir}{$oldImage}")) {
                    unlink("{$uploadDir}{$oldImage}");
                }

                if ($this->userRepo->updateImage($userId, $newFileName)) {
                    $this->success[] = "Profile image updated successfully!";
                } else {
                    $this->errors[] = "Failed to update image in database.";
                }
            } else {
                $this->errors[] = "Failed to upload image.";
            }
        } catch (Exception $e) {
            $this->errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}

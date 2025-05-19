<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\UserRepository;

use App\Core\Route;
use App\Core\App;
use Exception;

class EditProfileController extends BaseController
{
    protected $success = '';
    protected $error = '';

    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['profile_update_success'])) {
            $_SESSION['profile_update_success'] = '';
        }
        if (!isset($_SESSION['profile_update_error'])) {
            $_SESSION['profile_update_error'] = '';
        }
    }

    // #[Route('/profile_edit', 'GET')]


    public function show()
    {
        $user = $this->getProfile();

        $this->handleProfileUpdate($user['id']);

        $success = $this->getSuccessMessage();
        $error = $this->getErrorMessage();

        echo $this->view->renderWithLayout('profile_edit.view.php', 'layouts/main.php', [
            'title' => "Edit Profile",
            'user' => $user,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function getProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /Login");
            exit();
        }
        return $this->app->userRepo->getUserById($_SESSION['user_id']);
    }

    public function deleteAccount($userId)
    {
        if (isset($_POST['delete_account'])) {
            if ($this->app->userRepo->deleteUser($userId)) {
                session_destroy();
                header("Location: /Login");
                exit();
            } else {
                $_SESSION['profile_update_error'] = "Failed to delete account.";
                return false;
            }
        }
        return false;
    }

    public function changePassword($userId)
    {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $_SESSION['profile_update_error'] = "New passwords do not match.";
            return false;
        } elseif (strlen($newPassword) < 8) {
            $_SESSION['profile_update_error'] = "Password length should be more than 7 characters.";
            return false;
        } else {
            if ($this->app->userRepo->changePassword($userId, $newPassword)) {
                $_SESSION['profile_update_success'] = "Password updated successfully!";
                header("Location: /profile");
                exit();
            } else {
                $_SESSION['profile_update_error'] = "Failed to update password.";
                return false;
            }
        }
    }

    public function changeImage($userId)
    {
        try {
            $file = $_FILES['image'];
            $fileName = basename($file['name']);
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $maxFileSize = 5 * 1024 * 1024;

            if (!in_array($fileExt, $allowedExtensions)) {
                $_SESSION['profile_update_error'] = "Only JPG, JPEG, PNG files are allowed.";
                return false;
            } elseif ($file['size'] > $maxFileSize) {
                $_SESSION['profile_update_error'] = "File size must be less than 5MB.";
                return false;
            } else {
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $uploadDir = 'uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], "{$uploadDir}{$newFileName}")) {
                    $oldImage = $this->app->userRepo->updateImage($userId, $newFileName);
                    $_SESSION['profile_update_success'] = "Profile image updated successfully!";
                    header("Location: /profile");
                    exit();
                } else {
                    $_SESSION['profile_update_error'] = "Failed to upload image.";
                    return false;
                }
            }
        } catch (Exception $e) {
            $_SESSION['profile_update_error'] = "An error occurred: " . $e->getMessage();
            return false;
        }
    }

    public function updateProfile($userId)
    {
        $name = $_POST['name'] ;
        $title = $_POST['title'];
        $age = $_POST['age'] ;
        $email = $_POST['email'] ;
        $bio = $_POST['bio'] ;
        $location = $_POST['location'] ?? 'Palestine';

        $stmt = $this->app->db->connection->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $userId]);

        if (!$name || !$age || !$email) {
            $_SESSION['profile_update_error'] = "Name, age, and email are required fields.";
            return false;
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['profile_update_error'] = "Invalid email format.";
            return false;
        }
        elseif ($stmt->fetch()) {
            $_SESSION['profile_update_error'] = "Email already registered by another user.";
            return false;
        }
        elseif ($age > 80 || $age < 18) {
            $_SESSION['profile_update_error'] = "The age must be between 18 and 80.";
            return false;
        }
        else {
            if ($this->app->userRepo->updateUser($userId, $name, $title, $age, $email, $bio, $location)) {
                $_SESSION['profile_update_success'] = "Profile updated successfully!";
                return true;
            } else {
                $_SESSION['profile_update_error'] = "Failed to update profile.";
                return false;
            }
        }
    }

    public function handleProfileUpdate($userId)
    {
        if (isset($_POST['name']) || isset($_POST['age']) || isset($_POST['email']) || isset($_POST['bio']) || isset($_POST['location']) || isset($_POST['title'])) {
            $this->updateProfile($userId);
        }

        if ((isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK)) {
            $this->changeImage($userId);
        }

        if (isset($_POST['delete_account'])) {
            $this->deleteAccount($userId);
        }

        if (isset($_POST['change_password'])) {
            $this->changePassword($userId);
        }
    }

    public function getSuccessMessage()
    {
        $success = '';
        if (isset($_SESSION['profile_update_success']) && !empty($_SESSION['profile_update_success'])) {
            $success = $_SESSION['profile_update_success'];
            $_SESSION['profile_update_success'] = '';
        }
        return $success;
    }

    public function getErrorMessage()
    {
        $error = '';
        if (isset($_SESSION['profile_update_error']) && !empty($_SESSION['profile_update_error'])) {
            $error = $_SESSION['profile_update_error'];
            $_SESSION['profile_update_error'] = '';
        }
        return $error;
    }
}

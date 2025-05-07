<?php


$success = '';
$error = '';
$_SESSION['profile_update_success'] = $success;
$_SESSION['profile_update_error'] = $error;
class EditProfileController
{
    private $db;
    private $userRepo;



    public function __construct(Database $db, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    public function getProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /Login");
            exit();
        }
        return $this->userRepo->getUserById($_SESSION['user_id']);
    }


    public function deleteAccount($userId)
    {
        if (isset($_POST['delete_account'])) {
            if ($this->userRepo->deleteUser($userId)) {
                session_destroy();
                header("Location: /Login");
                exit();
            } else {
                $error = "Failed to delete account.";

                return $error;
            }
        }
    }

    public function changePassword($userId)
    {

        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $error = "Password length should be more than 7 characters.";
        } else {
            if ($this->userRepo->changePassword($userId, $newPassword)) {
                $success = "Password updated successfully!";
                header("Location: /profile");
                return $success;
            } else {
                $error = "Failed to update password.";
                return $error;
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
                $error = "Only JPG, JPEG, PNG files are allowed.";
            } elseif ($file['size'] > $maxFileSize) {
                $error = "File size must be less than 5MB.";
                return $error;
            } else {
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $uploadDir = 'uploads/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file['tmp_name'], "{$uploadDir}{$newFileName}")) {
                    $oldImage = $this->userRepo->updateImage($userId, $newFileName);
                    $success = "Profile image updated successfully!";
                    header("Location: /profile");
                    return $success;
                } else {
                    $error = "Failed to upload image.";
                    return $error;
                }
            }
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
            return $error;
        }
    }

    public function updateProfile($userId)
    {
        $name = $_POST['name'];
        $title = $_POST['title'];
        $age = $_POST['age'];
        $email = $_POST['email'];
        $bio = $_POST['bio'];
        $location = $_POST['location'] ?? 'Palestine';

        $stmt = $this->db->connection->prepare("SELECT * FROM users WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $userId]);

        if (!$name || !$age || !$email) {
            $error = "Name, age, and email are required fields.";
            return $error;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
            return $error;
        } elseif ($stmt->fetch()) {
            $error = "Email already registered by another user.";
            return $error;
        } elseif ($age > 80 || $age < 18) {
            $error = "The age must be between 18 and 80.";
            return $error;
        } else {
            $this->userRepo->updateUser(userId: $userId, name: $name, title: $title, age: $age, email: $email, bio: $bio, location: $location);
            $success = "Profile updated successfully!";
            header("Location: /profile");
            return $success;
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
        if (isset($_SESSION['profile_update_success'])) {
            $success = $_SESSION['profile_update_success'];
            unset($_SESSION['profile_update_success']);
        }
        return $success;
    }
    public function getErrorMessage()
    {
        if (isset($_SESSION['profile_update_error'])) {
            $error = $_SESSION['profile_update_error'];
            unset($_SESSION['profile_update_error']);
        }
        return $error;
    }
}

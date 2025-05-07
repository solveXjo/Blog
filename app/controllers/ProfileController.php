<?php

class ProfileController
{
    private $db;
    private $userRepo;

    private $errors = [];
    private $success = [];
    private $errorMessage = '';
    private $successMessage = '';

    public function __construct(Database $db, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    public function getUser()
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
                header("Location: /profile_edit");
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
            header("Location: /profile_edit");
            return $error;
        } elseif (strlen($newPassword) < 8) {
            $error = "password length should be more than 7 charecters";
            header("Location: /profile_edit");
            return $error;
        } else {
            if ($this->userRepo->changePassword($userId, $newPassword)) {
                $success = "Password updated successfully!";
                return $success;
            } else {
                $error = "Current password is incorrect.";
                header("Location: /profile_edit");

                return $error;
            }
        }
    }

    public function uploadImage($userId)
    {
        try {

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $fileName = basename($file['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $maxFileSize = 5 * 1024 * 1024;

                if (!in_array($fileExt, $allowedExtensions)) {
                    $error = "Only JPG, JPEG, PNG files are allowed.";
                    header("Location: /profile_edit");
                    return $error;
                } elseif ($file['size'] > $maxFileSize) {
                    $error = "File size must be less than 5MB.";
                    header("Location: /profile_edit");
                    return $error;
                } else {
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

                        $this->userRepo->updateImage($userId, $newFileName);
                    } else {
                        $error = "Failed to upload image.";
                        header("Location: /profile_edit");

                        return $error;
                    }
                }
            }

            $success = "Profile updated successfully!";
            return $success;
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
            header("Location: /profile_edit");
            return $error;
        }
    }


    public function updateProfile($userId)
    {
        if (isset($_POST['name']) || isset($_POST['age']) || isset($_POST['email']) || isset($_POST['bio']) || isset($_POST['location']) || isset($_POST['title'])) {
            $name = $_POST['name'];
            $title = $_POST['title'];
            $age = $_POST['age'];
            $email = $_POST['email'];
            $bio = $_POST['bio'];
            $location = $_POST['location'];

            if ($location == "" || $location == NULL) {
                $location = 'Palestine';
            }


            $stmt = $this->db->connection->prepare("SELECT * FROM users WHERE email = :email and id != :id");
            $stmt->execute(['email' => $email, 'id' => $userId]);

            if (!$name || !$age || !$email) {
                $error = "Name, age, and email are required fields.";
                header("Location: /profile_edit");
                return $error;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format";
                header("Location: /profile_edit");

                return $error;
            } elseif ($stmt->fetch()) {
                header("Location: /profile_edit");
                $error = "Email already registered by another user";
                return $error;
            } elseif ($age > 80 || $age < 18) {
                $error = "the age must be between 18 and 80";
                header("Location: /profile_edit");

                return $error;
            } else {
                $this->userRepo->updateUser(userId: $userId, name: $name, title: $title, age: $age, email: $email, bio: $bio, location: $location);
                $success = "Profile updated successfully!";
                return $success;
            }
        }
    }




    public function getProfileUpdateMessage()
    {
        if (isset($_SESSION['profile_update_success'])) {
            $success = $_SESSION['profile_update_success'];
            unset($_SESSION['profile_update_success']);
            return $success;
        }
        if (isset($_SESSION['profile_update_error'])) {
            $error = $_SESSION['profile_update_error'];
            unset($_SESSION['profile_update_error']);
            header("Location: /profile_edit");
            return $error;
        }
    }

    public function getProfile()
    {
        $user = $this->getUser();
        if (!$user) {
            header("Location: /Login");
            exit();
        }
        return $user;
    }

    public function handleProfileUpdate($userId)
    {

        if (isset($_POST['change_password'])) {
            $this->changePassword($userId);
        }
        if (isset($_POST['update_profile'])) {
            $this->updateProfile($userId);
        }
        if (isset($_POST['delete_account'])) {
            $this->deleteAccount($userId);
        }
        if (isset($_FILES['image'])) {
            $this->uploadImage($userId);
        }
    }
}

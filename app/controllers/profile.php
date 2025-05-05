<?php



if (!isset($_SESSION['user_id'])) {
    header("Location: /Login");
    exit();
}

$db = new Database(require 'config/config.php');
$userRepo = new UserRepository($db);
$userId = $_SESSION['user_id'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_account'])) {
        // Handle account deletion
        if ($userRepo->deleteUser($userId)) {
            session_destroy();
            header("Location: /Login");
            exit();
        } else {
            $error = "Failed to delete account.";
        }
    } elseif (isset($_POST['change_password'])) {
        $newPassword = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match.";
        } elseif (strlen($newPassword) < 8) {
            $error = "password length should be more than 7 charecters";
        } else {
            if ($userRepo->changePassword($userId, $newPassword)) {
                $success = "Password updated successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        try {

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['image'];
                $fileName = basename($file['name']);
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $maxFileSize = 5 * 1024 * 1024;

                if (!in_array($fileExt, $allowedExtensions)) {
                    $error = "Only JPG, JPEG, PNG files are allowed.";
                } elseif ($file['size'] > $maxFileSize) {
                    $error = "File size must be less than 5MB.";
                } else {
                    $newFileName = uniqid('', true) . '.' . $fileExt;
                    $uploadDir = 'uploads/';

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    if (move_uploaded_file($file['tmp_name'], "{$uploadDir}{$newFileName}")) {
                        $oldImage = $userRepo->getUserImage($userId);
                        if ($oldImage && $oldImage !== 'default.png' && file_exists("{$uploadDir}{$oldImage}")) {
                            unlink("{$uploadDir}{$oldImage}");
                        }

                        $userRepo->updateImage($userId, $newFileName);
                    } else {
                        $error = "Failed to upload image.";
                    }
                }
            }

            $success = "Profile updated successfully!";
        } catch (Exception $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    } elseif (isset($_POST['name']) || isset($_POST['age']) || isset($_POST['email']) || isset($_POST['bio']) || isset($_POST['location']) || isset($_POST['title'])) {
        $name = $_POST['name'];
        $title = $_POST['title'];
        $age = $_POST['age'];
        $email = $_POST['email'];
        $bio = $_POST['bio'];
        $location = $_POST['location'];

        if ($location == "" || $location == NULL) {
            $location = 'Palestine';
        }


        $stmt = $db->connection->prepare("SELECT * FROM users WHERE email = :email and id != :id");
        $stmt->execute(['email' => $email, 'id' => $userId]);

        if (!$name || !$age || !$email) {
            $error = "Name, age, and email are required fields.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } elseif ($stmt->fetch()) {
            $error = "Email already registered by another user";
        } elseif ($age > 80 || $age < 18) {
            $error = "the age must be between 18 and 80";
        } else {
            $userRepo->updateUser(userId: $userId, name: $name, title: $title, age: $age, email: $email, bio: $bio, location: $location);
            $success = "Profile updated successfully!";
        }
    }

    $user = $userRepo->getUserById($userId);

    $_SESSION['profile_update_success'] = $success;
    $_SESSION['profile_update_error'] = $error;
    header("Location: /profile");
    exit();
}

if (isset($_SESSION['profile_update_success'])) {
    $success = $_SESSION['profile_update_success'];
    unset($_SESSION['profile_update_success']);
}
if (isset($_SESSION['profile_update_error'])) {
    $error = $_SESSION['profile_update_error'];
    unset($_SESSION['profile_update_error']);
}

$user = $userRepo->getUserById($userId);
// require 'profile_edit.php';

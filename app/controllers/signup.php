<?php
session_start();

$db = new Database(require 'config/config.php');



$nameErr = $emailErr = $passErr = $imageErr = $ageErr = "";
$name = $age = $email = $password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {

        $name = $_POST["name"];
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $nameErr = "Name must contain only letters";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST["email"];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        } else {
            $stmt = $db->connection->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            if ($stmt->fetch()) {
                $emailErr = "Email already registered";
            }
        }
    }

    if (empty($_POST["password"])) {
        $passErr = "Password is required";
    } else if (strlen($_POST["password"]) < 8) {
        $passErr = "Password length should be greater than 7 characters";
    } else {
        $password = $_POST["password"];
    }

    if (empty($_POST["age"])) {
        $ageErr = "Age is required";
    } else {
        $age = $_POST["age"];
        if (!is_numeric($age) || $age < 18 || $age > 80) {
            $ageErr = "Age must be 18-80";
        }
    }

    if (!empty($_FILES['fileToUpload']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $fileExt = strtolower(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowed)) {
            $imageErr = "Only JPG, JPEG, PNG allowed";
        }
    }

    if (empty($nameErr) && empty($emailErr) && empty($passErr) && empty($ageErr) && empty($imageErr)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->connection->prepare("INSERT INTO users (name, age, email, password) VALUES (:name, :age, :email, :password)");
        $stmt->execute([
            'name' => $name,
            'age' => $age,
            'email' => $email,
            'password' => $passwordHash
        ]);

        $userId = $db->connection->lastInsertId();

        if (!empty($_FILES['fileToUpload']['name'])) {
            $fileName = uniqid() . '.' . $fileExt;
            $destination = 'uploads/' . $fileName;
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $destination);

            $mediaStmt = $db->connection->prepare("INSERT INTO media (user_id, image_path) VALUES (:user_id, :image_path)");
            $mediaStmt->execute(['user_id' => $userId, 'image_path' => $fileName]);
        }

        header("Location: /Login");
        exit();
    }
}

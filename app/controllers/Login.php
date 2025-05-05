<?php
session_start();


$db = new Database(require 'config/config.php');
$invalid = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $emailErr = $passErr = "";

    if (empty($_POST['email'])) {
        $emailErr = "Email is required";
    } else {
        $email = $_POST['email'];
    }

    if (empty($_POST['password'])) {
        $passErr = "Password is required";
    } else {
        $password = $_POST['password'];
    }

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->connection->prepare($query);
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

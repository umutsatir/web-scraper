<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    try {
        $gump = new GUMP();
        $_POST = $gump->sanitize($_POST);

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'] . "@" . $_POST['email-2'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception("Invalid email");
            exit;
        }

        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();

        $usernameExists = $db->select("users", "*", ["username" => $username]);
        $emailExists = $db->select("users", "*", ["email" => $email]);
        if ($usernameExists || $emailExists) {
            throw new Exception("Username or email already exists");
            exit;
        }
        $insertion = $pdo->prepare("INSERT INTO users (username, password, email, creationDate) VALUES (:username, :password, :email, now())");
        $insertion->execute(['username' => $username, 'password' => $password, 'email' => $email]);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        error_log('Error: ' . $e->getMessage(), 3, __DIR__ . '/logs/register.log');
    }
?>
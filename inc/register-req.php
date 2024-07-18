<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();

    try {
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $gump = new GUMP();
        $_POST = $gump->sanitize($_POST);

        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'] . "@" . $_POST['email-2'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new Exception("Invalid email");
        }
    
        $usernameExists = $db->get_results("SELECT * FROM users WHERE username = '$username'");
        $emailExists = $db->get_results("SELECT * FROM users WHERE email = '$email'");
        if ($usernameExists || $emailExists) {
            throw new Exception("Username or email already exists");
        }
        $insertion = $pdo->prepare("INSERT INTO users (username, password, email, creationDate) VALUES (:username, :password, :email, now())");
        $insertion->execute(['username' => $username, 'password' => $password, 'email' => $email]);
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: ../index.php');
    } catch (Exception $e) {
        header('Location: ../register.php?result=error&message=' . $e->getMessage());
        error_log('Error: ' . $e->getMessage() . "\n", 3, dirname(__DIR__) . '/logs/register.log');
    }
?>
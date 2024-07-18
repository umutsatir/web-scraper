<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";

    session_start();

    try {
        $gump = new GUMP();
        $_POST = $gump->sanitize($_POST);

        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $currPw = $db->get_var("SELECT password FROM users WHERE username = '{$_SESSION['username']}'");
        if (!password_verify($_POST['password'], $currPw)) {
            throw new Exception("Invalid password");
        }

        $query = $pdo->prepare("UPDATE users SET name = :name, surname = :surname, email = :email WHERE username = :username");
        $query->execute(['name' => $_POST['name'], 'surname' => $_POST['surname'], 'email' => $_POST['email'], 'username' => $_SESSION['username']]);
        header('Location: profile.php?result=success');
    } catch (Exception $e) {
        header('Location: profile.php?result=error');
        error_log('Error: ' . $e->getMessage() . "\n", 3, 'logs/profile.log');
    }
?>
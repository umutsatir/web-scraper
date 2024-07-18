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
        if (!password_verify($_POST['oldPassword'], $currPw)) {
            throw new Exception("Invalid password");
        }

        if ($_POST['newPassword'] !== $_POST['confirmPassword']) {
            throw new Exception("Passwords do not match");
        }

        $query = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
        $query->execute(['password' => password_hash($_POST['newPassword'], PASSWORD_DEFAULT), 'username' => $_SESSION['username']]);
        header('Location: ../profile.php?result=success&process=change-pw');
    } catch (Exception $e) {
        header('Location: ../profile.php?result=error&message=' . $e->getMessage());
        error_log('Error: ' . $e->getMessage() . "\n", 3, '../logs/profile.log');
    }
?>
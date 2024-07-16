<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();

    $gump = new GUMP();
    $_POST = $gump->sanitize($_POST);

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'] . "@" . $_POST['email-2'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo "Invalid email";
        exit;
    }

    $db = (new DB())->connect();
    $pdo = (new PDOClass())->connect();

    $usernameExists = $db->select("users", "*", ["username" => $username]);
    $emailExists = $db->select("users", "*", ["email" => $email]);
    if ($usernameExists || $emailExists) {
        echo "Username or email already exists";
        exit;
    }
    $insertion = $pdo->prepare("INSERT INTO users (username, password, email, creationDate) VALUES (:username, :password, :email, now())");
    $insertion->execute(['username' => $username, 'password' => $password, 'email' => $email]);
    if ($insertion) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>";
    }
?>
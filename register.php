<?php
    include './db.php';
    session_start();

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'] . "@" . $_POST['email-2'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo "Invalid email";
        exit;
    }

    $db = (new DB())->connect();

    $usernameExists = $db->select("users", "*", ["username" => $username]);
    $emailExists = $db->select("users", "*", ["email" => $email]);
    if ($usernameExists || $emailExists) {
        echo "Username or email already exists";
        exit;
    }
    $insertion = $db->query("INSERT INTO users (username, password, email, creationDate) VALUES ('{$username}', '{$password}', '{$email}', now())");
    if ($insertion) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>";
    }
?>
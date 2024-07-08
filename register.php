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

    $exists = $db->select("users", "*", ["username" => $username]);
    if ($exists) {
        echo "Username already exists";
        exit;
    }

    $insertion = $db->insert("users", ["username" => $username, "password" => $password, "email" => $email]);
    if ($insertion) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>";
    }
?>
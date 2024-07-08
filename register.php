<?php
    include './db.php';
    session_start();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'] . "@" . $_POST['email-2'];
    $db = (new DB())->connect();

    $exists = $db->get_results("SELECT * FROM users WHERE username = '{$username}'");
    if ($exists) {
        echo "Username already exists";
        exit;
    }

    $sql = "INSERT INTO users (username, password, email) VALUES ('{$username}', '{$password}', '{$email}')";
    if ($db->query($sql) == true) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>";
    }
?>
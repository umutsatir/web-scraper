<?php
    include './db.php';
    include 'gump.class.php';
    session_start();

    $gump = new GUMP();
    $_POST = $gump->sanitize($_POST);

    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $db = (new DB())->connect();
    $query = $db->get_row("SELECT * FROM users WHERE username = '{$username}'");
    if ($query && password_verify($password, $query->password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo 'Invalid username or password';
        error_log('Error: ' . $e->getMessage(), 3, 'logs/login.log');
    }
?>
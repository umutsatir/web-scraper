<?php
    include './db.php';
    session_start();

    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $db = (new DB())->connect();
    $query = $db->get_row("SELECT * FROM users WHERE username = '{$username}'");
    var_dump($query->password);
    if ($query && hash_equals($query->password, $password)) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
    } else {
        echo 'Invalid username or password';
    }
?>
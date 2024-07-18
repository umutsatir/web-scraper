<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();

    $gump = new GUMP();
    $_POST = $gump->sanitize($_POST);
    
    try {
        $article_id = $_POST['article_id'];
        $text = $_POST['text'];
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $sql = $pdo->prepare("UPDATE articles SET text = :text WHERE id = :id");
        $sql->execute(['text' => $text, 'id' => $article_id]);
        header('Location: ../view-article.php?article_id=' . $article_id . '&result=success&process=update');
    } catch (Exception $e) {
        header('Location: ../view-article.php?article_id=' . $article_id . '&result=error');
        error_log('Error: ' . $e->getMessage() . $_SESSION['username'] . "\n", 3, '../logs/update.log');
    }
?>
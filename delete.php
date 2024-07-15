<?php
    include './db.php';
    include './pdo.php';
    session_start();
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $id = $_GET['article_id'];
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $sql = $pdo->prepare("DELETE FROM articles WHERE id = :id");
        $sql->execute(['id' => $id]);
        header('Location: view.php?sitemapId=' . $sitemapId . '&result=success&article_id=' . $id . '&process=delete');
    } catch (Exception $e) {
        header('Location: view.php?sitemapId=' . $sitemapId . '&result=error');
    }
?>
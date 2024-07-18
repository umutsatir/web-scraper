<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();
    
    try {
        $gump = new GUMP();
        $_GET = $gump->sanitize($_GET);
        $sitemapId = $_GET['sitemapId'];
        $id = $_GET['article_id'];
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $sql = $pdo->prepare("DELETE FROM articles WHERE id = :id");
        $sql->execute(['id' => $id]);
        $db->query("DELETE FROM articles WHERE id = $id");
        header('Location: ../view.php?sitemapId=' . $sitemapId . '&result=success&article_id=' . $id . '&process=delete');
    } catch (Exception $e) {
        header('Location: ../view.php?sitemapId=' . $sitemapId . '&result=error');
        error_log('Error: ' . $e->getMessage() . "\n", 3, '../logs/delete.log');
    }
?>
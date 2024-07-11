<?php
    include './db.php';
    session_start();
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $id = $_GET['article_id'];
        $db = (new DB())->connect();
        $db->query("DELETE FROM articles WHERE id = $id");
        header('Location: view.php?sitemapId=' . $sitemapId . '&result=success&article_id=' . $id . '&process=delete');
    } catch (Exception $e) {
        header('Location: view.php?sitemapId=' . $sitemapId . '&result=error');
    }
?>
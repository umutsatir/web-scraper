<?php
    include './db.php';
    include './pdo.php';
    session_start();
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $update = $pdo->prepare("UPDATE links SET status = -1 WHERE sitemapId = :sitemapId");
        $update->execute(['sitemapId' => $sitemapId]);
        header('Location: scrapings.php?result=success&sitemapId=' . $sitemapId . '&process=deactivate');
    } catch (Exception $e) {
        header('Location: scrapings.php?result=error');
    }
?>
<?php
    include './db.php';
    include './pdo.php';
    include "gump.class.php";
    session_start();

    $gump = new GUMP();
    $_GET = $gump->sanitize($_GET);
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $db = (new DB())->connect();
        $pdo = (new PDOClass())->connect();
        $update = $pdo->prepare("UPDATE links SET status = -1 WHERE sitemapId = :sitemapId");
        $update->execute(['sitemapId' => $sitemapId]);
        header('Location: ../scrapings.php?result=success&sitemapId=' . $sitemapId . '&process=deactivate');
    } catch (Exception $e) {
        header('Location: ../scrapings.php?result=error');
        error_log('Error: ' . $e->getMessage() . "\n", 3, '../logs/deactivate.log');
    }
?>
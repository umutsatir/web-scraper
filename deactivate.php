<?php
    include './db.php';
    session_start();
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $db = (new DB())->connect();
        $db->query("UPDATE status = 1 FROM links WHERE sitemapId = $sitemapId");
        header('Location: scrapings.php?result=success&sitemapId=' . $sitemapId . '&process=deactivate');
    } catch (Exception $e) {
        header('Location: scrapings.php?result=error');
    }
?>
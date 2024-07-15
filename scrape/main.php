<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
session_start();

include './scraper.php';
include './gpt-detector.php';
include '../db.php';
include '../pdo.php';

try {
    $url = $_GET['sitemapLink']; 
    $extension = explode(".", $url);
    $extension = pathinfo($url, PATHINFO_EXTENSION);
    if ($extension != 'xml') {
        echo "Invalid sitemap link. Please provide a valid sitemap link.";
        exit();
    }

    $scraper = new Scraper();
    $detector = new GPTDetector();
    $title_xpath = $_GET['titleXPath'] . "/text()";
    $article_xpath = $_GET['textXPath'] . "//text()";
    
    if(isset($_GET['filters'])) {
        $filters = explode(",", $_GET['filters']);
        $filters = array_map('trim', $filters);
        $filters = array_map('strtolower', $filters);
        $links = $scraper->filter_links($scraper->get_links($url), $filters);
    } else {
        $links = $scraper->get_links($url);
    }
    
    $threshold = intval($_GET['threshold']);

    $db = (new DB())->connect();
    $pdo = (new PDOClass())->connect();

    $username = $_SESSION['username'];
    $userId = $db->get_var("SELECT userId FROM users WHERE username = '{$username}'");

    $insertSitemap = $pdo->prepare("INSERT INTO sitemaps (userId, url, titleXPath, textXPath, threshold, creationDate) VALUES (:userId, :url, :titleXPath, :textXPath, :threshold, now())");
    $insertSitemap->execute(['userId' => $userId, 'url' => $url, 'titleXPath' => $title_xpath, 'textXPath' => $article_xpath, 'threshold' => $threshold]);

    $sitemap_sql = sprintf("SELECT * FROM sitemaps WHERE url = '%s' ORDER BY sitemapId DESC", $url);
    $sitemap_id = $db->get_results($sitemap_sql)[0]->sitemapId;

    foreach ($links as $link) {
        $insertLinks = $pdo->prepare("INSERT INTO links (sitemapId, url, status) VALUES (:sitemapId, :link, 0)");
        $insertLinks->execute(['sitemapId' => $sitemap_id, 'link' => $link]);
    }

    header("Location: ../index.php?result=success");
} catch (Exception $e) {
    header("Location: ../index.php?result=error");

    $deleteSitemap = $pdo->prepare("DELETE FROM sitemaps WHERE sitemapId = :sitemapId");
    $deleteSitemap->execute(['sitemapId' => $sitemap_id]);

    $deleteLinks = $pdo->prepare("DELETE FROM links WHERE sitemapId = :sitemapId");
    $deleteLinks->execute(['sitemapId' => $sitemap_id]);
}
?>
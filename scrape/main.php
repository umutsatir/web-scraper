<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
session_start();

include './scraper.php';
include './gpt-detector.php';
include '../db.php';

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
    $links = $scraper->get_links($url);
    $title_xpath = $_GET['titleXPath'] . "/text()";
    $article_xpath = $_GET['textXPath'] . "//text()";

    $threshold = intval($_GET['threshold']);

    $db = (new DB())->connect();

    $username = $_SESSION['username'];
    $userId = $db->get_var("SELECT userId FROM users WHERE username = '{$username}'");

    $db->query("INSERT INTO sitemaps (userId, url, titleXPath, textXPath, threshold, creationDate) VALUES ($userId, '{$url}', '{$title_xpath}', '{$article_xpath}', $threshold, now())");
    $sitemap_sql = sprintf("SELECT * FROM sitemaps WHERE url = '%s' ORDER BY sitemapId DESC", $url);
    $sitemap_id = $db->get_results($sitemap_sql)[0]->sitemapId;

    foreach ($links as $link) {
        $db->query("INSERT INTO links (sitemapId, url, status) VALUES ($sitemap_id, '{$link}', 0)");
    }
    header("Location: ../index.php?result=success");
} catch (Exception $e) {
    header("Location: ../index.php?result=error");
    $db->query("DELETE FROM sitemaps WHERE sitemapId = $sitemap_id");
    $db->query("DELETE FROM links WHERE sitemapId = $sitemap_id");
}

// try {
//     $path = realpath(__DIR__) . "/cron.php";
//     $cronJob = sprintf("* * * * * php %s %d %d '%s' '%s' %d > /dev/null 2>&1 &", $path, $userId, $sitemap_id, $title_xpath, $article_xpath, $threshold);
//     $output = [];
//     exec("crontab -l", $output);
//     $output[] = $cronJob;
//     $newCronTab = implode("\n", $output) . "\n";
//     file_put_contents('/tmp/crontab.txt', $newCronTab);

//     // !! this needs admin permissions
//     exec("crontab /tmp/crontab.txt");
//     header("Location: ../index.php?result=success");
// } catch (Exception $e) {

// }
?>
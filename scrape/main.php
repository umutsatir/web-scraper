<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
session_start();

include './scraper.php';
include './gpt-detector.php';
include '../db.php';

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
$scraper->title_xpath = $_GET['titleXPath'] . "/text()";
$scraper->article_xpath = $_GET['textXPath'] . "//text()";

$totalLinks = count($links);
$processedLinks = 0;
$threshold = intval($_GET['threshold']);

$db = (new DB())->connect();

$username = $_SESSION['username'];
$userId = $db->get_var("SELECT userId FROM users WHERE username = '{$username}'");

$db->query("INSERT INTO sitemaps (userId, url, creationDate) VALUES ($userId, '{$url}', now())");
$sitemap_sql = sprintf("SELECT * FROM sitemaps WHERE url = '%s' ORDER BY sitemapId DESC", $url);
$sitemap_id = $db->get_results($sitemap_sql)[0]->sitemapId;

ignore_user_abort(false); // * Close connection when the client disconnects

foreach ($links as $link) {
    try {
        $page = $scraper->get_page($link);
        $texts = $scraper->get_text($page);
        $result = $detector->get_percentage($texts[1]);
        $result = round($result, 2);
        $date = date('Y-m-d');
        if ($result < $threshold) {
            $sql = sprintf("INSERT INTO articles (sitemapId, title, text, gptPercentage, creationDate) VALUES (%d, '%s', '%s', %f, '%s')", $sitemap_id, $texts[0], $texts[1], $result, $date);
            $db->query($sql);
        }
    } catch (Exception $e) {
        echo "Error: " . $e . "\n";
        continue;
    }

    $processedLinks++;
    $progress = ($processedLinks / $totalLinks) * 100;
}
?>
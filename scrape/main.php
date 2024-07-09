<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

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

$db->query("INSERT INTO sitemaps (url, creationDate) VALUES ('{$url}', now())");
$sitemap_sql = sprintf("SELECT sitemapId FROM sitemaps WHERE url = '%s'", $url);
$sitemap_id = intval($db->query($sitemap_sql));

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
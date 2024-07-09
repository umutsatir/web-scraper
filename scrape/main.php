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

$db->query("INSERT INTO sitemaps (url) VALUES ('{$url}')");

ignore_user_abort(false); // * Close connection when the client disconnects

foreach ($links as $link) {
    try {
        $sitemap_id = $db->select("sitemaps", "id", ["url" => $link]);
        $page = $scraper->get_page($link);
        $texts = $scraper->get_text($page);
        $result = $detector->get_percentage($texts[1]);
        if ($result < $threshold) {
            $db->query("INSERT INTO articles (title, text, gpt_percentage, sitemap_id) VALUES ('{$texts[0]}', '{$texts[1]}', '{$result}', '{$sitemap_id}')");
        }
        var_dump($texts, $result);
    } catch (Exception $e) {
        echo "Error: " . $e . "\n";
        continue;
    }

    $processedLinks++;
    $progress = ($processedLinks / $totalLinks) * 100;
}
?>
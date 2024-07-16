<?php
include dirname(dirname(__FILE__)) . '/db.php';
include 'scraper.php';
include 'gpt-detector.php';

$cronThreshold = 100;

$detector = new GPTDetector();
$scraper = new Scraper();
$db = (new DB())->connect();

$activeJobs = $db->get_results("SELECT * FROM links WHERE status = 2");
if (isset($activeJobs) && count($activeJobs) >= $cronThreshold) {
    exit();
}

do {
    try {
        $query = $db->get_results("SELECT * FROM links WHERE status = 0");
        if (!isset($query) || count($query) == 0) {
            $url = null;
            break;
        }
        $url = $query[0]->url;
        $sitemapId = $query[0]->sitemapId;
        $linkId = $query[0]->linkId;
        $db->query("UPDATE links SET status = 2 WHERE url = '$url' AND sitemapId = $sitemapId");
        
        $sitemap = $db->get_row("SELECT * FROM sitemaps WHERE sitemapId = $sitemapId");
        $scraper->title_xpath = $sitemap->titleXPath;
        $scraper->article_xpath = $sitemap->textXPath;
        $page = $scraper->get_page($url);
        $texts = $scraper->get_text($page);
        $result = $detector->get_percentage($texts[1]);
        $result = round($result, 2);
        $date = date('Y-m-d');
        if ($result < $sitemap->threshold) {
            $db->query("INSERT INTO articles (sitemapId, title, text, gptPercentage, creationDate) VALUES ($sitemapId, '$texts[0]', '$texts[1]', $result, '$date')");
        }
        $db->query("UPDATE links SET status = 1 WHERE url = '$url' AND sitemapId = $sitemapId");
    } catch (Exception $e) {
        $db->query("UPDATE links SET status = -1 WHERE url = '$url' AND sitemapId = $sitemapId");
        echo "Error: sitemapId=$sitemapId linkId=$linkId, Message: {$e->getMessage()}\n";
    }
} while ($url != null);
?>
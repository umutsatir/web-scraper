<?php
include 'scraper.php';
include 'gpt-detector.php';
include '/Applications/XAMPP/xamppfiles/htdocs/web-scraper/db.php';

$cronThresholdPerPerson = 100;

$sitemapId = $argv[1];
$detector = new GPTDetector();
$scraper = new Scraper();
$db = (new DB())->connect();

$activeJobs = $db->get_results("SELECT * FROM cronjobs WHERE isActive = 1");
if (isset($activeJobs) && count($activeJobs) >= $cronThreshold) {
    exit();
}

$db->query("INSERT INTO cronjobs (sitemapId, isActive) VALUES ($sitemapId, 1)");

do {
    try {
        $query = $db->get_results("SELECT * FROM links WHERE sitemapId = $sitemapId AND status = 0");
        if (!isset($query) || count($query) == 0) {
            $url = null;
            break;
        }
        $url = $query[0]->url;
        $db->query("UPDATE links SET status = 1 WHERE url = '{$url}' AND sitemapId = $sitemapId");
        $sitemap = $db->get_row("SELECT * FROM sitemaps WHERE sitemapId = $sitemapId");
        $scraper->title_xpath = $sitemap->titleXPath;
        $scraper->article_xpath = $sitemap->textXPath;
        $page = $scraper->get_page($url);
        $texts = $scraper->get_text($page);
        $result = $detector->get_percentage($texts[1]);
        $result = round($result, 2);
        $date = date('Y-m-d');
        if ($result < $sitemap->threshold) {
            $sql = sprintf("INSERT INTO articles (sitemapId, title, text, gptPercentage, creationDate) VALUES (%d, '%s', '%s', %f, '%s')", $sitemapId, $texts[0], $texts[1], $result, $date);
            $db->query($sql);
        }
    } catch (Exception $e) {
        $db->query("UPDATE links SET status = -1 WHERE url = '{$url}' AND sitemapId = $sitemapId");
    }
} while ($url != null);

$db->query("UPDATE cronjobs SET isActive = 0 WHERE sitemapId = $sitemapId");
?>
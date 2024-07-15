<?php
include '../db.php';
include '../pdo.php';
include 'scraper.php';
include 'gpt-detector.php';

$cronThreshold = 100;

$sitemapId = $argv[1];
$detector = new GPTDetector();
$scraper = new Scraper();
$db = (new DB())->connect();
$pdo = (new PDOClass())->connect();

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
        $pdo->prepare("UPDATE links SET status = 2 WHERE url = :url AND sitemapId = :sitemapId")->execute(['url' => $url, 'sitemapId' => $sitemapId]);

        $sitemap = $db->get_row("SELECT * FROM sitemaps WHERE sitemapId = $sitemapId");
        $scraper->title_xpath = $sitemap->titleXPath;
        $scraper->article_xpath = $sitemap->textXPath;
        $page = $scraper->get_page($url);
        $texts = $scraper->get_text($page);
        $result = $detector->get_percentage($texts[1]);
        $result = round($result, 2);
        $date = date('Y-m-d');
        if ($result < $sitemap->threshold) {
            $createArticle = $pdo->prepare("INSERT INTO articles (sitemapId, title, text, gptPercentage, creationDate) VALUES (:sitemapId, :title, :text, :gptPercentage, :creationDate)");
            $createArticle->execute(['sitemapId' => $sitemapId, 'title' => $texts[0], 'text' => $texts[1], 'gptPercentage' => $result, 'creationDate' => $date]);
        }
        $pdo->prepare("UPDATE links SET status = 1 WHERE url = :url AND sitemapId = :sitemapId")->execute(['url' => $url, 'sitemapId' => $sitemapId]);
    } catch (Exception $e) {
        $pdo->prepare("UPDATE links SET status = -1 WHERE url = :url AND sitemapId = :sitemapId")->execute(['url' => $url, 'sitemapId' => $sitemapId]);
    }
} while ($url != null);
?>
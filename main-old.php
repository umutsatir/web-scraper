<?php
    include './scraper.php';
    include './gpt-detector.php';

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
    $scraper->title_xpath = $_GET['titleXpath'] . "/text()";
    $scraper->article_xpath = $_GET['articleXpath'] . "//text()";
    
    foreach ($links as $link) {
        $page = $scraper->get_page($link);
        $status_code = get_headers($link)[0];
        preg_match('/\d{3}/', $status_code, $matches); // get the status code only (int)
        $status_code = isset($matches[0]) ? (int)$matches[0] : 0; // if status code is not set, set it to 0
        if ($status_code == 200) {
            try {
                $texts = $scraper->get_text($page);
                echo "Title: " . $texts[0] . PHP_EOL;
                echo "URL: " . $link . PHP_EOL;
                echo "--------------------------------------" . PHP_EOL;
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . ": " . $link .  "\n";
            }
        } else {
            echo "Error: " . $status_code . "\n";
        }
    }
?>
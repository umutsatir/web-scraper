<?php
    include './scraper.php';
    include './gpt-detector.php';

    $scraper = new Scraper();
    $detector = new GPTDetector();
    $links = $scraper->get_links('https://creakyjoints.org/post-sitemap.xml');
    $scraper->title_xpath = "/html/body/div[1]/div/div[1]/div/article/h1/text()";
    $scraper->article_xpath = "/html/body/div[1]/div/div[1]/div/article/div[2]/div//text()";
    
    foreach ($links as $link) {
        $page = $scraper->get_page($link);
        $status_code = get_headers($link)[0];
        preg_match('/\d{3}/', $status_code, $matches); // get the status code only (int)
        $status_code = isset($matches[0]) ? (int)$matches[0] : 0; // if status code is not set, set it to 0
        if ($status_code == 200) {
            try {
                $texts = $scraper->get_text($page);
                $percentage = $detector->get_percentage($texts[1]);
                echo $percentage . "\n";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . ": " . $link .  "\n";
            }
        } else {
            echo "Error: " . $status_code . "\n";
        }
    }
?>
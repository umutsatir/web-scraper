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

$maxProcesses = 10;  // Number of active forks you want
$processes = [];

// Function to fork a process
function fork_process($link) {
    global $scraper, $detector;
    $pid = pcntl_fork();
    if ($pid == -1) {
        die('Could not fork');
    } elseif ($pid) {
        // Parent process
        return $pid;
    } else {
        // Child process
        try {
            $page = $scraper->get_page($link);
            $texts = $scraper->get_text($page);
            $result = $detector->get_percentage($texts[1]);
            echo "Title: " . $texts[0] . PHP_EOL;
            echo "URL: " . $link . PHP_EOL;
            echo "GPT Percentage: " . $result . PHP_EOL;
            echo "--------------------------------------" . PHP_EOL;
        } catch (Exception $e) {
            echo "Error processing link: " . $e . PHP_EOL;
            exit(1); // Terminate the child process after finishing its work
        }
        exit(1); // Terminate the child process after finishing its work
    }
}

// Start initial forks
for ($i = 0; $i < $maxProcesses && !empty($links); ++$i) {
    $link = array_pop($links);
    $pid = fork_process($link);
    $processes[$pid] = $link;
}

// Monitor and refill forks
while (!empty($links) || !empty($processes)) {
    foreach ($processes as $pid => $link) {
        $status = null;
        $res = pcntl_waitpid($pid, $status, WNOHANG);
        if ($res == -1 || $res > 0) {
            // Process finished
            unset($processes[$pid]);
            if (!empty($links)) {
                $link = array_pop($links);
                $new_pid = fork_process($link);
                $processes[$new_pid] = $link;
            }
        }
    }
    usleep(100000); // Sleep for a short time to avoid busy-waiting
}

foreach ($processes as $pid) {
    pcntl_waitpid($pid, $status);
}

echo "All links processed." . PHP_EOL;

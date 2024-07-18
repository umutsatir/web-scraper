<?php
    include './db.php';
    include 'gump.class.php';
    session_start();

    $db = (new DB())->connect();
    $gump = new GUMP();
    $_GET = $gump->sanitize($_GET);

    try {
        $directory = dirname(__DIR__) . '/downloaded-files/';
        $sitemapId = $_GET['sitemapId'];
        $zipFile = $directory . "batch-articles-" . $sitemapId . '.zip';
        $files = [];

        // Ensure the directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // Delete existing ZIP file if it exists
        if (file_exists($zipFile)) {
            unlink($zipFile);
        }
        $articles = $db->get_results("SELECT * FROM articles WHERE sitemapId = $sitemapId");
        if (!isset($articles)) {
            throw new Exception("Error: No articles found for sitemap: $sitemapId\n");
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($articles as $article) {
                $fileName = $directory . "article-" . $article->id . ".txt";
                $newFile = fopen($fileName, "w");
                fwrite($newFile, $article->title . "\n");
                fwrite($newFile, $article->text);
                fclose($newFile);

                if (!file_exists($fileName)) {
                    throw new Exception("Error: Unable to create file: $fileName\n");
                }

                if ($zip->addFile($fileName, basename($fileName)) === false) {
                    throw new Exception("Error: Unable to add file to ZIP archive: $fileName\n");
                }
                $files[] = $fileName;
            }
            $zip->close();
        } else {
            throw new Exception("Error: Unable to open ZIP archive: $zipFile\n");
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="batch-articles-' . $sitemapId . '.zip"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        unlink($zipFile);
        foreach ($files as $file)
            unlink($file);
    } catch (Exception $e) {
        header('Location: ../scrapings.php?result=error');
        error_log('Error: ' . $e->getMessage() . "\n", 3, '../logs/download.log');
    }
?>
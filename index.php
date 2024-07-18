<?php
include 'inc/db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$db = (new DB())->connect();
$userId = $db->get_var("SELECT userId FROM users WHERE username = '{$_SESSION['username']}'");
$mySitemaps = $db->get_results("SELECT * FROM sitemaps WHERE userId = $userId");
$mySitemapCount = 0;
if ($mySitemaps != null)
    $mySitemapCount = count($mySitemaps);

$myArticleCount = 0;
if ($mySitemapCount != 0) {
    foreach ($mySitemaps as $sitemap) {
        $myArticleCount += $db->get_var("SELECT COUNT(*) FROM articles WHERE sitemapId = $sitemap->userId");
    }
}

$totalSitemapCount = $db->get_var("SELECT COUNT(*) FROM sitemaps");
$totalArticleCount = $db->get_var("SELECT COUNT(*) FROM articles");
$totalUsers = $db->get_var("SELECT COUNT(*) FROM users");
$totalLinks = $db->get_var("SELECT COUNT(*) FROM links");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>XON Web Scraper</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <link rel="stylesheet" href="./style.css" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        
        <!-- Bootstrap CSS v5.2.1 -->
        <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous"
        />
    </head>

    <body>
        <header>
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="">XON Web Scraper Tool</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="scraper.php">Scraper</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="scrapings.php">My Scrapings</a>
                        </li>
                    </ul>
                        <form action="profile.php">
                            <label class="mx-2">Hello, <strong><?php echo $_SESSION['username']; ?></strong></label>
                            <button class="btn btn-secondary">Profile</button>
                        </form>
                        <form action="inc/logout.php" class="mx-2">
                            <button class="btn btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>
        </header>
        <main class="d-flex flex-column justify-content-center align-items-center">
            <div class="d-flex justify-content-center align-items-center mt-5 mb-3">
                <h1>Scraper Stats</h1>
            </div>
            <div class="d-flex flex-wrap gap-4 justify-content-center align-items-stretch m-4 w-75">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $mySitemapCount; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Your Scraped Sitemaps</h6>
                            <p class="card-text">Total number of sitemaps that you have been scraped.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $myArticleCount; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Your Articles</h6>
                            <p class="card-text">Total number of articles that you have been scraped.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $totalSitemapCount; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Total Scraped Sitemaps</h6>
                            <p class="card-text">Total number of sitemaps that have been scraped.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $totalLinks; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Total Scraped Links</h6>
                            <p class="card-text">Total number of links that have been scraped.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $totalArticleCount; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Total Articles</h6>
                            <p class="card-text">Total number of articles that have been scraped.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-flex">
                    <div class="card text-center w-100">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $totalUsers; ?></h1>
                            <h6 class="card-subtitle mb-2 text-body-secondary">Total Users</h6>
                            <p class="card-text">Total number of users that have been used XON web scraper tool.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>

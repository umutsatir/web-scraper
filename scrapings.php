<?php
session_start();
include './db.php';
$db = (new DB())->connect();
$username = $_SESSION['username'];
$userId = $db->get_var("SELECT userId FROM users WHERE username = '{$username}'");

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

function deactivateJob($sitemapId, $userId) {
    $db = (new DB())->connect();
    $db->query("UPDATE cronjobs SET isActive = 1 WHERE userId = $userId AND sitemapId = $sitemapId");
}
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
        <link rel="stylesheet" href="style.css" />

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
                            <a class="nav-link" aria-current="page" href="index.php">Scraper</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">My Scrapings</a>
                        </li>
                    </ul>
                        <form action="logout.php">
                            <label class="mx-3"><?php echo "Hello, " ?> <strong><?php echo $_SESSION['username']; ?></strong></label>
                            <button class="btn btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container w-50 justify-content-center">
                <div class="container d-flex justify-content-center mt-5">
                    <h1>Active Scrapings</h1>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Sitemap ID</th>
                            <th scope="col">URL</th>
                            <th scope="col">Creation Date</th>
                            <th scope="col">Deactivate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = sprintf("SELECT * FROM sitemaps WHERE userId = %d", $userId);
                            $sitemaps = $db->get_results($sql);
                            foreach ($sitemaps as $sitemap) {
                                $isActive = $db->get_var("SELECT isActive FROM cronjobs WHERE userId = $userId AND sitemapId = $sitemap->sitemapId");
                                if ($isActive == true) {
                                    echo "<tr>";
                                    echo "<td>{$sitemap->sitemapId}</td>";
                                    echo "<td>{$sitemap->url}</td>";
                                    echo "<td>{$sitemap->creationDate}</td>";
                                    echo "<td><button class='btn btn-primary' onclick='<?php deactivateJob({$sitemapId}, {$userId}) ?>'>Deactivate</button></td>";
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="container w-50 justify-content-center">
                <div class="container d-flex justify-content-center mt-5">
                    <h1>Done Scrapings</h1>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Sitemap ID</th>
                            <th scope="col">URL</th>
                            <th scope="col">Creation Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = sprintf("SELECT * FROM sitemaps WHERE userId = %d", $userId);
                            $sitemaps = $db->get_results($sql);
                            foreach ($sitemaps as $sitemap) {
                                $isActive = $db->get_var("SELECT isActive FROM cronjobs WHERE userId = $userId AND sitemapId = $sitemap->sitemapId");
                                if ($isActive == false) {
                                    echo "<tr>";
                                    echo "<td>{$sitemap->sitemapId}</td>";
                                    echo "<td>{$sitemap->url}</td>";
                                    echo "<td>{$sitemap->creationDate}</td>";
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
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

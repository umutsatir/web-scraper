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
            <div class="container w-100 justify-content-center">
                <?php if (isset($_GET['result'])) { ?>
                    <div class="alert alert-<?php echo $_GET['result'] == 'success' ? 'warning' : 'danger'; ?> m-3" role="alert">
                        <?php 
                            if ($_GET['result'] == 'success') {
                                $process = $_GET['process'];
                                $sitemapId = $_GET['sitemapId'];
                                echo "Sitemap " . $sitemapId . " has been successfully " . $process . "d.";
                            } else {
                                echo "An error occurred, please try again later.";
                            }
                        ?>
                    </div>
                <?php } ?>
                <div class="container d-flex justify-content-center mt-5 mb-4">
                    <h1 class="site-header">My Scrapings</h1>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Sitemap ID</th>
                            <th scope="col">URL</th>
                            <th scope="col">Creation Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sitemaps = $db->get_results("SELECT * FROM sitemaps WHERE userId = $userId"); ?>
                        <?php foreach ($sitemaps as $sitemap): ?>
                            <tr>
                                <td><?php echo $sitemap->sitemapId; ?></td>
                                <td><?php echo $sitemap->url; ?></td>
                                <td><?php echo $sitemap->creationDate; ?></td>
                                <td><?php
                                    $totalLinks = $db->get_results("SELECT * FROM links WHERE status = 0 AND sitemapId = $sitemap->sitemapId");
                                    if (!isset($totalLinks)) {
                                        echo "Completed";
                                    } else {
                                        echo "In Progress";
                                    }
                                ?></td>
                                <td>
                                    <div class="container d-flex gap-1">
                                        <a href="view.php?sitemapId=<?php echo $sitemap->sitemapId; ?>" class="btn btn-primary"><ion-icon name="eye-outline"></ion-icon></a>
                                        <a href="deactivate.php?sitemapId=<?php echo $sitemap->sitemapId; ?>" class="btn btn-danger"><ion-icon name="stop-circle-outline"></ion-icon></a>
                                        <a href="download.php?sitemapId=<?php echo $sitemap->sitemapId; ?>" class="btn btn-success"><ion-icon name="download-outline"></ion-icon></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
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
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>

<?php
    session_start();
    include './db.php';
    include 'gump.class.php';

    $gump = new GUMP();
    $_GET = $gump->sanitize($_GET);

    // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    $sitemapId = $_GET['sitemapId'];

    $db = (new DB())->connect();

    $perPage = 25;
    $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
    $startAt = $perPage * ($page - 1);
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
                            <a class="nav-link" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="index.php">Scraper</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="scrapings.php">My Scrapings</a>
                        </li>
                    </ul>
                        <form action="profile.php">
                            <label class="mx-2">Hello, <strong><?php echo $_SESSION['username']; ?></strong></label>
                            <button class="btn btn-secondary">Profile</button>
                        </form>
                        <form action="logout.php" class="mx-2">
                            <button class="btn btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="justify-content-center mx-5">
                <?php if (isset($_GET['result'])) { ?>
                    <div class="alert alert-<?php echo $_GET['result'] == 'success' ? 'warning' : 'danger'; ?> m-3" role="alert">
                        <?php 
                            if ($_GET['result'] == 'success') {
                                $process = $_GET['process'];
                                echo "Article " . $_GET['article_id'] . " has been successfully " . $process . "d.";
                            } else {
                                echo "An error occurred. Please try again.";
                            }
                        ?>
                    </div>
                <?php } ?>
                <div class="container d-flex justify-content-center mt-5 mb-4">
                    <h1 class="site-header">Sitemap ID <?php echo $sitemapId; ?></h1>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Article ID</th>
                            <th scope="col">Title</th>
                            <th scope="col">Article</th>
                            <th scope="col">GPT Percentage</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $articles = $db->get_results("SELECT * from articles WHERE sitemapId = $sitemapId");
                            $articleCount = count($articles);
                            $articles = array_slice($articles, $startAt, $perPage);
                        ?>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo $article->id; ?></td>
                                <td><?php echo $article->title; ?></td>
                                <td><?php echo mb_strimwidth($article->text, 0, 250, "..."); ?></td>
                                <td><?php echo $article->gptPercentage; ?></td>
                                <td>
                                    <div class="container d-flex flex-column gap-1">
                                        <a href="view-article.php?sitemapId=<?php echo $article->sitemapId ?>&article_id=<?php echo $article->id; ?>" class="btn btn-primary"><ion-icon name="eye-outline"></ion-icon></a>
                                        <a href="delete.php?sitemapId=<?php echo $article->sitemapId ?>&article_id=<?php echo $article->id; ?>" class="btn btn-danger"><ion-icon name="trash-outline"></ion-icon></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="container w-75 justify-content-center">
                <nav aria-label="Article navigation">
                    <ul class="pagination table-responsive">
                        <li class="page-item">
                            <?php if ($page > 1) { ?>
                                <a class="page-link" href="view.php?sitemapId=<?php echo $sitemapId . "&page=" . $page - 1; ?>">Previous</a>
                            <?php } else { ?>
                                <a class="page-link disabled">Previous</a>
                            <?php } ?>
                        </li>
                        <?php
                            for ($i = 1; $i <= ceil($articleCount / $perPage); $i++) {
                                $activeTag = ($i == $page) ? 'active' : '';
                                echo '<li class="page-item ' . $activeTag . '"><a class="page-link" href="view.php?sitemapId=' . $sitemapId . '&page=' . $i . '">' . $i . '</a></li>';
                            }
                        ?>
                        <li class="page-item">
                        <?php if ($page < ceil($articleCount / $perPage)) { ?>
                            <a class="page-link" href="view.php?sitemapId=<?php echo $sitemapId . "&page=" . $page + 1; ?>">Next</a>
                        <?php } else { ?>
                            <a class="page-link disabled">Next</a>
                        <?php } ?>
                        </li>
                    </ul>
                </nav>
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

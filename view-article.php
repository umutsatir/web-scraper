<?php
    include './db.php';
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }
    
    try {
        $sitemapId = $_GET['sitemapId'];
        $id = $_GET['article_id'];
        $db = (new DB())->connect();
    } catch (Exception $e) {
        header('Location: view.php?result=error');
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
                            <a class="nav-link" aria-current="page" href="scrapings.php">My Scrapings</a>
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
            <div class="container w-50 d-flex flex-column justify-content-center gap-4 mb-5">
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
                <div class="container d-flex justify-content-center my-4">
                    <h1>Article <?php echo $id; ?></h1>
                </div>
                <form action="update.php" method="post" class="d-flex flex-column justify-content-center gap-4">
                    <input type="hidden" name="article_id" value="<?php echo $id; ?>">
                    <?php
                        $article = $db->get_results("SELECT * FROM articles WHERE id = $id")[0];
                        echo "<h2>{$article->title}</h2>";
                        echo "<textarea name='text' rows='30'>{$article->text}</textarea>";
                    ?>
                    <div>
                    <button type="submit" class="btn btn-warning w-25"><ion-icon name="create-outline"></ion-icon></button>
                    <a href="delete.php?sitemapId=<?php echo $article->sitemapId ?>&article_id=<?php echo $article->id; ?>" class="btn btn-danger w-25"><ion-icon name="trash-outline"></ion-icon></a>
                    </div>
                </form>
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
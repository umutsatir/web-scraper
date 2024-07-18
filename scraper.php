<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['last_submission'])) {
    $_SESSION['last_submission'] = time();
    $_SESSION['submission_count'] = 0;
}

$submission_interval = 30;
$submission_limit = 5;

if (time() - $_SESSION['last_submission'] < $submission_interval) {
    $_SESSION['submission_count']++;
} else {
    $_SESSION['submission_count'] = 1;
    $_SESSION['last_submission'] = time();
}

if ($_SESSION['submission_count'] > $submission_limit) {
    die('Rate limit exceeded, wait for ' . ($submission_interval - (time() - $_SESSION['last_submission'])) . ' seconds before submitting again.');
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
                            <a class="nav-link" aria-current="page" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Scraper</a>
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
            <div class="container d-flex justify-content-center mt-5">
                <h1 class="site-header">Scraper Tool</h1>
            </div>
            <div class="container w-75 justify-content-center">
                <form action="./scrape/main.php" method="get">
                    <div class="input-group m-3">
                        <label for="sitemapLink" class="input-group-text"
                            >Sitemap Link</label
                        >
                        <input
                            type="link"
                            class="form-control"
                            name="sitemapLink"
                            placeholder="https://www.example.com"
                            required
                        />
                    </div>
                    <div class="input-group m-3">
                        <label for="titleXPath" class="input-group-text"
                            >Title XPath</label
                        >
                        <input
                            type="text"
                            name="titleXPath"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="input-group m-3">
                        <label for="textXPath" class="input-group-text"
                            >Text XPath</label
                        >
                        <input
                            type="text"
                            name="textXPath"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="input-group m-3">
                        <label for="threshold" class="form-label">GPT Threshold</label>
                        <input type="range" class="form-range" min="0" max="100" name="threshold">
                        <p id="thresholdValue"></p>
                    </div>
                    <div class="m-3 w-100">
                        <label for="tag-input1" class="form-label">Filters (if not, leave blank)</label>
                        <input type="text" id="tag-input1" name="filters" class="w-100">
                    </div>
                    <input type="submit" class="btn btn-primary mx-3" />
                </form>
                <?php if (isset($_GET['result'])) { ?>
                    <div class="alert alert-<?php echo $_GET['result'] == 'success' ? 'success' : 'danger'; ?> m-3" role="alert">
                        <?php echo $_GET['result'] == 'success' ? 'Sitemap has been successfully added to the scraping queue. You can check the results in the <a href="./scrapings.php" class="alert-link">"My Scrapings"</a> tab.' : 'An error occurred. Please try again later.'; ?>
                    </div>
                <?php } ?>
            </div>
        </main>
        <script>
            var text = document.getElementById('thresholdValue');
            var threshold = document.querySelector('.form-range');
            text.innerText = threshold.valueAsNumber + "%"; 
            threshold.addEventListener('change', () => { 
                text.innerText = threshold.valueAsNumber + "%"; 
            });
        </script>
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

        <script src="./bootstrap-tagsinput.js"></script>
    </body>
</html>

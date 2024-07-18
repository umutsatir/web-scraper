<?php
    session_start();

    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        header('Location: index.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>XON Web Scraper Register</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <link rel="stylesheet" href="style.css" />

        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet"
        />

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
            <!-- As a heading -->
            <nav class="navbar bg-body-tertiary">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1"
                        >XON Web Scraper Tool</span
                    >
                </div>
            </nav>
        </header>
        <main>
            <?php if (isset($_GET['result'])) { ?>
                <div class="alert alert-danger m-3 mx-5" role="alert">
                    Error: <?php echo $_GET['message']; ?>
                </div>
            <?php } ?>
            <div class="container d-flex justify-content-center mt-5">
                <h1>User Register</h1>
            </div>
            <div class="container w-50">
                <form action="inc/register-req.php" method="post">
                    <div class="input-group m-3">
                        <label for="username" class="input-group-text"
                            >Username</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            required
                        />
                    </div>
                    <div class="input-group m-3">
                        <input
                            type="text"
                            class="form-control"
                            placeholder="Domain"
                            aria-label="Domain"
                            name="email"
                        />
                        <span class="input-group-text">@</span>
                        <input
                            type="text"
                            class="form-control"
                            placeholder="example.com"
                            aria-label="example.com"
                            name="email-2"
                        />
                    </div>
                    <div class="input-group m-3">
                        <label for="password" class="input-group-text"
                            >Password</label
                        >
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required
                        />
                    </div>
                    <input
                        type="submit"
                        value="Register"
                        class="btn btn-primary mx-3 mb-2"
                        style="width: 150px"
                    />
                </form>
                <a href="login.php"
                    ><button
                        class="btn btn-secondary mx-3"
                        style="width: 150px"
                    >
                    Login
                </button></a>
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

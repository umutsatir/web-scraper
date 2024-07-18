<?php
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        header('Location: login.php');
        exit;
    }

    include './db.php';
    include './pdo.php';

    $db = (new DB())->connect();
    $pdo = (new PDOClass())->connect();

    $user = $db->get_row("SELECT * FROM users WHERE username = '{$_SESSION['username']}'");
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
                        <form action="logout.php" class="mx-2">
                            <button class="btn btn-primary">Logout</button>
                        </form>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container">
                <?php if (isset($_GET['result'])) { ?>
                        <div class="alert alert-<?php echo $_GET['result'] == 'success' ? 'success' : 'danger'; ?> m-3" role="alert">
                            <?php echo $_GET['result'] == 'success' ? 'Data successfully updated.' : 'An error occurred, please try again.'; ?>
                        </div>
                    <?php } ?>
                <div class="container-fluid mb-5">
                    <div class="col-md-6 offset-md-3 d-flex flex-column gap-5">
                        <h1 class="text-center mt-5">Profile</h1>
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">User Information</h3>
                                <form action="change.php" method="post">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $noneEditableFields = [
                                            'Username' => $_SESSION['username'],
                                            'Created At' => $user->creationDate,
                                        ];
                                        foreach ($noneEditableFields as $field => $value) {
                                            echo "<tr>
                                                    <td>$field</td>
                                                    <td><strong>$value</strong></td>
                                                </tr>";
                                        }
                                        $fields = [
                                            'Name' => $user->name,
                                            'Surname' => $user->surname,
                                            'Email' => $user->email,
                                        ];
                                        foreach ($fields as $field => $value) {
                                            echo "<tr>
                                                    <td>$field</td>
                                                    <td>
                                                        <input type='text' class='form-control w-75' value='$value' name='" . strtolower($field) .  "' />
                                                    </td>
                                                </tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="container w-75">
                                    <h5>Update Informations</h5>
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                                        <label for="password">Password</label>
                                    </div>
                                    <input type="submit" value="Confirm" class="btn btn-primary my-2">
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-4">Update Password</h3>
                                <form action="change-pw.php" method="post">
                                    <div class="container d-flex flex-column gap-3 my-3">
                                        <div class="form-floating">
                                            <input type="password" class="form-control" name="oldPassword" placeholder="Old Password" required>
                                            <label for="oldPassword">Old Password</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="password" class="form-control" name="newPassword" placeholder="New Password" required>
                                            <label for="newPassword">New Password</label>
                                        </div>
                                        <div class="form-floating">
                                            <input type="password" class="form-control" name="confirmPassword" placeholder="Confirm Password" required>
                                            <label for="confirmPassword">Confirm Password</label>
                                        </div>
                                    </div>
                                    <div class="container">
                                        <input type="submit" value="Update" class="btn btn-primary">
                                    </div>
                            </div>
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
        <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    </body>
</html>

<?php
include('../db_conn.php');
$info = '';
$info = '';
// Vérifiez si le formulaire est soumis
if (isset($_POST['login'])) {
    // Récupérer les données du formulaire
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);


    // Récupérer les données utilisateur de la base de données en fonction de l'e-mail fourni
    $query = "SELECT * FROM `admin` WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // L'utilisateur avec l'e-mail fourni existe dans la base de données
        $user = mysqli_fetch_assoc($result);

        // Vérifiez le mot de passe
        if (password_verify($password, $user['password'])) {
            // Le mot de passe est correct

            // Stocker les informations utilisateur pertinentes dans les variables de session
            $_SESSION['adminLogin'] = true;
            $_SESSION['a_id'] = $user['a_id'];
            $_SESSION['a_name'] = 'Admin';
            $_SESSION['type'] = 'admin';

            // Redirection vers la page souhaitée après une connexion réussie
            header("Location: index.php"); // Replace 'dashboard.php' with your desired page
            exit();
        } else {
            // Le mot de passe est incorrect
            $info = '<div class="alert alert-danger">Invalid Password..</div>';
        }
    } else {
        // L'utilisateur avec l'e-mail fourni n'existe pas dans la base de données
        $info = '<div class="alert alert-danger">Account not found!</div>';
    }
}
?>
<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login</title>
        <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/png">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="./assets/vendor/bootstrap/css/bootstrap.min.css">
        <link href="./assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
        <link rel="stylesheet" href="./assets/libs/css/style.css">
        <link rel="stylesheet" href="./assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
        <style>
            html,
            body {
                height: 100%;
            }

            body {
                display: -ms-flexbox;
                display: flex;
                -ms-flex-align: center;
                align-items: center;
                padding-top: 40px;
                padding-bottom: 40px;
            }
        </style>
    </head>

    <body>
        <!-- ============================================================== -->
        <!-- login page  -->
        <!-- ============================================================== -->
        <div class="splash-container">
            <div class="card ">
                <div class="card-header">
                    <h3 class="mb-1">Admin Login</h3>
                    <p>Please enter your login information.</p>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <?php echo $info; ?>
                        <div class="form-group">
                            <input class="form-control form-control-lg" id="email" name="email" type="text"
                                placeholder="Email" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <input class="form-control form-control-lg" id="password" name="password" type="password"
                                placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block" name="login">Sign in</button>
                    </form>
                </div>
                <div class="card-footer bg-white p-0  ">
                    <div class="card-footer-item card-footer-item-bordered">
                        <a href="../login.php" class="footer-link">User Login</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- end login page  -->
        <!-- ============================================================== -->
        <!-- Optional JavaScript -->
        <script src="./assets/vendor/jquery/jquery-3.3.1.min.js"></script>
        <script src="./assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
        <div class="gtranslate_wrapper"></div>
        <script>window.gtranslateSettings = { "default_language": "en", "languages": ["en", "fr", "nl"], "wrapper_selector": ".gtranslate_wrapper", "switcher_horizontal_position": "right", "flag_style": "3d" }</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    </body>

</html>
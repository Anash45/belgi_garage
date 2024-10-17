<?php
include('db_conn.php');
// Vérifiez si le formulaire est soumis
$info = '';
// Vérifiez si le formulaire est soumis
if (isset($_POST['login'])) {
    // Récupérer les données du formulaire
    $email = $_POST['email'];
    $password = $_POST['password'];

    // En supposant que vous ayez déjà établi une connexion à la base de données

    // Échapper aux caractères spéciaux pour empêcher l'injection SQL
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Récupérer les données utilisateur de la base de données en fonction de l'e-mail fourni
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // L'utilisateur avec l'e-mail fourni existe dans la base de données
        $user = mysqli_fetch_assoc($result);

        // Vérifiez le mot de passe
        if (password_verify($password, $user['password'])) {
            // Le mot de passe est correct

            // Stocker les informations utilisateur pertinentes dans les variables de session
            $_SESSION['loggedIn'] = true;
            $_SESSION['u_id'] = $user['u_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['type'] = $user['type'];

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
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Belgi Garage</title>
        <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/png">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="./assets/css/style.css?v=1_1">
    </head>

    <body>
        <?php include('header.php'); ?>
        <main>
            <section class="py-5">
                <div class="container">
                    <h1 class="text-center mb-5"> Login </h1>
                    <div class="col-lg-5 col-md-6 col-sm-8 mx-auto">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <?php echo $info; ?>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <input name="email" type="email" class="form-control form-control-lg" placeholder="E-Mail">
                                    </div>
                                    <div class="mb-3">
                                        <input name="password" type="password" class="form-control form-control-lg"
                                            placeholder="Password">
                                    </div>
                                    <div class="mt-5">
                                        <button class="btn btn-dark w-100" name="login"> Login </button>
                                    </div>
                                    <div class="py-3 text-center">
                                        <p class="m-0"><a href="admin/" class="text-decoration-underline">Admin Login</a></p>
                                        <p class="m-0">Don't have an account? <a href="signup.php" class="text-decoration-underline">Signup</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once './footer.php'; ?>
    <div class="gtranslate_wrapper"></div>
        <script>window.gtranslateSettings = { "default_language": "en", "languages": ["en", "fr", "nl"], "wrapper_selector": ".gtranslate_wrapper", "switcher_horizontal_position": "right", "flag_style": "3d" }</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>

</html>
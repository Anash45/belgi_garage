<?php
include('db_conn.php');
// Vérifiez si le formulaire est soumis
$info = '';
if (isset($_POST['signup'])) {
    // Récupérer les données du formulaire
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $type = $_POST['type'];

    // Valider la longueur et la correspondance du mot de passe
    if (strlen($password) < 6 || $password !== $confirmPassword) {
        $info = '<div class="alert alert-danger">Password should be at least 6 characters long and match the confirmation.</div>';
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $email = mysqli_real_escape_string($conn, $email);
        $password = password_hash($password,PASSWORD_DEFAULT);
        $type = mysqli_real_escape_string($conn, $type);

        $emailExistsQuery = "SELECT * FROM users WHERE email = '$email'";
        $emailExistsResult = mysqli_query($conn, $emailExistsQuery);

        if (mysqli_num_rows($emailExistsResult) == 0) {
            // Insérer les données utilisateur dans le tableau
            $query = "INSERT INTO users (name, email, password, type) VALUES ('$name', '$email', '$password', '$type')";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Enregistrement de l'utilisateur réussi
                $info = '<div class="alert alert-success">Registration successful!</div>';
            } else {
                // Une erreur s'est produite lors de l'inscription
                $info = '<div class="alert alert-danger">Error: ' . mysqli_error($conn);
            }
        } else {
            $info = '<div class="alert alert-danger">Email already registered!</div>';
        }
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="./assets/css/style.css?v=1_1">
    </head>

    <body>
        <?php include('header.php'); ?>
        <main>
            <section class="py-5">
                <div class="container">
                    <h1 class="text-center mb-5"> Signup </h1>
                    <div class="col-lg-5 col-md-6 col-sm-8 mx-auto">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                            <?php echo $info; ?>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <input type="text" required name="name" class="form-control form-control-lg"
                                            placeholder="Fullname">
                                    </div>
                                    <div class="mb-3">
                                        <input type="email" required name="email" class="form-control form-control-lg"
                                            placeholder="E-Mail">
                                    </div>
                                    <div class="mb-3">
                                        <select name="type" required class="form-control form-control-lg">
                                            <option value="" selected disabled>Select user type</option>
                                            <option value="Driver">Driver</option>
                                            <option value="Owner">Space Owner</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" required name="password"
                                            class="form-control form-control-lg" placeholder="Password">
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" required name="confirm_password"
                                            class="form-control form-control-lg" placeholder="Confirm Password">
                                    </div>
                                    <div class="mt-5">
                                        <button class="btn btn-dark w-100" name="signup"> Signup </button>
                                    </div>
                                    <div class="py-3 text-center">
                                        <p class="m-0">Already have an account? <a href="login.php"
                                                class="text-decoration-underline">Login</a></p>
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
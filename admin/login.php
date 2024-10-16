<?php
include('../db_conn.php');
$info = '';
$info = '';
// Check if the form is submitted
if (isset($_POST['login'])) {
    // Retrieve form data
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);


    // Fetch user data from the database based on the provided email
    $query = "SELECT * FROM `admin` WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User with the provided email exists in the database
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct

            // Store relevant user information in session variables
            $_SESSION['adminLogin'] = true;
            $_SESSION['a_id'] = $user['a_id'];
            $_SESSION['a_name'] = 'Admin';
            $_SESSION['type'] = 'admin';

            // Redirect to the desired page after successful login
            header("Location: index.php"); // Replace 'dashboard.php' with your desired page
            exit();
        } else {
            // Password is incorrect
            $info = '<div class="alert alert-danger">Invalid Password..</div>';
        }
    } else {
        // User with the provided email does not exist in the database
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
    </body>

</html>
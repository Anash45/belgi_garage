<?php
include('db_conn.php');
// Check if the form is submitted
$info = '';
// Check if the form is submitted
if (isset($_POST['login'])) {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Assuming you have already established a database connection

    // Escape special characters to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Fetch user data from the database based on the provided email
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User with the provided email exists in the database
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct

            // Store relevant user information in session variables
            $_SESSION['loggedIn'] = true;
            $_SESSION['u_id'] = $user['u_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['type'] = $user['type'];

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
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>

</html>
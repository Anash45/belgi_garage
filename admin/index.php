<?php
include('../db_conn.php');
$info = $show1 = '';
$page = 'home'; // Set the page name
if (!isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] != true) {
    header('location:login.php');
    die();
}

// Check if the delete parameter is set in the URL
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = intval($_GET['delete']); // Get the user ID to delete

    // Préparez l'instruction SQL DELETE
    $sql = "DELETE FROM users WHERE u_id = ?";

    // Initialiser une instruction préparée
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Liez l'ID utilisateur à l'instruction préparée
        $stmt->bind_param("i", $user_id);

        // Exécuter l'instruction préparée
        if ($stmt->execute()) {
            $info = "User with ID $user_id has been deleted successfully."; // Message de réussite
        } else {
            $info = "Error deleting user: " . $stmt->error; // Message d'erreur
        }

        // Close the statement
        $stmt->close();
    } else {
        $info = "Error preparing statement: " . $conn->error; // Erreur dans la préparation de la déclaration
    }
}

$sql1 = "SELECT * FROM `users`";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_assoc($result1);
if (!empty($row1)) {
    // print_r($row1);
    do {
        $show1 .= '<tr>
            <td class="text-start">' . $row1['u_id'] . '</td>
            <td class="text-start"><img src="../assets/uploads/' . $row1['image'] . '" style="height: 50px; width: 50px; object-fit: cover;" class="rounded-circle"></td>
            <td class="text-start">' . $row1['name'] . '</td>
            <td class="text-start">' . $row1['email'] . '</td>
            <td class="text-start">' . $row1['type'] . '</td>
            <td class="text-start"><a onclick="return confirm(\'do you really want to delete this user?\')" href="?delete=' . $row1['u_id'] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>
        </tr>';
    } while ($row1 = mysqli_fetch_assoc($result1));
} else {
    $show1 = '<div class="alert alert-danger">No users found!</div>';
}
?>
<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
        <link href="assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/libs/css/style.css">
        <link rel="stylesheet" href="assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
        <link rel="stylesheet" href="assets/vendor/charts/chartist-bundle/chartist.css">
        <link rel="stylesheet" href="assets/vendor/charts/morris-bundle/morris.css">
        <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="assets/vendor/charts/c3charts/c3.css">
        <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
        <title>Admin Panel</title>
        <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/png">
    </head>

    <body>
        <!-- ============================================================== -->
        <!-- main wrapper -->
        <!-- ============================================================== -->
        <div class="dashboard-main-wrapper">
            <!-- ============================================================== -->
            <!-- navbar -->
            <!-- ============================================================== -->
            <?php
            include('header.php');
            ?>
            <!-- ============================================================== -->
            <!-- end navbar -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- left sidebar -->
            <!-- ============================================================== -->
            <?php
            include('sidebar.php');
            ?>
            <!-- ============================================================== -->
            <!-- end left sidebar -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- wrapper  -->
            <!-- ============================================================== -->
            <div class="dashboard-wrapper">
                <div class="dashboard-ecommerce">
                    <div class="container-fluid dashboard-content ">
                        <div class="ecommerce-widget">
                            <div class="row">
                                <!-- ============================================================== -->
                                <div class="col-12 mb-4">
                                    <div class="card">
                                        <h5 class="card-header d-flex justify-content-between align-items-center">Users
                                        </h5>
                                        <div class="card-body">
                                            <?php
                                            if (!empty($info)) {
                                                echo '<div class="alert alert-success">' . htmlspecialchars($info) . '</div>';
                                            }
                                            ?>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead class="bg-light">
                                                        <tr class="border-0">
                                                            <th class="border-0">User ID</th>
                                                            <th class="border-0">Image</th>
                                                            <th class="border-0">Name</th>
                                                            <th class="border-0">E-mail</th>
                                                            <th class="border-0">User Type</th>
                                                            <th class="border-0" colspan="1">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php echo $show1; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- ============================================================== -->
                                <!-- end recent orders  -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jquery 3.3.1 -->
        <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
        <!-- bootstap bundle js -->
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <div class="gtranslate_wrapper"></div>
        <script>window.gtranslateSettings = { "default_language": "en", "languages": ["en", "fr", "nl"], "wrapper_selector": ".gtranslate_wrapper", "switcher_horizontal_position": "right", "flag_style": "3d" }</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    </body>

</html>
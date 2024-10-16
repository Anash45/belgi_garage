<?php
include('../db_conn.php');

// Check if the admin is logged in
if (!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] !== true) {
    header('location:login.php');
    exit();
}

// Initialize variables for feedback and form handling
$info = '';
$currentPassword = '';
$newPassword = '';
$confirmPassword = '';

// Check if the form is submitted for changing the password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get admin ID from session
    $a_id = $_SESSION['a_id'];

    // Escape special characters to prevent SQL injection
    $currentPassword = mysqli_real_escape_string($conn, $_POST['current_password']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if new password and confirm password match
    if ($newPassword !== $confirmPassword) {
        $info = "<div class='alert alert-danger'>Error: New password and confirm password do not match.</div>";
    } else {
        // Fetch the current password from the database
        $sql = "SELECT password FROM admin WHERE a_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $a_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Check if the current password matches the password in the database
            if (password_verify($currentPassword, $row['password'])) {
                // If the password matches, update the password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql = "UPDATE admin SET password = ? WHERE a_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("si", $hashedNewPassword, $a_id);

                if ($updateStmt->execute()) {
                    $info = "<div class='alert alert-success'>Password updated successfully.</div>";
                } else {
                    $info = "<div class='alert alert-danger'>Error: Failed to update password.</div>";
                }
                $updateStmt->close();
            } else {
                $info = "<div class='alert alert-danger'>Error: Current password is incorrect.</div>";
            }
        } else {
            $info = "<div class='alert alert-danger'>Error: Admin not found.</div>";
        }

        $stmt->close();
    }
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
        <link rel="stylesheet" href="assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
        <title>Admin Panel - Change Password</title>
        <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/png">
    </head>

    <body><!-- ============================================================== -->
        <!-- main wrapper -->
        <!-- ============================================================== -->
        <div class="dashboard-main-wrapper">
            <!-- ============================================================== -->
            <!-- navbar -->
            <!-- ============================================================== -->
            <?php include('header.php'); ?>
            <!-- ============================================================== -->
            <!-- end navbar -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- left sidebar -->
            <!-- ============================================================== -->
            <?php include('sidebar.php'); ?>
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
                                        <h5 class="card-header d-flex justify-content-between align-items-center">Change
                                            password </h5>
                                        <div class="card-body">
                                            <?php
                                            if (!empty($info)) {
                                                echo $info;
                                            }
                                            ?>
                                            <form method="post" action="">
                                                <div class="mb-3">
                                                    <label for="current_password" class="form-label">Current
                                                        Password</label>
                                                    <input type="password" class="form-control" id="current_password"
                                                        name="current_password" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="new_password" class="form-label">New Password</label>
                                                    <input type="password" class="form-control" id="new_password"
                                                        name="new_password" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="confirm_password" class="form-label">Confirm New
                                                        Password</label>
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Change Password</button>
                                            </form>
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
        <script src="assets/vendor/jquery/jquery-3.3.1.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    </body>

</html>
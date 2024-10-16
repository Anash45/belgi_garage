<?php
include('db_conn.php');
// Check if the form is submitted
$info = $show = '';
// Check if the form is submitted
if (checkUserType() == 'Driver' || checkUserType() == 'Owner') {
    // Check if the form is submitted
    if (isset($_REQUEST['update'])) {
        // Retrieve form data
        $u_id = $_SESSION['u_id'];
        $name1 = mysqli_real_escape_string($conn, $_POST['name']);
        $email1 = mysqli_real_escape_string($conn, $_POST['email']);

        // Validate and process the uploaded image file
        $targetDir = './assets/uploads/'; // Specify the directory to store uploaded images
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];

        if (!empty($imageName)) {
            $imagePath = $targetDir . $imageName;
            move_uploaded_file($imageTmpName, $imagePath);
            $attach = ",`image` = '$imageName'";
        } else {
            $attach = "";
        }

        $sql3 = "SELECT * FROM `users` WHERE `email` = '$email1' AND `u_id` != '$u_id'";
        $result3 = mysqli_query($conn, $sql3);
        if (mysqli_num_rows($result3) > 0) {
            $info = "<div class='alert alert-danger'>E-mail already registered</div>";
        } else {
            // echo  "UPDATE users SET name = '$name1', email = '$email1' ".$attach." WHERE u_id = '$u_id'";
            $sql1 = mysqli_query($conn, "UPDATE users SET name = '$name1', email = '$email1' ".$attach." WHERE u_id = '$u_id'");

            if ($sql1) {
                // Provide feedback to the user
                $info = "<div class='alert alert-success'>Profile updated successfully.</div>";
            } else {
                // Handle any errors
                $info = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . '</div>';
            }
        }

    }elseif (isset($_REQUEST['updatePass'])) {
        // Retrieve form data
        $u_id = $_SESSION['u_id'];
        $opassword = $_POST['opassword'];
        $npassword = $_POST['npassword'];

        $sql4 = "SELECT * FROM `users` WHERE `u_id` = '$u_id'";
        $result4 = mysqli_query($conn, $sql4);
        if (mysqli_num_rows($result4) == 0) {
            $info = "<div class='alert alert-danger'>No user found!</div>";
        } else {
            $row4 = mysqli_fetch_assoc($result4);
            if (password_verify($opassword,$row4['password'])) {
                $password = password_hash($npassword,PASSWORD_DEFAULT);
                // echo  "UPDATE users SET name = '$name1', email = '$email1' ".$attach." WHERE u_id = '$u_id'";
                $sql1 = mysqli_query($conn, "UPDATE users SET password = '$password' WHERE u_id = '$u_id'");

                if ($sql1) {
                    // Provide feedback to the user
                    $info = "<div class='alert alert-success'>Password updated successfully.</div>";
                } else {
                    // Handle any errors
                    $info = "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . '</div>';
                }
            } else {
                $info = "<div class='alert alert-danger'>Incorrect old password!</div>";
            }
            
        }

    }

    // print_r($_REQUEST);
    $u_id = $_SESSION['u_id'];

    $sql2 = "SELECT * FROM `users` WHERE `u_id` = '$u_id'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    if (!empty($row2)) {
        $name = $row2['name'];
        $email = $row2['email'];
        $image = './assets/uploads/' . $row2['image'];
    } else {
        header('location:index.php?err=1');
    }
} else {
    header('location:index.php?err=1');
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
                    <h2 class="fw-normal">My Profile</h2>
                    <?php echo $info; ?>
                    <div class="row py-4 align-items-center">
                        <div class="col-md-4 text-md-start text-center">
                            <img src="<?php echo $image; ?>" alt="" class="img-fluid profile-img">
                        </div>
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between pb-4 border-bottom">
                                <div>
                                    <p class="pb-2"><span class="label">Name:</span> <span class="ps-3">
                                            <?php echo $name; ?>
                                        </span></p>
                                    <p class="mb-0"><span class="label">E-mail:</span> <span class="ps-3">
                                            <?php echo $email; ?>
                                        </span></p>
                                </div>
                                <div><button class="btn btn-secondary px-5" data-bs-toggle="modal"
                                        data-bs-target="#updateModal">Edit</button></div>
                            </div>
                            <p class="pt-4"><span class="label">Password:</span> <button
                                    class="btn btn-dark ms-3 px-5" data-bs-toggle="modal"
                                        data-bs-target="#passwordModal">Reset</button></p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Update Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="profile.php" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="hidden" name="u_id" value="<?php echo $u_id; ?>">
                                <label for="image" class="form-label">Profile Image</label>
                                <div class="py-3">
                                    <img src="<?php echo $image; ?>" alt="Profile Image" class="profile-img">
                                </div>
                                <input type="file" class="form-control" name="image" id="image">
                                <p><small>Leave empty if don't want to update!</small></p>
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>"
                                    placeholder="Enter your name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>"
                                    placeholder="Enter your email" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="update">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Update Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="profile.php" method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="opassword" class="form-label">Old Password</label>
                                <input type="password" class="form-control" id="opassword" name="opassword"
                                    placeholder="Enter your old password" required>
                            </div>
                            <div class="mb-3">
                                <label for="npassword" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="npassword" name="npassword"
                                    placeholder="Enter your new password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="updatePass">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once './footer.php'; ?>
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
</html>
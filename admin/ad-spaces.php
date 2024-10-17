<?php
include('../db_conn.php');
$info = $showSpaces = '';

$page = 'spaces'; // Définir le nom de la page

// Vérifiez si l'administrateur est connecté
if (!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] != true) {
    header('location:login.php');
    die();
}

// Vérifiez si le paramètre de suppression est défini dans l'URL
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $space_id = intval($_GET['delete']); // Obtenez l'ID de l'espace à supprimer

    // Préparez l'instruction SQL DELETE
    $sql = "DELETE FROM spaces WHERE s_id = ?";

    //  Initialiser une instruction préparée
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Liez l'ID d'espace à l'instruction préparée
        $stmt->bind_param("i", $space_id);

        //Exécuter l'instruction préparée
        if ($stmt->execute()) {
            $info = "Space with ID $space_id has been deleted successfully."; // Message de réussite
        } else {
            $info = "Error deleting space: " . $stmt->error; // Message d'erreur
        }

        // Fermez la déclaration
        $stmt->close();
    } else {
        $info = "Error preparing statement: " . $conn->error; // Error in statement preparation
    }
}

//  Requête pour obtenir tous les espaces
$sqlSpaces = "SELECT s.*, u.name AS owner_name, u.image AS owner_image FROM spaces s LEFT JOIN users u ON s.u_id = u.u_id";
$resultSpaces = mysqli_query($conn, $sqlSpaces);

$days = array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
if (mysqli_num_rows($resultSpaces) > 0) {
    while ($row = mysqli_fetch_assoc($resultSpaces)) {
        $availableTime = '';
        if($row['full_time']){
            $availableTime = 'Full Time';
        }else{
            foreach ($days as $day) {
                if ($row[$day . '_start'] && $row[$day . '_end'] && ($row[$day . '_start'] != '00:00:00' && $row[$day . '_end'] != '00:00:00')) {
                    $availableTime .= ucfirst($day) . ': ' . date('h:i A', strtotime($row[$day . '_start'])) . ' - ' . date('h:i A', strtotime($row[$day . '_end'])) . '<br>';
                }
            }
        }
        $location = 'https://www.google.com/maps?q=' . $row['latitude'] . ',' . $row['longitude'] . '';
        $showSpaces .= '<tr>
            <td class="text-start">' . $row['s_id'] . '</td>
            <td class="text-start"><div class="d-flex align-items-center gap-2"><img src="../assets/uploads/' . $row['owner_image'] . '" style="height: 40px; width: 40px; object-fit: cover;" class="rounded-circle mr-2"><span>' . htmlspecialchars($row['owner_name']) . '</span></div></td>
            <td class="text-start">' . htmlspecialchars($row['type']) . '</td>
            <td class="text-start">' . htmlspecialchars($row['post_code']) . '</td>
            <td class="text-start">' . htmlspecialchars($row['description']) . '</td>
            <td class="text-start">'.$availableTime.'</td>
            <td class="text-start"><a href="'.$location.'" target="_blank"><i class="fa fa-location-arrow mr-2"></i>Location</a></td>
            <td class="text-start"><a href="../edit-space.php?s_id=' . $row['s_id'] . '" class="btn btn-primary mr-1"><i class="fa fa-edit"></i></a><a onclick="return confirm(\'Do you really want to delete this space?\')" href="?delete=' . $row['s_id'] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a></td>
        </tr>';
    }
} else {
    $showSpaces = '<tr><td colspan="7" class="text-center">No spaces found!</td></tr>';
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
    <title>Admin Panel - Spaces</title>
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
                                    <h5 class="card-header d-flex justify-content-between align-items-center">Spaces</h5>
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
                                                        <th class="border-0">Space ID</th>
                                                        <th class="border-0">Owner</th>
                                                        <th class="border-0">Type</th>
                                                        <th class="border-0">Post Code</th>
                                                        <th class="border-0">Description</th>
                                                        <th class="border-0">Availability</th>
                                                        <th class="border-0" colspan="1">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php echo $showSpaces; ?>
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
    <!-- bootstrap bundle js -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<div class="gtranslate_wrapper"></div>
        <script>window.gtranslateSettings = { "default_language": "en", "languages": ["en", "fr", "nl"], "wrapper_selector": ".gtranslate_wrapper", "switcher_horizontal_position": "right", "flag_style": "3d" }</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    </body>
</html>

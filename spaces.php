<?php
include('db_conn.php');
include('functions.php');
// Vérifiez si le formulaire est soumis
$info = $show = '';
// Vérifiez si le formulaire est soumis
if (checkUserType() == 'Owner') {
    $u_id = $_SESSION['u_id'];
    $sql = "SELECT * FROM `spaces` WHERE `u_id` = '$u_id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!empty($row)) {
        $show = ''; // Initialisez $show avant utilisation
        do {
            $s_id = $row['s_id'];
            $type = $row['type'];
            $post_code = $row['post_code'];
            $rate = $row['rate'];
            $description = $row['description'];

            $spaceEarnings = getEarningsBySpaceId($conn, $s_id);

            // Requête pour compter le nombre de réservations pour l'espace actuel
            $sql_bookings = "SELECT COUNT(*) AS `booking_count` FROM `bookings` WHERE `s_id` = '$s_id'";
            $result_bookings = mysqli_query($conn, $sql_bookings);
            if (!$result_bookings) {
                echo "Error fetching booking count: " . mysqli_error($conn);
            }
            $row_bookings = mysqli_fetch_assoc($result_bookings);
            $booking_count = !empty($row_bookings['booking_count']) ? $row_bookings['booking_count'] : 0;

            // Facultatif : Calculer la note moyenne de l'espace
            $sql_rating = "SELECT AVG(rating) AS `avg_rating`, COUNT(*) AS `total_rating` FROM `ratings` WHERE `s_id` = '$s_id'";
            $result_rating = mysqli_query($conn, $sql_rating);
            if (!$result_rating) {
                echo "Error fetching rating: " . mysqli_error($conn);
            }
            $row_rating = mysqli_fetch_assoc($result_rating);
            $avg_rating = !empty($row_rating['avg_rating']) ? round($row_rating['avg_rating']) : 0;

            // Create rating stars
            $rating = '';
            for ($i = 0; $i < 5; $i++) {
                $filled = $i < $avg_rating ? 'filled' : '';
                $rating .= '<i class="fa fa-star ' . $filled . '"></i>';
            }
            $show .= '<div class="col-lg-4 col-md-6 col-12 py-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="fw-normal">' . htmlspecialchars($type) . ' at ' . htmlspecialchars($post_code) . '</h4>
                        <p class="mb-2 stars">' . $rating . '</p>
                        <p class="m-0">Rate: &euro; ' . number_format($rate, 2, '.', '') . '</p>
                        <a href="space-details.php?s_id=' . $s_id . '" class="m-0 text-decoration-underline">Bookings: ' . $booking_count . '</a>
                        <h4 class="text-success mb-0 mt-2">&euro; '.$spaceEarnings.'</h4>
                    </div>
                    <div class="card-footer text-end">
                        <a href="edit-space.php?s_id=' . $s_id . '" class="btn btn-primary">Edit</a>
                    </div>
                </div>
            </div>';
        } while ($row = mysqli_fetch_assoc($result)); // Assurez-vous que $result est défini et valide avant cette ligne
    } else {
        $show = '<div class="alert alert-danger">No spaces available!</div>';
    }

    // Close connection after all operations are done
    mysqli_close($conn);
} else {
    $show = '<div class="alert alert-danger">Only owners allowed!</div>';
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
            <section class="py-3">
                <div class="container">
                    <h2 class="text-dark text-center mt-4">My Spaces</h2>
                    <div class="row">
                        <?php echo $show; ?>
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
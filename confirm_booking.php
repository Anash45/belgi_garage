<?php
include('db_conn.php');
//  Vérifiez si le formulaire est soumis
$info = $show = '';
if (checkUserType() != 'Driver') {
    $show = '<div class="alert alert-danger">Only drivers are allowed on this page.</div>';
} elseif (isset($_REQUEST['start_time']) && isset($_REQUEST['end_time']) && isset($_REQUEST['total']) && isset($_REQUEST['day']) && isset($_REQUEST['s_id'])) {
    $u_id = $_SESSION['u_id'];
    $s_id = mysqli_real_escape_string($conn, $_REQUEST['s_id']);
    $total = mysqli_real_escape_string($conn, $_REQUEST['total']);
    $day = date('Y-m-d', strtotime($_REQUEST['day']));
    $start_time = mysqli_real_escape_string($conn, $_REQUEST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_REQUEST['end_time']);
    $duration = mysqli_real_escape_string($conn, $_REQUEST['duration']);
    $payment_method = mysqli_real_escape_string($conn, $_REQUEST['payment_method']);
    $query = "INSERT INTO bookings (s_id, u_id, date, start_time, end_time, duration, total, payment_method) 
          VALUES ('$s_id', '$u_id', '$day', '$start_time', '$end_time', '$duration', '$total', '$payment_method')";

    // Exécuter la requête
    if (mysqli_query($conn, $query)) {
        $show = '<div class="alert alert-success"><b>Success</b><br>Booking created successfully. You can see all booking on your bookings page.</div>';
        unset($_SESSION['search']);
        header('refresh:2,url=bookings.php');
    } else {
        $show = '<div class="alert alert-danger">Error: An error occurred.</div>';
    }
} else {
    // header('location:index.php');
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
                    <h3 class="fw-normal">Booking Confirmation</h3>
                </div>
            </section>
            <section class="py-3">
                <div class="container">
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
<?php
include('db_conn.php');
// Check if the form is submitted
$info = $show = '';
// Check if the form is submitted
if (checkUserType() == 'Driver') {
    // print_r($_REQUEST);
    $u_id = $_SESSION['u_id'];
    $booking_id = isset($_GET['cancel']) ? intval($_GET['cancel']) : 0; // Booking ID from URL

    if ($booking_id > 0) {
        // Check if the booking belongs to the logged-in user
        $query = "SELECT * FROM bookings WHERE b_id = ? AND u_id = ? AND `status` = 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $booking_id, $u_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Booking belongs to the user, update the status to cancel it
            $update_query = "UPDATE bookings SET status = 0 WHERE b_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param('i', $booking_id);

            if ($update_stmt->execute()) {
                $info = "<p class='alert alert-success'>Booking canceled successfully.";
            } else {
                $info = "<p class='alert alert-danger'>Error: Could not cancel booking. Please try again later.";
            }
        } else {
            $info = "<p class='alert alert-danger'>This booking either does not belong to you or is already canceled.";
        }
    }

    if (isset($_REQUEST['rating'])) {
        $s_id = $_REQUEST['sp_id'];
        $b_id = $_REQUEST['b_id'];
        $rating1 = mysqli_real_escape_string($conn, $_REQUEST['rating']);
        $reviewText = mysqli_real_escape_string($conn, $_REQUEST['review']);
        $sql2 = "SELECT * FROM `ratings` WHERE `b_id` = '$b_id'";
        $result2 = mysqli_query($conn, $sql2);
        if (mysqli_num_rows($result2) > 0) {
            $show = '<div class="alert alert-danger">Already rated!</div>';
        } else {
            $sql4 = "INSERT INTO `ratings` (`s_id`,`u_id`,`b_id`,`rating`,`review`) VALUES ('$s_id','$u_id','$b_id','$rating1','$reviewText')";
            if (mysqli_query($conn, $sql4)) {
                $sql5 = "UPDATE `bookings` SET `status` = '2' WHERE `b_id` = '$b_id'";
                mysqli_query($conn, $sql5);
                $show = '<div class="alert alert-success">Rating successful!</div>';
            } else {
                $show = '<div class="alert alert-danger">Error!</div>';
            }
        }

    }
    $sql2 = "SELECT * FROM `bookings` WHERE `u_id` = '$u_id'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    if (!empty($row2)) {
        do {
            $reviewHtml = '';
            $s_id = $row2['s_id'];
            $b_id = $row2['b_id'];
            $bookingDate = new DateTime($row2['date']);
            $startTime = new DateTime($row2['date'].' '. $row2['start_time']);
            $endTime = new DateTime($row2['date'].' '. $row2['end_time']);
            $currentDateTime = new DateTime();


            $sql1 = "SELECT * FROM `spaces` WHERE `s_id` = '$s_id'";
            $result1 = mysqli_query($conn, $sql1);
            $row1 = mysqli_fetch_assoc($result1);
            if (!empty($row1)) {
                $location = 'https://www.google.com/maps?q=' . $row1['latitude'] . ',' . $row1['longitude'];

                // Determine if "Cancel" button should be shown
                $showCancelButton = ($bookingDate > $currentDateTime && $bookingDate->diff($currentDateTime)->days >= 1 && $row2['status'] != 0);
                // Determine if "Rate" button should be shown
                $showRateButton = ($endTime < $currentDateTime);

                $rating = '';
                // Generate button HTML
                $buttonsHtml = $cardFooter = '';
                if ($showCancelButton) {
                    $buttonsHtml .= '<a href="?cancel=' . $row2['b_id'] . '" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="You can cancel the booking 1 day before the date of booking">Cancel</a>';
                }elseif($row2['status'] == 0){
                    $buttonsHtml .= '<span class="text-danger">Booking canceled!</span>';
                }
                if ($showRateButton) {

                    if ($row2['status'] == 2) {
                    // Fetch and display rating
                    $sql3 = "SELECT rating, review FROM `ratings` 
                    WHERE `b_id` = '$b_id'";
                    $result3 = mysqli_query($conn, $sql3);
                    $row3 = mysqli_fetch_assoc($result3);
                    $rating = !empty($row3['rating']) ? round($row3['rating']) : 0;

                    $reviewHtml .= '<div class="text-start d-flex flex-column gap-1"><p class="mb-0 stars">';
                    for ($i = 0; $i < 5; $i++) {
                        $filled = $i < $rating ? ' filled' : '';
                        $reviewHtml .= '<i class="fa fa-star' . $filled . '"></i>';
                    }
                    $reviewHtml .= '</p>';
                    $reviewHtml .= (!empty($row3['review'])) ? '<q class="m-0">' . $row3['review'] . '</q>' : '';
                    $reviewHtml .= '</div>';
                } else if($row2['status'] == 1) {
                        $buttonsHtml .= '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#ratingModal" onclick="space_id(' . $s_id . ',' . $row2['b_id'] . ')">Rate</button>';
                    }
                }

                if ($showCancelButton || $showRateButton || $row2['status'] == 0 || $row2['status'] == 2) {

                    $cardFooter = '<div class="card-footer text-end">
                                ' . $buttonsHtml . '
                                ' . $reviewHtml . '
                            </div>';

                }

                $show .= '
                <div class="col-lg-4 col-md-6 col-12 py-3">
                    <div class="card h-100">
                        <div class="card-body position-relative">
                            <span class="created-at text-muted">Created At: '.date("m/d/Y h:i a", strtotime($row2['created_at'])).'</span>
                            <h4 class="fw-normal">' . $row1['type'] . ' at ' . $row1['post_code'] . '</h4>
                            <p class="m-0">' . date('d F, Y', strtotime($row2['date'])) . ' | ' . date('h:i A', strtotime($row2['start_time'])) . ' - ' . date('h:i A', strtotime($row2['end_time'])) . '</p>
                            <p class="m-0"><a class="text-primary" href="' . $location . '" target="_blank"><i class="fa fa-location-pin"></i> <span>Location </span></a></p>
                            <p class="m-0 fw-bold">&euro; ' . number_format($row2['total'], 2, '.', '') . '</p>
                        </div>
                        '.$cardFooter.'
                    </div>
                </div>';
            }
        } while ($row2 = mysqli_fetch_assoc($result2));
    } else {
        $show = '<div class="alert alert-danger">No bookings made!</div>';
    }
} else {
    $show = '<div class="alert alert-danger">Only drivers allowed!</div>';
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
                    <h2 class="text-dark text-center mt-4">My Bookings</h2>
                    <?php echo $info; ?>
                    <div class="row">
                        <?php echo $show; ?>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once './footer.php'; ?>
    </body>
    <!-- Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">Rate your visit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="bookings.php">
                        <div class="mb-3">
                            <input type="hidden" name="sp_id" id="s_id">
                            <input type="hidden" name="b_id" id="b_id">
                            <label for="ratingSelect" class="form-label">Choose from 1 to 5 (<small>5 means
                                    best.</small>)</label>
                            <select class="form-select mb-2" id="ratingSelect" name="rating" required>
                                <option value="" selected>Choose rating</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                            <textarea name="review" id="review" class="form-control" placeholder="Type your review..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script>
        function space_id(s_id, b_id) {
            $('#s_id').val(s_id);
            $('#b_id').val(b_id);
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>

</html>
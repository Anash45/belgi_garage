<?php
include('db_conn.php');
include('functions.php');

// Check if the user is logged in and is of type Owner
$info = $show = '';
if (checkUserType() == 'Owner') {
    $u_id = $_SESSION['u_id'];
    $s_id = isset($_GET['s_id']) ? intval($_GET['s_id']) : 0; // Space ID from URL
    $booking_id = isset($_GET['cancel']) ? intval($_GET['cancel']) : 0; // Booking ID from URL

    // Check if the space belongs to the logged-in user
    $spaceQuery = "SELECT * FROM spaces WHERE s_id = ? AND u_id = ? ";
    $spaceStmt = $conn->prepare($spaceQuery);
    $spaceStmt->bind_param('ii', $s_id, $u_id);
    $spaceStmt->execute();
    $spaceResult = $spaceStmt->get_result();
    $spaceRow = mysqli_fetch_assoc($spaceResult);

    if ($spaceResult->num_rows > 0) {
        // Space belongs to the owner
        if ($booking_id > 0) {
            // Check if the booking belongs to the space and is active
            $query = "SELECT * FROM bookings WHERE b_id = ? AND s_id = ? AND `status` = 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $booking_id, $s_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Booking belongs to the space, update the status to cancel it
                $update_query = "UPDATE bookings SET status = 0 WHERE b_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param('i', $booking_id);

                if ($update_stmt->execute()) {
                    $info = "<p class='alert alert-success'>Booking canceled successfully.</p>";
                } else {
                    $info = "<p class='alert alert-danger'>Error: Could not cancel booking. Please try again later.</p>";
                }
            } else {
                $info = "<p class='alert alert-danger'>This booking either does not belong to this space or is already canceled.</p>";
            }
        }

        // Fetch all bookings for the specific space
        $sql2 = "SELECT * FROM bookings WHERE s_id = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param('i', $s_id);
        $stmt->execute();
        $result2 = $stmt->get_result();

        if ($result2->num_rows > 0) {
            while ($row2 = $result2->fetch_assoc()) {
                $b_id = $row2['b_id'];
                $bookingDate = new DateTime($row2['date']);
                $startTime = new DateTime($row2['start_time']);
                $endTime = new DateTime($row2['end_time']);
                $currentDateTime = new DateTime();

                // Determine if "Cancel" button should be shown
                $showCancelButton = ($bookingDate > $currentDateTime && $bookingDate->diff($currentDateTime)->days >= 1 && $row2['status'] != 0);
                $rating = ''; // Removed rating functionality

                $buttonsHtml = '';
                if ($showCancelButton) {
                    $buttonsHtml .= '<a href="?s_id=' . $s_id . '&cancel=' . $row2['b_id'] . '" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="You can cancel the booking 1 day before the date of booking">Cancel</a>';
                } elseif ($row2['status'] == 0) {
                    $buttonsHtml .= '<span class="text-danger">Booking canceled!</span>';
                }

                $user = getUserById($conn, $row2['u_id']);
                 if ($user){ 
                    $userHtml = '<!-- Display user image -->
                    <div class="d-flex mb-3 align-items-center gap-3"><img src="./assets/uploads/'.htmlspecialchars($user['image']).'" alt="User Image" class="img-fluid rounded-circle"
                        style="width: 40px; height: 40px;">
                    <h5 class="h6 my-0">'.htmlspecialchars($user['name']).'</h5></div>';
                 }else{ 
                    $userHtml = '<!-- Placeholder for image -->
                    <div class="d-flex mb-3 align-items-center gap-3"><img src="./assets/img/Portrait_Placeholder.png" alt="Placeholder" class="img-fluid rounded-circle"
                        style="width: 40px; height: 40px;">
                    <h5 class="h6 my-0">Deleted User</h5></div>';
                 }

                $reviewHtml = $cardFooter = '';
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
                }

                $cardFooter = '<div class="card-footer text-end">
                                ' . $userHtml . '
                                ' . $buttonsHtml . '
                                ' . $reviewHtml . '
                            </div>';

                $show .= '
                <div class="col-lg-4 col-md-6 col-12 py-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="fw-normal">' . $spaceRow['type'] . ' at ' . $spaceRow['post_code'] . '</h4>
                            <p class="m-0">' . date('d F, Y', strtotime($row2['date'])) . '</p>
                            <p class="m-0">&euro; ' . number_format($row2['total'], 2, '.', '') . '</p>
                        </div>
                        ' . $cardFooter . '
                    </div>
                </div>';
            }
        } else {
            $show = '<div class="alert alert-danger">No bookings found for this space!</div>';
        }
    } else {
        $show = '<div class="alert alert-danger">You do not have permission to view bookings for this space.</div>';
    }
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
                    <h2 class="text-dark text-center mt-4">Space Bookings</h2>
                    <?php echo $info; ?>
                    <div class="row">
                        <?php echo $show; ?>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once './footer.php'; ?>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/jquery-3.6.1.min.js"></script>
        <script>
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>
    </body>

</html>
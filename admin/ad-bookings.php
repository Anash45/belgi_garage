<?php
include('../db_conn.php');

$info = '';

$page = 'bookings'; // Set the page name
$showBookings = $reviewModals = '';

// Check if the user is an admin
if (!isset($_SESSION['adminLogin']) || $_SESSION['adminLogin'] !== true) {
    header('location:login.php');
    die();
}

// Check if the delete parameter is set in the URL
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $booking_id = intval($_GET['delete']); // Get the booking ID to delete

    // Prepare the DELETE SQL statement
    $sql = "DELETE FROM bookings WHERE b_id = ?";

    // Initialize a prepared statement
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Bind the booking ID to the prepared statement
        $stmt->bind_param("i", $booking_id);

        // Execute the prepared statement
        if ($stmt->execute()) {
            $info = "Booking with ID $booking_id has been deleted successfully."; // Success message
        } else {
            $info = "Error deleting booking: " . $stmt->error; // Error message
        }

        // Close the statement
        $stmt->close();
    } else {
        $info = "Error preparing statement: " . $conn->error; // Error in statement preparation
    }
}

// Fetch all bookings
$sqlBookings = "SELECT b.*, s.address, u.name as user_name FROM bookings b 
                JOIN spaces s ON b.s_id = s.s_id 
                JOIN users u ON b.u_id = u.u_id";
$resultBookings = mysqli_query($conn, $sqlBookings);

if (mysqli_num_rows($resultBookings) > 0) {
    while ($row = mysqli_fetch_assoc($resultBookings)) {
        $showBookings .= '<tr>
            <td class="text-start">' . $row['b_id'] . '</td>
            <td class="text-start">' . $row['user_name'] . '</td>
            <td class="text-start">' . $row['address'] . '</td>
            <td class="text-start">' . $row['duration'] . ' hours</td>
            <td class="text-start">$' . $row['total'] . '</td>
            <td class="text-start">' . $row['payment_method'] . '</td>
            <td class="text-start">' . getStatusText($row['status']) . '</td>
            <td class="text-start">' . $row['start_time'] . '</td>
            <td class="text-start">
                <a onclick="return confirm(\'Do you really want to delete this booking?\')" href="?delete=' . $row['b_id'] . '" class="btn btn-danger"><i class="fa fa-trash"></i></a>';

        // Check if the booking can be canceled
        if ($row['status'] == 1 && strtotime($row['start_time']) > time()) {
            $showBookings .= ' <a href="cancel.php?b_id=' . $row['b_id'] . '" class="btn btn-warning">Cancel</a>';
        }

        // If booking is completed, show the rating button
        if ($row['status'] == 2) {
            $showBookings .= ' <button class="btn btn-info" data-toggle="modal" data-target="#ratingModal' . $row['b_id'] . '">View Rating</button>';
        }

        $showBookings .= '</td>
        </tr>';

        // Modal for viewing rating
        // Modal for viewing rating
        if ($row['status'] == 2) {
            $reviewHtml = '';
            $rating = getRating($row['b_id']);
            for ($i = 0; $i < 5; $i++) {
                $filled = ($i < $rating) ? 'fas' : 'far';
                $reviewHtml .= '<i class="' . $filled . ' fa-star"></i>';
            }
            $showBookings .= '
    <div class="modal fade" id="ratingModal' . $row['b_id'] . '" tabindex="-1" role="dialog" aria-labelledby="ratingModalLabel' . $row['b_id'] . '" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel' . $row['b_id'] . '">Rating for Booking ID ' . $row['b_id'] . '</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Rating:</strong> <span class="d-flex align-items-center">' . $reviewHtml . '</span></p>
                    <p><strong>Review:</strong> ' . getReview($row['b_id']) . '</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>';
        }

    }
} else {
    $showBookings = '<tr><td colspan="8" class="text-center">No bookings found!</td></tr>';
}

// Helper function to get booking status text
function getStatusText($status)
{
    switch ($status) {
        case 0:
            return 'Cancelled';
        case 1:
            return 'On Schedule';
        case 2:
            return 'Completed';
        default:
            return 'Unknown';
    }
}

// Helper function to get rating (stub, replace with actual logic)
function getRating($b_id)
{
    global $conn; // Use the global connection variable
    $ratingQuery = "SELECT rating FROM ratings WHERE b_id = ?";
    $stmt = $conn->prepare($ratingQuery);
    $stmt->bind_param("i", $b_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['rating'] : 'No rating found';
}

// Helper function to get review (stub, replace with actual logic)
function getReview($b_id)
{
    global $conn; // Use the global connection variable
    $reviewQuery = "SELECT review FROM ratings WHERE b_id = ?";
    $stmt = $conn->prepare($reviewQuery);
    $stmt->bind_param("i", $b_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc()['review'] : 'No review found';
}
?>
<!doctype html>
<html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="./assets/vendor/bootstrap/css/bootstrap.min.css">
        <link href="./assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
        <link rel="stylesheet" href="./assets/libs/css/style.css">
        <link rel="stylesheet" href="./assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
        <title>Admin Panel - Bookings</title>
        <link rel="shortcut icon" href="./assets/img/favicon.png" type="image/png">
    </head>

    <body>
        <div class="dashboard-main-wrapper">
            <?php include('header.php'); ?>
            <?php include('sidebar.php'); ?>
            <div class="dashboard-wrapper">
                <div class="dashboard-ecommerce">
                    <div class="container-fluid dashboard-content ">
                        <div class="ecommerce-widget">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="card">
                                        <h5 class="card-header">Bookings</h5>
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
                                                            <th class="border-0">Booking ID</th>
                                                            <th class="border-0">User Name</th>
                                                            <th class="border-0">Space Address</th>
                                                            <th class="border-0">Duration</th>
                                                            <th class="border-0">Total</th>
                                                            <th class="border-0">Payment Method</th>
                                                            <th class="border-0">Status</th>
                                                            <th class="border-0">Start Time</th>
                                                            <th class="border-0">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php echo $showBookings; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $reviewModals; ?>
            <script src="./assets/vendor/jquery/jquery-3.3.1.min.js"></script>
            <script src="./assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
        </div>
    </body>

</html>
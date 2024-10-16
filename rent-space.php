<?php
include('db_conn.php');
// Check if the form is submitted
$info = '';
// Assuming you have already established a database connection
if (checkUserType() != 'Owner') {
    header('location:index.php?err=1');
}
// Check if the form is submitted
if (isset($_POST['submit'])) {

    // Escape special characters to prevent SQL injection
    $u_id = $_SESSION['u_id'];
    $post_code = mysqli_real_escape_string($conn, $_REQUEST['post_code']);
    $address = mysqli_real_escape_string($conn, $_REQUEST['address']);
    $latitude = mysqli_real_escape_string($conn, $_REQUEST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_REQUEST['longitude']);
    $type = '';
    if (isset($_REQUEST['type'])) {
        $type = mysqli_real_escape_string($conn, $_REQUEST['type']);
    }
    $description = '';
    if (isset($_REQUEST['description'])) {
        $description = mysqli_real_escape_string($conn, $_REQUEST['description']);
    }
    $full_time = '0';
    if (isset($_REQUEST['full_time'])) {
        $full_time = 1;
    }
    $mon_start = '';
    $mon_end = '';
    if ((isset($_REQUEST['mon_start']) && !empty($_REQUEST['mon_start'])) && (isset($_REQUEST['mon_end']) && !empty($_REQUEST['mon_end']))) {
        $mon_start = date('H:i:s', strtotime($_REQUEST['mon_start']));
        $mon_end = date('H:i:s', strtotime($_REQUEST['mon_end']));
    }
    $tue_start = '';
    $tue_end = '';
    if ((isset($_REQUEST['tue_start']) && !empty($_REQUEST['tue_start'])) && (isset($_REQUEST['tue_end']) && !empty($_REQUEST['tue_end']))) {
        $tue_start = date('H:i:s', strtotime($_REQUEST['tue_start']));
        $tue_end = date('H:i:s', strtotime($_REQUEST['tue_end']));
    }
    $wed_start = '';
    $wed_end = '';
    if ((isset($_REQUEST['wed_start']) && !empty($_REQUEST['wed_start'])) && (isset($_REQUEST['wed_end']) && !empty($_REQUEST['wed_end']))) {
        $wed_start = date('H:i:s', strtotime($_REQUEST['wed_start']));
        $wed_end = date('H:i:s', strtotime($_REQUEST['wed_end']));
    }
    $thu_start = '';
    $thu_end = '';
    if ((isset($_REQUEST['thu_start']) && !empty($_REQUEST['thu_start'])) && (isset($_REQUEST['thu_end']) && !empty($_REQUEST['thu_end']))) {
        $thu_start = date('H:i:s', strtotime($_REQUEST['thu_start']));
        $thu_end = date('H:i:s', strtotime($_REQUEST['thu_end']));
    }
    $fri_start = '';
    $fri_end = '';
    if ((isset($_REQUEST['fri_start']) && !empty($_REQUEST['fri_start'])) && (isset($_REQUEST['fri_end']) && !empty($_REQUEST['fri_end']))) {
        $fri_start = date('H:i:s', strtotime($_REQUEST['fri_start']));
        $fri_end = date('H:i:s', strtotime($_REQUEST['fri_end']));
    }
    $sat_start = '';
    $sat_end = '';
    if ((isset($_REQUEST['sat_start']) && !empty($_REQUEST['sat_start'])) && (isset($_REQUEST['sat_end']) && !empty($_REQUEST['sat_end']))) {
        $sat_start = date('H:i:s', strtotime($_REQUEST['sat_start']));
        $sat_end = date('H:i:s', strtotime($_REQUEST['sat_end']));
    }
    $sun_start = '';
    $sun_end = '';
    if ((isset($_REQUEST['sun_start']) && !empty($_REQUEST['sun_start'])) && (isset($_REQUEST['sun_end']) && !empty($_REQUEST['sun_end']))) {
        $sun_start = date('H:i:s', strtotime($_REQUEST['sun_start']));
        $sun_end = date('H:i:s', strtotime($_REQUEST['sun_end']));
    }
    $rate = mysqli_real_escape_string($conn, $_REQUEST['rate']);

    if ($full_time == 1 || ((!empty($mon_start) && (!empty($mon_end)) || (!empty($tue_start) && !empty($tue_end)) || (!empty($wed_start) && !empty($wed_end)) || (!empty($thu_start) && !empty($thu_end)) || (!empty($fri_start) && !empty($fri_end)) || (!empty($sat_start) && !empty($sat_end)) || (!empty($sun_start) && !empty($sun_end))))) {
        // Check if the same post code, address, and type already exist in the table
        $checkQuery = "SELECT * FROM spaces WHERE post_code = '$post_code' AND address = '$address' AND type = '$type'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            // Duplicate entry exists
            $info = "<div class='alert alert-danger'>Error: The space with the same post code, address, and type already exists.</div>";
        } else {
            // Insert the data into the spaces table
            $insertQuery = "INSERT INTO spaces (u_id, post_code, `address`, `type`, latitude, longitude, `description`, full_time,
                        mon_start, mon_end, tue_start, tue_end, wed_start, wed_end, thu_start, thu_end,
                        fri_start, fri_end, sat_start, sat_end, sun_start, sun_end, rate)
                        VALUES ('$u_id', '$post_code', '$address', '$type', '$latitude', '$longitude', '$description', '$full_time',
                        '$mon_start', '$mon_end', '$tue_start', '$tue_end', '$wed_start', '$wed_end', '$thu_start',
                        '$thu_end', '$fri_start', '$fri_end', '$sat_start', '$sat_end', '$sun_start', '$sun_end', '$rate')";
            $insertResult = mysqli_query($conn, $insertQuery);

            if ($insertResult) {
                // Data insertion successful
                $info = "<div class='alert alert-success'>Space saved successfully.</div>";
            } else {
                // Failed to insert data
                $info = "<div class='alert alert-danger'>Error: Failed to insert data into the table.</div>";
            }
        }

    } else {
        $info = "<div class='alert alert-danger'>Select at least 1 time slot.</div>";
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
            crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="./assets/css/style.css?v=1_1">
    </head>

    <body>
        <?php include('header.php'); ?>
        <main>
            <section>
                <div class="row mx-0">
                    <div class="col-md-6 col-12 mx-auto">
                        <form action="" method="post" class="px-5 py-5 needs-validation" id="owner-form" novalidate>
                            <?php echo $info; ?>
                            <div class="mb-3">
                                <label class="fw-semibold">Your post code</label>
                                <input type="text" name="post_code" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Address</label>
                                <input type="text" name="address" required class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold d-flex align-items-center gap-3"><span>Select location on
                                        Google Map or select current location</span><button id="currentLocationBtn"
                                        class="btn btn-primary btn-sm"><i
                                            class="fa-solid fa-location-crosshairs"></i></button></label>
                                <div id="map-selection" style="height: 300px;" class="w-100 mt-2"></div>
                                <input type="text" name="latitude" id="lat" class="d-none form-control" required>
                                <input type="text" name="longitude" id="lng" class="d-none form-control" required>
                                <p class="mb-0 invalid-feedback">Select a location from the map!</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Space Type</label>
                                <div class="form-check py-1">
                                    <input class="form-check-input p-2 me-3" type="radio" value="Driveway" required
                                        name="type" id="opt1">
                                    <label class="form-check-label" for="opt1"> Driveway </label>
                                </div>
                                <div class="form-check py-1">
                                    <input class="form-check-input p-2 me-3" type="radio" value="Car Park" name="type"
                                        id="opt2">
                                    <label class="form-check-label" for="opt2"> Car Park </label>
                                </div>
                                <div class="form-check py-1">
                                    <input class="form-check-input p-2 me-3" type="radio" value="On Street" name="type"
                                        id="opt3">
                                    <label class="form-check-label" for="opt3"> On Street </label>
                                </div>
                                <div class="form-check py-1">
                                    <input class="form-check-input p-2 me-3" type="radio" value="Garage" name="type"
                                        id="opt4">
                                    <label class="form-check-label" for="opt4"> Garage </label>
                                </div>
                                <div class="form-check py-1">
                                    <input class="form-check-input p-2 me-3" type="radio" value="Other" name="type"
                                        id="opt5">
                                    <label class="form-check-label" for="opt5"> Other </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Space Description</label>
                                <textarea rows="4" name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Space Availability</label>
                                <div class="card my-2">
                                    <div class="card-body">
                                        <div class="form-check form-switch p-0 d-flex flex-row justify-content-between">
                                            <label class="form-check-label" for="full_time">Always available <small
                                                    class="text-muted">(24/7)</small></label>
                                            <input class="form-check-input" name="full_time" value="1" checked
                                                type="checkbox" id="full_time">
                                        </div>
                                    </div>
                                </div>
                                <div class="days-availability" id="days-availability">
                                    <div class="card my-2">
                                        <div class="card-header"> Monday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="mon_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="mon_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Tuesday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="tue_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="tue_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Wednesday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="wed_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="wed_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Thursday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="thu_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="thu_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Friday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="fri_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="fri_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Saturday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="sat_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="sat_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card my-2">
                                        <div class="card-header"> Sunday </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label>Start</label>
                                                    <select class="form-select form-select-sm" name="sun_start">
                                                        <option value="" selected>Select start time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label>End</label>
                                                    <select class="form-select form-select-sm" name="sun_end">
                                                        <option value="" selected>Select end time</option>
                                                        <option value="00:00:00">00:00</option>
                                                        <option value="01:00:00">01:00</option>
                                                        <option value="02:00:00">02:00</option>
                                                        <option value="03:00:00">03:00</option>
                                                        <option value="04:00:00">04:00</option>
                                                        <option value="05:00:00">05:00</option>
                                                        <option value="06:00:00">06:00</option>
                                                        <option value="07:00:00">07:00</option>
                                                        <option value="08:00:00">08:00</option>
                                                        <option value="09:00:00">09:00</option>
                                                        <option value="10:00:00">10:00</option>
                                                        <option value="11:00:00">11:00</option>
                                                        <option value="12:00:00">12:00</option>
                                                        <option value="13:00:00">13:00</option>
                                                        <option value="14:00:00">14:00</option>
                                                        <option value="15:00:00">15:00</option>
                                                        <option value="16:00:00">16:00</option>
                                                        <option value="17:00:00">17:00</option>
                                                        <option value="18:00:00">18:00</option>
                                                        <option value="19:00:00">19:00</option>
                                                        <option value="20:00:00">20:00</option>
                                                        <option value="21:00:00">21:00</option>
                                                        <option value="22:00:00">22:00</option>
                                                        <option value="23:00:00">23:00</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-semibold">Rate per hour</label>
                                <input type="text" pattern="[0-9]*\.?[0-9]+" placeholder="eg: 10.0" required name="rate"
                                    class="form-control">
                            </div>
                            <div class="mt-3">
                                <button class="btn btn-dark w-100" name="submit"> Save </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once './footer.php'; ?>
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwa3l7oekDnz3VVsTrsnLR17NslkgHqvA&callback=initMap"
        async defer></script>
    <script src="./assets/js/script.js?v=1_1"></script>
    <script>
        let map;
        let marker;

        function initMap() {
            const initialPosition = { lat: 50.5039, lng: 4.4699 }; // Default location (Belgium)

            // Create map centered on the initial position
            map = new google.maps.Map(document.getElementById("map-selection"), {
                zoom: 7,
                center: initialPosition,
            });

            // Add click event listener to the map
            map.addListener("click", (e) => {
                placeMarker(e.latLng);
            });

            // Set up the "Use Current Location" button
            $("#currentLocationBtn").click(() => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const currentPos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            };
                            map.setCenter(currentPos);
                            placeMarker(currentPos);
                        },
                        () => {
                            alert("Unable to retrieve your location.");
                        }
                    );
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            }
            $("#lat").val(location.lat);
            $("#lng").val(location.lng);
        }
    </script>

</html>
<?php
include('db_conn.php');

// Function to check if the user is an admin or an owner
function isAdminOrOwner($conn, $s_id) {
    $userType = checkUserType();
    if ($userType === 'Admin') {
        return true; // Allow access if user is admin
    } elseif ($userType === 'Owner') {
        // Check if the user is the owner of the space
        $u_id = $_SESSION['u_id'];
        $query = "SELECT u_id FROM spaces WHERE s_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $s_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $space = $result->fetch_assoc();
            return $space['u_id'] == $u_id; // Return true if the user is the owner
        }
    }
    return false; // Not authorized
}

// Check if space ID is provided
if (!isset($_REQUEST['s_id'])) {
    header('location:spaces.php');
    die();
}

// Get the space ID from the URL parameter
$s_id = intval($_GET['s_id']);

// Check user authorization
if (!isAdminOrOwner($conn, $s_id)) {
    header('location:index.php?err=1');
    die();
}

// Initialize variables for feedback and existing data
$info = '';
$existingData = null;

// Check if the form is submitted for updating data
if (isset($_POST['submit'])) {
    // Escape special characters to prevent SQL injection
    $post_code = mysqli_real_escape_string($conn, $_POST['post_code']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
    $type = isset($_POST['type']) ? mysqli_real_escape_string($conn, $_POST['type']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $full_time = isset($_POST['full_time']) ? 1 : 0;

    // Define day-specific timings with validation
    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    $dayTimings = [];
    foreach ($days as $day) {
        $start = isset($_POST[$day . '_start']) ? $_POST[$day . '_start'] : '';
        $end = isset($_POST[$day . '_end']) ? $_POST[$day . '_end'] : '';
        $dayTimings[$day] = ['start' => $start, 'end' => $end];
    }
    $rate = mysqli_real_escape_string($conn, $_POST['rate']);

    // Validate form submission for day timings or full-time
    $hasTimeSlot = $full_time || array_filter($dayTimings, function ($times) {
        return !empty($times['start']) && !empty($times['end']);
    });

    if ($hasTimeSlot) {
        // Check for duplicate space based on post_code, address, and type
        $checkQuery = "SELECT * FROM spaces WHERE post_code = '$post_code' AND address = '$address' AND type = '$type' AND s_id != $s_id";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $info = "<div class='alert alert-danger'>Error: Space with the same post code, address, and type already exists.</div>";
        } else {
            // Prepare update query for the existing space
            $updateQuery = "
                UPDATE spaces SET 
                    post_code = '$post_code', 
                    address = '$address', 
                    type = '$type', 
                    latitude = '$latitude', 
                    longitude = '$longitude', 
                    description = '$description', 
                    full_time = '$full_time', 
                    mon_start = '{$dayTimings['mon']['start']}', 
                    mon_end = '{$dayTimings['mon']['end']}', 
                    tue_start = '{$dayTimings['tue']['start']}', 
                    tue_end = '{$dayTimings['tue']['end']}', 
                    wed_start = '{$dayTimings['wed']['start']}', 
                    wed_end = '{$dayTimings['wed']['end']}', 
                    thu_start = '{$dayTimings['thu']['start']}', 
                    thu_end = '{$dayTimings['thu']['end']}', 
                    fri_start = '{$dayTimings['fri']['start']}', 
                    fri_end = '{$dayTimings['fri']['end']}', 
                    sat_start = '{$dayTimings['sat']['start']}', 
                    sat_end = '{$dayTimings['sat']['end']}', 
                    sun_start = '{$dayTimings['sun']['start']}', 
                    sun_end = '{$dayTimings['sun']['end']}', 
                    rate = '$rate' 
                WHERE s_id = $s_id";

            if (mysqli_query($conn, $updateQuery)) {
                $info = "<div class='alert alert-success'>Space updated successfully.</div>";
            } else {
                $info = "<div class='alert alert-danger'>Error: Failed to update space data.</div>";
            }
        }
    } else {
        $info = "<div class='alert alert-danger'>Error: Select at least 1 time slot or mark as 24/7.</div>";
    }
}

// Fetch existing data for the given space ID
if ($s_id > 0) {
    $fetchQuery = "SELECT * FROM spaces WHERE s_id = $s_id";
    $fetchResult = mysqli_query($conn, $fetchQuery);
    if ($fetchResult && mysqli_num_rows($fetchResult) > 0) {
        $existingData = mysqli_fetch_assoc($fetchResult);
    } else {
        $info = "<div class='alert alert-danger'>Error: Space not found.</div>";
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
                        <form action="?s_id=<?php echo $s_id; ?>" method="post" class="px-5 py-5 needs-validation"
                            id="owner-form" novalidate>
                            <h3 class="text-center fw-semibold">Edit your space</h3>
                            <?php echo $info; ?>
                            <?php if (!empty($existingData)): ?>
                                <div class="mb-3">
                                    <label class="fw-semibold">Your post code</label>
                                    <input type="text" name="post_code"
                                        value="<?php echo $existingData['post_code'] ?? ''; ?>" required
                                        class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold">Address</label>
                                    <input type="text" name="address" value="<?php echo $existingData['address'] ?? ''; ?>"
                                        required class="form-control">
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
                                        <input class="form-check-input p-2 me-3" type="radio" value="Driveway" <?php echo $typeCheck = ($existingData['type'] == 'Driveway') ? 'checked' : ''; ?> required
                                            name="type" id="opt1">
                                        <label class="form-check-label" for="opt1"> Driveway </label>
                                    </div>
                                    <div class="form-check py-1">
                                        <input class="form-check-input p-2 me-3" type="radio" value="Car Park" <?php echo $typeCheck = ($existingData['type'] == 'Car Park') ? 'checked' : ''; ?> name="type"
                                            id="opt2">
                                        <label class="form-check-label" for="opt2"> Car Park </label>
                                    </div>
                                    <div class="form-check py-1">
                                        <input class="form-check-input p-2 me-3" type="radio" value="On Street" <?php echo $typeCheck = ($existingData['type'] == 'On Street') ? 'checked' : ''; ?> name="type"
                                            id="opt3">
                                        <label class="form-check-label" for="opt3"> On Street </label>
                                    </div>
                                    <div class="form-check py-1">
                                        <input class="form-check-input p-2 me-3" type="radio" value="Garage" <?php echo $typeCheck = ($existingData['type'] == 'Garage') ? 'checked' : ''; ?> name="type"
                                            id="opt4">
                                        <label class="form-check-label" for="opt4"> Garage </label>
                                    </div>
                                    <div class="form-check py-1">
                                        <input class="form-check-input p-2 me-3" type="radio" value="Other" <?php echo $typeCheck = ($existingData['type'] == 'Other') ? 'checked' : ''; ?> name="type"
                                            id="opt5">
                                        <label class="form-check-label" for="opt5"> Other </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold">Space Description</label>
                                    <textarea rows="4" name="description" class="form-control"
                                        required><?php echo $existingData['description'] ?? ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-semibold">Space Availability</label>
                                    <div class="card my-2">
                                        <div class="card-body">
                                            <div class="form-check form-switch p-0 d-flex flex-row justify-content-between">
                                                <label class="form-check-label" for="full_time">Always available <small
                                                        class="text-muted">(24/7)</small></label>
                                                <input class="form-check-input" name="full_time" value="1" <?php echo $typeCheck = ($existingData['full_time'] == 1) ? 'checked' : ''; ?>
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="mon_start"
                                                            value="<?php echo $existingData['mon_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="mon_end" value="<?php echo $existingData['mon_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="tue_start"
                                                            value="<?php echo $existingData['tue_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="tue_end" value="<?php echo $existingData['tue_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="wed_start"
                                                            value="<?php echo $existingData['wed_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="wed_end" value="<?php echo $existingData['wed_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="thu_start"
                                                            value="<?php echo $existingData['thu_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="thu_end" value="<?php echo $existingData['thu_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="fri_start"
                                                            value="<?php echo $existingData['fri_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="fri_end" value="<?php echo $existingData['fri_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="sat_start"
                                                            value="<?php echo $existingData['sat_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="sat_end" value="<?php echo $existingData['sat_end']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="sun_start"
                                                            value="<?php echo $existingData['sun_start']; ?>">
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
                                                        <select class="form-select form-select-sm time-select"
                                                            name="sun_end" value="<?php echo $existingData['sun_end']; ?>">
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
                                    <input type="text" pattern="[0-9]*\.?[0-9]+"
                                        value="<?php echo $existingData['rate'] ?? ''; ?>" placeholder="eg: 10.0" required
                                        name="rate" class="form-control">
                                </div>
                                <div class="mt-3">
                                    <button class="btn btn-dark w-100" name="submit"> Save </button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </section>
        </main>
        <?php include_once './footer.php'; ?>
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script>
        let map;
        let marker;

        function initMap() {
            // PHP coordinates from the database (with fallback to Belgium if not set)
            const dbLat = <?php echo isset($existingData['latitude']) ? $existingData['latitude'] : 51.0447; ?>;
            const dbLng = <?php echo isset($existingData['longitude']) ? $existingData['longitude'] : -114.0719; ?>;

            const initialPosition = { lat: dbLat, lng: dbLng };

            // Create map centered on the database location
            map = new google.maps.Map(document.getElementById("map-selection"), {
                zoom: 10,
                center: initialPosition,
            });

            // Place marker at the initial position
            placeMarker(initialPosition);

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
            // If marker exists, update its position, otherwise create it
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            }
            // Update the hidden fields with the new marker position
            $("#lat").val(location.lat);
            $("#lng").val(location.lng);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwa3l7oekDnz3VVsTrsnLR17NslkgHqvA&callback=initMap"
        async defer></script>
    <script>

        $(document).ready(() => {
            $('.days-availability .row').each(function () {
                // Get both time-select elements in the same row
                let timeSiblings = $(this).find('.time-select');

                // Check if both have the value "00:00:00"
                let bothHaveDefault = true;
                timeSiblings.each(function () {
                    if ($(this).attr('value') !== "00:00:00") {
                        bothHaveDefault = false;
                        return false; // Break the loop if one is not default
                    }
                });

                console.log(bothHaveDefault);
                // Only set default values if both elements have "00:00:00"
                if (!bothHaveDefault) {
                    timeSiblings.each(function () {
                        $(this).val($(this).attr('value')); // Set to default value attribute
                    });
                }
            });
        });
    </script>
    <script src="./assets/js/script.js?v=1_1"></script>

</html>
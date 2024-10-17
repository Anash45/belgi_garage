<?php
include('db_conn.php');

// Fonction pour vérifier si l'utilisateur est un administrateur ou un propriétaire
function isAdminOrOwner($conn, $s_id) {
    $userType = checkUserType();
    if ($userType === 'Admin') {
        return true; // Autoriser l'accès si l'utilisateur est administrateur
    } elseif ($userType === 'Owner') {
        // Vérifier si l'utilisateur est le propriétaire de l'espace
        $u_id = $_SESSION['u_id'];
        $query = "SELECT u_id FROM spaces WHERE s_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $s_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $space = $result->fetch_assoc();
            return $space['u_id'] == $u_id; // Renvoie vrai si l'utilisateur est le propriétaire
        }
    }
    return false; // Non autorisé
}

// Vérifiez si l'ID d'espace est fourni
if (!isset($_REQUEST['s_id'])) {
    header('location:spaces.php');
    die();
}

// Obtenez l'ID de l'espace à partir du paramètre URL
$s_id = intval($_GET['s_id']);

// Vérifier l'autorisation de l'utilisateur
if (!isAdminOrOwner($conn, $s_id)) {
    header('location:index.php?err=1');
    die();
}

// Initialiser les variables pour les commentaires et les données existantes
$info = '';
$existingData = null;

// Vérifiez si le formulaire est soumis pour la mise à jour des données
if (isset($_POST['submit'])) {
    // Échapper aux caractères spéciaux pour empêcher l'injection SQL
    $post_code = mysqli_real_escape_string($conn, $_POST['post_code']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($conn, $_POST['longitude']);
    $type = isset($_POST['type']) ? mysqli_real_escape_string($conn, $_POST['type']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $full_time = isset($_POST['full_time']) ? 1 : 0;

    // Définir des horaires spécifiques à la journée avec validation
    $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
    $dayTimings = [];
    foreach ($days as $day) {
        $start = isset($_POST[$day . '_start']) ? $_POST[$day . '_start'] : '';
        $end = isset($_POST[$day . '_end']) ? $_POST[$day . '_end'] : '';
        $dayTimings[$day] = ['start' => $start, 'end' => $end];
    }
    $rate = mysqli_real_escape_string($conn, $_POST['rate']);

    // Valider la soumission du formulaire pour les horaires de jour ou à temps plein
    $hasTimeSlot = $full_time || array_filter($dayTimings, function ($times) {
        return !empty($times['start']) && !empty($times['end']);
    });

    if ($hasTimeSlot) {
        // Vérifiez l'espace en double en fonction du code postal, de l'adresse et du type
        $checkQuery = "SELECT * FROM spaces WHERE post_code = '$post_code' AND address = '$address' AND type = '$type' AND s_id != $s_id";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $info = "<div class='alert alert-danger'>Error: Space with the same post code, address, and type already exists.</div>";
        } else {
            // Préparer une requête de mise à jour pour l'espace existant
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

// Récupérer les données existantes pour l'ID d'espace donné
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
    <div class="gtranslate_wrapper"></div>
        <script>window.gtranslateSettings = { "default_language": "en", "languages": ["en", "fr", "nl"], "wrapper_selector": ".gtranslate_wrapper", "switcher_horizontal_position": "right", "flag_style": "3d" }</script>
        <script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
    </body>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script>
        let map;
        let marker;

        function initMap() {
            // Coordonnées PHP de la base de données (avec repli vers la Belgique si non défini)
            const dbLat = <?php echo isset($existingData['latitude']) ? $existingData['latitude'] : 51.0447; ?>;
            const dbLng = <?php echo isset($existingData['longitude']) ? $existingData['longitude'] : -114.0719; ?>;

            const initialPosition = { lat: dbLat, lng: dbLng };

            // Créer une carte centrée sur l'emplacement de la base de données
            map = new google.maps.Map(document.getElementById("map-selection"), {
                zoom: 10,
                center: initialPosition,
            });

            // Placer le marqueur à la position initiale
            placeMarker(initialPosition);

            // Ajouter un écouteur d'événement de clic à la carte
            map.addListener("click", (e) => {
                placeMarker(e.latLng);
            });

            // Configurer le bouton "Utiliser l'emplacement actuel"
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
            // Si le marqueur existe, mettez à jour sa position, sinon créez-le
            if (marker) {
                marker.setPosition(location);
            } else {
                marker = new google.maps.Marker({
                    position: location,
                    map: map,
                });
            }
            // Mettez à jour les champs masqués avec la nouvelle position du marqueur
            $("#lat").val(location.lat);
            $("#lng").val(location.lng);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCwa3l7oekDnz3VVsTrsnLR17NslkgHqvA&callback=initMap"
        async defer></script>
    <script>

        $(document).ready(() => {
            $('.days-availability .row').each(function () {
                // Obtenez les deux éléments de sélection temporelle dans la même ligne
                let timeSiblings = $(this).find('.time-select');

                // Vérifiez si les deux ont la valeur "00:00:00"
                let bothHaveDefault = true;
                timeSiblings.each(function () {
                    if ($(this).attr('value') !== "00:00:00") {
                        bothHaveDefault = false;
                        return false; // Rompre la boucle si ce n'est pas le cas par défaut
                    }
                });

                console.log(bothHaveDefault);
                // Ne définissez les valeurs par défaut que si les deux éléments ont "00:00:00"
                if (!bothHaveDefault) {
                    timeSiblings.each(function () {
                        $(this).val($(this).attr('value')); // Définir l'attribut de valeur par défaut
                    });
                }
            });
        });
    </script>
    <script src="./assets/js/script.js?v=1_1"></script>

</html>
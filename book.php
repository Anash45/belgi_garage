<?php
include('db_conn.php');
// Vérifiez si le formulaire est soumis
$info = $show = '';
if (checkUserType() != 'Driver') {
    $show = '<div class="alert alert-danger">Only drivers are allowed on this page. <a href="./login.php" class="text-primary text-decoration-underline">Login here</a></div>';
} elseif (isset($_REQUEST['s_id'])) {
    // print_r($_REQUEST);
    $s_id = mysqli_real_escape_string($conn, $_REQUEST['s_id']);


    $sql1 = "SELECT * FROM `spaces` WHERE `s_id` = '$s_id'";
    $result1 = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result1);
    $total = 0;
    if (!empty($row1)) {
        do {

            $rating = '';
            $sql3 = "SELECT AVG(rating) AS `avg_rating`, COUNT(*) AS `total_rating` FROM `ratings` WHERE `s_id` = '$s_id'";
            $result3 = mysqli_query($conn, $sql3);
            $row3 = mysqli_fetch_assoc($result3);
            $avg_rating = !empty($row3['avg_rating']) ? $row3['avg_rating'] : 0;
            $avg_rating = round($avg_rating);
            for ($i = 0; $i < 5; $i++) {
                if ($i < $avg_rating) {
                    $filled = ' filled';
                } else {
                    $filled = '';
                }
                $rating .= '<i class="fa fa-star ' . $filled . '"></i>';
            }

            $avaliability = [];
            $avaliability[] = array('full_time' => $row1['full_time']);
            $avaliability[] = array('mon' => array('mon_start' => $row1['mon_start'], 'mon_end' => $row1['mon_end']));
            $avaliability[] = array('tue' => array('tue_start' => $row1['tue_start'], 'tue_end' => $row1['tue_end']));
            $avaliability[] = array('wed' => array('wed_start' => $row1['wed_start'], 'wed_end' => $row1['wed_end']));
            $avaliability[] = array('thu' => array('thu_start' => $row1['thu_start'], 'thu_end' => $row1['thu_end']));
            $avaliability[] = array('fri' => array('fri_start' => $row1['fri_start'], 'fri_end' => $row1['fri_end']));
            $avaliability[] = array('sat' => array('sat_start' => $row1['sat_start'], 'sat_end' => $row1['sat_end']));
            $avaliability[] = array('sun' => array('sun_start' => $row1['sun_start'], 'sun_end' => $row1['sun_end']));
            $avaliability_json = json_encode($avaliability);

            $location = '';
            if ($row1['post_code']) {
                $location = $row1['type'];
            }
            $show .= '<div class="col-md-7">
                <form action="confirm_booking.php" oninput="updateBookingForm()" method="post" class="needs-validation" novalidate id="booking-form">
            <div class="card rounded-0">
                <div class="card-body">
                    <p class="fw-semibold">Booking Details</p>
                    <div class="row py-2">
                        <div class="col-6">
                            <span>Parking Type:</span>
                        </div>
                        <div class="col-6 text-end">
                            <span>' . $row1['type'] . '</span>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-4">
                            <span>Day:</span>
                        </div>
                        <div class="col-8 text-end">
                            <div class="position-relative">
                                <input name="day" id="day" class="form-control form-control-sm bg-transparent position-relative" placeholder="Select date" required>
                                <div class="cal-icon"><i class="fa fa-calendar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-4">
                            <span>Start & End Time:</span>
                        </div>
                        <div class="col-8 text-end">
                            <div class="row">
                                <div class="col-6">
                                    <select class="form-select form-select-sm" id="start_time" name="start_time" required>
                                        <option selected disabled value="">Select Start Time</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <select class="form-select form-select-sm" id="end_time" name="end_time" required>
                                        <option selected disabled value="">Select End Time</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">
                            <span>Duration:</span>
                        </div>
                        <div class="col-6 text-end">
                            <span>
                            <span class="hrs_duration"></span> hr</span>
                        </div>
                    </div>
                    <div class="row py-2">
                        <div class="col-6">
                            <span>Address:</span>
                        </div>
                        <div class="col-6 text-end">
                            <span>' . $row1['address'] . '</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <p class="fw-semibold">Payment Details</p>
                    <div class="mt-3">
                        <input type="hidden" name="s_id" value="' . $row1['s_id'] . '">
                        <input id="rate" name="rate" value="' . $row1['rate'] . '" type="hidden">
                        <input id="total" name="total" value="" type="hidden">
                        <input id="duration" name="duration" value="' . $row1['rate'] . '" type="hidden">
                        <div class="form-check py-1">
                            <input class="form-check-input p-2 me-3" type="radio" value="Cash" checked required
                                name="payment_method" id="opt1">
                            <label class="form-check-label" for="opt1"> Cash </label>
                        </div>
                        <div class="form-check py-1">
                            <input class="form-check-input p-2 me-3" disabled type="radio" value="PayPal"
                                name="payment_method" id="opt2">
                            <label class="form-check-label text-muted" for="opt2"> PayPal </label>
                        </div>
                        <p class="text-danger"><small>PayPal is not available!</small></p>
                    </div>

                    <div id="stripe-payment" class="py-4" style="display: none;">
                        <div id="card-element"><!-- Stripe Element will be inserted here --></div>
                        <div id="card-errors" role="alert" class="text-danger" style="font-size: 14px;"></div>
                    </div>

                    <div id="err_msg"></div>
                    <div class="mt-3 text-center">
                        <button type="submit" class="btn btn-dark w-100" name="confirm">Confirm Booking</button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card rounded-0 h-100">
                    <div class="card-body">
                        <div class="pb-4 border-bottom">
                            <p class="fw-semibold">' . $row1['type'] . ' at ' . $row1['post_code'] . '</p>
                            <p class="mb-2 stars">' . $rating . ' (' . $row3['total_rating'] . ') </p>
                        </div>
                        <div class="py-4 border-bottom">
                            <div class="row py-2">
                                <div class="col-6">
                                    <span>Fee per hour:</span>
                                </div>
                                <div class="col-6 text-end">
                                <span> &euro; ' . number_format($row1['rate'], 2, '.', '') . '</span>
                                </div>
                            </div>
                            <div class="row py-2">
                                <div class="col-6">
                                    <span>Duration:</span>
                                </div>
                                <div class="col-6 text-end">
                                    <span><span class="hrs_duration"></span> hr</span>
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            <div class="row py-2">
                                <div class="col-6">
                                    <span>Total:</span>
                                </div>
                                <div class="col-6 text-end">
                                    <span> &euro; <span class="total"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
        </div>
    ';



        } while ($row1 = mysqli_fetch_assoc($result1));
    } else {
        $show = '<div class="alert alert-danger">No parking spaces available!</div>';
    }
} else {
    header('location:index.php');
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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="./assets/css/style.css?v=1_1">
        <script src="https://js.stripe.com/v3/"></script>
    </head>

    <body> <?php include('header.php'); ?>
        <main>
            <section class="py-5">
                <div class="container">
                    <h3 class="fw-normal">Confirm your booking and pay</h3>
                </div>
            </section>
            <section class="py-3">
                <div class="container">
                    <div class="row"> <?php echo $show; ?> </div>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script src="./assets/js/script.js?v=1_1"></script>
    <script>
        let availabilityData = <?php echo $avaliability_json; ?>;
        console.log(JSON.stringify(availabilityData));

        // Vérifiez la valeur de `full_time`
        const full_time = availabilityData[0].full_time === "1";
        // Fonction pour vérifier si une journée a des heures de début et de fin disponibles
        function isAvailable(day) {
            const dayData = availabilityData.find(obj => obj[day]);
            console.log(dayData);
            if (dayData) {
                const { [`${day}_start`]: start, [`${day}_end`]: end } = dayData[day];
                return start !== "00:00:00" && end !== "00:00:00";
            }
            return false;
        }
        // Fonction pour obtenir les heures disponibles pour un jour donné
        function getAvailableTimes(day) {
            const dayData = availabilityData.find(obj => obj[day]);
            if (dayData) {
                const { [`${day}_start`]: start, [`${day}_end`]: end } = dayData[day];
                return { start, end };
            }
            return { start: "00:00:00", end: "00:00:00" };
        }

        // Initialiser Flatpickr avec l'activation de la date conditionnelle
        
        flatpickr("#day", {
            minDate: "today",
            maxDate: new Date().fp_incr(6), // 6 prochains jours
            dateFormat: "m/d/Y",        // Spécifiez le format de date
            enable: [
                function (date) {
                    // Si full_time est vrai, activez toutes les dates
                    if (full_time) return true;

                    console.log(full_time);
                    // Obtenez le nom du jour à partir de la date
                    const dayNames = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
                    const dayName = dayNames[date.getDay()]; // Obtenez le nom du jour à partir de la date

                    // Activer uniquement si la journée a des heures disponibles
                    return isAvailable(dayName);
                }
            ],
            onChange: function (selectedDates) {
                const selectedDate = selectedDates[0];
                const dayNames = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
                const dayName = dayNames[selectedDate.getDay()]; // Obtenez le nom du jour à partir de la date

                // Effacer les options actuelles
                const startSelect = document.getElementById('start_time');
                const endSelect = document.getElementById('end_time');
                startSelect.innerHTML = '<option value="">Select Start Time</option>';
                endSelect.innerHTML = '<option value="">Select End Time</option>';

                // Remplir les options de sélection de l'heure
                if (full_time) {
                    // Si full_time est vrai, créez des options de 00h00 à 23h00
                    for (let hour = 0; hour < 24; hour++) {
                        const timeOption = hour < 10 ? `0${hour}:00` : `${hour}:00`;

                        // Créer une option d'heure de début
                        const startOption = document.createElement("option");
                        startOption.value = timeOption;
                        startOption.textContent = timeOption;
                        startSelect.appendChild(startOption);

                        // Créer une option d'heure de fin
                        const endOption = document.createElement("option");
                        endOption.value = timeOption;
                        endOption.textContent = timeOption;
                        endSelect.appendChild(endOption);
                    }
                } else {
                    // Si full_time est faux, obtenez les heures disponibles pour le jour sélectionné
                    const { start, end } = getAvailableTimes(dayName);

                    if (start !== "00:00:00" && end !== "00:00:00") {
                        const startTime = new Date(`1970-01-01T${start}Z`);
                        const endTime = new Date(`1970-01-01T${end}Z`);

                        //  Générer des options de temps par intervalles d'une heure pour start_time
                        for (let hour = startTime.getUTCHours(); hour <= endTime.getUTCHours(); hour++) {
                            const timeOption = hour < 10 ? `0${hour}:00` : `${hour}:00`;
                            const option = document.createElement("option");
                            option.value = timeOption;
                            option.textContent = timeOption;
                            startSelect.appendChild(option);
                        }

                        // Remplir la sélection end_time
                        for (let hour = startTime.getUTCHours(); hour <= endTime.getUTCHours(); hour++) {
                            const timeOption = hour < 10 ? `0${hour}:00` : `${hour}:00`;
                            const option = document.createElement("option");
                            option.value = timeOption;
                            option.textContent = timeOption;
                            endSelect.appendChild(option);
                        }
                    }
                }
            }
        });

    </script>

</html>
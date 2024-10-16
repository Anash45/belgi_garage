<?php
include('db_conn.php');
// Check if the form is submitted
$info = $show = '';
// Check if the form is submitted
if (isset($_POST['search'])) {
    // print_r($_REQUEST);
    $day = mysqli_real_escape_string($conn, $_REQUEST['start_day']);
    $post_code = mysqli_real_escape_string($conn, $_REQUEST['post_code']);
    $start_time = date(('H:i:s'), strtotime($_REQUEST['start_time']));
    $end_time = date(('H:i:s'), strtotime($_REQUEST['end_time']));
    $start_col = strtolower($day) . "_start";
    $end_col = strtolower($day) . "_end";
    $sql1 = "SELECT * FROM `spaces` WHERE `post_code` = '$post_code' AND (`full_time` = 1 OR (TIME(`$start_col`) <= '$start_time' AND TIME(`$end_col`) >= '$end_time')) AND `status` = 1";
    $result1 = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result1);
    if (!empty($row1)) {
        $_SESSION['search']['day'] = $day;
        $_SESSION['search']['start_time'] = $start_time;
        $_SESSION['search']['end_time'] = $end_time;
        // $_SESSION['search']['post_code'] = $end_time;
        do {
            $s_id = $row1['s_id'];
            $location = 'https://www.google.com/maps?q=' . $row1['latitude'] . ',' . $row1['longitude'] . '';

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

            $show .= '<div class="col-lg-4 col-md-6 col-12 py-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="fw-normal">' . $row1['type'] . ' at ' . $row1['post_code'] . '</h4>
                    <p class="mb-2 stars">' . $rating . ' (' . $row3['total_rating'] . ')
                    </p>
                    <p class="mb-1"><a href="' . $location . '" target="_blank" class="text-primary">See location</a></p>
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="m-0">&euro; ' . number_format($row1['rate'], 2, '.', '') . '</p>
                        <a href="book.php?s_id=' . $row1['s_id'] . '" class="btn btn-dark btn-sm ms-1">Book</a>
                    </div>
                </div>
            </div>
        </div>';
        } while ($row1 = mysqli_fetch_assoc($result1));
    } else {
        $show = '<div class="alert alert-danger">No parking spaces available!</div>';
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
            <section class="py-4">
                <div class="container">
                    <form method="post" action="./search.php"
                        class="booking_form bg-light p-4 rounded-3 needs-validation shadow-sm" novalidate>
                        <?php echo $info; ?>
                        <div class="row align-items-end">
                            <div class="col-lg-4 px-2 mb-lg-0 mb-3">
                                <label class="fw-semibold">Location:</label>
                                <input type="text" name="post_code" required class="form-control form-control-sm"
                                    placeholder="Search by postcode">
                            </div>
                            <div class="col-lg-3 px-2 mb-lg-0 mb-3">
                                <label class="fw-semibold">Day:</label>
                                <div class="d-flex">
                                    <select name="start_day" required class="form-select form-select-sm me-1">
                                        <option value="" selected disabled>Select Day</option>
                                        <option value="Mon">Mon</option>
                                        <option value="Tue">Tue</option>
                                        <option value="Wed">Wed</option>
                                        <option value="Thu">Thu</option>
                                        <option value="Fri">Fri</option>
                                        <option value="Sat">Sat</option>
                                        <option value="Sun">Sun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 px-2 mb-lg-0 mb-3">
                                <label class="fw-semibold">Time:</label>
                                <div class="d-flex">
                                    <select name="start_time" required class="form-select form-select-sm">
                                        <option value="" selected disabled>Start time</option>
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
                                    <select name="end_time" required class="form-select form-select-sm ms-2">
                                        <option value="" selected disabled>End time</option>
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
                            <div class="col-lg-2 px-2 mb-lg-0 mb-3">
                                <button type="submit" class="btn btn-dark w-100 btn-sm" name="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
            <section class="py-4">
                <div class="container">
                    <div class="card bg-light rounded-3 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div id="map" style="height: 500px;" class="w-100 mt-2"></div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="py-4">
                <div class="container">
                    <div class="row">
                        <?php echo $show; ?>
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
        // Initialize the map
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 50.5039, lng: 4.4699 },
                zoom: 7
            });

            // Fetch available spaces and place markers on the map
            fetchSpaces(map);
        }

        // Fetch spaces from the server
        function fetchSpaces(map) {
            $.ajax({
                url: 'ajax/fetch_all_spaces.php',
                method: 'GET',
                dataType: 'json',
                success: function (spaces) {
                    spaces.forEach(space => {
                        const { latitude, type, longitude, description, full_time, s_id, mon_start, mon_end,
                            tue_start, tue_end, wed_start, wed_end, thu_start, thu_end,
                            fri_start, fri_end, sat_start, sat_end, sun_start, sun_end } = space;

                        const days = {
                            'mon': { start: mon_start, end: mon_end },
                            'tue': { start: tue_start, end: tue_end },
                            'wed': { start: wed_start, end: wed_end },
                            'thu': { start: thu_start, end: thu_end },
                            'fri': { start: fri_start, end: fri_end },
                            'sat': { start: sat_start, end: sat_end },
                            'sun': { start: sun_start, end: sun_end }
                        };

                        const today = new Date();
                        const dayNames = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
                        const dayName = dayNames[today.getDay()];

                        const availableTime = days[dayName];
                        let timing = '';

                        if (full_time == 1) {
                            timing = `<p class="mb-1"><b>Available 24/7</b></p>`;
                        } else {
                            console.log(days);
                            for (let day in days) {
                                if (days.hasOwnProperty(day)) {
                                    const { start, end } = days[day];
                                    if (start !== '00:00:00' && end != '00:00:00') {
                                        timing += `<p class="mb-1"><small><b>${day.toUpperCase()}:</b> ${start} - ${end}</small></p>`;
                                    }
                                }
                            }
                        }
                        const customIcon = {
                            url: './assets/img/location.png', // URL or relative path to the custom icon
                            scaledSize: new google.maps.Size(38, 38), // Adjust the size as needed
                        };

                        const marker = new google.maps.Marker({
                            position: { lat: parseFloat(latitude), lng: parseFloat(longitude) },
                            map: map,
                            title: description,
                            icon: customIcon
                        });

                        const contentString = `
                      <div class="custom-popup">
                          <h4 class="mb-3">${type}</h4>
                          <p class="mb-2">${description}</p>
                          <p class="mb-2">${timing}</p>
                          <p class="mb-2"><a href="book.php?s_id=${s_id}" class="btn btn-sm btn-warning" target="_blank">Book Now</a></p>
                      </div>`;

                        const infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });

                        marker.addListener('click', () => {
                            infowindow.open(map, marker);
                        });
                    });
                },
                error: function (error) {
                    console.error("Error fetching spaces:", error);
                }
            });
        }
    </script>

</html>
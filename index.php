<?php
include('db_conn.php');
// Vérifiez si le formulaire est soumis
$info = '';
if (isset($_REQUEST['err'])) {
    $info = '<div class="alert alert-danger">Not allowed!</div>';
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
            <section class="hero" id="hero">
                <div class="container px-lg-5">
                    <?php
                    if (checkUserType() == 'Owner') {
                        echo '
                    <h1 class="text-center text-white mb-5 fw-normal"> Rent your space </h1>
                    <div class="text-center col-lg-4 mx-auto"><a href="./rent-space.php" class="btn btn-light w-100 btn-lg">Rent your space</a></div>';
                    }else {
                        ?>
                        <h1 class="text-center text-white mb-5 fw-normal"> Find Your Parking Spot </h1>
                        <form method="post" action="./search.php" class="booking_form bg-light p-4 shadow-sm rounded-3 needs-validation" novalidate>
                            <?php echo $info; ?>
                            <div class="row align-items-end">
                                <div class="col-lg-3 px-2 mb-lg-0 mb-3">
                                    <label class="fw-semibold">Location:</label>
                                    <input type="text" name="post_code" required class="form-control form-control-sm"
                                        placeholder="Search by postcode">
                                </div>
                                <div class="col-lg-2 px-2 mb-lg-0 mb-3">
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
                                    <a href="./search.php#search-map" class="btn btn-warning w-100 btn-sm">Select from Map</a>
                                </div>
                                <div class="col-lg-2 px-2 mb-lg-0 mb-3">
                                    <button type="submit" class="btn btn-dark w-100 btn-sm" name="search">Search</button>
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                    ?>
                </div>
            </section>
            <section class="py-5 my-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class=" fw-normal">About Us</h5>
                            <h2 class="h-title fw-normal">Belgi Garage</h2>
                            <p class="mt-5 mb-3"> Belgi Garage is a convenient and innovative platform connecting
                                space owners with drivers looking for parking solutions. Our web app allows owners to
                                easily list their available parking spaces for rent, while providing drivers with a
                                simple way to discover and book those spaces. With Belgi Garage, finding and
                                renting a parking spot has never been easier. Whether you're a space owner looking to
                                earn extra income or a driver in need of a reliable parking spot, Belgi Garage
                                provides a seamless, user-friendly experience for everyone.</p>
                        </div>
                        <div class="col-md-6 ps-md-5">
                            <img src="./assets/img/mobile-phones-car.png" alt="" class="img-fluid obj-cover h-100">
                        </div>
                    </div>
                </div>
            </section>
            <section class="py-5 my-5">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <h1 class="fw-normal">A Simple Way To Book A Parking Space.</h1>
                            <div class="mt-4 services">
                                <div class="d-flex py-2">
                                    <i class="fa fa-location-arrow mt-2"></i>
                                    <div class="px-3">
                                        <h4 class="fw-normal">Choose Your Location</h4>
                                        <p>Select the most convenient parking spot near your destination from our available options.
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex py-2">
                                    <i class="fa fa-location-arrow mt-2"></i>
                                    <div class="px-3">
                                        <h4 class="fw-normal">Select Your Time Slot
                                        </h4>
                                        <p>Reserve your parking for the exact time you need, with flexible booking options.
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex py-2">
                                    <i class="fa fa-location-arrow mt-2"></i>
                                    <div class="px-3">
                                        <h4 class="fw-normal">Confirm and Reserve
                                        </h4>
                                        <p>Complete your booking to secure your parking spot, ensuring it’s ready when you arrive.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-md-0 mb-4 px-md-5">
                            <img src="./assets/img/service-img.png" alt="Service" class="img-fluid h-100 obj-cover">
                        </div>
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
    <script src="./assets/js/script.js?v=1_1"></script>

</html>
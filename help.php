<?php
include('db_conn.php');

// Initialiser les variables pour les commentaires
$response = '';
if(isset($_POST['name'])){
    $response = '<div class="alert alert-success">Your message has been sent successfully!</div>';
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
            <section class="banner">
                <div class="container">
                    <h2 class="text-white">Help</h2>
                </div>
            </section>
            <section class="py-5">
                <div class="container py-4">
                    <div class="row flex-md-row flex-column-reverse">
                        <div class="col-md-6 col-12">
                            <h1>Have A Question?<br> Reach Out To Us!</h1>
                            <div class="mt-4">
                                <div class="d-flex py-2">
                                    <i class="fa fa-envelope"></i> <a href="mailto:admin@sparepark.com"
                                        class="ps-4">admin@belgigarage.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 mb-md-0 mb-4">
                            <h3 class="mb-3 fw-light"> Send a message </h3>
                            <form action="" method="post" class="needs-validation" novalidate>
                                <?php echo $response; ?>
                                <div class="mb-3">
                                    <input name="name" required type="text" class="form-control" placeholder="Your Name">
                                </div>
                                <div class="mb-3">
                                    <input name="email" required type="email" class="form-control" placeholder="Your E-Mail">
                                </div>
                                <div class="mb-3">
                                    <textarea name="message" required class="form-control" placeholder="Your Message"
                                        rows="4"></textarea>
                                </div>
                                <div class="mt-4">
                                    <button class="btn btn-dark w-100"> Send </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="mt-5">
                        <h1 class="mb-3">FAQ</h1>
                        <div class="py-2">
                            <h4>How does Belgi Garage work?</h4>
                            <p>Belgi Garage connects space owners with drivers looking for parking. Owners can list
                                their available parking spots, and drivers can easily browse and book these spaces
                                through our platform. Payments and reservations are managed directly in the app,
                                ensuring a secure and convenient experience for both parties.</p>
                        </div>
                        <div class="py-2">
                            <h4>How do I list my parking space?</h4>
                            <p>To list your parking space, create an account on Belgi Garage, provide details about
                                your space (such as location, availability, and price), and publish your listing. Your
                                space will then be available for drivers to view and book according to your set
                                availability.</p>
                        </div>
                        <div class="py-2">
                            <h4>What types of parking spaces can I rent on Belgi Garage?</h4>
                            <p>Belgi Garage supports a variety of parking spaces, including private driveways,
                                commercial lots, and more. As long as you have the authority to rent the space, you can
                                list it for drivers to book through our app.</p>
                        </div>
                        <div class="py-2">
                            <h4>Is payment secure on Belgi Garage?</h4>
                            <p>Yes, Belgi Garage uses secure payment processing to protect your financial
                                information. Drivers pay through the app, and owners receive their earnings directly,
                                ensuring a safe and reliable transaction for everyone involved.</p>
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
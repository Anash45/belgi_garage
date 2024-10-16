<footer class="py-4 bg-dark text-white">
    <div class="container">
        <div
            class="d-flex justify-content-between flex-wrap align-items-md-center align-items-start flex-md-row flex-column">
            <img src="./assets/img/Logo-white.png" alt="Logo" height="100">
            <ul class="d-flex text-white p-0 m-0 flex-sm-row flex-column">
                <li class="px-sm-3 ps-0 py-2">
                    <a class="px-1" href="./index.php">Home</a>
                </li>
                <?php
                if (checkUserType() == 'Owner') {
                    echo '<li class="px-sm-3 ps-0 py-2">
                        <a class="px-1" href="./rent-space.php">Rent your space</a>
                    </li>';
                }else {
                    echo '<li class="px-sm-3 ps-0 py-2">
                        <a class="px-1" href="./index.php#hero">Book a space</a>
                    </li>';
                }
                ?>
                <li class="px-sm-3 ps-0 py-2">
                    <a class="px-1" href="./services.php">Services</a>
                </li>
                <li class="px-sm-3 ps-0 py-2">
                    <a class="px-1" href="./help.php">Help</a>
                </li>
            </ul>
        </div>
        <div class="d-flex pt-3 align-items-baseline justify-content-between flex-sm-row flex-column-reverse">
            <p class="text-secondary m-0 copyright">&copy; 2024 Belgi Garage. All rights reserved.</p>
        </div>
    </div>
</footer>
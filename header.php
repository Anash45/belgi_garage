<header class="shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="./assets/img/Logo-black.png" alt="Logo" height="60">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item px-3">
                        <a class="nav-link" href="./index.php">Home</a>
                    </li>
                    <?php
                    if (checkUserType() == 'Owner') {
                        echo '<li class="nav-item px-3">
                            <a class="nav-link" href="./rent-space.php">Rent your space</a>
                        </li>';
                    } else {
                        echo '<li class="nav-item px-3">
                            <a class="nav-link" href="./search.php">Book a space</a>
                        </li>';
                    }
                    ?>
                    <li class="nav-item px-3">
                        <a class="nav-link" href="./services.php">Services</a>
                    </li>
                    <li class="nav-item px-3">
                        <a class="nav-link" href="./help.php">Help</a>
                    </li>
                    <?php
                    if (checkUserType() == 'Driver' || checkUserType() == 'Owner') {
                        echo '<li class="nav-item dropdown border-start ps-3">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          ' . $_SESSION['name'] . '
                        </a>
                        
                        
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profile.php">My Profile</a></li>
                        ' ?>     <?php
                              echo $menuItem = (checkUserType() == 'Driver') ? '<li><a class="dropdown-item" href="bookings.php">My Bookings</a></li>' : '<li><a class="dropdown-item" href="spaces.php">My Spaces</a></li>';
                              echo '<li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                      </li>';
                    } else if (checkUserType() == 'Admin') {
                            echo '<li class="nav-item px-3">
                            <a class="btn btn-dark px-4" href="./admin/index.php">Admin Panel</a>
                        </li>';
                        } else {
                            echo '<li class="nav-item px-3">
                        <a href="login.php" class="btn btn-dark px-4">Login</a>
                    </li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
</header>
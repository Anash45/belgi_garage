<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">
                    <li class="nav-divider"> Menu </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page=='home') ? 'active' : ''; ?>" href="index.php"><i
                                class="fa fa-fw fa-user-circle"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page=='spaces') ? 'active' : ''; ?>" href="ad-spaces.php"><i
                                class="fa fa-fw fa-road"></i>Spaces</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page=='bookings') ? 'active' : ''; ?>" href="ad-bookings.php"><i
                                class="fa fa-fw fa-check"></i>Bookings</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
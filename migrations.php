<?php

require './db_conn.php';

$sql = "ALTER TABLE bookings
ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ALTER TABLE `bookings` CHANGE `date` `date` DATE NOT NULL
";

mysqli_query($conn,$sql);
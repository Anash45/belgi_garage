<?php
session_start();
// $conn = mysqli_connect('localhost', 'root', 'root', 'belgi_garage');
$conn = mysqli_connect('localhost', 'u956940883_belgi_garage', '$VTE5F4s', 'u956940883_belgi_garage');
if (!$conn) {
    die('Cannot connect to the database!');
}

function checkUserType()
{

    if (isset($_SESSION['type'])) {
        $userType = $_SESSION['type'];

        switch ($userType) {
            case 'Owner':
                return 'Owner';
            case 'Driver':
                return 'Driver';
            case 'admin':
                return 'Admin';
            default:
                return 'Unknown';
        }
    } else {
        return 'NotLoggedIn';
    }
}
?>
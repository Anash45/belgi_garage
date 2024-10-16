<?php
require '../db_conn.php';

// Fetch available spaces where status = 1
$sql = "SELECT s_id, u_id, post_code, address, type, description, latitude, longitude, full_time, 
        mon_start, mon_end, tue_start, tue_end, wed_start, wed_end, thu_start, thu_end, 
        fri_start, fri_end, sat_start, sat_end, sun_start, sun_end, rate, status 
        FROM spaces WHERE status = 1";
        
$result = $conn->query($sql);

$spaces = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $spaces[] = $row;
    }
}

echo json_encode($spaces);
$conn->close();
?>
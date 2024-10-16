<?php
function getEarningsBySpaceId($conn, $s_id) {
    // Sanitize s_id to ensure it's an integer
    $s_id = intval($s_id); // This converts the variable to an integer

    // SQL query to sum up the total earnings for the given space ID (s_id)
    $sql = "SELECT SUM(total) AS total_earnings FROM bookings WHERE s_id = $s_id AND `status` != 0";
    
    // Execute the query
    $result = $conn->query($sql);

    // Check if the query was successful
    if ($result) {
        $row = $result->fetch_assoc();
        $totalEarnings = $row['total_earnings'];
    } else {
        // Handle query failure (optional)
        echo "Query Error: " . $conn->error;
        return 0; // Return 0 on error
    }


    // If there are no earnings, default to zero
    return $totalEarnings ? $totalEarnings : 0;
}

function getUserById($conn, $userId) {
    // Prepare the SQL statement to fetch the user by ID
    $sql = "SELECT * FROM users WHERE u_id = ?"; // Change 'users' and 'id' to your actual table and column names
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        // Handle error if prepare fails
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind the user ID to the statement
    $stmt->bind_param("i", $userId); // 'i' indicates that the parameter is an integer
    
    // Execute the statement
    $stmt->execute();
    
    // Bind the result to variables
    $result = $stmt->get_result();
    
    // Fetch the user data
    if ($row = $result->fetch_assoc()) {
        // Return the user data as an associative array
        return $row;
    } else {
        // If no user found, return null or handle accordingly
        return null;
    }
}
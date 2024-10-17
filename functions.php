<?php
function getEarningsBySpaceId($conn, $s_id) {
    // Désinfectez s_id pour vous assurer qu'il s'agit d'un nombre entier
    $s_id = intval($s_id); // Cela convertit la variable en entier

    // Requête SQL pour résumer les gains totaux pour l'ID d'espace donné (s_id)
    $sql = "SELECT SUM(total) AS total_earnings FROM bookings WHERE s_id = $s_id AND `status` != 0";
    
    // Exécuter la requête
    $result = $conn->query($sql);

    // Vérifiez si la requête a réussi
    if ($result) {
        $row = $result->fetch_assoc();
        $totalEarnings = $row['total_earnings'];
    } else {
        // Gérer les échecs de requête (facultatif)
        echo "Query Error: " . $conn->error;
        return 0; // Renvoie 0 en cas d'erreur
    }


    // S'il n'y a pas de gains, par défaut à zéro
    return $totalEarnings ? $totalEarnings : 0;
}

function getUserById($conn, $userId) {
    // Préparez l'instruction SQL pour récupérer l'utilisateur par ID
    $sql = "SELECT * FROM users WHERE u_id = ?"; // Remplacez « utilisateurs » et « id » par les noms réels de vos tables et colonnes.
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        // Gérer l'erreur si la préparation échoue
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Lier l'ID utilisateur à l'instruction
    $stmt->bind_param("i", $userId); // 'i' indique que le paramètre est un entier
    
    // Exécuter l'instruction
    $stmt->execute();
    
    // Lier le résultat à des variables
    $result = $stmt->get_result();
    
    // Récupérer les données utilisateur
    if ($row = $result->fetch_assoc()) {
        // Renvoie les données utilisateur sous forme de tableau associatif
        return $row;
    } else {
        // If no user found, return null or handle accordingly
        return null;
    }
}
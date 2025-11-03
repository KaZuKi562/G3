<?php
include "db_connect.php"; // include database connection

// Check if ID is provided
if (isset($_GET['user_id'])) {
    $id = $_GET['user_id'];

    // Delete query
    $sql = "UPDATE account 
            SET user_address = '', user_number = '' 
            WHERE user_id = $id";
    
 if ($conn->query($sql) === TRUE) {
        // Redirect back to index after delete
        header("Location: address.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
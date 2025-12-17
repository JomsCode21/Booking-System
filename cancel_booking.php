<?php
session_start();
require 'connection/db_con.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_GET['ref'])) {
    $ref_number = $_GET['ref'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE bookings SET status = 'Cancelled' WHERE ref_number = ? AND user_id = ? AND status != 'Cancelled'");
    $stmt->bind_param("si", $ref_number, $user_id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['flash_message'] = "Booking #$ref_number has been successfully cancelled.";
        $_SESSION['flash_type'] = "success";
    } else {
        $_SESSION['flash_message'] = "Unable to cancel booking. It may utilize invalid reference or is already cancelled.";
        $_SESSION['flash_type'] = "error";
    }
    
    $stmt->close();
}

header("Location: user_profile.php");
exit();
?>
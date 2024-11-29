<?php
session_start();
header('Content-Type: application/json');
include("config.php");  // Připojení k databázi

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$userId = $_SESSION['id'];

// Dotaz pro získání počtu nepřečtených notifikací
$query_count = "
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE user_id = '$userId' AND is_read = 0";  // is_read = 0 znamená nepřečtené
$result_count = mysqli_query($con, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$unreadCount = $row_count['unread_count'];

// Vrátíme odpověď ve formátu JSON
echo json_encode(['success' => true, 'unreadCount' => $unreadCount]);
?>

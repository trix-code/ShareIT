<?php
session_start();
header('Content-Type: application/json');
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$userId = $_SESSION['id'];

// Opravený dotaz: zajistí, že jsou načteny pouze notifikace pro aktuálního uživatele (user_id)
$query = "
    SELECT 
        n.*, 
        u.username AS sender_name, 
        s.service_name AS subscription_name 
    FROM 
        notifications n
    LEFT JOIN 
        users u ON n.sender_id = u.id
    LEFT JOIN 
        subscriptions s ON n.subscription_id = s.id
    WHERE 
        n.user_id = '$userId'
    ORDER BY 
        n.created_at DESC";

$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
    echo json_encode(['success' => true, 'notifications' => $notifications]);
} else {
    echo json_encode(['success' => false, 'notifications' => []]);
}
?>

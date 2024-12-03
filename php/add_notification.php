<?php
session_start();
header('Content-Type: application/json');
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['subscription_id'], $data['subscription_name'], $data['recipient_id'])) {
    echo json_encode(['success' => false, 'error' => 'Chybí potřebné údaje.']);
    exit();
}

$senderId = $_SESSION['id'];
$recipientId = $data['recipient_id'];
$subscriptionId = $data['subscription_id'];
$subscriptionName = $data['subscription_name'];

// Kontrola, zda již existuje podobná notifikace
$checkQuery = "SELECT * FROM notifications WHERE user_id = '$recipientId' AND sender_id = '$senderId' AND subscription_id = '$subscriptionId'";
$result = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($result) > 0) {
    echo json_encode(['success' => false, 'error' => 'Žádost jiý byla odeslána.']);
    exit();
}

// Vložení notifikace do databáze
$query = "INSERT INTO notifications (user_id, sender_id, subscription_id, message) 
          VALUES ('$recipientId', '$senderId', '$subscriptionId', 'má zájem')";

if (mysqli_query($con, $query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
}
?>

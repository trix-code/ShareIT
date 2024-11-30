<?php
session_start();
header('Content-Type: application/json');
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$userId = $_SESSION['id'];

// Retrieve the notification data from the frontend
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = $data['id'];
$action = $data['action']; // 'confirm' or 'reject'

if ($action === 'confirm') {
    // Fetch subscription ID associated with the notification
    $query = "SELECT subscription_id FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();
    $stmt->bind_result($subscriptionId);
    $stmt->fetch();
    $stmt->close();

    if (!$subscriptionId) {
        echo json_encode(['success' => false, 'error' => 'Předplatné nenalezeno.']);
        exit();
    }

    // Deduct one slot from the subscription
    $updateSubscriptionQuery = "
        UPDATE subscriptions
        SET slots_available = GREATEST(slots_available - 1, 0)
        WHERE id = ? AND slots_available > 0
    ";
    $stmt = $con->prepare($updateSubscriptionQuery);
    $stmt->bind_param("i", $subscriptionId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Check if slots are now zero and delete the subscription if necessary
        $checkSlotsQuery = "SELECT slots_available FROM subscriptions WHERE id = ?";
        $stmt = $con->prepare($checkSlotsQuery);
        $stmt->bind_param("i", $subscriptionId);
        $stmt->execute();
        $stmt->bind_result($slotsAvailable);
        $stmt->fetch();
        $stmt->close();

        if ($slotsAvailable === 0) {
            $deleteSubscriptionQuery = "DELETE FROM subscriptions WHERE id = ?";
            $stmt = $con->prepare($deleteSubscriptionQuery);
            $stmt->bind_param("i", $subscriptionId);
            $stmt->execute();
            $stmt->close();
        }

        // Delete the notification
        $deleteNotificationQuery = "DELETE FROM notifications WHERE id = ?";
        $stmt = $con->prepare($deleteNotificationQuery);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Předplatné bylo úspěšně potvrzeno.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Nelze potvrdit. Možná již nejsou žádná dostupná místa.']);
    }
} elseif ($action === 'reject') {
    // Reject the notification (delete it)
    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Notifikace byla úspěšně odmítnuta.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Chyba při odmítnutí notifikace.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Neplatná akce.']);
}

?>

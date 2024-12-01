<?php
session_start();
header('Content-Type: application/json');
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$userId = $_SESSION['id'];

// Získání dat z požadavku
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = $data['id'];
$action = $data['action']; // 'confirm', 'reject', nebo 'delete'

// Funkce pro zasílání odpovědí
function sendResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit();
}

// Potvrzení žádosti
if ($action === 'confirm') {
    // Získání ID předplatného
    $query = "SELECT subscription_id, sender_id FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();
    $stmt->bind_result($subscriptionId, $senderId);
    $stmt->fetch();
    $stmt->close();

    if (!$subscriptionId) {
        sendResponse(false, 'Předplatné nenalezeno.');
    }

    // Snížení počtu volných míst o 1
    $updateSubscriptionQuery = "UPDATE subscriptions SET slots_available = GREATEST(slots_available - 1, 0) WHERE id = ? AND slots_available > 0";
    $stmt = $con->prepare($updateSubscriptionQuery);
    $stmt->bind_param("i", $subscriptionId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Smazání notifikace po potvrzení
        $deleteNotificationQuery = "DELETE FROM notifications WHERE id = ?";
        $stmt = $con->prepare($deleteNotificationQuery);
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $stmt->close();

        // Odeslání nové notifikace žadateli
        $message = "Uživatel přijal vaši žádost o předplatné.";
        $insertNotificationQuery = "INSERT INTO notifications (user_id, sender_id, subscription_id, message) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($insertNotificationQuery);
        $stmt->bind_param("iiis", $senderId, $userId, $subscriptionId, $message);
        $stmt->execute();
        $stmt->close();

        sendResponse(true, 'Předplatné bylo úspěšně potvrzeno.');
    } else {
        sendResponse(false, 'Nelze potvrdit žádost. Možná již nejsou žádná volná místa.');
    }
}

// Odmítnutí žádosti
if ($action === 'reject') {
    // Získání ID žadatele
    $query = "SELECT sender_id, subscription_id FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();
    $stmt->bind_result($senderId, $subscriptionId);
    $stmt->fetch();
    $stmt->close();

    if (!$senderId) {
        sendResponse(false, 'Žadatel nenalezen.');
    }

    // Smazání notifikace po odmítnutí
    $deleteNotificationQuery = "DELETE FROM notifications WHERE id = ?";
    $stmt = $con->prepare($deleteNotificationQuery);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();

    // Odeslání nové notifikace žadateli
    $message = "Uživatel odmítnul vaši žádost o předplatné.";
    $insertNotificationQuery = "INSERT INTO notifications (user_id, sender_id, subscription_id, message) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($insertNotificationQuery);
    $stmt->bind_param("iiis", $senderId, $userId, $subscriptionId, $message);
    $stmt->execute();
    $stmt->close();

    sendResponse(true, 'Žádost byla odmítnuta.');
}

// Smazání notifikace pomocí tlačítka OK
if ($action === 'delete') {
    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        sendResponse(true, 'Notifikace byla úspěšně odstraněna.');
    } else {
        sendResponse(false, 'Chyba při mazání notifikace.');
    }
}

// Neplatná akce
sendResponse(false, 'Neplatná akce.');
?>

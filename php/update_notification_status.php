<?php
session_start();
header('Content-Type: application/json');
include("config.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Uživatel není přihlášen.']);
    exit();
}

$userId = $_SESSION['id'];

// Získání ID notifikace a akce
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = $data['id'];
$action = $data['action'];  // 'confirm' nebo 'reject'

// Pokud akce je potvrzení, označíme notifikaci jako přečtenou
if ($action === 'confirm') {
    // Aktualizace stavu na přečteno (is_read = 1)
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId); // Parametry pro SQL dotaz
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Notifikace byla označena jako přečtená.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Chyba při aktualizaci notifikace.']);
    }

    $stmt->close();
} elseif ($action === 'reject') {
    // Pokud je akce 'reject', smažeme notifikaci
    $query = "DELETE FROM notifications WHERE id = ? AND user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $notificationId, $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Notifikace byla úspěšně odstraněna.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Chyba při mazání notifikace.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Neplatná akce.']);
}
?>

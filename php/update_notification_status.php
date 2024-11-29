<?php
// Zapnutí chybového výpisu pro ladění
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Načtení konfigurace databáze
include("../php/config.php");

// Kontrola připojení k databázi
if (!$con) {
    echo json_encode(["success" => false, "error" => "Chyba připojení k databázi."]);
    exit();
}

// Povolení pouze POST požadavků
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Pouze POST požadavky jsou povoleny."]);
    exit();
}

// Načtení dat z požadavku
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'], $data['action'])) {
    echo json_encode(["success" => false, "error" => "Chybí parametry: 'id' nebo 'action'."]);
    exit();
}

$notificationId = intval($data['id']);
$action = $data['action'];

try {
    if ($action === 'confirm') {
        // Aktualizace stavu na přečteno
        $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            throw new Exception("Chyba při přípravě SQL dotazu: " . $con->error);
        }

        $stmt->bind_param("i", $notificationId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Notifikace byla potvrzena."]);
        } else {
            echo json_encode(["success" => false, "error" => "Notifikace nebyla nalezena."]);
        }

        $stmt->close();
    } elseif ($action === 'reject') {
        // Odmítnutí (mazání) notifikace
        $query = "DELETE FROM notifications WHERE id = ?";
        $stmt = $con->prepare($query);

        if (!$stmt) {
            throw new Exception("Chyba při přípravě SQL dotazu: " . $con->error);
        }

        $stmt->bind_param("i", $notificationId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true, "message" => "Notifikace byla odmítnuta a smazána."]);
        } else {
            echo json_encode(["success" => false, "error" => "Notifikace nebyla nalezena."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Neplatná akce."]);
    }
} catch (Exception $e) {
    // Zpracování serverové chyby
    error_log("Chyba: " . $e->getMessage());
    echo json_encode(["success" => false, "error" => "Serverová chyba."]);
}

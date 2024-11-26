<?php
session_start();
include("php/config.php");

$conn = mysqli_connect("localhost", "root", "", "login");

if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

// Načtení vstupu z JSON požadavku
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_GET['action'];

if ($action === 'load') {
    // Načítání předplatných pro přihlášeného uživatele
    $userId = $_SESSION['id'];
    $result = mysqli_query($conn, "SELECT * FROM spravce_predplatneho WHERE user_id='$userId'");
    $subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($subscriptions);

} elseif ($action === 'add') {
    // Přidání nového předplatného
    $userId = $_SESSION['id'];
    $name = $input['name'];
    $price = $input['price'];
    $frequency = $input['frequency'];
    $nextPayment = $input['nextPayment'];
    $category = $input['category'];

    $stmt = $conn->prepare("INSERT INTO spravce_predplatneho (user_id, name, price, frequency, next_payment, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdsss", $userId, $name, $price, $frequency, $nextPayment, $category);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

} elseif ($action === 'update') {
    // Úprava existujícího předplatného
    $id = $input['id'];
    $name = $input['name'];
    $price = $input['price'];
    $frequency = $input['frequency'];
    $nextPayment = $input['nextPayment'];
    $category = $input['category'];

    $stmt = $conn->prepare("UPDATE spravce_predplatneho SET name=?, price=?, frequency=?, next_payment=?, category=? WHERE id=?");
    $stmt->bind_param("sdsssi", $name, $price, $frequency, $nextPayment, $category, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

} elseif ($action === 'delete') {
    // Smazání předplatného
    $id = $input['id'];
    $stmt = $conn->prepare("DELETE FROM spravce_predplatneho WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

} elseif ($action === 'loadSubscriptionsForFinance') {
    // Načítání pro finance
    $userId = $_SESSION['id'];
    $result = mysqli_query($conn, "SELECT * FROM spravce_predplatneho WHERE user_id='$userId'");
    $subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($subscriptions);
}

mysqli_close($conn);
?>

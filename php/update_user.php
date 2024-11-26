<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    die(json_encode(['success' => false, 'message' => 'Uživatel není přihlášen.']));
}

$userId = $_SESSION['id'];
$type = $_POST['type'];
$newValue = $_POST['newValue'] ?? '';
$currentPassword = $_POST['currentPassword'] ?? '';

// Ověření typu úpravy
if (!in_array($type, ['name', 'email', 'password', 'age'])) {
    die(json_encode(['success' => false, 'message' => 'Neplatný typ úpravy.']));
}

// Získání aktuálního hesla
$query = "SELECT password FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    die(json_encode(['success' => false, 'message' => 'Chyba při získávání údajů.']));
}

// Ověření hesla při změně emailu nebo hesla
if (in_array($type, ['email', 'password']) && !password_verify($currentPassword, $userData['password'])) {
    echo json_encode(['success' => false, 'message' => 'Špatné heslo.']);
    exit;
}

// Aktualizace podle typu
switch ($type) {
    case 'name':
        $updateQuery = "UPDATE users SET username = '$newValue' WHERE id = '$userId'";
        break;
    case 'email':
        $updateQuery = "UPDATE users SET email = '$newValue' WHERE id = '$userId'";
        break;
    case 'password':
        $hashedPassword = password_hash($newValue, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE id = '$userId'";
        break;
    case 'age':
        $updateQuery = "UPDATE users SET age = '$newValue' WHERE id = '$userId'";
        break;
    default:
        die(json_encode(['success' => false, 'message' => 'Neplatný typ úpravy.']));
}

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true, 'message' => 'Údaje byly úspěšně aktualizovány.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Chyba při aktualizaci údajů: ' . mysqli_error($con)]);
}
?>

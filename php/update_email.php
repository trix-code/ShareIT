<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    die(json_encode(['success' => false, 'message' => 'Uživatel není přihlášen.']));
}

$userId = $_SESSION['id'];
$newEmail = $_POST['newEmail'];
$currentPassword = $_POST['currentPassword'];

// Získání aktuálního hesla z databáze
$query = "SELECT password FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);
$userData = mysqli_fetch_assoc($result);

// Ověření hesla
if (password_verify($currentPassword, $userData['password'])) {
    // Aktualizace emailu
    $updateQuery = "UPDATE users SET email = '$newEmail' WHERE id = '$userId'";
    if (mysqli_query($con, $updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Email byl úspěšně změněn.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Chyba při aktualizaci emailu.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Neplatné heslo.']);
}
?>

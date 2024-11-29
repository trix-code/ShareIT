<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    die(json_encode(['success' => false, 'message' => 'Uživatel není přihlášen.']));
}

$userId = $_SESSION['id'];
$newEmail = mysqli_real_escape_string($con, $_POST['newEmail']); // Ochrana proti SQL injection
$currentPassword = $_POST['currentPassword'];

// Kontrola, zda nový email již existuje v databázi
$emailCheckQuery = "SELECT id FROM users WHERE email = '$newEmail'";
$emailCheckResult = mysqli_query($con, $emailCheckQuery);

if (mysqli_num_rows($emailCheckResult) > 0) {
    // Pokud email existuje a nepatří přihlášenému uživateli, zobrazení chyby
    $existingUser = mysqli_fetch_assoc($emailCheckResult);
    if ($existingUser['id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Tento email již používá jiný uživatel.']);
        exit;
    }
}

// Získání aktuálního hesla uživatele z databáze
$query = "SELECT password FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);
$userData = mysqli_fetch_assoc($result);

if ($userData && password_verify($currentPassword, $userData['password'])) {
    // Pokud heslo sedí, aktualizace emailu
    $updateQuery = "UPDATE users SET email = '$newEmail' WHERE id = '$userId'";
    if (mysqli_query($con, $updateQuery)) {
        echo json_encode(['success' => true, 'message' => 'Email byl úspěšně změněn.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Chyba při aktualizaci emailu.']);
    }
} else {
    // Pokud heslo nesedí
    echo json_encode(['success' => false, 'message' => 'Nesprávné heslo.']);
}
?>

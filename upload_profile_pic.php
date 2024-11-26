<?php
session_start();
include("php/config.php");

if (!$con) {
    die("Chyba při připojení k databázi.");
}

if (!isset($_SESSION['id'])) {
    die("Chyba: Uživatel není přihlášen.");
}

$userId = $_SESSION['id'];

// Zkontroluj, zda byl soubor nahrán
if (empty($_FILES['profilePic']['name'])) {
    header("Location: user.php?error=Nebyl vybrán žádný soubor.");
    exit();
}

// Nastavení pro zpracování souboru
$targetDir = "img/profiles/";
$filename = uniqid() . "_" . basename($_FILES['profilePic']['name']);
$targetFile = $targetDir . $filename;


$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

// Ověření typu souboru
$fileType = mime_content_type($_FILES['profilePic']['tmp_name']);
if (!in_array($fileType, $allowedTypes)) {
    header("Location: user.php?error=Soubor musí být ve formátu JPG, PNG nebo JPEG.");
    exit();
}


if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFile)) {
    // Aktualizace databáze s novou profilovou fotkou
    $query = "UPDATE users SET profile_pic = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $filename, $userId);

    if ($stmt->execute()) {
        header("Location: user.php?success=Profilová fotka byla úspěšně změněna.");
        exit();
    } else {
        header("Location: user.php?error=Nepodařilo se aktualizovat profilovou fotku v databázi.");
        exit();
    }
} else {
    header("Location: user.php?error=Chyba při nahrávání souboru. Zkontrolujte oprávnění složky nebo zkuste jiný soubor.");
    exit();
}

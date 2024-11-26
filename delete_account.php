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

$deleteQuery = "DELETE FROM users WHERE id = '$userId'";
$deleteSubscriptionsQuery = "DELETE FROM subscriptions WHERE user_id = '$userId'";

if (mysqli_query($con, $deleteSubscriptionsQuery) && mysqli_query($con, $deleteQuery)) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
} else {
    die("Chyba: Účet se nepodařilo smazat.");
}
?>

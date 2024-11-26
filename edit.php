<?php
session_start();
include("php/config.php");

// Ověření připojení k databázi
if (!$con) {
    die("Chyba při připojení k databázi.");
}

// Ověření přihlášení uživatele
if (!isset($_SESSION['id'])) {
    die("Chyba: Uživatel není přihlášen.");
}

$userId = $_SESSION['id'];
$type = $_GET['type'] ?? '';

// Ověření platného typu úpravy
if (!in_array($type, ['name', 'email', 'password', 'age'])) {
    die("Neplatný typ úpravy.");
}

// Získání uživatelských dat
$query = "SELECT username, email, age FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);
$userData = mysqli_fetch_assoc($result);

if (!$userData) {
    die("Chyba: Uživatelská data nebyla nalezena.");
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uživatelský účet</title>
    <script src="https://kit.fontawesome.com/f8e1a90484.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
<body>
    <div id="loadingScreen" class="loading-screen">
        <div class="loader"></div>
    </div>
    <nav>
        <div class="logo">
            <p><a href="home.php"><b>ShareIT</b></a></p>
        </div>
        <ul id="menuList">
            <li><a href="spravce_predplatneho.php">Správce Předplatných</a></li>
            <li><a href="finance.html">Finance</a></li>
            <li><a href="contact.php">Kontakt</a></li>
            <li><a href="user.php"><img src="img/user.png" height="40px"></a></li>
        </ul>
            <div class="menu-icon" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
    </nav>

    <div class="edit-container">
        <div id="successMessage" class="message success" style="display: none;">Úspěšná změna údajů.</div>
        <div id="errorMessage" class="message error" style="display: none;">Chyba při aktualizaci nebo špatné heslo.</div>

        <!-- Formuláře pro úpravu údajů -->
        <?php if ($type == 'name'): ?>
            <h2>Upravit Jméno</h2>
            <form id="updateForm">
                <input type="hidden" name="type" value="name">
                <input type="text" name="newValue" placeholder="Nové jméno" required>
                <button type="submit">Potvrdit</button>
                <a href="user.php" class="cancel-btn">Zrušit</a>
            </form>
        <?php elseif ($type == 'email'): ?>
            <h2>Upravit Email</h2>
            <form id="updateForm">
                <input type="hidden" name="type" value="email">
                <input type="email" name="newValue" placeholder="Nový email" required>
                <input type="password" name="currentPassword" placeholder="Zadejte aktuální heslo" required>
                <button type="submit">Potvrdit</button>
                <a href="user.php" class="cancel-btn">Zrušit</a>
            </form>
        <?php elseif ($type == 'password'): ?>
            <h2>Upravit Heslo</h2>
            <form id="updateForm">
                <input type="hidden" name="type" value="password">
                <input type="password" name="currentPassword" placeholder="Zadejte aktuální heslo" required>
                <input type="password" name="newValue" placeholder="Nové heslo" required>
                <button type="submit">Potvrdit</button>
                <a href="user.php" class="cancel-btn">Zrušit</a>
            </form>
        <?php elseif ($type == 'age'): ?>
            <h2>Upravit Věk</h2>
            <form id="updateForm">
                <input type="hidden" name="type" value="age">
                <input type="number" name="newValue" placeholder="Nový věk" required>
                <button type="submit">Potvrdit</button>
                <a href="user.php" class="cancel-btn">Zrušit</a>
            </form>
        <?php endif; ?>
    </div>

    <script src="js/edit.js"></script>
    <script src="js/navbar.js"></script>
    <script src="js/loading.js"></script>
</body>
</html>

<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['id'])) {
    die("Chyba: Uživatel není přihlášen.");
}

// Načtení uživatelského emailu z databáze
$userId = $_SESSION['id'];
$query = "SELECT email FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if (!$userData) {
    die("Chyba: Uživatelská data nebyla nalezena.");
}

$userEmail = $userData['email'];

// Zpracování formuláře
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $topic = $_POST['topic'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validace polí
    if (empty($firstName) || empty($lastName) || empty($email) || empty($topic) || empty($message)) {
        $errorMessage = "Všechna pole jsou povinná.";
    } elseif ($email !== $userEmail) {
        $errorMessage = "Email musí odpovídat vašemu účtu.";
    } else {
        // Bezpečné vložení dat
        $sql = "INSERT INTO contact_form (first_name, last_name, email, topic, message) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssss", $firstName, $lastName, $email, $topic, $message);

        if ($stmt->execute()) {
            $successMessage = "Formulář byl úspěšně odeslán!";
        } else {
            $errorMessage = "Chyba při odesílání formuláře: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Kontakt</title>
</head>
<body>
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

    <div class="contact-container">
        <h2>Ozvěte se nám!</h2>
        <?php if (isset($successMessage)): ?>
            <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php elseif (isset($errorMessage)): ?>
            <div class="error"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form id="contactForm" method="POST">
            <div class="input-group">
                <div class="input-box">
                    <label for="firstName">Jméno:</label>
                    <input type="text" id="firstName" name="firstName" placeholder="Tomas" required>
                </div>
                <div class="input-box">
                    <label for="lastName">Příjmení:</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Kotik" required>
                </div>
            </div>
            <div class="input-box">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
            </div>
            <div class="input-box">
                <label for="topic">Téma:</label>
                <select id="topic" name="topic" required>
                    <option value="Změna hesla">Změna hesla</option>
                    <option value="Ostatní">Ostatní</option>
                </select>
            </div>
            <div class="input-box">
                <label for="message">Zpráva:</label>
                <textarea id="message" name="message" placeholder="Váš problém..." required></textarea>
            </div>
            <button type="submit" class="contact-btn">Odeslat</button>
        </form>
    </div>


    <script src="js/navbar.js"></script>

</body>
</html>

<?php
// Spuštění session
session_start();
include("php/config.php");

// Připojení k databázi
$conn = mysqli_connect("localhost", "root", "", "login");
if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

// Načtení ID předplatného z URL
$subscriptionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($subscriptionId === 0) {
    echo "Chybějící nebo neplatné ID předplatného.";
    exit();
}

// Načtení detailů předplatného z databáze
$sql = "SELECT * FROM subscriptions WHERE id = '$subscriptionId'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "Předplatné nenalezeno.";
    exit();
}

// Pokud data existují, načti je do proměnné $subscription
$subscription = mysqli_fetch_assoc($result);
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
    <title>Detaily</title>
</head>

<style>
    .container {
        margin-top: 40px;
    }

    .subscription-img {
        height: 200px;
        border-radius: 10px;
        display: block;
        margin: 0 auto 20px;
    }

    .subscription-card {
        text-align: center;
    }

    .subscription-card h4{
        font-size : 3vw;
    }
    .subscription-card p{
        font-size : 1.4vw;
    }

    .subscription-card{
        width: 500px;
    }

    .send-email-container {
    text-align: center;
    margin-top: 20px;
}

.btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-primary:hover {
    background-color: #0056b3;
}

    
</style>

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

    <div class="container">

        <div class="subscription-card">
            <?php
            // Zobrazení ikony podle názvu služby
            if ($subscription['service_name'] === 'Netflix') {
                echo '<img src="img/Netflix.png" alt="Netflix" class="subscription-img">';
            } elseif ($subscription['service_name'] === 'Spotify') {
                echo '<img src="img/Spotify.png" alt="Spotify" class="subscription-img">';
            } elseif ($subscription['service_name'] === 'HBO Max') {
                echo '<img src="img/HBO Max.png" alt="HBO Max" class="subscription-img">';
            } elseif ($subscription['service_name'] === 'Apple Music') {
                echo '<img src="img/Apple Music.png" alt="Apple Music" class="subscription-img">';
            } elseif ($subscription['service_name'] === 'Voyo') {
                echo '<img src="img/Voyo.png" alt="Voyo" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Apple TV') {
                echo '<img src="img/AppleTV.png" alt="AppleTV" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Amazon Prime Video') {
                echo '<img src="img/Prime.png" alt="Prime" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Disney') {
                echo '<img src="img/Disney.png" alt="Disney" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Xbox') {
                echo '<img src="img/Xbox.png" alt="Xbox" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Youtube') {
                echo '<img src="img/Youtube.png" alt="Youtube" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Dropbox') {
                echo '<img src="img/Dropbox.png" alt="Dropbox" class="subscription-img">';
            }elseif ($subscription['service_name'] === 'Microsoft') {
                echo '<img src="img/Microsoft.png" alt="Microsoft" class="subscription-img">';
            }
            
            
            ?>

            <h4><?php echo htmlspecialchars($subscription['service_name']) . ' - ' . htmlspecialchars($subscription['plan']); ?></h4>
            <p><b>Kategorie:</b> <?php echo htmlspecialchars($subscription['category']); ?></p>
            <p><b>Cena:</b> <?php echo htmlspecialchars($subscription['price']); ?> Kč</p>
            <p><b>Volná místa:</b> <?php echo htmlspecialchars($subscription['slots_available']); ?></p>
            <p><b>Nabízí:</b> @<?php echo htmlspecialchars($subscription['contact_info']); ?></p>
            <p><b>Další informace:</b> <?php echo htmlspecialchars($subscription['additional_info']); ?></p>

            <a href="home.php" class="btn btn-info">Zpět na hlavní stránku</a>
            <div class="send-email-container">
                <?php if (isset($subscription['user_id']) && isset($subscription['service_name'])): ?>
                    <button class="btn-primary" onclick="sendInterest(
                        <?php echo $subscriptionId; ?>,
                        '<?php echo addslashes($subscription['service_name']); ?>',
                        <?php echo $subscription['user_id']; ?>
                    )">Mám zájem</button>
                <?php else: ?>
                    <p style="color: red;">Chybí data pro odeslání zájmu.</p>
                <?php endif; ?>
            </div>
        </div>
</div>
        <script src="js/home.js"></script>
        <script src="https://kit.fontawesome.com/f8e1a90484.js" crossorigin="anonymous"></script>
        <script src="js/navbar.js"></script>
        <script src="js/notification.js"></script>
    </div>
</body>
</html>

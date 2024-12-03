<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
}

$conn = mysqli_connect("localhost", "root", "", "login");

if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

$checkSlotsQuery = "SELECT id, slots_available FROM subscriptions WHERE slots_available = 0";
$checkSlotsResult = mysqli_query($conn, $checkSlotsQuery);

while ($row = mysqli_fetch_assoc($checkSlotsResult)) {
    $subscriptionId = $row['id'];
    // Odstranění předplatného, pokud má slots_available = 0
    $deleteSubscriptionQuery = "DELETE FROM subscriptions WHERE id = '$subscriptionId'";
    mysqli_query($conn, $deleteSubscriptionQuery);
}

// Formuláře pro přidání a odstranění předplatného
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_subscription'])) {
    $category = $_POST['category'];
    $serviceName = $_POST['serviceName'];
    $plan = $_POST['plan'];
    $price = $_POST['price'];
    $availableSpots = $_POST['availableSpots'];
    $additionalInfo = $_POST['additionalInfo'];
    $userId = $_SESSION['id'];
    $contactInfo = $_SESSION['valid']; 

    // Validace negativních čísel
    if ($price < 0 || $availableSpots < 0) {
        echo "<script>alert('Cena nebo počet míst nesmí být záporné.');</script>";
    } else {
        $sql = "INSERT INTO subscriptions (user_id, category, service_name, plan, price, slots_available, contact_info, additional_info) 
                VALUES ('$userId', '$category', '$serviceName', '$plan', '$price', '$availableSpots', '$contactInfo', '$additionalInfo')";

        if (mysqli_query($conn, $sql)) {
            header("Location: home.php");
            exit();
        } else {
            echo "Chyba: " . mysqli_error($conn);
        }
    }
}

// Odstranění předplatného
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_subscription'])) {
    $subscriptionId = $_POST['subscription_id'];
    $userId = $_SESSION['id']; 

    $checkOwnershipSql = "SELECT * FROM subscriptions WHERE id = '$subscriptionId' AND user_id = '$userId'";
    $ownershipResult = mysqli_query($conn, $checkOwnershipSql);

    if (mysqli_num_rows($ownershipResult) > 0) {
        $deleteSql = "DELETE FROM subscriptions WHERE id = '$subscriptionId'";
        if (mysqli_query($conn, $deleteSql)) {
            header("Location: home.php");
            exit();
        } else {
            echo "Chyba při odstraňování: " . mysqli_error($conn);
        }
    } else {
        echo "Nemáte oprávnění k odstranění tohoto předplatného.";
    }
}

$result = mysqli_query($conn, "SELECT * FROM subscriptions");

?>



<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kulim+Park:ital,wght@0,200;0,300;0,400;0,600;0,700;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Home - Sdílej předplatné</title>


</head>
<body>
<nav>
    <div class="logo">
        <p><a href="home.php"><b>ShareIT</b><img src="img/logo.png" alt="" height="30px"></a></p>
    </div>
    <ul id="menuList">
        <li><a href="spravce_predplatneho.php">Správce předplatných</a></li>
        <li><a href="finance.html">Finance</a></li>
        <li><a href="contact.php">Kontakt</a></li>
        <li><a href="user.php"><img src="img/user.png" height="40px"></a></li>
        <li>
            <div class="notification-icon" onclick="location.href='php/notifications.php'">
                <img src="img/notification.png" alt="Notifikace" class="bell-icon">
                <span id="notificationCount" class="notification-count" style="display: none;"></span> 
            </div>
        </li>
    </ul>
</nav>

    <div class="container">
    <div class="custom-dropdown">
    <button class="dropdown-button">Kategorie &#x25BC;</button>
    <ul class="dropdown-menu">
        <li data-value="Všechny">Všechny</li>
        <li data-value="Hudba">Hudba</li>
        <li data-value="Filmy">Filmy</li>
        <li data-value="Hry">Hry</li>
        <li data-value="Ostatní">Ostatní</li>
    </ul>
</div>


        <!-- Formulář pro přidání nového předplatného -->
        <div class="subscription-form-container" id="subscriptionForm">
            <form method="POST" action="home.php" class="subscription-form">
                <h2>Přidat Nové Předplatné:</h2>
                <div class="form-group">
                    <label for="category">Kategorie:</label>
                    <select id="category" name="category" required onchange="updateServiceOptions()">
                        <option value="Hudba">Hudba</option>
                        <option value="Filmy">Filmy</option>
                        <option value="Hry">Hry</option>
                        <option value="Ostatní">Ostatní</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="serviceName">Název služby:</label>
                    <select id="serviceName" name="serviceName" required onchange="updatePlanOptions()"></select>
                </div>

                <div class="form-group">
                    <label for="plan">Tarif:</label>
                    <select id="plan" name="plan" required></select>
                </div>

                <div class="form-group">
                    <label for="price">Cena služby (Kč):</label>
                    <input type="number" id="price" name="price" required readonly>
                </div>

                <div class="form-group">
                    <label for="availableSpots">Volná místa:</label>
                    <input type="number" id="availableSpots" name="availableSpots" required>
                </div>

                <div class="form-group">
                    <label for="additionalInfo">Další informace:</label>
                    <textarea id="additionalInfo" name="additionalInfo" placeholder="Libovolný popis"></textarea>
                </div>

                <button type="submit" name="add_subscription" class="btn"><b>Přidat předplatné</b></button>
            </form>
        </div>

        <div class="subscription-list">
            <div class="subscription-card-list">
        <?php
            while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="subscription-card" data-category="' . htmlspecialchars($row['category']) . '">';
      
        if ($row['service_name'] === 'Netflix') {
            echo '<img src="img/Netflix.png" alt="Netflix" class="subscription-img">';
        } elseif ($row['service_name'] === 'Spotify') {
            echo '<img src="img/Spotify.png" alt="Spotify" class="subscription-img">';
        } elseif ($row['service_name'] === 'HBO Max') {
            echo '<img src="img/HBO Max.png" alt="HBO Max" class="subscription-img">';
        } elseif ($row['service_name'] === 'Apple Music') {
            echo '<img src="img/Apple Music.png" alt="Apple Music" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Voyo') {
            echo '<img src="img/Voyo.png" alt="Apple Music" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Apple TV') {
            echo '<img src="img/AppleTV.png" alt="Apple TV" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Amazon Prime Video') {
            echo '<img src="img/Prime.png" alt="Amazon Prime Video" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Disney') {
            echo '<img src="img/Disney.png" alt="Disney" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Xbox') {
            echo '<img src="img/Xbox.png" alt="Xbox" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Youtube') {
            echo '<img src="img/Youtube.png" alt="Youtube" class="subscription-img">';

        }elseif ($row['service_name'] === 'Dropbox') {
            echo '<img src="img/Dropbox.png" alt="Dropbox" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Microsoft 365') {
            echo '<img src="img/Microsoft.png" alt="Microsoft" class="subscription-img">';
        }
        elseif ($row['service_name'] === 'Chat GPT') {
            echo '<img src="img/GPT.png" alt="GPT" class="subscription-img">';
        }

        echo '<h4 style="font-size: 1.4em;"><b>' . htmlspecialchars($row['service_name']) . ' - ' . htmlspecialchars($row['plan']) . '</b></h4>';
        echo '<div class="underline"></div>'; 
        echo '<p><b>Kategorie:</b> ' . htmlspecialchars($row['category']) . '</p>';
        echo '<p><b>Počet míst:</b> ' . htmlspecialchars($row['slots_available']) . '</p>';
        echo '<p><b>Nabízí:</b> @' . htmlspecialchars($row['contact_info']) . '</p>';

        echo '<a href="subscription_details.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-info">Číst více</a>';

        if ($row['user_id'] == $_SESSION['id']) {
            echo '<form method="POST" action="home.php" style="display: inline-block;">';
            echo '<input type="hidden" name="subscription_id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<button type="submit" name="delete_subscription" class="btn-delete">Odstranit</button>';
            echo '</form>';
        }

        echo '</div>';
    }
    ?>
        </div>
    
    </div>
        <div class="add-subscription-card" onclick="toggleForm()">
            <div class="plus-icon">
                <img src="img/plus.png" alt="" height=100px >
            </div>
            <p><b>Přidat nové předplatné</b></p>
        </div>

    <script src="js/home.js"></script>
    <script src="js/navbar.js"></script>
    <script src="js/notification-all.js"></script>
</body>
</html>

<?php 
session_start();

include("php/config.php"); 

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
}

$conn = mysqli_connect("localhost", "root", "", "login");

// Zkontroluj připojení
if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

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
        echo "<script>alert('Cena a počet míst nesmí být záporné.');</script>";
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
    <style>
        body {
            font-family: "Jura", sans-serif;
            font-optical-sizing: auto;
            font-weight: 200;
            font-style: normal;
        }

        nav{
            background: #f8f8f8;
        }
        .container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
        }

        .subscription-card h4 {
            font-size: 1.4em; 
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .add-subscription-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            padding: 40px;
            margin: 20px;
            cursor: pointer;
            font-size: 24px;
            color: #007bff;
            text-align: center;
            border: 1px solid #ddd;
        }

        .plus-icon {
            font-size: 4em;
            color: #00A9DE;
        }

        .add-subscription-card:hover .plus-icon {
            color: #008bb2;
        }
        .add-subscription-card p {
            color: black;
        }

        .subscription-form-container {
            display: none;
            min-width: 500px;
            margin-top: -20px;
            max-width: 400px;
        }

        .subscription-form {
            width: 100%;
        }

        h2 {
            font-size: 26px;
            color: #333333;
            margin-bottom: 20px;
            text-align: center;
            font-family: "Jura", sans-serif;
    font-optical-sizing: auto;
    font-weight: 200;
    font-style: normal;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        textarea {
            resize: none;
        }

        .subscription-img {
            width: 180px;
            height: auto;
            margin-bottom: 10px;
            border-radius: 10px;
        }

        .subscription-details {
            margin-top: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .filter-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-family: "Jura", sans-serif;
            font-optical-sizing: auto;
            font-weight: 200;
            font-style: normal;
            font-size: 16px;
            z-index: -1;
        }

        #categoryFilter {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #ffffff;
            border: 1px solid #cccccc;
            border-radius: 4px;
            padding: 10px 40px 10px 15px;
            font-size: 16px;
            color: #333333;
            cursor: pointer;
            position: relative;
            width: 200px;
            transition: border-color 0.3s ease;
        }

        #categoryFilter::after {
            content: '\25BC';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: #333333;
            pointer-events: none;
        }

    #categoryFilter:focus {
        outline: none;
        border-color: #007bff;
    }

    #categoryFilter:hover {
        border-color: #007bff;
    }

    .filter-container label {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
        color: #000000;
    }

</style>

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

            <li>
                <div class="notification-icon" onclick="location.href='php/notifications.php'">
                    <img src="img/notification.png" alt="Notifikace" class="bell-icon">
                    <span id="notificationCount" class="notification-count hidden">0</span>
                </div>
            </li>

        </ul>
            <div class="menu-icon" onclick="toggleMenu()">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
    </nav>

    <div class="container">
        <div id="categoryFilterContainer" class="filter-container">
            <label for="categoryFilter"><b>Filtrovat podle kategorie:</b></label>
            <select id="categoryFilter">
                <option value="Všechny">Všechny</option>
                <option value="Hudba">Hudba</option>
                <option value="Filmy">Filmy</option>
                <option value="Hry">Hry</option>
                <option value="Ostatní">Ostatní</option>
            </select>
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
            <h2><b>Všechna dostupná předplatná:</b></h2>
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

        // Zobrazení názvu služby ve formátu "Spotify - Tarif"
        echo '<h4 style="font-size: 1.5em;">' . htmlspecialchars($row['service_name']) . ' - ' . htmlspecialchars($row['plan']) . '</h4>';
        echo '<p><b>Kategorie:</b> ' . htmlspecialchars($row['category']) . '</p>';
        echo '<p><b>Počet míst:</b> ' . htmlspecialchars($row['slots_available']) . '</p>';
        echo '<p><b>Nabízí:</b> @' . htmlspecialchars($row['contact_info']) . '</p>';

        echo '<a href="subscription_details.php?id=' . htmlspecialchars($row['id']) . '" class="btn btn-info">Číst více</a>';

        if ($row['user_id'] == $_SESSION['id']) {
            echo '<form method="POST" action="home.php" style="display: inline-block;">';
            echo '<input type="hidden" name="subscription_id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<button type="submit" name="delete_subscription" class="btn btn-danger">Odstranit</button>';
            echo '</form>';
        }

        echo '</div>';
    }
    ?>
        </div>
    
    </div>
        <div class="add-subscription-card" onclick="toggleForm()">
            <div class="plus-icon">+</div>
                <p><b>Vytvoř si nové předplatné!</b></p>
            </div>
        </div>

    <script src="js/home.js"></script>
    <script src="https://kit.fontawesome.com/f8e1a90484.js" crossorigin="anonymous"></script>
    <script src="js/navbar.js"></script>
    <script src="js/notification.js"></script>
</body>
</html>
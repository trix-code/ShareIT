<?php 
session_start();
include("php/config.php"); 

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "login");

if (!$conn) {
    die("Připojení selhalo: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serviceName = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $frequency = mysqli_real_escape_string($conn, $_POST['frequency']);
    $nextPayment = mysqli_real_escape_string($conn, $_POST['nextPayment']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $userId = $_SESSION['id']; 

    if (!empty($serviceName) && $price > 0) {
        $sql = "INSERT INTO spravce_predplatneho (user_id, name, price, frequency, next_payment, category) 
                VALUES ('$userId', '$serviceName', '$price', '$frequency', '$nextPayment', '$category')";

        if (mysqli_query($conn, $sql)) {
            echo "Předplatné bylo úspěšně přidáno!";
        } else {
            echo "Chyba: " . mysqli_error($conn);
        }
    } else {
        echo "Neplatné údaje!";
    }
}

$userId = $_SESSION['id'];
$result = mysqli_query($conn, "SELECT * FROM spravce_predplatneho WHERE user_id='$userId'");
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Správce Předplatných</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kulim+Park:ital,wght@0,200;0,300;0,400;0,600;0,700;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

</head>
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
        <li class="notification-container">
            <div class="notification-icon" onclick="toggleNotifications()">
                <img src="img/notification.png" alt="Notifikace" />
                <span id="notificationCount" class="notification-count hidden"></span>
            </div>
            <div id="notificationSidebar" class="notification-sidebar hidden">
                <h4>Notifikace</h4>
                <ul id="notificationList"></ul>
            </div>
        </li>
    </ul>
    <div class="menu-icon" onclick="toggleMenu()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
</nav>



    <div class="subscription-container">
        <div id="noSubscriptionsMessage" class="no-subscriptions">
            <p><b>Tvoje předplatné: <span style="color: red;">Momentálně nemáš vytvořené žádné předplatné!</span></b></p>
            <div class="add-subscription-button" onclick="showSubscriptionForm()">
                <div class="plus-icon">+</div>
                <b>Vytvoř si nové předplatné!</b>
            </div>  
        </div>

        <div id="subscriptionFormContainer" class="subscription-form hidden">
            <h2>Přidat Nové Předplatné:</h2>
            <form method="POST" action="spravce_predplatneho.php" id="subscriptionForm">
                <div class="field input">
                    <label for="serviceName"><b>Název služby:</b></label>
                    <input type="text" id="serviceName" name="name" required>
                </div>

                <div class="field input">
                    <label for="price"><b>Cena služby:</b></label>
                    <input type="number" id="price" name="price" placeholder="380 Kč,-" required>
                </div>

                <div class="field input">
                    <label for="paymentFrequency"><b>Frekvence plateb:</b></label>
                    <select id="paymentFrequency" name="frequency" required>
                        <option value="měsíčně">Měsíční</option>
                        <option value="roční">Roční</option>
                    </select>
                </div>

                <div class="field input">
                    <label for="nextPayment"><b>Datum další platby:</b></label>
                    <input type="date" id="nextPayment" name="nextPayment" required>
                </div>

                <div class="field input">
                    <label for="category">Kategorie:</label>
                    <select id="category" name="category" required>
                        <option value="Zábava">Zábava</option>
                        <option value="Filmy">Filmy</option>
                        <option value="Sporty">Sporty</option>
                        <option value="Hudba">Hudba</option>
                        <option value="Škola">Škola</option>
                    </select>
                </div>

                <div class="field">
                    <button type="submit" class="btn"><b>Přidat předplatné</b></button>
                </div>
            </form>
        </div>
    
        <!-- Zobrazení uložených předplatných -->
        <div id="subscriptionsContainer" class="subscription-list-spravce">
            <div class="subscriptions-text">
                <h2><b>Tvoje Předplatné:</b></h2>    
            </div>
            <div id="subscriptions">
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="subscription-card">';
                        echo '<h3>' . $row['name'] . ' (' . $row['category'] . ')</h3>';
                        echo '<p>Cena: ' . $row['price'] . ' Kč</p>';
                        echo '<p>Frekvence: ' . $row['frequency'] . '</p>';
                        echo '<p>Další platba: ' . $row['next_payment'] . '</p>';
                        echo '</div>';
                    }
                } else {
                    echo '<p><b>Žádná předplatná k zobrazení.</b></p>';
                }
                ?>
            </div>
            <div class="add-subscription-button" onclick="showSubscriptionForm()">
                <div class="plus-icon">+</div>
                <p><b>Přidat nové předplatné</b></p>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script src="https://kit.fontawesome.com/f8e1a90484.js" crossorigin="anonymous"></script>
    <script src="js/navbar.js"></script>
    <script src="js/loading.js"></script>
</body>
</html>

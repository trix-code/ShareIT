<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['id'];

$query = "
    SELECT COUNT(*) AS unread_count 
    FROM notifications 
    WHERE user_id = '$userId' AND is_read = 0";  // is_read = 0 znamená nepřečtené
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$unreadCount = $row['unread_count'];

$query = "
    SELECT 
        n.*, 
        u.username AS sender_name, 
        u.profile_pic, 
        s.service_name, 
        s.plan,
        s.category
    FROM 
        notifications n
    LEFT JOIN 
        users u ON n.sender_id = u.id
    LEFT JOIN 
        subscriptions s ON n.subscription_id = s.id
    WHERE 
        n.user_id = '$userId'
    ORDER BY 
        n.created_at DESC";

$result = mysqli_query($con, $query);

$notifications = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}

function time_elapsed_string_short($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) {
        return $diff->y . 'y';
    } elseif ($diff->m > 0) {
        return $diff->m . 'm';
    } elseif ($diff->d > 0) {
        return $diff->d . 'd';
    } elseif ($diff->h > 0) {
        return $diff->h . 'h';
    } elseif ($diff->i > 0) {
        return $diff->i . 'm';
    } else {
        return $diff->s . 's';
    }
}

// Funkce pro získání správné ikony na základě kategorie
function get_category_icon($category) {
    switch ($category) {
        case 'Hudba':
            return '../img/icons/music.png';
        case 'Filmy':
            return '../img/icons/film.png';
        case 'Hry':
            return '../img/icons/games.png';
        case 'Ostatní':
            return '../img/icons/other.png';
    }
}

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kulim+Park:ital,wght@0,200;0,300;0,400;0,600;0,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Home - Sdílej předplatné</title>
</head>
<body>
<nav>
    <div class="logo">
        <p><a href="../home.php"><b>ShareIT</b></a></p>
    </div>
    <ul id="menuList">
        <li><a href="../spravce_predplatneho.php">Správce Předplatných</a></li>
        <li><a href="../finance.html">Finance</a></li>
        <li><a href="../contact.php">Kontakt</a></li>
        <li><a href="../user.php"><img src="../img/user.png" height="40px"></a></li>

        <!-- Ikona notifikace s počtem -->
        <li>
            <div class="notification-icon" onclick="location.href='notifications.php'">
                <img src="../img/notification.png" alt="Notifikace" class="bell-icon">
                <span id="notificationCount" class="notification-count" style="display: none;"></span> <!-- Počet notifikací -->
            </div>
        </li>
    </ul>
</nav>

    </nav>
    <div class="notification-container">
        <h2>Notifikace</h2>
        <?php if (empty($notifications)): ?>
            <p>Nemáte žádné notifikace.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item">
                    <div class="notification-header">
                        <img 
                            src="../img/profiles/<?php echo htmlspecialchars($notification['profile_pic']); ?>" 
                            alt="Profilová fotka" 
                            class="profile-picture"
                        >
                        <div class="notification-info">
                            <h3><?php echo htmlspecialchars($notification['sender_name']); ?></h3>
                            <p>Má zájem o tvoje sdílené předplatné: 
                                <strong><?php echo htmlspecialchars($notification['service_name'] . ' – ' . $notification['plan']); ?></strong>
                            </p>
                        </div>
                        <span class="notification-time">
                            Před <?php echo time_elapsed_string_short($notification['created_at']); ?>
                        </span>
                    </div>
                    <div class="notification-actions">
        <button class="contact-button">📧 Kontakt</button>
        <!-- Tlačítko pro potvrzení -->
        <button class="accept-button" onclick="handleNotificationAction(<?php echo $notification['id']; ?>, 'confirm')">✔️ Potvrdit</button>
        <!-- Tlačítko pro odmítnutí -->
        <button class="cancel-button" onclick="handleNotificationAction(<?php echo $notification['id']; ?>, 'reject')">❌ Zrušit</button>
        <img src="<?php echo get_category_icon($notification['category']); ?>" alt="Ikona kategorie" class="category-icon">
    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="successMessage" class="message success" style="display: none;">Úspěšně provedeno.</div>
    <div id="errorMessage" class="message error" style="display: none;">Chyba při zpracování požadavku. Zkuste to prosím znovu.</div>

    <script src="../js/notification-php.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>

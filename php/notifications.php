<?php
session_start();
include("config.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

$userId = $_SESSION['id'];

$query = "
    SELECT 
        n.*, 
        u.username AS sender_name, 
        s.service_name AS subscription_name 
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
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kulim+Park:ital,wght@0,200;0,300;0,400;0,600;0,700;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Home - Sdílej předplatné</title>
</head>
<body>
    <nav>
        <div class="logo">
            <p><a href="../home.php"><b>ShareIT</b></a></p>
        </div>
        <ul id="menuList">
            <li><a href="../spravce_predplatneho.php">Správce předplatných</a></li>
            <li><a href="../finance.html">Finance</a></li>
            <li><a href="support.html">Podpora</a></li>
            <li><a href="../user.php"><img src="../img/user.png" height="40px"></a></li>
            <li>
                <div class="notification-icon" onclick="location.href='notifications.php'">
                    <img src="../img/notification.png" alt="Notifikace" class="bell-icon">
                    <span id="notificationCount" class="notification-count hidden">0</span>
                </div>
            </li>
        </ul>
    </nav>
    <div class="notification-container">
        <h2>Notifikace</h2>
        <?php if (empty($notifications)): ?>
            <p>Nemáte žádné notifikace.</p>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item">
                    <p><strong><?php echo htmlspecialchars($notification['sender_name']); ?></strong> 
                    má zájem o vaše předplatné: 
                    <em><?php echo htmlspecialchars($notification['subscription_name']); ?></em>.</p>
                    <span><?php echo htmlspecialchars($notification['created_at']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="../js/notification.js"></script>
    <script src="../js/navbar.js"></script>
</body>
</html>

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

$query = "SELECT username, email, age, created_at, profile_pic FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $userData = mysqli_fetch_assoc($result);
    $profilePic = !empty($userData['profile_pic']) ? 'img/profiles/' . $userData['profile_pic'] : 'img/default-profile.png';
} else {
    die("Chyba: Uživatelská data nebyla nalezena.");
}


$query = "SELECT username, email, age, created_at, profile_pic FROM users WHERE id = '$userId'";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $userData = mysqli_fetch_assoc($result);
    $profilePic = !empty($userData['profile_pic']) ? 'img/profiles/' . $userData['profile_pic'] : 'img/default-profile.png';
} else {
    die("Chyba: Uživatelská data nebyla nalezena.");
}


// Dotaz na počet aktivních předplatných
$subscriptionQuery = "SELECT COUNT(*) AS subscription_count FROM subscriptions WHERE user_id = '$userId'";
$subscriptionResult = mysqli_query($con, $subscriptionQuery);
$subscriptionData = mysqli_fetch_assoc($subscriptionResult);

// Proměnné
$username = $userData['username'] ?? 'Neznámé uživatelské jméno';
$email = $userData['email'] ?? 'Neznámý email';
$age = $userData['age'] ?? 'Neznámý věk';
$createdAt = $userData['created_at'] ?? 'Neznámé datum';
$subscriptionCount = $subscriptionData['subscription_count'] ?? 0;
?>


<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uživatelský účet</title>
    <script src="https://kit.fontawesome.com/f8e1a90484.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Kulim+Park:wght@200;300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">

</head>
<body>
    <style>
.custom-alert {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #fff;
    padding: 20px 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    width: 300px;
}

.custom-alert p {
    margin-bottom: 15px;
    font-size: 16px;
}

.custom-alert button.close-alert {
    padding: 10px 15px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.custom-alert button.close-alert:hover {
    background-color: #0056b3;
}

.custom-alert.error {
    border: 2px solid #f44336;
    color: #f44336;
}

.custom-alert.success {
    border: 2px solid #4CAF50;
    color: #4CAF50;
}

.hidden {
    display: none;
}

    </style>
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

 <!-- Vlastní alert -->
 <div id="customAlert" class="custom-alert hidden">
        <p id="alertMessage"></p>
        <button onclick="closeAlert()" class="close-alert">OK</button>
    </div>
    
    <div class="user-settings-container">

        <div class="settings-flex">
            <!-- Sekce profilové fotky a informací -->
            <div class="profile-section">
            <div class="profile-box">
                    <img id="profilePreview" src="<?php echo $profilePic; ?>" alt="Profilová fotka">
                    <form id="uploadForm" enctype="multipart/form-data" method="POST" action="upload_profile_pic.php">
                        <input type="file" id="profileInput" name="profilePic" accept="image/*" onchange="previewImage(event)">
                        <label for="profileInput" class="change-profile-pic">Změnit profilovku</label>
                        <button type="submit" class="upload-button" id="uploadButton">Nahrát</button>
                    </form>
                </div>

                <div class="info-box">
                    <h2>Vítej <b><?php echo htmlspecialchars($username); ?>!</b></h2>
                    <p>Poslední přihlášení: <b>Dnes</b></p>
                    <p>Vytvořené předplatné: <b><?php echo $subscriptionCount; ?></b></p>
                    <p>Účet vytvořen: <b><?php echo htmlspecialchars($createdAt); ?></b></p>
                </div>

            </div>

            <!-- Sekce uživatelských údajů -->
            <div class="user-details-section">
                <div class="detail-row">
                    <span class="detail-label">Jméno:</span>
                    <div class="detail-container">
                        <span class="detail-value"><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <a href="edit.php?type=name"><button class="edit-btn"><i class="fa fa-pencil"></i> Upravit</button></a>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <div class="detail-container">
                        <span class="detail-value"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <a href="edit.php?type=email"><button class="edit-btn"><i class="fa fa-pencil"></i> Upravit</button></a>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Heslo:</span>
                    <div class="detail-container">
                        <span class="detail-value">**********</span>
                    </div>
                    <a href="edit.php?type=password"><button class="edit-btn"><i class="fa fa-pencil"></i> Upravit</button></a>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Věk:</span>
                    <div class="detail-container">
                        <span class="detail-value"><?php echo htmlspecialchars($age); ?></span>
                    </div>
                    <a href="edit.php?type=age"><button class="edit-btn"><i class="fa fa-pencil"></i> Upravit</button></a>
                </div>
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="php/logout.php" class="logout-btn">Odhlásit se</a>
            <a href="delete_account.php" class="delete-account-btn">Smazat účet</a>

    </div>

    <script src="/shareIT/js/navbar.js"></script>
    <script src="/shareIT/js/user.js"></script>

  
    <script>   
        const urlParams = new URLSearchParams(window.location.search);
        const errorMessage = urlParams.get('error');
        const successMessage = urlParams.get('success');

        if (errorMessage) {
            showAlert(errorMessage, "error");
        } else if (successMessage) {
            showAlert(successMessage, "success");
        }

        function showAlert(message, type) {
            const alert = document.getElementById("customAlert");
            const alertMessage = document.getElementById("alertMessage");

            alertMessage.textContent = message;

            // Nastylujeme alert podle typu
            if (type === "error") {
                alert.classList.add("error");
            } else {
                alert.classList.add("success");
            }

            alert.classList.remove("hidden");
        }

        function closeAlert() {
            const alert = document.getElementById("customAlert");
            alert.classList.add("hidden");
        }
    </script>

<script src="js/notification-all.js"></script>

</body>
</html>

<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <div class="text-box">
            <h1>Welcome in <br><b>ShareIT</b></h1>
        </div>
        <div class="box form-box">
            <?php 
             
              include("php/config.php");
              if(isset($_POST['submit'])){
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = mysqli_real_escape_string($con, $_POST['password']);

                // Ověření uživatele podle emailu
                $result = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'") or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                // Kontrola, zda uživatel existuje a zda je heslo správné
                if($row && password_verify($password, $row['Password'])){
                    // Přihlášení úspěšné
                    $_SESSION['valid'] = $row['Email'];
                    $_SESSION['username'] = $row['Username'];
                    $_SESSION['age'] = $row['Age'];
                    $_SESSION['id'] = $row['Id'];

                    header("Location: home.php");
                    exit();
                } else {
                    // Chybné přihlašovací údaje
                    echo "<div class='message'>
                              <p>Wrong Email or Password</p>
                          </div> <br>";
                    echo "<a href='index.php'><button class='btn'>Go Back</button>";
                }
              } else {
            ?>
            <header>Welcome Back!</header>
            <div class="underline"></div>
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" autocomplete="off" required placeholder="example@gmail.com">
                </div>

                <div class="field input password-wrapper">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" autocomplete="off" required placeholder="password">
                    <span class="toggle-password" onclick="togglePassword()">
                        <img src="img/password-eye.png" alt="Show Password" id="toggleIcon">
                    </span>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login">
                </div>
                <div class="links">
                    Don't have account? <a href="register.php"><b> Sign Up Now</b></a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>

    <script src="js/password.js"></script>

</body>
</html>

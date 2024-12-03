<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Jura:wght@300..700&family=Krona+One&family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <h1>Welcome in <br><b>ShareIT</b></h1>
        <div class="box form-box">

        <?php 
         
         include("php/config.php");
         if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $age = $_POST['age'];
            $password = $_POST['password'];

            // Validace věku
            if($age < 16 || $age > 100){
                echo "<div class='message'>
                          <p>Age must be between 16 and 100.</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            }
            // Validace hesla
            elseif(strlen($password) < 8 || !preg_match('/[A-Z]/', $password)){
                echo "<div class='message'>
                          <p>Password must be at least 8 characters long and contain at least one uppercase letter.</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn-messege'>Go Back</button>";
            }
            // Validace emailu
            elseif(!preg_match('/@(gmail\.com|seznam\.cz|yahoo\.com)$/', $email)){
                echo "<div class='message'>
                          <p>Invalid email, try again.</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn-messege'>Go Back</button>";
            }
            else {
                // Verifikace unikátního emailu
                $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");

                if(mysqli_num_rows($verify_query) != 0){
                    echo "<div class='message'>
                              <p>This email is already in use. Please try another one!</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                }
                else {
                    // Hashování hesla pomocí bcrypt
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                    // Použití připraveného dotazu pro vložení uživatele do databáze
                    $stmt = $con->prepare("INSERT INTO users (Username, Email, Age, Password) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("ssis", $username, $email, $age, $hashedPassword);

                    if ($stmt->execute()) {
                        echo "<div class='message-green'>
                                  <p>Registration successful!</p>
                              </div> <br>";
                        echo "<a href='index.php'><button class='btn'>Login Now</button>";
                    } else {
                        echo "<div class='message'>
                                  <p>Error occurred during registration. Please try again.</p>
                              </div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
                    }

                    $stmt->close(); // Zavření příkazu
                }
            }
         } else {
        ?>

            <header>Create an account!</header>
            <div class="underline"></div>
            <form action="register.php" method="post" id="registrationForm">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="age">Age</label>
                    <input type="number" name="age" id="age" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                    <div class="error" id="passwordError">Password must be at least 8 characters long and contain at least one uppercase letter.</div>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" id="submitBtn" disabled>
                </div>
                <div class="links">
                    Already a member? <a href="index.php"><b> Sign In</b></a>
                </div>
            </form>
        </div>
        <?php } ?>
    </div>

    <script src="js/function.js"></script> 
</body>
</html>

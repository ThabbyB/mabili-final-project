<?php
session_start();
require "./includes/connection.php";

//if all input is available, continue to validate it in function validate
if(isset($_POST['first']) && isset($_POST['last']) && isset($_POST['mail']) && isset($_POST['user']) && isset($_POST['pwd1']) && isset($_POST['pwd2'])){
    //sanitize user input
    $firstName = trim($_POST["first"]);
    $lastName = trim($_POST["last"]);
    $email = trim($_POST["mail"]);
    $userName = trim($_POST["user"]);

    //using regex to validate user input, if email is valid, if passwords meet the security requirements and if the two passwords entered match
    $emailPattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^"; 
    $password1 = $_POST["pwd1"];
    $password2 = $_POST["pwd2"];
    $pwdLower = "/[a-z]/";
    $pwdUpper = "/[A-Z]/";
    $pwdDigit = "/\d/";
    $pwdSpecial = "/[\W_]/";
    $msg = [];//empty array before input
    
    if(strlen($firstName) < 2){
        $msg = "First name should be two or more characters long";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(strlen($lastName) < 2){
        $msg = "Last name should be two or more characters long";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(!preg_match("/^[a-zA-Z]*$/", $firstName)){
        $msg = "First name should not contain any special characters";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(!preg_match("/^[a-zA-Z]*$/", $lastName)){
        $msg = "Last name should not contain any special characters";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(!preg_match($emailPattern, $email )){
        $msg = "Email not valid";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(strlen($userName) < 4 ){
        $msg = "Username should be at least 4 characters long";
        echo "<li style='color: red;'>$msg</li>";
    }
    
    if(!preg_match($pwdLower, $password1) || !preg_match($pwdLower, $password2)){
        $msg = "Your password should contain at least one lowercase letter";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(!preg_match($pwdUpper, $password1) || !preg_match($pwdUpper, $password2)){
        $msg = "Your password should contain at least one uppercase letter";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(!preg_match($pwdDigit, $password1) || !preg_match($pwdDigit, $password2)){
        $msg = "Your password should contain at least one number/digit";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(!preg_match($pwdSpecial, $password1) || !preg_match($pwdSpecial, $password2)){
        $msg = "Your password should contain at least one special character";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(strlen($password1) < 8 || strlen($password2) < 8 ){
        $msg = "Your password should contain at least eight characters";
        echo "<li style='color: red;'>$msg</li>";
    }

    if($password1 !== $password2){
        $msg = "Passwords should match";
        echo "<li style='color: red;'>$msg</li>";
    }

    //to check if the email entered is already taken in the database, if so user should enter a different one
    $query1 = "SELECT email FROM users WHERE email = ?";
    $stmt1 = $db->prepare($query1);
    $stmt1->execute([$email]);
    $rowEmail = $stmt1->fetch();

    if($rowEmail){
        $msg = "Email already taken";
        echo "<li style='color: red;'>$msg</li>";
    }

    //to check if username is taken in the database, if so user should enter a different one
    $query2 = "SELECT username FROM users WHERE username = ?";
    $stmt2 = $db->prepare($query2);
    $stmt2->execute([$userName]);
    $rowUsername = $stmt2->fetch();

    if($rowUsername){
        $msg= "Username already taken";
        echo "<li style='color: red;'>$msg</li>";
    }

    //if validation passes it means the $msg array is empty (which contains a string(s)/ message(s) of a field failing validation) and the regitration is successful
   if(empty($msg)){
    $password2Hashed = password_hash($password2, PASSWORD_DEFAULT);//must hash password first before inserting in database for security reasons
    try {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $insert1 = "INSERT INTO users (first_name, last_name, email, username, pass_code, registration_confirmed)
        VALUES ('$firstName', '$lastName', '$email', '$userName', '$password2Hashed', '1')";
        $db->exec($insert1);

    }catch(PDOException $e){
        echo $insert1 . "<br>" . $e->getMessage(); //catch error in case the query fails
    }

    echo "<p style='color: green;'>Registration successful! <em>An email was sent to your inbox.</em></p>";
    //send email to user, using mailtrap
    $phpmailer = new PHPMailer\PHPMailer\PHPMailer();
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = $_ENV['PHPMAILER_USER'];
    $phpmailer->Password = $_ENV['PHPMAILER_PASSWORD'];

    $phpmailer->addAddress($email);

    $phpmailer->isHTML(true);
    $phpmailer->Subject = "<p>Welcome!</p>";
    $phpmailer->Body = "<div>
    <h3>Hi $userName.</h3>
    <p style= 'padding-bottom: 20px;'>We are so happy that you have joined our <em>Recipe Ranch</em> community.
    We are looking forward to seeing the recipes you will share and hope you will also <em>tap into our 
    world of deliciousness</em> to bring joy into your kitchen!</p>

    <h4>Yours in flavor, <br>
    The Recipe Ranch Team.</h4>
    <img src='../media/branding/logo.png'>
    </div>";

    /*if (!$phpmailer->send()) {
        echo "Oops, we are currently unable to send a confirmation email";
    }else{
        echo "<p style='color: green;'>Registration successful! <em>An email was sent to your inbox.</em></p>";
    } */
    
   }

    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>

<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <div id="drop-down"><i class="fa-solid fa-bars"></i></div>
        <nav id="bar">
            <a href="./about.html">About us</a>
            <a href="./contact.html">Contact</a>
            <a href="./sign-in.php">Sign in</a>
        </nav>
    </header>
        
    <section>
        <main class="signUp">
            <div>
                <h5>Sign up below and tap into a world of deliciousness!</h5>
                <form method="post"> 
                    <div class="mb-3">
                        <label for="firstName" class="form-label">First Name</label><br>
                        <input type="text" name="firstName" id="firstName" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="lastName" class="form-label">Last Name</label><br>
                        <input type="text" name="lastName" id="lastName" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label><br>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="userName" class="form-label">Username</label><br>
                        <input type="text" name="userName" id="userName" class="form-control">
                    </div>

                    <label for="password1" class="form-label">Password <em style="font-size: 0.8em;">min 8 characters</em></label>
                    <div class="mb-3 input-group">
                        <input type="password" name="password1" id="password1" class="form-control" aria-describedby="viewPword1">
                        <button class="btn btn-outline-secondary" type="button" id="viewPword1"><i class="fa-solid fa-eye-slash" id="pwordEye1"></i></button>
                    </div>

                    <label for="password2" class="form-label">Confirm Password</label>
                    <div class="mb-3 input-group">
                        <input type="password" name="password2" id="password2" class="form-control" aria-describedby="viewPword2">
                        <button class="btn btn-outline-secondary" type="button" id="viewPword2"><i class="fa-solid fa-eye-slash" id="pwordEye2"></i></button>
                    </div>

                </form>

                <button id="signupButton">Sign up</button><br>
                <div id="loader"></div>
                <div class="msg"></div>
                <h5>Already have an account?</h5>
                <a href="./sign-in.php">Sign in</a>
            </div>    
        </main>
    </section>

    <footer>
        <div>
            <img src="./media/branding/logo.png" width="50px" height="50px" alt="company logo">
            <h6>&copy;Copyright 2024</h6>
        </div>

        <div class="rightFooter">
            <h5>Follow our social media accounts @RecipeRanch on:</h5>
            <div class="socials">
                <i class="fa-brands fa-facebook-f"></i>
                <i class="fa-brands fa-x-twitter"></i>
                <i class="fa-brands fa-instagram"></i>
                <i class="fa-brands fa-tiktok"></i>
            </div>
        </div>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/sign-up.js"></script>
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
</body>


</html>































    




    
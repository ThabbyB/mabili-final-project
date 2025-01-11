<?php
//for forgotten password table
session_start();
require "./includes/connection.php";

if(isset($_POST["email"])){

    //sanitize and check if user input meets email requirements
    $email = trim($_POST["email"]);
    $emailPattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";

    if(!preg_match($emailPattern, $email )){
        echo "<li style='color: red;'>Email not valid</li>";

    }else{

        //check if email exists in the db
        $query1 = "SELECT email FROM users WHERE email = ?";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute([$email]);
        $emailResult = $stmt1->fetch(PDO::FETCH_ASSOC);

        if($emailResult){

            //to get user id and set as session variable
            $query1b = "SELECT user_id FROM users WHERE email = ?";
            $stmt1b = $db->prepare($query1b);
            $stmt1b->execute([$email]);
            $id = $stmt1b->fetch(PDO::FETCH_ASSOC);

            $_SESSION["id"] = $id; //session variable

           

            //to get username for email greeting

            $query2 = "SELECT username FROM users WHERE email = ?";
            $stmt2 = $db->prepare($query2);
            $stmt2->execute([$email]);
            $userResults = $stmt2->fetch(PDO::FETCH_ASSOC);

            $user = $userResults["username"];

            //proceed to check if there is already a token and pin in reset password table
            $query3 = "SELECT token, pin, token_created, token_expires FROM reset_password WHERE email = ?";
            $stmt3 = $db->prepare($query3);
            $stmt3->execute([$email]);
            $tokens = $stmt3->fetch(PDO::FETCH_ASSOC);
        
            //declaring empty array variables, if the results for the query above exists the arrays will have values equal to that, if they don't then they will be newly inserted into the database
            $token = [];
            $pin = [];
            $tokenCreated = [];
            $tokenExpires = [];
    
            if(!$tokens){

                //create new values if not already available
                $token = bin2hex(random_bytes(15));
                $pin = random_int(10000,99999);
                $tokenCreated = date('Y-m-d H:i:s');
                $tokenExpires = date('Y-m-d H:i:s', strtotime('+ 1 hour', strtotime($tokenCreated)));
                    
                try {
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $insert = "INSERT INTO reset_password (email,token, pin, token_created, token_expires, token_used)
                    VALUES ('$email','$token', '$pin', '$tokenCreated', '$tokenExpires', '1')";
                    $db->exec($insert);
        
            
                }catch(PDOException $e){
                    echo $insert . "<br>" . $e->getMessage();
                }

                
            }else{
                //if there are values available in table then check if expired
                $token = $tokens["token"];
                $pin = $tokens["pin"];
                $tokenCreated = $tokens["token_created"];
                $tokenExpires = $tokens["token_expires"];

                $now = time();
                $timePassed = $now - strtotime($tokenCreated);

                if($timePassed = 3600 && $timePassed > 3600){
                    //delete entry if available and redirect to sign in page
                    $delete = "DELETE FROM reset_password WHERE email = ?";
                    $stmt4 = $db->prepare($delete);
                    $stmt4->execute([$email]);

                    header("Location:./index.html");
                }
            }
            
            echo "<li style= 'color:green;'>An email was sent to your inbox to reset password</li>";
            //send email to user if all verification successful, send link with token and pin
            $phpmailer = new PHPMailer\PHPMailer\PHPMailer();
            $phpmailer->isSMTP();
            $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Port = 2525;
            $phpmailer->Username = $_ENV['PHPMAILER_USER'];
            $phpmailer->Password = $_ENV['PHPMAILER_PASSWORD'];

            $phpmailer->addAddress($email);

            $phpmailer->isHTML(true);
            $phpmailer->Subject = "<p>Forgotten Password</p>";
            $phpmailer->Body = "<div>
            <h3>Hi $user.</h3>
            <p>Click the <a href= 'http://localhost:81/final-project/set-password.php?token=$token&pin=$pin'>link</a> to reset your password</p>

            <h4>Yours in flavor, <br>
            The Recipe Ranch Team.</h4>
            <img src='../media/branding/logo.png'>
            </div>";

            /*if (!$phpmailer->send()) {
                echo "Oops, we are currently unable to send the email";

            }else{
                echo "<li style= 'color:green;'>An email was sent to your inbox to reset password</li>";
            }*/
    

        }else{
            echo "<li style= 'color: red;'>Email not valid</li>";
        }
    die();
        
    }
    die();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Email</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>
<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <div id="drop-down"><i class="fa-solid fa-bars"></i></div>
        <nav id="bar">
            <a href="./about.html">About</a>
            <a href="./contact.html">Contact</a>
            <a href="./sign-up.php">Sign-up</a>
        </nav>
    </header>

    

    <section>

        <main class="emailReset">
                <div>
                    <div class="formHead">
                        <h4>Enter your email below</h4>
                    </div>
                    <form method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label><br>
                            <input type="email" name="email" id="email" class="form-control">
                        </div>
                    </form>
                    <button id="resetButton">Reset password</button>
                    <div id="loader"></div>
                    <div class="msg">
                    </div>
                    <h3>Don't have an account?</h3>
                    <a href="./sign-up.php">Sign up</a>
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
    <script src="./js/forgotten-password.js"></script>
<!--Make sure in the email.js script you enter the code for toggling the nav menu when screen is smaller-->
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
</body>
</html>
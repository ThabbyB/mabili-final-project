<?php
session_start();
require "./includes/connection.php";

//if the user has input, do the following
if(isset($_POST["user"]) && isset($_POST["pwd"])){

    $userName = trim($_POST["user"]);
    $pword = $_POST["pwd"];

    //regex to check if the password meets the requirements
    $pwdLower = "/[a-z]/";
    $pwdUpper = "/[A-Z]/";
    $pwdDigit = "/\d/";
    $pwdSpecial = "/[\W_]/";

    //empty message array before input
    $msg = [];

    if(strlen($userName) < 4 ){
        $msg = "Username should be at least 4 characters long";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(!preg_match($pwdLower, $pword)){
        $msg = "Your password should contain at least one lowercase letter";
        echo "<li style= 'color: red;'>$msg</li>";
    }

    if(!preg_match($pwdUpper, $pword)){
        $msg = "Your password should contain at least one uppercase letter";
        echo "<li style= 'color: red;'>$msg</li>";
    }

    if(!preg_match($pwdDigit, $pword)){
        $msg = "Your password should contain at least one number/digit";
        echo "<li style= 'color: red;'>$msg</li>";
    }

    if(!preg_match($pwdSpecial, $pword)){
        $msg = "Your password should contain at least one special character";
        echo "<li style= 'color: red;'>$msg</li>";
    }
    
    if(empty($msg)){ //if message array empty, continue to validate input against database info

        //to check if username exists
        $query1 = "SELECT username FROM users WHERE username = ?";
        $stmt1 = $db->prepare($query1);
        $stmt1->execute([$userName]);
        $resUser = $stmt1->fetch(PDO::FETCH_ASSOC);


        if($resUser){
            //to check if the username that exits has a matching passcode in the database
            $query2 = "SELECT pass_code  FROM users WHERE username= '$userName'";
            $stmt2 = $db->prepare($query2);
            $stmt2->execute();
            $resPwd = $stmt2->fetch(PDO::FETCH_ASSOC);
            $dbPwd = implode($resPwd);
            
            //to verify if the enetered password matches the one in the database with the respective username
            if(password_verify($pword, $dbPwd)){
                echo "<p style= 'color:green;'>Successful log in!</p>";

                //getting the user id to create a session variable $id which can be used to retrieve the other user info and output the info on the dashboard (eg.to output the username)
                $query3 = "SELECT user_id FROM users WHERE username= '$userName'";
                $stmt3 = $db->prepare($query3);
                $stmt3->execute();
                $id = $stmt3->fetch(PDO::FETCH_ASSOC);

                $_SESSION["id"] = $id;

            }else{
                echo "<p style= 'color: red;'>Invalid credentials</p>";
                
            }

    }
    else{
        echo "<p style= 'color: red;'>Invalid credentials</p>";
    }
    }
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Ranch</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>

<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <div id="drop-down"><i class="fa-solid fa-bars"></i></div>
        <nav id="bar">
            <a href="./about.html">About Us</a>
            <a href="./contact.html">Contact</a>
            <a href="./sign-up.php">Sign-up</a>
        </nav>
    </header>
    
    <section>
        <main class="signIn">
            <div>
                <div class="formHead">
                    <h4>Sign in below to explore our <em>easy-to follow</em> recipes</h4>
                </div>
                <form method="post">
                    <div class="mb-3" input-group>
                        <label for="username" class="form-label">Username</label><br>
                        <input type="text" name="userName" id="userName" class="form-control">
                    </div>

                    <label for="password" class="form-label">Password</label>
                    <div class="mb-3 input-group">
                        <input type="password" name="password" id="password" class="form-control"aria-describedby="viewPword">
                        <button class="btn btn-outline-secondary" type="button" id="viewPword"><i class="fa-solid fa-eye-slash" id="pwordEye"></i></button>
                    </div>

                </form>
                <button id="signinButton">Sign in</button>
                <div class="msg">
                    <a href="./forgotten-password.php">Forgot password?</a>
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
    <script src="./js/sign-in.js"></script>
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
</body>
</html>
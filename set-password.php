<?php
require "./includes/connection.php";

//the pin and token are obtained from url using get method
if(isset($_GET["token"]) && isset($_GET["pin"])){
    $token = $_GET["token"];
    $pin = $_GET["pin"];

    //using pin to get time token was created from db
    $query1 = "SELECT token_created FROM reset_password WHERE pin = ? ";
    $stmt1 = $db->prepare($query1);
    $stmt1->execute([$pin]);
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    //if the value of token created is valid then check if it has expired (after 1 hour)
    if($result1){
        $tokenCreated = $result1["token_created"];
        $now = time();
        $timePassed = $now - strtotime($tokenCreated);//$tokenCreated stored in db in datetime format so it needs to be coverted to unix timestamp using strtotime() function
        
        if($timePassed = 3600 && $timePassed > 3600){
            header("Location:./whoops.html");//redirect to sign in page if token expired
        }
            
    }

    }

    if(isset($_POST["new"]) && isset($_POST["confirm"])){
        $newPword = $_POST["new"];
        $confirmPword = $_POST["confirm"];
    
        //password validation regex
        $pwdLower = "/[a-z]/";
        $pwdUpper = "/[A-Z]/";
        $pwdDigit = "/\d/";
        $pwdSpecial = "/[\W_]/";
        
        //empty $msg array
        $msg = [];
    
        //checking for password requirements
        if(strlen($newPword) < 8  || strlen($confirmPword) < 8){
            $msg = "<li style = 'color: red;'>Your password should be at least 8 characters long.</li>"; //msg to be displayed for user
        }
    
        if(!preg_match($pwdLower, $newPword) || !preg_match($pwdLower, $confirmPword)){
            $msg = "<li style = 'color: red;'>Your password should contain at least one lowercase letter.</li>";
        }
    
        if(!preg_match($pwdUpper, $newPword) || !preg_match($pwdUpper, $confirmPword)){
            $msg = "<li style = 'color: red;'>Your password should contain at least one uppercase letter.</li>";
        }
    
        if(!preg_match($pwdDigit, $newPword) || !preg_match($pwdDigit, $confirmPword)){
            $msg = "<li style = 'color: red;'>Your password should contain at least one digit.</li>";
        }
    
        if(!preg_match($pwdSpecial, $newPword) || !preg_match($pwdSpecial, $confirmPword)){
            $msg = "<li style = 'color: red;'>Your password should contain at least one special character.</li>";
        }
    
        if($newPword !== $confirmPword){
            $msg = "<li style = 'color: red;'>Passwords should match.</li>";
        }
        
        //if there is no error message with regards to strength of password and passwords matching then continue to check if the password in databse and new one is not the same (if same send me=sg to user that it should not be the same), then alter password entry of user in database
        if(empty($msg)){

        $query2 = "SELECT pass_code FROM users WHERE user_id = ?";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute([$id]);
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        $dbPwd = implode($result2);

        $newPword = password_hash($confirmPword, PASSWORD_DEFAULT);

        if(password_verify($confirmPword, $dbPwd)){
            echo "<p style= 'color:red;'>Your new password should not be the same as the old one</p>";

        }else{
            $alter = "UPDATE users SET pass_code = '$newPword' WHERE user_id = ? ";
            $stmt3 = $db->prepare($alter);
            $stmt3->execute([$id]);

            echo "<p style= 'color:green;'>Your password has been changed successfully!</p>";
        }
    
        }else{
            echo $msg;
        }

        die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Password</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>

<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <div id="drop-down"><i class="fa-solid fa-bars"></i></div>
        <nav id="profileBar">
            <a href="profile.php">My Profile</a>
        </nav>
    </header>

    <section id="resetSection">
        <div>
            <div class="reset">
                
                <form method="post" id="form">
                    <img src="./media/branding/logo.png" alt="company logo" id="logo">
                    <div class="formHead">
                        <h5 id="resetTableHead">Reset your Password Below</h5>
                    </div>

                    <div class="mb-3">
                        <label for="new-password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new-password">
                    </div>

                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm-password">
                    </div>
                    <button class="btn btn-light" id="resetPwd">Reset Password</button>
                    <div class="space"></div>
                    <div id="msg"></div>
                </form>

            </div>
        </div>
    </section>

    <footer id="resetFooter"><h6>&copy;RecipeRanch 2024</h6></footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/set-password.js"></script>
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
</body>
</html>
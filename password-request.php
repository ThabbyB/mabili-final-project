<?php
    require "./includes/session.php";
    require "./includes/connection.php";

    //trying to get the email of the user using user_id
    $query = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result){
        $email = implode($result);

        //if email exists then check if the email has reset_password table data (i.e pin, token, etc)
        $query2 = "SELECT token, pin, token_created, token_expires FROM reset_password WHERE email = ?";
        $stmt2 = $db->prepare($query2);
        $stmt2->execute([$email]);
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        //declaring empty array variables, if the results for the query above exists the arrays will have values equal to that, if they don't then they will be newly inserted into the database
        $token = [];
        $pin = [];
        $tokenCreated = [];
        $tokenExpires = [];

        //generate new token and pin if they are not available already
        if(empty($result2)){
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
        //if results already exist (token, pin, etc) in table reset_password
        }else{
            $token = $result2["token"];
            $pin = $result2["pin"];
            $tokenCreated = $result2["token_created"];
            $tokenExpires = $result2["token_expires"];

        $now = time();

        $timePassed = $now - strtotime($tokenCreated); //$tokenCreated stored in db in datetime format so it needs to be coverted to unix timestamp using strtotime() function
        echo $timepassed;

        //if there is already a pin and a token then check if the token has expired (expires after 1hr = 3600s) if so, delete the row (a new one will be generated when page is reloaded)
        if($timePassed = 3600 && $timePassed > 3600){
            $delete = "DELETE FROM reset_password WHERE email = ?";
            $stmt3 = $db->prepare($delete);
            $stmt3->execute([$email]);
        }  
            
        }
        
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email sent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Serif:ital,opsz,wght@0,8..144,100..900;1,8..144,100..900&display=swap" rel="stylesheet">
    <style>
        h3{
            margin: 150px auto 0px auto;
            text-align: center;
            font-family: "Roboto Slab", serif;
            border: 2px solid black;
            background-color: #001233;
            color: #ffffff;
            height: 100px;
            width: 600px;
            font-size: 2em;
            padding: 10px;
            border-radius: 10px;
        }

        button{
            background-color: #001233;
            color: #ffffff;
            margin: 20px 0px 0px 700px;
        }

        #email{
            margin-top: 20px;
            text-align: center;
        }

        h2{
            font-size: 1.5em;
            padding-bottom: 10px;
        }

        @media(max-width: 900px){
            button{
                margin-left: 20px;
            }
        }

    </style>
    
</head>
<body>
    <h3>An email has been sent to your inbox to reset your password</h3>
    <button id="button">Click to view email</button>
    <div id="email">
        <h2>Hello <?=$email?>!</h2>
        <p>Click on the link to reset your password <a href="./set-password.php?token=<?=$token?>&pin=<?=$pin?>">Reset</a></p>
    </div>
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(function(){
            $("#email").hide();
            $("#button").on("click", function(){
                $("#email").toggle();
            });
        });
    </script>
</body>
</html>
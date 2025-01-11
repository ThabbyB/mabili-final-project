<?php
require "./includes/session.php";
require "./includes/connection.php";
$query = "SELECT username
    FROM users
    WHERE user_id = $id";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    if($results){

        $user = $results['username'];

    }else{
        echo "query error";
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="./css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>

<body>
    <section>
        <header>
            <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
            
            <div class="navi">
                <p class="greeting">Hello, <?=$user?></p>
                <nav>
                    <a href="./profile.php" id="profile">My Profile</a>
                    <a href="./contact.html">Contact</a>
                    <a href="./bye.php" id="signOut">Sign Out</a>
                </nav>
            </div>
        </header>
    </section>

    <section class="welcome">

        <div class="item-1">
            <h1>we are excited to see the recipes you will share!</h1>
            <h4>click <a href="./add-recipe.php">here</a> to share.</h4>
            <h2>or browse the categories below for some inspo.</h2>
        </div>

        <div class="item-2">
        </div>
    </section>

    <section class="cat-sec">
        <h3 id="cat">Categories</h3>
        <div class="categories">
            <a href="./category.php?catId=1" class="breakfast">Breakfast</a>
            <a href="./category.php?catId=2" class="lunch">Lunch</a>
            <a href="./category.php?catId=3" class="dinner">Dinner</a>
            <a href="./category.php?catId=4" class="snacks">Snacks</a>
        </div>
        
    </section>

    <section class="newsletter">
        <div class="nl">
            <h1>Don't miss out</h1>
            <h1>on weekily deliciousness!</h1>
            <h3>Subscribe to our newsletter.</h3>
            <input type="text" name="email" id="email">
            <button id="join">join</button>
            <div id="joinMsg">Thank you for joining!</div>
            <div id="noEmailMsg">Please enter email</div>
        </div>
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
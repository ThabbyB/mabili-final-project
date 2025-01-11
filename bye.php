<?php
session_start();
session_unset();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5; URL=./sign-in.php">
    <title>Goodbye!</title>
    <link href="./css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>
<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <div id="drop-down"><i class="fa-solid fa-bars"></i></div>
        <nav id="bar">
            <a href="./sign-up.php">Sign-up</a>
        </nav>
    </header>

    <section>
        <main class="signIn">
            <div id="redirect">
                <h2>Goodbye!</h2>
                <p>See you soon  <i class="fa-regular fa-face-smile-wink"></i></p>
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
    
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
</body>
</html>
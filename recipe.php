<?php
require "./includes/session.php";
require "./includes/connection.php";

if(isset($_GET["rid"])){

    $recipeId = $_GET["rid"];
    //query recipe info using recipe id
    $query = "SELECT r.recipe_name, r.recipe_pics, r.prep_time, r.serving, r.ingredients, r.method, c.category, c.category_id
    FROM categories c
    JOIN recipes r ON c.category_id = r.category_id
    WHERE recipe_id = $recipeId AND recipe_approved = 1";

    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);

    if($results){

        $recipeName = $results["recipe_name"];
        $recipePic = $results["recipe_pics"];
        $prepTime = $results["prep_time"];
        $serving = $results["serving"];
        $ingredients = $results["ingredients"];
        $method = $results["method"];
        $category = $results["category"];
        $categoryId = $results["category_id"];
    }

    $userQuery = "SELECT u.username
    FROM recipes r
    JOIN users u ON r.user_id = u.user_id
    WHERE recipe_id = $recipeId";

    $stmt2 = $db->prepare($userQuery);
    $stmt2->execute();
    $results2 = $stmt2->fetch(PDO::FETCH_ASSOC);

    $userName = implode($results2);

}else{
    header("Location:./whoops.html");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe</title>
    <link href="./css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="./media/branding/icon.png" rel="icon">
</head>

<body>
    <header>
        <h1 class="ranch"><strong>R</strong>ecipe<strong>R</strong>anch</h1>
        <nav>
            <a href="./bye.php">Sign-out</a>
        </nav>
    </header>

    <section class="recipe-section">

        <div id="recipeImage">
            <img src="<?=$recipePic?>" alt="recipe image">
        </div>
        <div class="recipe-content">

            <div class="heading">
                <h1><?=$recipeName?></h1>
                <h5><i class="fa-regular fa-clock icon"></i><?=$prepTime?></h5>
                <h5><i class="fa-solid fa-bowl-food icon"></i><?=$serving?></h5>
                <h5><i class="fa-regular fa-user icon"></i>Author: <?=$userName?></h5>
            </div>

            <div class="recipe">

                <div id="ingredients">
                    <h4>Ingredients</h4>
                    <div><?=$ingredients?></div>
                </div>
                
                <div id="method">
                    <h4>Method</h4>
                    <div><?=$method?></div>
                </div>

            </div>

            <div id="recipeOptions">
                <a href="./category.php?catId=<?=$categoryId?>">Browse Category</a>
                <a href="./add-recipe.php">Add Recipe</a>
            </div>

        

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
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>-->
</body>
</html>
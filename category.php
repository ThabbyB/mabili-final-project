<?php
require "./includes/session.php";
require "./includes/connection.php";

    if(isset($_GET["catId"])){

        $catId = $_GET["catId"];

        $queryRecipeNames = "SELECT recipe_name
        FROM recipes
        WHERE category_id = $catId AND recipe_approved = '1'";
    
        $stmt1 = $db->prepare($queryRecipeNames);
        $stmt1->execute();
        $recipeNames = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        

        $queryRecipePics = "SELECT recipe_pics
        FROM recipes
        WHERE category_id = $catId AND recipe_approved = '1'";

        $stmt2 = $db->prepare($queryRecipePics);
        $stmt2->execute();
        $recipePics = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $queryRecipeIds = "SELECT recipe_id
        FROM recipes
        WHERE category_id = $catId AND recipe_approved = '1'";

        $stmt3 = $db->prepare($queryRecipeIds);
        $stmt3->execute();
        $recipeIds = $stmt3->fetchAll(PDO::FETCH_ASSOC);

        $queryCategory = "SELECT category
        FROM categories
        WHERE category_id = $catId";
    
        $stmt4 = $db->prepare($queryCategory);
        $stmt4->execute();
        $results = $stmt4->fetch(PDO::FETCH_ASSOC);

        $category = implode($results);

    }else{
        header("Location:./whoops.html");
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category</title>
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

    <section id="categorySection">
        <h1><?=$category?> Recipes</h1>
        <div id="categoryRecipes">
            <?php
            $recipePic = [];
            $recipeName = [];
            $recipeId = [];
            if($recipePics){
                $recipePicsCount = count($recipePics);
                
                if($recipePicsCount > 1){
                    for($x = 0; $x<$recipePicsCount; $x++){
                        $recipePic = implode($recipePics[$x]);
                        $recipeName = implode($recipeNames[$x]);
                        $recipeId = implode($recipeIds[$x]);
                        echo "<div><img src= '$recipePic'><br><p><a href= './recipe.php?rid=$recipeId'>$recipeName</a></p></div>";
                    }
                }

                if($recipePicsCount === 1){
                    $recipePic = implode($recipePics[0]);
                    $recipeName = implode($recipeNames[0]);
                    $recipeId = implode($recipeIds[0]);
                    echo "<div><img src= '$recipePic'><br><p><a href= './recipe.php?rid=$recipeId'>$recipeName</a></p></div>";
                }
            }
            ?>
            
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
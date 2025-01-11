<?php
require "./includes/session.php";
require "./includes/connection.php";

$query1 = "SELECT recipe_name
FROM recipes
WHERE user_id = $id AND recipe_approved = '1'"; //only query recipes that have been approved 

$stmt1 = $db->prepare($query1);
$stmt1->execute();
$results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$query2 = "SELECT recipe_id
FROM recipes
WHERE user_id = $id AND recipe_approved = '1'";

$stmt2 = $db->prepare($query2);
$stmt2->execute([]);
$results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

    <section class="profile">
        <h1>My Recipes</h1>
        <div class="userInfo">
            <ul>
                <?php
                //$recipes = null;

                if($results1 && $results2){ //loop through recipe names as well as the recipe ids to use in the url

                    $recipeNumber = count($results1); // to check for the number of recipes available for user and loop through them if more than one
                    $recipeIdNumber = count($results2);

                
                    if($recipeNumber > 1 && $recipeIdNumber > 1){
                        for($x = 0; $x<$recipeNumber; $x++){ //where $x is the index value
                            $recipes = implode($results1[$x]);
                            $recipeIDs = implode($results2[$x]);
                            echo "<li style= 'color: rgb(32, 139, 58);'><a href='./recipe.php?rid=$recipeIDs' style= 'color: rgb(32, 139, 58);'>$recipes</a></li>";
                        } 
                     
                    }
                    
                    if($recipeNumber === 1 && $recipeIdNumber === 1){ //if user only has one recipe in the database
                        $recipes = implode($results1[0]); //does not show on page if index value not specified
                        $recipeIDs = implode($results2[0]);
                        echo "<li><a href='./recipe.php?rid=$recipeIDs'>$recipes</a></li>";
                    }
                
                }else{
                    $recipes = "<h2>You currently don't have any recipes</h2>"; //if there user does not have any recipes
                    echo $recipes;
                }
                
                ?>
            </ul>
        </div>

        <div class="links">
            <a href="./add-recipe.php" class="addRecipe">Add Recipe</a>
            <a href="./password-request.php" class="reset">Reset Password</a>
            <a href="./dashboard.php" class="category">Categories</a>
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
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>     
</body>
</html>
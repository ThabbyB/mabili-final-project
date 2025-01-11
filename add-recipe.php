<?php
require "./includes/session.php";
require "./includes/connection.php";

if(isset($_POST["recipe"]) && isset($_POST["cat"]) && isset($_POST["time"]) && isset($_POST["serve"]) && isset($_POST["ingre"]) && isset($_POST["met"]) && isset($_POST["img"])){
    $recipeName = $_POST["recipe"];
    $category = $_POST["cat"];
    $prepTime = $_POST["time"];
    $serving = $_POST["serve"];
    $ingredients = $_POST["ingre"];
    $method = $_POST["met"];
    $recipeImage = $_POST["img"];

    $digits = "/\d/";
    $specialCharCheck = "/[^a-zA-Z\d\s]/";
    /*$singleQuotes = "/[^']/";
    $doubleQuotes = '/[^"]/';*/

    $msg = [];

    if(preg_match($digits, $recipeName)){
        $msg = "Your recipe name should not contain numbers";
        echo $msg;
    }

    if(preg_match($specialCharCheck, $recipeName)){
        $msg = "Your recipe name should not contain any special characters";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(preg_match($specialCharCheck, $prepTime)){
        $msg = "The prep time should not contain any special characters";
        echo "<li style='color: red;'>$msg</li>";
    }

    if(preg_match($specialCharCheck, $serving)){
        $msg = "The serving should not contain any special characters";
        echo "<li style='color: red;'>$msg</li>";
    }



    if(empty($msg)){
        //for category id
        $queryCategory = "SELECT category_id FROM categories WHERE category = ?";
        $stmt1 = $db->prepare($queryCategory);
        $stmt1->execute([$category]);
        $results1 = $stmt1->fetch(PDO::FETCH_ASSOC);

        $categoryId = implode($results1);


        //to send email
        $queryEmail = "SELECT email, username FROM users WHERE user_id = $id";
        $stmt2 = $db->prepare($queryEmail);
        $stmt2->execute();
        $results2 = $stmt2->fetch(PDO::FETCH_ASSOC);

        $email = $results2["email"];
        $userName = $results2["username"];

      
        try {
            $insert = "INSERT INTO recipes (recipe_name, prep_time, serving, ingredients, method, category_id, user_id, recipe_approved, recipe_pics)
            VALUES ('$recipeName', '$prepTime', '$serving', '$ingredients', '$method', '$categoryId', '$id', '1', '$recipeImage')";
            $db->exec($insert);
    
        }catch(PDOException $e){
            echo $insert . "<br>" . $e->getMessage(); //catch error in case the query fails
        }
        echo "<p style='color: green;'>Yay! Your recipe has been sent and waiting for approval.</p>";
        //send email using mailtrap
        $phpmailer = new PHPMailer\PHPMailer\PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = $_ENV['PHPMAILER_USER'];
        $phpmailer->Password = $_ENV['PHPMAILER_PASSWORD'];
    
        $phpmailer->addAddress($email);
    
        $phpmailer->isHTML(true);
        $phpmailer->Subject = "<p>You have added a new recipe</p>";
        $phpmailer->Body = "<div>
        <h3>Hi $userName.</h3>
        <p style= 'padding-bottom: 20px;'>You have added a new recipe to your profile and it is waiting for approval. If you do not recognize this activity please contact us on support@reciperanch.com</p>
    
        <h4>Yours in flavor, <br>
        The Recipe Ranch Team.</h4>
        <img src='../media/branding/logo.png'>
        </div>";
    
        /*if (!$phpmailer->send()) {
            echo "Oops, we are currently unable to send the email";

        }else{
            echo "<p style='color: green;'>Yay! Your recipe has been sent and waiting for approval.</p>";
        }*/
        
       
    }else{
        echo "<p style= 'All fields are required'></p>";
    }

    die();
}



?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Recipe</title>
        <link href="./css/style.css" rel="stylesheet">
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
        
        <section>
            <div class="modal" id="modalPop">
                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Do you wish to add this image?</h4>
                            <button type="button" class="btn-close closeX" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <img src="#" id="chosenImage" style="width:300px; height:200px; margin-left: 50px;">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success modalOption" data-bs-dismiss="modal" value="yes">yes</button>
                            <button type="button" class="btn btn-danger modalOption" data-bs-dismiss="modal" value="no">no</button>
                        </div>

                    </div>
                </div>
            </div>
            <main class="addRecipeSection" >
                <div>
                    <div class="formHead">
                        <h4>Add recipe below</h4>
                    </div>
                    <form method="post">
                        <div class="mb-3">
                            <label for="recipeName" class="form-label label">Recipe Name</label><br>
                            <input type="text" name="recipeName" id="recipeName" class="form-control input">
                        </div>
                        <br>
                        <span class="label">Choose Category</span>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="Breakfast" id="breakfast">
                            <label class="form-check-label" for="breakfast">Breakfast</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="Lunch" id="lunch">
                            <label class="form-check-label" for="lunch">Lunch</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="Dinner" id="dinner">
                            <label class="form-check-label" for="dinner">Dinner</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="category" value="Snacks" id="snacks">
                            <label class="form-check-label" for="snacks">Snacks</label>
                        </div>
                        <br>
                        <div class="mb-3">
                            <label for="prepTime" class="form-label label">Preparation Time</label><br>
                            <input type="text" name="prepTime" id="prepTime" class="form-control input">
                        </div>

                        <div class="mb-3">
                            <label for="serving" class="form-label label">Serving</label><br>
                            <input type="text" name="serving" id="serving" class="form-control input">
                        </div>

                        <div class="mb-3">
                            <label for="ingredients" class="form-label label">Ingredient List</label><br>
                            <textarea id="ingredients" name="ingredients" class="form-control input" rows="15" cols="50"></textarea>
                        </div>

                
                        <div class="mb-3">
                            <label for="method" class="form-label label">Method</label><br>
                            <textarea id="method" name="method" class="form-control input" rows="15" cols="50"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label label"> Enter Image link</label><br>
                            <input type="text" name="image" id="image" class="form-control input">
                        </div>

                    </form>
                    <button type="button" id="addRecipeButton" data-bs-toggle="modal" data-bs-target="#modalPop">Add recipe</button>
                    <div id="loader"></div>
                    <div class="msg"></div>
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
    <script src="./js/add-recipe.js"></script>
    <script src="https://kit.fontawesome.com/12b985fac2.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
 
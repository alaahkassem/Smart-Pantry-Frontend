<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$mood_sql = "SELECT * FROM mood";
$mood_result = mysqli_query($con, $mood_sql);

$selected_mood = isset($_GET['mood_id']) ? $_GET['mood_id'] : null;

$recipes = null;

if ($selected_mood) {
    $recipe_sql = "SELECT * FROM recipes
     JOIN mood_recipe WHERE recipes.id_recipe=mood_recipe.id_recipe AND
    id_mood= '$selected_mood'";
    $recipes = mysqli_query($con, $recipe_sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mood | Smart Pantry Chef</title>
    <link rel="stylesheet" href="mood.css">
    <link rel="website icon" type="png" href="logo.png">
</head>
<body>
<div class="container">
    <header>
       
        <nav class="navbar">
    <a href="logout.php">
          <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;margin:0;padding:0;display:block;">         
</a>  
          <a href="home.php">Home</a>
             <a href="meals.php">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php">Recipes</a>
            <a href="matching.php">Matching</a>
         <a href="budget.php">Budget</a>
           <a href="mood.php" class="active">Mood</a>
           <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
            <a href="supermarket.php">Market</a>
        </nav>
    </header>

    <hr>

   
    <h1>Mood Based Meals</h1>

    <h2>How are you feeling today?</h2>

    <p>Your mood deserves the perfect meal! Tell us how you're feeling, and we'll suggest recipes that match your emotional state.</p>

    
    <div class="mood-buttons">
        <?php
        if (mysqli_num_rows($mood_result) > 0) {
            while ($mood = mysqli_fetch_assoc($mood_result)) {
        ?>
            <a href="mood.php?mood_id=<?php echo $mood['id_mood']; ?>">
                <button><?php echo $mood['name_mood']; ?></button>
            </a>
        <?php
            }
        } else {
            echo "No moods found in the database.";
        }
        ?>
    </div>

    
    <?php if ($selected_mood) { ?>

        <h3>Recipes for your mood</h3>

        <div class="recipe-container" >

            <?php
            if ($recipes && mysqli_num_rows($recipes) > 0) {
                while ($row = mysqli_fetch_assoc($recipes)) {
            ?>
                <div class="recipe-card">
                    <img src="Image project/<?php echo $row['image']; ?>" 
                        alt="<?php echo $row['name_recipe']; ?>" 
                        style="width:100%; height:150px; object-fit:cover;">

                    <h4><?php echo $row['name_recipe']; ?></h4>

                    <br><br>
                    <a href="recipe_detail.php?id_recipe=<?php echo $row['id_recipe']; ?>">View Recipe</a>
                </div>

            <?php
                }
            } else {
                echo "No recipes found for this mood.";
            }
            ?>

        </div>

    <?php } ?>

</body>
</html>
<?php
session_start();
include 'db.php';

$cat_sql = "SELECT * FROM categories";
$categories = mysqli_query($con, $cat_sql);

$recipe_sql = "SELECT * FROM recipes";
$recipes = mysqli_query($con, $recipe_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | Smart Pantry Chef</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</head>
<body>

<div class="container">
    <header>
        <nav class="navbar">
        <a href="logout.php">    <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;margin:0;padding:0;display:block;">  </a>        
            <a href="home.php" class="active">Home</a>
            <a href="meals.php">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php">Recipes</a>
            <a href="matching.php">Matching</a>
            <a href="budget.php">Budget</a>
            <a href="mood.php">Mood</a>
            <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
                 <a href="supermarket.php">Market</a>

        </nav>
    </header>

    <hr>
    <h1>Discover Delicious Recipes</h1>

    <h3>Categories</h3>
    <div class=cat>
        <?php
        if(mysqli_num_rows($categories) > 0){
            while($cat = mysqli_fetch_assoc($categories)){
        ?>
            <a href="recipes.php?id_category=<?php echo $cat['id_category']; ?>">
                <button><?php echo $cat['name_category']; ?></button>
            </a>
        <?php
            }
        } else {
            echo "No categories found.";
        }
        ?>
    </div>

    <hr>

    <h3>All Recipes</h3>

    <div style="display:flex; flex-wrap:wrap; gap:20px; max-width:900px;">

        <?php
        if(mysqli_num_rows($recipes) > 0){
            while($row = mysqli_fetch_assoc($recipes)){
        ?>
            <!-- Single recipe card -->
            <div style="
                width: 30%;
                border: 1px solid #ccc;
                padding: 10px;
            ">
                <img src="Image project/<?php echo $row['image']; ?>" 
                     alt="<?php echo $row['name_recipe']; ?>" 
                     style="width:100%; height:150px; object-fit:cover;">
<a href="favorite.php?id_recipe=<?php echo $row['id_recipe']; ?>" title="Add to Favorites">
  <span class="material-symbols-outlined">favorite</span>
</a>
                <h4><?php echo $row['name_recipe']; ?></h4>
                
                <?php if($row['health_type'] == 1){ ?>
                    <span>Healthy</span>
                <?php } ?>

                <br><br>
                <a href="recipe_detail.php?id_recipe=<?php echo $row['id_recipe']; ?>">View Recipe</a>

            </div>
        <?php
            }
        } else {
            echo "No recipes found.";
        }
        ?>
    </div>
</div>
</body>
</html>
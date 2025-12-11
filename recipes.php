<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
$id_category = isset($_GET['id_category']) ? $_GET['id_category'] : "";

$recipeQuery = "SELECT * FROM recipes WHERE 1=1";
if($id_category != "") $recipeQuery .= " AND id_category='$id_category'";
if($searchTerm != "") $recipeQuery .= " AND name_recipe LIKE '%$searchTerm%'";

$recipeResult = mysqli_query($con, $recipeQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recipes</title>
<link rel="stylesheet" href="recipes.css">
</head>

<body>
<div class="container">
    <header>
        <nav class="navbar">
            <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;">
            <a href="home.php">Home</a>
            <a href="meals.php">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php" class="active">Recipes</a>
            <a href="matching.php">Matching</a>
            <a href="budget.php">Budget</a>
            <a href="mood.php">Mood</a>
            <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
        </nav>
    </header>

    <h2>Find Your Favorite Recipe 😋</h2>

    <div class="search-area">
        <form method="get">
            <input type="text" class="search-box" name="search" placeholder="Search recipe..." value="<?php echo $searchTerm; ?>">
            <button class="search-btn">Search</button>
        </form>
    </div>

    <div class="cards-container">
        <?php
        if($recipeResult && mysqli_num_rows($recipeResult) > 0){
            while($recipe = mysqli_fetch_assoc($recipeResult)){
                $img = "Image project/" . $recipe['image'];
        ?>
            <a href="recipe_detail.php?id_recipe=<?php echo $recipe['id_recipe']; ?>" class="card">
                <img src="<?php echo $img; ?>" alt="<?php echo $recipe['name_recipe']; ?>">
                <h4><?php echo $recipe['name_recipe']; ?></h4>
                <div class="details-box">
                    <span>
                        <?php
                        if(strtolower($recipe['health_type']) == "healthy"){
                            echo "🌿 Healthy";
                        } else {
                            echo "🍗 Regular";
                        }
                        ?>
                    </span>
                    <span>🔥 <?php echo $recipe['calories']; ?> kcal</span>
                </div>
            </a>
        <?php
            }
        } else {
            echo "<p style='text-align:center;color:#777;font-size:18px;'>No recipes found.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>

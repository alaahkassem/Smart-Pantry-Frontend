<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

/* ------- DELETE FROM HISTORY -------- */
if(isset($_GET['remove'])){
    $id_history = intval($_GET['remove']);
    mysqli_query($con, "DELETE FROM history WHERE id_history='$id_history' AND id_user='$id_user'");
    header("Location: history.php");
    exit();
}

/* ------- GET ALL HISTORY DATA -------- */
$query = "
SELECT history.id_history, history.date, recipes.id_recipe, recipes.name_recipe, recipes.image 
FROM history
JOIN recipes ON history.id_recipe = recipes.id_recipe
WHERE history.id_user = '$id_user'
ORDER BY history.date DESC
";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="fav_hist.css">
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
            <a href="mood.php">Mood</a>
            <a href="history.php" class="active">History</a>
            <a href="favorite.php">Favorites</a>
            <a href="supermarket.php">Market</a>
        </nav>
    </header>
<hr>
    <h2 class="title">Your History 🕘</h2>

    <div class="list">
        <?php
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
        ?>
        <div class="card">
            <img src="Image project/<?php echo $row['image']; ?>" class="img">

            <div class="info">
                <h3><?php echo $row['name_recipe']; ?></h3>
                <p>Viewed on: <?php echo $row['date']; ?></p>
            </div>

            
            <a href="history.php?remove=<?php echo $row['id_history']; ?>" class="remove">✖</a>

            <a href="recipe_detail.php?id_recipe=<?php echo $row['id_recipe']; ?>" class="view">View Recipe</a>
        </div>
        <?php }} else { ?>

        <p style="text-align:center; color:#555;">No history yet.</p>

        <?php } ?>
    </div>

</div>

</body>
</html>
<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

/* ADD TO FAVORITE */
if(isset($_GET['id_recipe'])){
    $id_recipe = intval($_GET['id_recipe']);
    $sq = "INSERT INTO favorite (id_user, id_recipe, add_date) 
           VALUES ('$id_user', '$id_recipe', NOW())";
    mysqli_query($con, $sq);
}

/*DELETE FROM FAVORITE*/
if(isset($_GET['remove'])){
    $id_fav = intval($_GET['remove']);
    mysqli_query($con, "DELETE FROM favorite WHERE id_fav='$id_fav' AND id_user='$id_user'");
    header("Location: favorite.php");
    exit();
}

/* GET ALL FAVORITE RECIPES*/
$query = "
SELECT favorite.id_fav, favorite.add_date, recipes.name_recipe, recipes.image 
FROM favorite
JOIN recipes ON favorite.id_recipe = recipes.id_recipe
WHERE favorite.id_user = '$id_user'
ORDER BY favorite.add_date DESC
";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites</title>
    <link rel="stylesheet" href="fav_hist.css">
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
            <a href="mood.php">Mood</a>
            <a href="history.php">History</a>
            <a href="favorite.php" class="active">Favorites</a>
            <a href="supermarket.php">Market</a>
        </nav>
    </header>
<hr>
    <h2 class="title">Your Favorites ❤️</h2>

    <div class="list">
        <?php
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
        ?>
        <div class="card">
            <img src="Image project/<?php echo $row['image']; ?>" class="img">

            <div class="info">
                <h3><?php echo $row['name_recipe']; ?></h3>
                <p>Added on: <?php echo $row['add_date']; ?></p>
            </div>

            <a href="favorite.php?remove=<?php echo $row['id_fav']; ?>" class="remove">✖</a>
        </div>
        <?php }} else { ?>

        <p style="text-align:center; color:#555;">No favorites yet.</p>

        <?php } ?>
    </div>

</div>

</body>
</html>
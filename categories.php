<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id_meal'])){
    $meal_id = intval($_GET['id_meal']);
    $qr = "SELECT * FROM categories WHERE id_meal='$meal_id'";
} else {
    $qr = "SELECT * FROM categories";
}
$res = mysqli_query($con, $qr);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories</title>
    <link rel="stylesheet" href="categories.css">
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
                <a href="categories.php" class="active">Categories</a>
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

        <h2>Categories</h2>

        <div class="cards">
            <?php
            if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_assoc($res)){
            ?>
                <a href="recipes.php?id_category=<?php echo $row['id_category']; ?>" class="card">
                    <p><?php echo htmlspecialchars($row['name_category']); ?></p>
                </a>
            <?php
                }
            } else {
                echo "<p style='text-align:center; color:#555;'>No categories found for this meal.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
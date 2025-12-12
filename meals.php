<?php
session_start();
include 'db.php';
if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['id_user'];
$name = $_SESSION['user_name'];
$query = "SELECT * FROM users WHERE id_user='$user_id'";
$result = mysqli_query($con, $query);
$row=mysqli_fetch_array($result);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="meals.css">
    <title>Meals</title>
</head>
<body>

<div class="container">
        <header>
        <nav class="navbar">
       <a href="logout.php">       
        <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;margin:0;padding:0;display:block;">  
</a>
            <a href="home.php">Home</a>
             <a href="meals.php" class="active">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php">Recipes</a>
            <a  href="matching.php">Matching</a>
         <a href="budget.php">Budget</a>
           <a href="mood.php">Mood</a>
           <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
             <a href="supermarket.php">Market</a>
        </nav>
    </header>
    <hr>
     <h3>   Welcome <?php echo $row['name']; ?> 👋 </h3> 
        <h2>What do you want to cook today?</h2>
<div class="con">
    <a href="categories.php?id_meal=1" class="card">
        <div class="icon">🍳</div>
        <p>Breakfast</p>
    </a>

    <a href="categories.php?id_meal=2" class="card">
        <div class="icon">🍽️</div>
        <p>Lunch</p>
    </a>

    <a href="categories.php?id_meal=3" class="card">
        <div class="icon">🌙</div>
        <p>Dinner</p>
    </a>

</div>
</div>

</body>
</html>

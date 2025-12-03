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
      <div class="top">
        Welcome <?php echo $row['name']; ?> 👋  

</div>

    <a href="meals.php?type=breakfast" class="card">
        <div class="icon">🍳</div>
        <p>Breakfast</p>
    </a>

    <a href="meals.php?type=lunch" class="card">
        <div class="icon">🍽️</div>
        <p>Lunch</p>
    </a>

    <a href="meals.php?type=dinner" class="card">
        <div class="icon">🌙</div>
        <p>Dinner</p>
    </a>


</div>

</body>
</html>

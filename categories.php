<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}
if(!isset($_GET['id_meal'])){
    die("Meal type not specified.");
}
$meal_id = intval($_GET['id_meal']);
$qr="SELECT * FROM categories WHERE id_meal='$meal_id'";
$res=mysqli_query($con, $qr);

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
    <div  class="container">
        <div class="top">
            <h2>Choose a Category</h2>
        </div>
        <div class="cards">
            <?php
            if(mysqli_num_rows($res)>0){
            while($row=mysqli_fetch_array($res)){
                ?>
                <a href="recipes.php?id_category=<?php echo $row['id_category']; ?>" class="card">
                    <p><?php echo $row['name_category']; ?></p>
                </a>
    
                <?php
            }}
            ?>
        </div>
    </div>
</body>
</html>
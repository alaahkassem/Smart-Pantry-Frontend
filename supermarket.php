<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$id_recipe = isset($_GET['from_recipe']) ? (int)$_GET['from_recipe'] : 0;

if (!isset($_SESSION['extra_ingredients'])) {
    $_SESSION['extra_ingredients'] = [];
}

if(isset($_POST['add_to_cart'])){
    $id_item = (int)$_POST['id_item'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $check = mysqli_query($con, "SELECT * FROM cart WHERE id_user='$id_user' AND id_item='$id_item'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($con, "UPDATE cart SET quantity = quantity + $quantity WHERE id_user='$id_user' AND id_item='$id_item'");
    } else {
        mysqli_query($con, "INSERT INTO cart (id_user, id_item, quantity) VALUES ('$id_user', '$id_item', '$quantity')");
    }

    header("Location: supermarket.php");
    exit();
}

if(isset($_POST['add_extra'])){
    $id_item = (int)$_POST['id_item'];
    $name = mysqli_real_escape_string($con, $_POST['name_ingredient']);
    $_SESSION['extra_ingredients'][$id_item] = $name;

    if($id_recipe > 0){
        header("Location: recipe_detail.php?id_recipe=$id_recipe");
        exit();
    }
}

$supermarketQuery = "
    SELECT s.id_item, s.price, s.unit, i.name_ingredient, i.id_ingredient
    FROM supermarket s
    JOIN ingredient i ON s.id_ingredient = i.id_ingredient
";
$supermarketResult = mysqli_query($con, $supermarketQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Supermarket</title>
<link rel="stylesheet" href="supermarket.css">
<link rel="website icon" type="png" href="logo.png">
<style>

.button-group {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 8px;
}


.item-card button {
    padding: 6px 14px;
    font-size: 13px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s ease;
    background-color: #b94666; /
    color: white;
}

.item-card button:hover {
    background-color: #8e2f4b; 
}
</style>
</head>
<body>

<header>
    <div class="logo">Supermarket</div>
    <nav>
        <a href="home.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h1>Supermarket</h1>

    <div class="items-container">
    <?php
    if($supermarketResult && mysqli_num_rows($supermarketResult) > 0){
        while($item = mysqli_fetch_assoc($supermarketResult)){
            echo "<div class='item-card'>
                    <h3>".$item['name_ingredient']."</h3>
                    <p>Price: ".$item['price']." / ".$item['unit']."</p>
                    <div class='quantity' style='text-align:center; margin-bottom:10px;'>
                        <label for='quantity_".$item['id_item']."' style='font-size:12px;'>Qty:</label>
                        <input type='number' id='quantity_".$item['id_item']."' name='quantity'
                               value='1' min='1' max='99'
                               style='width:50px; height:28px; font-size:12px;'>
                    </div>";

           
            echo "<div class='button-group'>";

            echo "<form method='post'>
                    <input type='hidden' name='id_item' value='".$item['id_item']."'>
                    <input type='hidden' name='name_ingredient' value='".$item['name_ingredient']."'>
                    <button type='submit' name='add_to_cart'>Add to Cart</button>
                  </form>";

            
            if($id_recipe > 0){
                echo "<form method='post'>
                        <input type='hidden' name='id_item' value='".$item['id_item']."'>
                        <input type='hidden' name='name_ingredient' value='".$item['name_ingredient']."'>
                        <button type='submit' name='add_extra'>Add to Recipe</button>
                      </form>";
            }

            echo "</div>"; 
            echo "</div>"; 
        }
    } else {
        echo "<p style='text-align:center;color:#777;font-size:18px;'>No items found.</p>";
    }
    ?>
    </div>
</div>

<footer>
    &copy; 2025 Supermarket. All rights reserved.
</footer>

</body>
</html>

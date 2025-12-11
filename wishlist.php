<?php 
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if(isset($_GET['remove'])){
    $id_wish = (int)$_GET['remove'];
    mysqli_query($con, "DELETE FROM wishlist WHERE id_wish='$id_wish' AND id_user='$id_user'");
    header("Location: wishlist.php");
    exit();
}

if(isset($_GET['move_to_cart'])){
    $id_wish = (int)$_GET['move_to_cart'];
    $wishQuery = mysqli_query($con, "SELECT id_item FROM wishlist WHERE id_wish='$id_wish' AND id_user='$id_user'");
    if(mysqli_num_rows($wishQuery) > 0){
        $wishItem = mysqli_fetch_assoc($wishQuery);
        $id_item = (int)$wishItem['id_item'];
        $checkCart = mysqli_query($con, "SELECT * FROM cart WHERE id_user='$id_user' AND id_item='$id_item'");
        if(mysqli_num_rows($checkCart) > 0){
            mysqli_query($con, "UPDATE cart SET quantity=quantity+1 WHERE id_user='$id_user' AND id_item='$id_item'");
        } else {
            mysqli_query($con, "INSERT INTO cart (id_user, id_item, quantity) VALUES ('$id_user','$id_item',1)");
        }
        mysqli_query($con, "DELETE FROM wishlist WHERE id_wish='$id_wish' AND id_user='$id_user'");
    }
    header("Location: wishlist.php");
    exit();
}

$wishlistQuery = "
    SELECT w.id_wish, w.id_item, i.name_ingredient
    FROM wishlist w
    JOIN supermarket s ON w.id_item = s.id_item
    JOIN ingredient i ON s.id_ingredient = i.id_ingredient
    WHERE w.id_user='$id_user'
";
$wishlistResult = mysqli_query($con, $wishlistQuery);


$cartCountQuery = mysqli_query($con, "SELECT SUM(quantity) AS total FROM cart WHERE id_user='$id_user'");
$cartCountRow = mysqli_fetch_assoc($cartCountQuery);
$cartCount = $cartCountRow['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Wishlist</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>
body{
    font-family: 'Lora', serif;
    background:#ffe4ec; 
    margin:0;
    padding:0;
    color:#111;
}
.container{
    max-width:900px;
    margin:40px auto;
    background:#ffffff;
    padding:35px;
    border-radius:25px;
    box-shadow:0 12px 40px rgba(179,59,90,0.25);
    border:1px solid #f0c0d2;
}
h1{
    text-align:center;
    color:#222;
    margin-bottom:25px;
    font-family:'Nunito', sans-serif;
}
.cart-info{
    text-align:center;
    margin-bottom:25px;
    font-size:18px;
    color:#555;
    font-weight:600;
}
.wishlist-container{
    display:flex;
    flex-wrap:wrap;
    gap:25px;
    justify-content:center;
}
.item-card{
    background:#fff;
    border-radius:20px;
    width:220px;
    padding:25px 15px;
    text-align:center;
    box-shadow: 0 0 20px rgba(179,59,90,0.2),
                0 8px 25px rgba(179,59,90,0.2),
                inset 0 0 10px rgba(179,59,90,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border:1px solid #f0c0d2;
    position: relative;
}
.item-card::after {
    content:'';
    position:absolute;
    top:0; left:0; right:0; bottom:0;
    border-radius:20px;
    padding:2px;
    background: linear-gradient(135deg, #f57c99, #b33b5a, #ff8db0);
    -webkit-mask: 
        linear-gradient(#fff 0 0) content-box, 
        linear-gradient(#fff 0 0);
    -webkit-mask-composite: destination-out;
    mask-composite: exclude;
    pointer-events:none;
}
.item-card:hover{
    transform: translateY(-5px);
    box-shadow: 0 0 30px rgba(179,59,90,0.6), 0 12px 35px rgba(179,59,90,0.3), inset 0 0 15px rgba(179,59,90,0.1);
}
.item-card h3{
    margin:12px 0 10px;
    color:#111;
    font-size:20px;
    font-family:'Nunito', sans-serif;
}
.action-icons{
    display:flex;
    justify-content:center;
    gap:25px;
    margin-top:15px;
    font-size:36px;
}
.action-icons a{
    text-decoration:none;
    color:black;
    transition: all 0.3s ease;
}
.action-icons a:hover{
    color:#b33b5a;
    transform: scale(1.3);
}
.material-symbols-outlined {
    font-variation-settings:
    'FILL' 0,
    'wght' 700,
    'GRAD' 0,
    'opsz' 40;
}
.back-button {
    text-decoration: none;
    background: #b33b5a;
    color: white;
    padding: 10px 20px;
    border-radius: 15px;
    display: inline-block;
    margin-bottom: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(179,59,90,0.3);
    font-size: 16px;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.back-button:hover {
    background: #922b45;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(146,43,69,0.4);
}
@media (max-width:650px){
    .wishlist-container{
        flex-direction:column;
        align-items:center;
    }
}
</style>
</head>
<body>
<div class="container">
    <a class="back-button" href="recipe_detail.php?id_recipe=7">Back </a>

    <h1>My Wishlist</h1>
    <p class="cart-info">Items in Cart: <?php echo (int)($cartCount ?? 0); ?></p>

    <div class="wishlist-container">
    <?php
    if($wishlistResult && mysqli_num_rows($wishlistResult) > 0){
        while($row = mysqli_fetch_assoc($wishlistResult)){
            echo "<div class='item-card'>
                    <h3>".$row['name_ingredient']."</h3>
                    <div class='action-icons'>
                        <a href='wishlist.php?move_to_cart=".$row['id_wish']."' title='Move to Cart'>
                            <span class='material-symbols-outlined'>add_shopping_cart</span>
                        </a>
                        <a href='wishlist.php?remove=".$row['id_wish']."' title='Remove'>
                            <span class='material-symbols-outlined'>remove_shopping_cart</span>
                        </a>
                    </div>
                  </div>";
        }
    } else {
        echo "<p style='text-align:center;color:#888;'>Your wishlist is empty.</p>";
    }
    ?>
    </div>
</div>
</body>
</html>
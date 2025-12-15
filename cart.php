<?php 
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if(isset($_POST['remove'])){
    $id_cart = (int)$_POST['remove'];
    mysqli_query($con, "DELETE FROM cart WHERE id_cart='$id_cart' AND id_user='$id_user'");
    exit();
}

if(isset($_POST['update_quantity'])){
    foreach($_POST['quantities'] as $id_cart => $qty){
        $id_cart = (int)$id_cart;
        $qty = (float)$qty;
        if($qty < 1) $qty = 1;
        mysqli_query($con, "UPDATE cart SET quantity='$qty' WHERE id_cart='$id_cart' AND id_user='$id_user'");
    }
    header("Location: cart.php");
    exit();
}

if(isset($_POST['checkout'])){
    mysqli_query($con, "DELETE FROM cart WHERE id_user='$id_user'");
    echo "<script>alert('Checkout successful! Cart is now empty.'); window.location='recipes.php';</script>";
    exit();
}

$cartQuery = "
    SELECT 
        c.id_cart, 
        c.quantity, 
        s.price, 
        s.unit, 
        i.name_ingredient
    FROM cart c
    JOIN supermarket s ON c.id_item = s.id_item
    JOIN ingredient i ON s.id_ingredient = i.id_ingredient
    WHERE c.id_user='$id_user'
";
$cartResult = mysqli_query($con, $cartQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart</title>

<link rel="website icon" type="png" href="logo.png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=delete" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="cart.css">

<script>
function removeItem(id_cart) {
    fetch('cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'remove=' + id_cart
    }).then(() => {
        document.getElementById('row-' + id_cart).remove();
    });
}
</script>
</head>
<body>

<div class="container">
<p><a class="back-link" href="recipes.php">Back to Recipes</a></p>

<h1>My Cart</h1>

<form method="post">
<table>
<tr>
<th>Ingredient</th>
<th>Unit</th>
<th>Price</th>
<th>Quantity</th>
<th>Subtotal</th>
<th>Action</th>
</tr>

<?php
$total = 0;

if($cartResult && mysqli_num_rows($cartResult) > 0){
    while($row = mysqli_fetch_assoc($cartResult)){
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;

        echo "
        <tr id='row-".$row['id_cart']."'>
            <td>".$row['name_ingredient']."</td>
            <td>".$row['unit']."</td>
            <td>".$row['price']."</td>
            <td><input type='number' name='quantities[".$row['id_cart']."]' value='".$row['quantity']."' min='1'></td>
            <td>".($row['price'] * $row['quantity'])."</td>
            <td>
                <span onclick='removeItem(".$row['id_cart'].")' class='material-symbols-outlined'>delete</span>
            </td>
        </tr>";
    }
} else {
    echo "<tr>
            <td colspan='6' class='empty-cart'>Your cart is empty. 🛒</td>
          </tr>";
}
?>
</table>

<div style="text-align:right;margin-top:30px;">
    <button name="update_quantity">Update Quantities</button>
    <button name="checkout" type="submit">Checkout</button>
</div>
</form>

<p class="total"><strong>Total:</strong> <?php echo $total; ?></p>

</div>
</body>
</html>

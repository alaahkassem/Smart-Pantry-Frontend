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


<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=delete" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', Arial, sans-serif;
    background: linear-gradient(135deg, #fdf1f5 0%, #f8e4eb 100%);
    margin: 0;
    padding: 20px;
    color: #000;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    background: #ffffff;
    padding: 40px;
    border-radius: 25px;
    box-shadow: 0 20px 50px rgba(200, 74, 117, 0.25);
    border: 2px solid #f7d6e0;
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h1 {
    text-align: center;
    color: #000;
    font-size: 38px;
    margin-bottom: 30px;
    font-weight: 700;
    text-shadow: none;
}

table {
    width: 100%;
    border-collapse: collapse;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 35px rgba(200,74,117,0.2);
}

th, td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid #f1e4eb;
}

th {
    background: linear-gradient(135deg, #ffe4ef 0%, #f7c8d8 100%);
    color: #000;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 16px;
}

td {
    background: #fff0f5;
    color: #000;
    font-weight: 500;
}

tr:hover td {
    background: #ffeef2;
    transform: scale(1.02);
    box-shadow: 0 0 25px rgba(200,74,117,0.25);
    transition: all 0.3s ease;
}

input[type=number] {
    width: 70px;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 8px;
    text-align: center;
    font-size: 16px;
    font-weight: 500;
    transition: border-color 0.3s ease;
}

input[type=number]:focus {
    border-color: #c84a75;
    outline: none;
}

button {
    padding: 14px 28px;
    background: linear-gradient(135deg, #c84a75 0%, #a63d61 100%);
    color: white;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    margin: 5px;
    box-shadow: 0 6px 15px rgba(200, 74, 117, 0.3);
}

button:hover {
    background: linear-gradient(135deg, #a63d61 0%, #8a2e4a 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(200, 74, 117, 0.4);
}

.total {
    text-align: right;
    font-size: 28px;
    margin-top: 30px;
    color: #000;
    font-weight: 700;
}

.back-link {
    text-decoration: none;
    background: linear-gradient(135deg, #c84a75 0%, #a63d61 100%);
    color: white;
    padding: 14px 28px;
    border-radius: 12px;
    display: inline-block;
    margin-bottom: 30px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 15px rgba(200, 74, 117, 0.3);
}

.back-link:hover {
    background: linear-gradient(135deg, #a63d61 0%, #8a2e4a 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(200, 74, 117, 0.4);
}

.material-symbols-outlined {
    font-variation-settings:
    'FILL' 0,
    'wght' 400,
    'GRAD' 0,
    'opsz' 28;
    cursor: pointer;
    color: #000;
    transition: all 0.3s ease;
}

.material-symbols-outlined:hover {
    color: #c84a75;
    transform: scale(1.3);
}

.empty-cart {
    text-align: center;
    color: #555;
    padding: 50px;
    font-size: 20px;
}
</style>
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

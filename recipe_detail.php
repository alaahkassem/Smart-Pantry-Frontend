<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if(!isset($_GET['id_recipe'])){
    echo "Recipe not found.";
    exit();
}

$id_recipe = (int)$_GET['id_recipe'];

$recipeQuery = mysqli_query($con, "SELECT * FROM recipes WHERE id_recipe='$id_recipe'");
if(!$recipeQuery || mysqli_num_rows($recipeQuery) == 0){
    echo "Recipe not found.";
    exit();
}
$recipe = mysqli_fetch_assoc($recipeQuery);


$ingredientQuery = mysqli_query($con, "
    SELECT i.id_ingredient, i.name_ingredient, ri.quantity
    FROM recipes_ingredient ri
    JOIN ingredient i ON ri.id_ingredient = i.id_ingredient
    WHERE ri.id_recipe='$id_recipe'
");

$ingredients = [];
while($ing = mysqli_fetch_assoc($ingredientQuery)){
    $ingredients[] = $ing;
}


if(isset($_POST['add_to_cart'])){
    mysqli_query($con, "DELETE FROM cart WHERE id_user='$id_user'");
    foreach($ingredients as $ing){
        $id_ingredient = (int)$ing['id_ingredient'];
        $itemQuery = mysqli_query($con, "SELECT id_item FROM supermarket WHERE id_ingredient='$id_ingredient' LIMIT 1");
        if($itemQuery && mysqli_num_rows($itemQuery) > 0){
            $item = mysqli_fetch_assoc($itemQuery);
            $id_item = (int)$item['id_item'];
            mysqli_query($con, "INSERT INTO cart (id_user, id_item, quantity) VALUES ('$id_user','$id_item',1)");
        }
    }
    header("Location: cart.php");
    exit();
}


if(isset($_POST['add_to_wishlist'])){
    foreach($ingredients as $ing){
        $id_ingredient = (int)$ing['id_ingredient'];
        $itemQuery = mysqli_query($con, "SELECT id_item FROM supermarket WHERE id_ingredient='$id_ingredient' LIMIT 1");
        if($itemQuery && mysqli_num_rows($itemQuery) > 0){
            $item = mysqli_fetch_assoc($itemQuery);
            $id_item = (int)$item['id_item'];
            $check = mysqli_query($con, "SELECT * FROM wishlist WHERE id_user='$id_user' AND id_item='$id_item'");
            if(mysqli_num_rows($check) == 0){
                mysqli_query($con, "INSERT INTO wishlist (id_user, id_item) VALUES ('$id_user','$id_item')");
            }
        }
    }
    header("Location: wishlist.php");
    exit();
}


if(isset($_POST['add_single_wish'])){
    $id_item = (int)$_POST['single_id_item'];
    $check = mysqli_query($con, "SELECT * FROM wishlist WHERE id_user='$id_user' AND id_item='$id_item'");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($con, "INSERT INTO wishlist (id_user, id_item) VALUES ('$id_user','$id_item')");
    }
    header("Location: recipe_detail.php?id_recipe=$id_recipe");
    exit();
}


mysqli_query($con, "INSERT INTO history (id_user, id_recipe) VALUES ('$id_user','$id_recipe')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $recipe['name_recipe']; ?></title>
<link rel="website icon" type="png" href="logo.png">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<style>
body {
    margin: 0;
    font-family: 'Lora', serif;
    background: linear-gradient(135deg, #fce4ec, #fde2f2);
    color: #111;
}

.recipe-container {
    max-width: 900px;
    margin: 50px auto;
    background: #fff;
    padding: 35px;
    border-radius: 25px;
    border: 2px solid #e3b0c4;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    transition: transform 0.3s, box-shadow 0.3s;
}
.recipe-container p.description {
    font-size: 17px;           
    line-height: 1.8;         
    color: #111;              
    text-align: justify;      
    margin-bottom: 25px;       
    font-family: 'Nunito', sans-serif; 
}

.recipe-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.top-actions {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
}

.back-button, .small-button {
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 22px;
    color: #fff;
    border: 2px solid #b94666;
    text-decoration: none;
    transition: all 0.3s ease;
}

.back-button {
    background: #c35a78;
}
.back-button:hover {
    background: #a13d5c;
    border-color: #902e4f;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(161,61,92,0.4);
}

.small-button {
    background: #b94666;
}
.small-button:hover {
    background: #9f3853;
    border-color: #c14769;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(159,56,83,0.4);
}

.recipe-container h1 {
    font-size: 38px;
    text-align: center;
    margin-bottom: 20px;
    font-weight: 700;
    font-family: 'Nunito', sans-serif;
}

.recipe-container img {
    width: 100%;
    max-height: 420px;
    object-fit: cover;
    border-radius: 20px;
    margin-bottom: 25px;
    border: 2px solid #e3b0c4;
    transition: transform 0.3s ease;
}
.recipe-container img:hover {
    transform: scale(1.03);
}

.recipe-details {
    display: flex;
    justify-content: space-around;
    padding: 18px;
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e3b0c4;
    margin-bottom: 25px;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
}

.recipe-details p {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
    color: #000;
}

.ingredients h3 {
    font-size: 26px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 15px;
    color: #c35a78;
    position: relative;
}
.ingredients h3::after {
    content: "";
    display: block;
    width: 60px;
    height: 3px;
    background: #c35a78;
    margin: 8px auto 0;
    border-radius: 2px;
}

.ingredients ul {
    list-style: none;
    padding: 0;
}

.ingredients li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    margin-bottom: 12px;
    background: #fff;
    border-radius: 15px;
    border: 2px solid #e3b0c4;
    font-family: 'Nunito', sans-serif;
    font-size: 16px;
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}
.ingredients li:hover {
    background: #f9f9f9;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.ingredients li::before {
    content: "🍴";
    margin-right: 8px;
    color: #b94666;
}

.material-symbols-outlined {
    font-size: 28px;
    cursor: pointer;
    color: #c35a78;
    transition: color 0.3s, transform 0.3s;
}
.material-symbols-outlined:hover {
    color: #9f3853;
    transform: scale(1.2);
}

.actions {
    display: flex;
    justify-content: center;
    margin-top: 25px;
}
.actions button {
    padding: 14px 28px;
    background: #c35a78;
    color: white;
    font-weight: 600;
    border: 2px solid #b94666;
    border-radius: 15px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.actions button:hover {
    background: #a13d5c;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(161,61,92,0.4);
}

@media (max-width: 650px) {
    .recipe-details {
        flex-direction: column;
        gap: 12px;
    }
    .ingredients li {
        flex-direction: column;
        align-items: flex-start;
    }
    .actions button, .back-button, .small-button {
        width: 100%;
        text-align: center;
    }
}
</style>

</head>
<body>

<div class="recipe-container">

    <div class="top-actions">
        <a class="back-button" href="recipes.php?id_category=<?php echo $recipe['id_category']; ?>">Back to Recipes</a>
        <a class="small-button" href="wishlist.php">❤ Wishlist</a>
    </div>

    <h1><?php echo $recipe['name_recipe']; ?></h1>
    <img src="<?php echo str_replace(' ', '%20', "Image project/" . $recipe['image']); ?>" alt="<?php echo $recipe['name_recipe']; ?>">

    <div class="recipe-details">
        <p>🔥 <strong>Calories:</strong> <?php echo $recipe['calories']; ?> kcal</p>
        <p>⏱ <strong>Time Needed:</strong> <?php echo $recipe['time_needed']; ?></p>
        <p><strong>Level:</strong> <?php echo $recipe['level']; ?></p>
    </div>

    <h3>Description:</h3>
    <p class="description"><?php echo nl2br($recipe['description']); ?></p>


    <div class="ingredients">
        <h3>Ingredients:</h3>
        <ul>
        <?php
        foreach($ingredients as $ing){
            $id_ingredient = (int)$ing['id_ingredient'];
            echo "<li>";
            echo "<span>".$ing['quantity']." of ".$ing['name_ingredient']."</span>";
            echo "<form style='display:inline-block;' method='post'>
                    <input type='hidden' name='single_id_item' value='".$id_ingredient."'>
                    <button type='submit' name='add_single_wish' style='background:none; border:none; padding:0; cursor:pointer;'>
                        <span class='material-symbols-outlined' title='Add to wishlist'>favorite</span>
                    </button>
                  </form>";
            echo "</li>";
        }
        ?>
        </ul>
    </div>

    <div class="actions">
        <form action="" method="post">
            <button type="submit" name="add_to_cart">Add Ingredients to Cart</button>
        </form>
    </div>

</div>

</body>
</html>

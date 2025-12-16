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


if (!isset($_SESSION['extra_ingredients'])) {
    $_SESSION['extra_ingredients'] = [];
}

if (isset($_GET['remove_extra'])) {
    $remove_id = (int)$_GET['remove_extra'];
    unset($_SESSION['extra_ingredients'][$remove_id]);
    header("Location: recipe_detail.php?id_recipe=$id_recipe");
    exit();
}



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
        $q = mysqli_query($con, "SELECT id_item FROM supermarket WHERE id_ingredient='$id_ingredient' LIMIT 1");
        if($row = mysqli_fetch_assoc($q)){
            mysqli_query($con, "INSERT INTO cart (id_user,id_item,quantity) VALUES ('$id_user','".$row['id_item']."',1)");
        }
    }

    foreach($_SESSION['extra_ingredients'] as $extra_id => $name){
        $q = mysqli_query($con, "SELECT id_item FROM supermarket WHERE id_ingredient='$extra_id' LIMIT 1");
        if($row = mysqli_fetch_assoc($q)){
            mysqli_query($con, "INSERT INTO cart (id_user,id_item,quantity) VALUES ('$id_user','".$row['id_item']."',1)");
        }
    }

    header("Location: cart.php");
    exit();
}


if(isset($_POST['add_single_wish'])){
    $id_item = (int)$_POST['single_id_item'];
    $check = mysqli_query($con, "SELECT * FROM wishlist WHERE id_user='$id_user' AND id_item='$id_item'");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($con, "INSERT INTO wishlist (id_user,id_item) VALUES ('$id_user','$id_item')");
    }
    header("Location: recipe_detail.php?id_recipe=$id_recipe");
    exit();
}

mysqli_query($con, "INSERT INTO history (id_user,id_recipe) VALUES ('$id_user','$id_recipe')");
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
}

.back-button { background: #c35a78; }
.small-button { background: #b94666; }

.recipe-container h1 {
    font-size: 38px;
    text-align: center;
    margin-bottom: 20px;
    font-family: 'Nunito', sans-serif;
}

.recipe-container img {
    width: 100%;
    max-height: 420px;
    object-fit: cover;
    border-radius: 20px;
    margin-bottom: 25px;
}

.recipe-details {
    display: flex;
    justify-content: space-around;
    padding: 18px;
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e3b0c4;
    margin-bottom: 25px;
}

.ingredients h3 {
    font-size: 26px;
    text-align: center;
    margin-bottom: 15px;
    color: #c35a78;
}

.ingredients ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
}

.ingredients li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 18px;
    background: #fff;
    border-radius: 15px;
    border: 2px solid #e3b0c4;
}

.ingredient-actions button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.material-symbols-outlined {
    font-size: 26px;
    color: #c35a78;
    cursor: pointer;
}


.add-extra-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 15px;
}

.material-symbols-outlined.add-extra {
    font-size: 36px;
    color: #c35a78;
    cursor: pointer;
    transition: transform 0.3s, color 0.3s;
}
.material-symbols-outlined.add-extra:hover {
    color: #8e2f4b;
    transform: scale(1.3);
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
    border-radius: 15px;
    border: none;
    font-weight: 600;
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
    <img src="<?php echo str_replace(' ', '%20', "Image project/" . $recipe['image']); ?>">

    <div class="recipe-details">
        <p>🔥 Calories: <?php echo $recipe['calories']; ?> kcal</p>
        <p>⏱ Time Needed: <?php echo $recipe['time_needed']; ?></p>
        <p>Level: <?php echo $recipe['level']; ?></p>
    </div>

    <h3>Description:</h3>
    <p><?php echo nl2br($recipe['description']); ?></p>

    <div class="ingredients">
        <h3>Ingredients:</h3>
        <ul>
        <?php foreach($ingredients as $ing): ?>
            <li>
                <span><?php echo $ing['quantity']." of ".$ing['name_ingredient']; ?></span>
                <div class="ingredient-actions">
                    <form method="post">
                        <input type="hidden" name="single_id_item" value="<?php echo $ing['id_ingredient']; ?>">
                        <button type="submit" name="add_single_wish">
                            <span class="material-symbols-outlined">favorite</span>
                        </button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
        <?php if(!empty($_SESSION['extra_ingredients'])): ?>
            <?php foreach($_SESSION['extra_ingredients'] as $id => $name): ?>
                <li>
                    <span><?php echo $name; ?></span>
                    <div class="ingredient-actions">
                        <form method="post">
                            <input type="hidden" name="single_id_item" value="<?php echo $id; ?>">
                            <button type="submit" name="add_single_wish">
                                <span class="material-symbols-outlined">favorite</span>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>

        <div class="add-extra-wrapper">
            <a href="supermarket.php?from_recipe=<?php echo $id_recipe; ?>">
                <span class="material-symbols-outlined add-extra" title="Add Extra Ingredient">add_circle</span>
            </a>
        </div>
    </div>

    <div class="actions">
        <form method="post">
            <button type="submit" name="add_to_cart">Add Ingredients to Cart</button>
        </form>
    </div>

</div>

</body>
</html>

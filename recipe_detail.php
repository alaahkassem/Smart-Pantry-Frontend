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

if(isset($_POST['add_to_cart'])){
    mysqli_query($con, "DELETE FROM cart WHERE id_user='$id_user'");
    mysqli_data_seek($ingredientQuery, 0);
    while($ing = mysqli_fetch_assoc($ingredientQuery)){
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
    mysqli_data_seek($ingredientQuery, 0);
    while($ing = mysqli_fetch_assoc($ingredientQuery)){
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $recipe['name_recipe']; ?></title>


<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">

<!-- Icon -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=favorite" />

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
    background: #ffffff;
    padding: 35px;
    border-radius: 25px;
    border: 2px solid #e3b0c4;
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recipe-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.top-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.back-button, .small-button {
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 22px;
    color: #fff;
    border: 2px solid #b94666;
    box-shadow: 0 4px 12px rgba(195,90,120,0.3);
    transition: all 0.3s ease;
    text-decoration: none;
}

.back-button { background: #c35a78; }
.back-button:hover { background: #a13d5c; border-color: #902e4f; transform: translateY(-2px); box-shadow: 0 6px 14px rgba(161,61,92,0.4); }

.small-button { background: #b94666; }
.small-button:hover { background: #9f3853; border-color: #c14769; transform: translateY(-2px); box-shadow: 0 6px 14px rgba(159,56,83,0.4); }

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

.recipe-container img:hover { transform: scale(1.03); }

.recipe-details {
    display: flex;
    justify-content: space-around;
    padding: 18px;
    background: #ffffff;
    border-radius: 18px;
    border: 1px solid #e3b0c4;
    margin-bottom: 25px;
    box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
}

.recipe-details p {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
    color: #9e1d4d;
}

.recipe-container p {
    font-size: 17px;
    line-height: 1.8;
    margin-bottom: 20px;
    color: #111;
    font-family: 'Nunito', sans-serif;
}

.ingredients h3 {
    font-size: 26px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 15px;
    color: #c35a78;
}

.ingredients h3::after {
    content: '';
    display: block;
    width: 60px;
    height: 3px;
    background: #c35a78;
    margin: 8px auto 0;
    border-radius: 2px;
}

.ingredients li {
    padding: 14px 18px;
    margin-bottom: 12px;
    background: #ffffff;
    border-radius: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 2px solid #e3b0c4;
    transition: all 0.3s ease;
    font-family: 'Nunito', sans-serif;
    font-size: 16px;
}

.ingredients li:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    background: #f9f9f9;
}

.ingredients li::before {
    content: "🍴";
    margin-right: 8px;
    color: #b94666;
}

.material-symbols-outlined {
    font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
    cursor: pointer;
    color: #c35a78;
    transition: 0.3s;
    font-size: 28px;
}

.material-symbols-outlined:hover {
    color: #9f3853;
    transform: scale(1.2);
}

.actions {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}

.actions button {
    padding: 14px 28px;
    background: #c35a78;
    color: white;
    font-weight: 600;
    border: 2px solid #b94666;
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(195,90,120,0.3);
    transition: all 0.3s ease;
    font-family: 'Nunito', sans-serif;
}

.actions button:hover {
    background: #a13d5c;
    border-color: #902e4f;
    transform: translateY(-2px);
    box-shadow: 0 6px 14px rgba(161,61,92,0.4);
}

@media (max-width: 650px) {
    .recipe-details { flex-direction: column; gap: 12px; }
    .ingredients li { flex-direction: column; align-items: flex-start; }
    .actions button, .back-button, .small-button { width: 100%; text-align: center; }
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
    <p><?php echo nl2br($recipe['description']); ?></p>

    <div class="ingredients">
        <h3>Ingredients:</h3>
        <ul>
        <?php
        mysqli_data_seek($ingredientQuery, 0);
        while($ing = mysqli_fetch_assoc($ingredientQuery)){
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

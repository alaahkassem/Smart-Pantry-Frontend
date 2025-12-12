<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

function clean($s) {
    return trim(htmlspecialchars($s, ENT_QUOTES, 'UTF-8'));
}

$results = [];
$error = '';
if (isset($_POST['submit'])) {
    $raw_ingredients = isset($_POST['ingredients']) ? $_POST['ingredients'] : '';
    $dietary = isset($_POST['dietary']) ? $_POST['dietary'] : []; // array of choices

    $raw_ingredients = clean($raw_ingredients);
    if ($raw_ingredients === '') {
        $error = 'Please Enter At least One Ingredient';
    } else {
        
        $parts = array_filter(array_map(function($i){ return mb_strtolower(trim($i)); }, explode(',', $raw_ingredients)));
        $parts = array_values(array_unique($parts));

        if (count($parts) === 0) {
            $error = 'No Ingredient was Recognized.Please make sure their are separated by (,)';
        } else {
            
            $escaped_parts = array_map(function($p) use ($con) {
                return mysqli_real_escape_string($con, $p);
            }, $parts);
            $sql = "SELECT id_ingredient, LOWER(name_ingredient) as name_ingredient FROM ingredient WHERE LOWER(name_ingredient) IN ('".implode("','", $escaped_parts)."')";
            $res=mysqli_query($con, $sql);
                $ingredient_ids = [];  
        while ($row = mysqli_fetch_assoc($res)) {
                    $ingredient_ids[] =$row['id_ingredient'];
                }
            

               
                if (count($ingredient_ids) === 0) {
                    $ingredient_ids = [];
                    foreach ($parts as $p) {
                        $p2= mysqli_real_escape_string($con, $p);
                        $likeSql = "SELECT id_ingredient FROM ingredient WHERE LOWER(name_ingredient) LIKE '%$p2%'";
                        $s=mysqli_query($con, $likeSql);
                        while ($row = mysqli_fetch_assoc($s)) {
                            $ingredient_ids[] =$row['id_ingredient'];
                        }
                    }
                    $ingredient_ids = array_values(array_unique($ingredient_ids));
                }

                if (count($ingredient_ids) === 0) {
                    $error = 'I couldn`t find any matching ingredient';
                } else {
                    $dietWhere = "";
                    if (!empty($dietary)) {
                       
                        $map = [
                            'Vegetarian' => "r.health_type IN ('healthy','regular')",
                            'Vegan' => "r.health_type = 'healthy'", 
                            'Gluten-Free' => "", 
                            'Low-Carb' => ""
                        ];
                        $partsDietWhere = [];
                        foreach ($dietary as $d) {
                            if (!empty($map[$d])) $partsDietWhere[] = $map[$d];
                        }
                        if (!empty($partsDietWhere)) {
                            $dietWhere = " AND (" . implode(" OR ", $partsDietWhere) . ") ";
                        }
                        $ids_safe = array_map('intval', $ingredient_ids);
                        $inlist = implode(',', $ids_safe);
                    }$sqlRecipes = "
                        SELECT r.id_recipe, r.name_recipe, r.description, r.image, r.time_needed,COUNT(ri.id_recipe_ingre) AS matches
                        FROM recipes r
                        JOIN recipes_ingredient ri ON ri.id_recipe = r.id_recipe
                        WHERE ri.id_ingredient IN ($inlist)
                        $dietWhere
                        GROUP BY r.id_recipe
                        ORDER BY matches DESC, r.name_recipe ASC
                        LIMIT 100
                    ";
                   
                    $sq=mysqli_query($con, $sqlRecipes);  
                    
                        while ($row = mysqli_fetch_assoc($sq)) {
                            $results[] = $row;
                        }
                }
                }
            }
        }

?>
<!doctype html>
<html lang="ar">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Smart Pantry Chef — Matching Ingredients</title>
<link rel="stylesheet" href="matching.css">
</head>
<body>
<div class="container">
    <header>
       
        <nav class="navbar">
             <a href="logout.php">
     <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;margin:0;padding:0;display:block;">         
    </a> 
     <a href="home.php">Home</a>
             <a href="meals.php">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php">Recipes</a>
            <a class="active" href="matching.php">Matching</a>
         <a href="budget.php">Budget</a>
           <a href="mood.php">Mood</a>
           <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
            <a href="supermarket.php">Market</a>
        </nav>
    </header>
<hr>
    
    <main>
        <section class="panel">
            <h2>Smart Ingredient Matching</h2>
            <h3>What's in Your Pantry?</h3>

            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="post">
                <label for="ingredients">Enter your ingredients (separated by commas):</label>
                <textarea id="ingredients" name="ingredients" placeholder="e.g., chicken, rice, tomatoes, garlic, onions, bell peppers..."><?= isset($_POST['ingredients']) ? htmlspecialchars($_POST['ingredients']) : '' ?></textarea><div class="diet">
                    <label>Dietary Preferences:</label><label><input type="checkbox" name="dietary[]" value="Vegetarian" <?= (isset($_POST['dietary']) && in_array('Vegetarian', $_POST['dietary']))? 'checked':'' ?>> Vegetarian</label>
                    <label><input type="checkbox" name="dietary[]" value="Vegan" <?= (isset($_POST['dietary']) && in_array('Vegan', $_POST['dietary']))? 'checked':'' ?>> Vegan</label>
                    <label><input type="checkbox" name="dietary[]" value="Gluten-Free" <?= (isset($_POST['dietary']) && in_array('Gluten-Free', $_POST['dietary']))? 'checked':'' ?>> Gluten-Free</label>
                    <label><input type="checkbox" name="dietary[]" value="Low-Carb" <?= (isset($_POST['dietary']) && in_array('Low-Carb', $_POST['dietary']))? 'checked':'' ?>> Low-Carb</label>
                </div>

                <button type="submit" class="btn" name="submit">Find Matching Recipes</button>
            </form>
        </section>

        <section class="panel results">
            <h3>Results</h3>
            <?php if (empty($results) && !$error): ?>
                <p>No recipes were found that match the ingredients you entered!</p>
            <?php elseif (!empty($results)): ?>
                <div class="grid">
                    <?php foreach($results as $r): ?>
                        <article class="recipe-card">
                            <?php if (!empty($r['image'])): ?>
                                <img src="Image project/<?= htmlspecialchars($r['image']) ?>" alt="<?= htmlspecialchars($r['name_recipe']) ?>">
                            <?php else: ?>
                                <div class="noimg">No image</div>
                            <?php endif; ?>
                            <h4><?= htmlspecialchars($r['name_recipe']) ?></h4>
                            <p class="meta">Matches: <?= (int)$r['matches'] ?> — Time: <?= htmlspecialchars($r['time_needed']) ?></p>
              <p class="desc"><?= nl2br(htmlspecialchars(substr($r['description'],0,200))) ?><?php if (strlen($r['description'])>200) echo '...'; ?></p>
                            <a class="view" href="recipe_detail.php?id_recipe=<?= (int)$r['id_recipe'] ?>">View recipe</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <footer>
        <p>Smart Pantry Chef &copy; <?= date('Y') ?></p>
    </footer>
</div>
</body>
</html>
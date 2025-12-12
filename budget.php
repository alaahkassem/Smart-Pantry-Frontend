<?php
session_start();
include "db.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id_user'];
$budget = 40; // default in USD
$grocery_cost = 0;
$saving = 0;
$recommendations = [];

if (isset($_POST['save_budget'])) {
    $budget = $_POST['amount'];


    $conversion_rate = 90000; // 1 USD = 90,000 LBP
    $budget_lbp = $budget * $conversion_rate;

    $sql = "INSERT INTO budget (id_user, amount, date)  
            VALUES ('$user_id', '$budget', CURDATE())";
    mysqli_query($con, $sql);

    $sql = "SELECT r.id_recipe, r.name_recipe, r.image,
            SUM(s.price) AS total_price
            FROM recipes r
            JOIN recipes_ingredient ri ON r.id_recipe = ri.id_recipe
            JOIN supermarket s ON ri.id_ingredient = s.id_ingredient
            GROUP BY r.id_recipe";
    $result = mysqli_query($con, $sql);

    while($row=mysqli_fetch_assoc($result)) {
        
        if ($row['total_price'] <= ($budget_lbp * 7)) {
            $recommendations[] = $row;
        }
    }


    $grocery_cost = $budget_lbp * 1.18;
    $saving = ($budget_lbp * 2) - $grocery_cost;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Planning</title>
    <link rel="stylesheet" href="budget.css">
</head>
<body>

<div class="container">
    <header>
        <nav class="navbar">
         <a href="logout.php">       <img src="logo.png" alt="Smart Pantry Chef" style="width:100px;height:auto;margin:0;padding:0;display:block;"> </a> 
            <a href="home.php">Home</a>
            <a href="meals.php">Meals</a>
            <a href="categories.php">Categories</a>
            <a href="recipes.php">Recipes</a>
            <a href="matching.php">Matching</a>
            <a href="budget.php" class="active">Budget</a>
            <a href="mood.php">Mood</a>
            <a href="history.php">History</a>
            <a href="favorite.php">Favorites</a>
             <a href="supermarket.php">Market</a>
          
        </nav>
    </header>
<hr>
<div class="container">

    <div class="title-box">
        <h2>Smart Budget Planning</h2>
    </div>

    <form method="POST">
        <h3>Set Your Meal Budget</h3>

        <div class="slider-box">
            <p class="amount">
                $<span id="amountDisplay"><?php echo $budget; ?></span> 
                (<?php echo number_format($budget * 90000, 0); ?> LBP)
            </p>

            <input type="range" min="15" max="60" value="<?php echo $budget; ?>" id="rangeInput" name="amount">
            <div class="labels">
                <span>$15/day (<?php echo number_format(15 * 90000, 0); ?> LBP)</span>
                <span>$25/day (<?php echo number_format(25 * 90000, 0); ?> LBP)</span>
                <span>$40/day (<?php echo number_format(40 * 90000, 0); ?> LBP)</span>
                <span>$60/day (<?php echo number_format(60 * 90000, 0); ?> LBP)</span>
            </div>
        </div>

        <h4>Meal Type:</h4>
        <div class="meal-types">
            <label><input type="checkbox" name="meal[]" checked> Breakfast</label>
            <label><input type="checkbox" name="meal[]" checked> Lunch</label>
            <label><input type="checkbox" name="meal[]" checked> Dinner</label>
            <label><input type="checkbox" name="meal[]" > Snacks</label>
        </div>

        <button class="btn" name="save_budget" type="submit">Find Budget-Friendly Meals</button>
    </form>

    <?php if (!empty($recommendations)) { ?>
    <div class="results">

        <h3>Budget Breakdown</h3>

        <div class="box-group">
            <div class="box">
                <h4>Meal Plan Total</h4>
                <p>
                    <?php echo number_format($budget_lbp * 7, 0); ?> LBP / week <br>
                    (~$<?php echo number_format(($budget_lbp * 7) / 90000, 2); ?>)
                </p>
            </div>

            <div class="box">
                <h4>Grocery Cost</h4>
                <p>
                    <?php echo number_format($grocery_cost, 0); ?> LBP <br>
                    (~$<?php echo number_format($grocery_cost / 90000, 2); ?>)
                </p>
            </div>

            <div class="box">
                <h4>You Save</h4>
                <p>
                    <?php echo number_format($saving, 0); ?> LBP <br>
                    (~$<?php echo number_format($saving / 90000, 2); ?>)
                </p>
            </div>
        </div>

        <h3>Recommended Meals Within Budget</h3>

        <div class="cards">
            <?php foreach ($recommendations as $r) { ?>
            <div class="card">
                <img src="Image project/<?php echo htmlspecialchars($r['image']); ?>">
                <h4><?php echo $r['name_recipe']; ?></h4>
                <p>
                    Estimated Cost: <?php echo number_format($r['total_price'], 0); ?> LBP <br>
                    (~$<?php echo number_format($r['total_price'] / 90000, 2); ?>)
                </p>
            </div>
            <?php } ?>
        </div>

    </div>
    <?php } ?>

</div>
<script>
    const range = document.getElementById("rangeInput");
    const display = document.getElementById("amountDisplay");
    range.oninput = function () {
        display.textContent = this.value;
    }
</script>

</body>
</html>
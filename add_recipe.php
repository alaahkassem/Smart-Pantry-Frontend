<?php
session_start();
include 'db.php';

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($con, $_POST['name_recipe']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $time_needed = mysqli_real_escape_string($con, $_POST['time_needed']);
    $calories = (int)$_POST['calories'];
    $level = mysqli_real_escape_string($con, $_POST['level']);
    $id_category = (int)$_POST['id_category'];
    $rating = (int)$_POST['rating'];
$serving = (int)$_POST['serving'];
$id_meal = (int)$_POST['id_meal'];
$health_type = mysqli_real_escape_string($con, $_POST['health_type']); 

   
    $image = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "Image project/".$image);
    }

   $sql = "INSERT INTO recipes 
    (name_recipe, description, time_needed, calories, level, id_category, image, rating, serving, id_meal, health_type)
    VALUES 
    ('$name','$description','$time_needed','$calories','$level','$id_category','$image',
    '$rating','$serving','$id_meal','$health_type')";
    if(mysqli_query($con, $sql)){
        header("Location: recipes.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Recipe</title>
    <link rel="stylesheet" href="add_recipe.css">
    <link rel="website icon" type="png" href="logo.png">
</head>

<body>
    <div class="container">
        <h2>Add Your Recipe</h2>

        <form method="post" enctype="multipart/form-data">

            <label>Name:</label>
            <input type="text" name="name_recipe" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Time Needed:</label>
            <input type="text" name="time_needed" required>

            <label>Calories:</label>
            <input type="number" name="calories" required>

            <label>Level:</label>
            <select name="level">
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>

            <label>Category:</label>
            <select name="id_category">
                <?php
                $catResult = mysqli_query($con, "SELECT * FROM categories");
                while($cat = mysqli_fetch_assoc($catResult)){
                    echo "<option value='".$cat['id_category']."'>".$cat['name_category']."</option>";
                }
                ?>
            </select>

            <label>Rating (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>

            <label>Serving:</label>
            <input type="number" name="serving" required>

            <label>Meal:</label>
            <select name="id_meal">
                <?php
                $mealResult = mysqli_query($con, "SELECT * FROM meals");
                while($meal = mysqli_fetch_assoc($mealResult)){
                    echo "<option value='".$meal['id_meal']."'>".$meal['name_meal']."</option>";
                }
                ?>
            </select>

            <label>Type:</label>
            <select name="health_type">
                <option value="Regular">Regular</option>
                <option value="Healthy">Healthy</option>
            </select>

            <label>Image:</label>
            <input type="file" name="image">

            <button type="submit" name="submit">Save Recipe</button>
        </form>
    </div>
</body>
</html>
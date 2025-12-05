<?php
session_start();
$errors = [];
include 'db.php';
if(isset($_POST['signup'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check = "SELECT * FROM users WHERE email='$email'";
    $rs = mysqli_query($con, $check);

    if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
        $errors['name'] = "Name should contain only letters and spaces";
    } 
    else if(!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errors['email'] = "Invalid email format";
    }
    else if (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }
    else {
        if(mysqli_num_rows($rs) > 0){
            $errors['email'] = "Email already exists";
        } else {
            $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (name, email, password) 
                    VALUES ('$name','$email','$passwordHashed')";
            
            if(mysqli_query($con, $sql)){
                $_SESSION['id_user'] = mysqli_insert_id($con);
                $_SESSION['user_name'] = $name;
                header("Location: meals.php");
                exit();
            } else {
                $errors['general'] = "Database error: " . mysqli_error($con);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
<form action="" method="post"> 
<div class="container">
<div class="form">
<img src="logo.png"  style="width:80px;height:80px;">

        <input type="text" name="name" placeholder="Enter your name" required>
        <?php if(isset($errors['name'])): ?>
            <div class="error"><?php echo $errors['name']; ?></div>
        <?php endif; ?>

        <input type="text" name="email" placeholder="Enter your email" required>
        <?php if(isset($errors['email'])): ?>
            <div class="error"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <input type="password" name="password" placeholder="Create password" required>
        <?php if(isset($errors['password'])): ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php endif; ?>

        <input class="btn" type="submit" name="signup" value="Sign Up">
        </div>
        </div>
</form>
</body>
</html>
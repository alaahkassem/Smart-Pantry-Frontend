<?php
session_start();
$errors = [];
include 'db.php';

/* ------------------ LOGIN ------------------ */
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_array($result);
//password verification for hashed password
        if(password_verify($password, $row['password'])){
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['user_name'] = $row['name'];
            header("Location: meals.php");
            exit();
        } else {
            $errors['general'] = "Invalid email or password";
        }
    } else {
        $errors['general'] = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Pantry Chef</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<form action="" method="post">
<div class="container">

    <div class="tabs">
        <div class="tab active">Login</div>
    </div>

    <div class="form">
        <input type="text" name="email" placeholder="Enter your email" required>

        <input type="password" name="password" placeholder="Enter your password" required>

        <?php if(isset($errors['general'])): ?>
            <div class="error"><?php echo $errors['general']; ?></div>
        <?php endif; ?>

        <input class="btn" type="submit" name="login" value="Login">
        <p class="par">Create new account <a href="signup.php">Sign up</a></p>
    </div>

</div>
</form>
</body>
</html>
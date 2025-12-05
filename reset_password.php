<?php
include 'db.php';
session_start();

if(!isset($_SESSION['reset_email'])){
    header("Location: login.php");
    exit();
}

$msg = "";
$email = $_SESSION['reset_email'];

if(isset($_POST['change'])){
    $password = $_POST['password'];

    // Hash password
    $password = password_hash($password, PASSWORD_BCRYPT);

    $update = "UPDATE users SET password='$password' WHERE email='$email'";

    if(mysqli_query($con, $update)){
        unset($_SESSION['reset_email']);
        header("Location: login.php");
        exit();
    } else {
        $msg = "Error updating password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create New Password</title>
    <link rel="stylesheet" href="reset_password.css">
</head>
<body>

<div class="container">
    <h2>Reset Your Password</h2>

    <form method="POST">
        <input type="password" name="password" placeholder="Enter new password" required>
        <button type="submit" name="change">Update Password</button>
    </form>

    <?php if(!empty($msg)): ?>
        <p class="msg"><?php echo $msg; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
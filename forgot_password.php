<?php
include 'db.php';
session_start();

$msg = "";

if(isset($_POST['reset'])){
    $email = $_POST['email'];

    $check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    
    if(mysqli_num_rows($check) == 1){
        $_SESSION['reset_email'] = $email;
        header("Location: reset_password.php");
        exit();
    } else {
        $msg = "Email not found";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="forgot_password.css">
</head>
<body>

<div class="container">
    <h2>Forgot Password?</h2>
    <p class="subtitle">Enter your email to reset your password</p>

    <form method="POST">
        <input type="text" name="email" placeholder="Enter your email" required>

        <button type="submit" name="reset">Next</button>
    </form>

    <?php if(!empty($msg)): ?>
        <p class="error"><?php echo $msg; ?></p>
    <?php endif; ?>
</div>

</body>
</html>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

if ($password != "") {
    $hashed = password_hash($password, PASSWORD_BCRYPT);
    $sql = "UPDATE users SET name='$name', email='$email', password='$hashed' WHERE id_user='$user_id'";
} else {
    $sql = "UPDATE users SET name='$name', email='$email' WHERE id_user='$user_id'";
}

if (mysqli_query($con, $sql)) {
    // update session
    $_SESSION['user_name'] = $name;
    $_SESSION['email'] = $email;
    header("Location: profile.php?updated=1");
    exit();
} else {
    echo "Error: " . mysqli_error($con);
}
?>
<?php
session_start();
include 'db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

$query = "SELECT * FROM users WHERE id_user='$user_id'";
$res = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<div class="container">

    <h2>Your Profile 👤</h2>

    <form action="update.php" method="POST">

        <label>Name</label>
        <input type="text" name="name" value="<?php echo $user['name']; ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo $user['email']; ?>">

        <label>New Password (optional)</label>
        <input type="password" name="password" placeholder="Leave empty if unchanged">

        <button type="submit">Update</button>
    </form>

</div>

</body>
</html>
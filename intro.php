<?php
session_start();
include "../db.php";

if(isset($_POST['join'])){

$name = $_POST['name'];
$phone = $_POST['phone'];

$check = mysqli_query($conn,"SELECT * FROM users WHERE phone='$phone'");

if(mysqli_num_rows($check) > 0){

$user = mysqli_fetch_assoc($check);
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];

}else{

mysqli_query($conn,"INSERT INTO users(username,phone) VALUES('$name','$phone')");
$id = mysqli_insert_id($conn);

$_SESSION['user_id'] = $id;
$_SESSION['username'] = $name;

}

header("Location: chat.php");
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Join Chat</title>
</head>

<body>

<h2>Church Chat Login</h2>

<form method="POST">

<input type="text" name="name" placeholder="Your Name" required><br><br>

<input type="text" name="phone" placeholder="Phone Number" required><br><br>

<button name="join">Join Chat</button>

</form>

</body>
</html>

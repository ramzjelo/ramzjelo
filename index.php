<?php
session_start();
include "../db.php"; // PDO connection

$error = ""; // for error messages

if (isset($_POST['join'])) {

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);

    if (!empty($name) && !empty($phone)) {

        // Check if phone exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = :phone");
        $stmt->execute([':phone' => $phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // If phone exists but the name is different, show error
            if ($user['username'] !== $name) {
                $error = "This phone number is already registered with another name.";
            } else {
                // Existing user with same name, log in
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: chat.php");
                exit();
            }
        } else {
            // New user, insert into database
            $stmt = $conn->prepare("INSERT INTO users (username, phone) VALUES (:username, :phone)");
            $stmt->execute([':username' => $name, ':phone' => $phone]);
            $id = $conn->lastInsertId();

            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $name;
            header("Location: chat.php");
            exit();
        }
    } else {
        $error = "Please enter both name and phone number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CHAT LOGIN</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body { 
        background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                url('https://images.unsplash.com/photo-1501004318641-b39e6451bec6');
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: center;
    align-items: center;
        height: 100vh; 
        display: flex; 
        justify-content: center; 
        align-items: center;
    }
    .login-card {
        background: rgba(255,255,255,0.12);
        backdrop-filter:blur(38px)
        padding: 35px;
        border-radius: 20px;
        width: 360px;
        height: 60%;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        color: var(--white);
        text-align: center;
        animation: fadeout is ease;
        
    }
    @key frames fade in {
        from{opacity:0; transform: translate(30px);} to {opacity: 1; transform: translate(0);}
    }
    logo{
        front size: 35px;
    }
    h2{
        margin: 12px 0;
        line-spacing: 3px
    }
    .login-card h2 { margin-bottom: 25px; color: #ffffff; font-size: 24px; }
    .login-card input{
        width: 80%; 
        padding: 15px; 
        margin-bottom: 15px;
        border-radius: 15px; 
        border: 1px solid #ffffff; 
        outline: none; 
        transition: all 0.3s ease;
    }
    .login-card input[type="text"]:focus { border-color:forestgreen; box-shadow: 0 0 5px rgba(52,152,219,0.5); }
    
    
    .login-card button {
        width: 80%; 
        border: none; 
        height: 50px;
        border-radius: 15px;
        background: #3498db; 
        text-align: center;
        color: white; 
        font-size: 16px; 
        cursor: pointer; 
        transition: all 0.3s ease;
        
    }
    .login-card button:hover { background: #2980b9; }
    .login-card .footer { margin-top: 15px; font-size: 14px; color:aliceblue; }
    .login-card .error {
        color: red; font-size: 14px; margin-bottom: 15px;
    }
    .login-card a.back-btn {
        display: inline-block; margin-top: 15px; text-decoration: none;
        padding: 10px 20px; border-radius: 25px; background:#27ae60; color: white; font-weight: bold;
        transition: all 0.3s ease;
        margin-bottom: 5px;
        width: 50%;
    }
    .login-card a.back-btn:hover { 
        background: red; 
    }
  
</style>
</head>
<body>

<div class="login-card">
    <h2>Chat Login</h2>

    <?php if (!empty($error)) echo "<div class='error'>{$error}</div>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <button name="join">Join Chat</button>
    </form>

    <div class="footer">Enter your name and phone to join the group chat</div>

    <a href="../index.html" class="back-btn">← Back</a>
</div>

</body>
</html>
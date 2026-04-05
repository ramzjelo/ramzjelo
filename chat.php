<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location:index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$phone = $_SESSION['phone'] ?? '';

// ADMIN (Dash)
$is_admin = ($phone === "0702983392");

// SEND MESSAGE
if (isset($_POST['send']) && !empty(trim($_POST['message']))) {
    $msg = trim($_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (:user_id, :message)");
    $stmt->execute([':user_id' => $user_id, ':message' => $msg]);
}

// DELETE MESSAGE
if ($is_admin && isset($_GET['delete_msg'])) {
    $msg_id = $_GET['delete_msg'];

    $stmt = $conn->prepare("DELETE FROM messages WHERE message_id = :id");
    $stmt->execute([':id' => $msg_id]);

    header("Location: chat.php");
    exit();
}

// DELETE USER
if ($is_admin && isset($_GET['delete_user'])) {
    $u_id = $_GET['delete_user'];

    if ($u_id != $user_id) { // prevent deleting self
        $stmt = $conn->prepare("DELETE FROM messages WHERE user_id = :id");
        $stmt->execute([':id' => $u_id]);

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = :id");
        $stmt->execute([':id' => $u_id]);
    }

    header("Location: chat.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>GROUP FOR SENSETISATION</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 20px auto;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin {
            color: red;
            font-weight: bold;
        }

        #chat-box {
            background: white;
            height: 350px;
            overflow-y: auto;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .msg {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 8px;
            background: #ecf0f1;
        }

        .msg b {
            color: #2c3e50;
        }

        .delete {
            color: red;
            margin-left: 10px;
            font-size: 12px;
            text-decoration: none;
        }

        form {
            margin-top: 10px;
            display: flex;
        }

        input[type="text"] {
            flex: 1;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            outline: none;
        }

        button {
            margin-left: 10px;
            padding: 10px 20px;
            border: none;
            background: #27ae60;
            color: white;
            border-radius: 20px;
            cursor: pointer;
        }

        button:hover {
            background: #219150;
        }

        .logout {
            text-decoration: none;
            color: white;
            background: red;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>

    <script>
        function confirmDelete(type) {
            return confirm("Are you sure you want to delete this " + type + "?");
        }
    </script>
</head>

<body>

<header>GROUP FOR SENSETISATION</header>

<div class="container">

<div class="top-bar">
    <h4>
        Welcome <?php echo htmlspecialchars($username); ?>
        <?php if($is_admin) echo "<span class='admin'>(ADMIN)</span>"; ?>
    </h4>

    <a class="logout" href="logout.php">Logout</a>
</div>

<div id="chat-box">

<?php
$stmt = $conn->query("
    SELECT messages.*, users.username, users.user_id 
    FROM messages 
    JOIN users ON messages.user_id = users.user_id
    ORDER BY sent_at ASC
");

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $row) {

    $msg_id = $row['message_id'];
    $user = htmlspecialchars($row['username']);
    $message = htmlspecialchars($row['message']);
    $uid = $row['user_id'];

    echo "<div class='msg'><b>$user</b>: $message";

    if ($is_admin) {
        echo "<a class='delete' href='?delete_msg=$msg_id' onclick='return confirmDelete(\"message\")'>🗑</a>";
        echo "<a class='delete' href='?delete_user=$uid' onclick='return confirmDelete(\"user\")'>❌</a>";
    }

    echo "</div>";
}
?>

</div>

<form method="POST">
    <input type="text" name="message" placeholder="Type message..." required>
    <button name="send">Send</button>
</form>

</div>

<script>
var chatBox = document.getElementById('chat-box');
chatBox.scrollTop = chatBox.scrollHeight;
</script>

</body>
</html>
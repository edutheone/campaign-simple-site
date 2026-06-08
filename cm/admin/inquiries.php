<?php
include '../config.php';

// Fetch users with messages + name & phone
$users = $conn->query(" 
    SELECT DISTINCT m.user_id, u.name, u.phone
    FROM messages m
    JOIN volunteers u ON m.user_id = u.id
    ORDER BY m.user_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width = device-width initial-scale=1.0">
<title>messages</title>
<link rel="stylesheet" href="../styles/messaging.css">
</head>
<body>

<div class="dashboard">

<!-- SIDEBAR -->
<div class="sidebar">
<h2>Users</h2>

<?php while($u = $users->fetch_assoc()): ?>
    <div class="user" onclick="window.location='?user_id=<?php echo $u['user_id']; ?>'">
        <strong><?php echo htmlspecialchars($u['name']); ?></strong><br>
        <span class="small"><?php echo htmlspecialchars($u['phone']); ?></span>
    </div>
<?php endwhile; ?>

</div>

<!-- MAIN -->
<div class="main">

<?php if(isset($_GET['user_id'])): 
    $user_id = $_GET['user_id'];

    // Get user details
    $stmtUser = $conn->prepare("SELECT name, phone FROM volunteers WHERE id=?");
    $stmtUser->bind_param("i", $user_id);
    $stmtUser->execute();
    $userData = $stmtUser->get_result()->fetch_assoc();

    // Fetch messages of that user
    $stmt = $conn->prepare("SELECT * FROM messages WHERE user_id=? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $messages = $stmt->get_result();
?>

<div class="header">
    <?php echo htmlspecialchars($userData['name']); ?> 
    (<?php echo htmlspecialchars($userData['phone']); ?>)
</div>

<div class="messages">

<?php while($msg = $messages->fetch_assoc()): ?>

<div class="card">
    <strong><?php echo htmlspecialchars($msg['subject']); ?></strong><br>
    <?php echo nl2br(htmlspecialchars($msg['content'])); ?><br>
    <small><?php echo $msg['created_at']; ?></small>

    <!-- Replies -->
    <?php
    $stmt2 = $conn->prepare("SELECT * FROM replies WHERE message_id=?");
    $stmt2->bind_param("i", $msg['id']);
    $stmt2->execute();
    $replies = $stmt2->get_result();

    while($rep = $replies->fetch_assoc()):
    ?>
        <div class="reply">
            <?php echo htmlspecialchars($rep['reply']); ?>
        </div>
    <?php endwhile; ?>

    <!-- Reply Form -->
    <form method="POST">
        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
        <textarea name="reply" placeholder="Type reply..." required></textarea>
        <button type="submit" name="send_reply">Reply</button>
    </form>

</div>

<?php endwhile; ?>

</div>

<?php else: ?>
<div class="header">Select a user</div>
<?php endif; ?>

</div>
</div>

</body>
</html>

<?php
// HANDLE REPLY
if(isset($_POST['send_reply'])){
    $reply = $_POST['reply'];
    $message_id = $_POST['message_id'];

    $stmt = $conn->prepare("INSERT INTO replies (message_id, reply) VALUES (?, ?)");
    $stmt->bind_param("is", $message_id, $reply);
    $stmt->execute();

    header("Location: ?user_id=".$_GET['user_id']);
}
?>

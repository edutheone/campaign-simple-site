<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

   

if(isset($_POST['send_inquiry'])){
    $subject = $_POST['subject'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("
        INSERT INTO messages (user_id, subject, content) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $user_id, $subject, $content);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
}

/* =========================
   FETCH USER MESSAGES
========================= */
$stmt = $conn->prepare("
    SELECT * FROM messages 
    WHERE user_id=? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
<title>User Dashboard</title>
<link rel="stylesheet" href="styles/dashboard.css">
<style>

</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>User Dashboard</h2>
</div>

<!-- =========================
     NEW INQUIRY FORM
========================= -->
<form method="POST">
    <h3>Send New Inquiry</h3>

    <input type="text" name="subject" placeholder="enter your ward" required>
    <textarea name="content" placeholder="Write your message..." required></textarea>

    <button type="submit" name="send_inquiry">Send</button>
</form>

<!-- =========================
     USER MESSAGES + REPLIES
========================= -->

<h3>Your Messages</h3>

<?php while($msg = $messages->fetch_assoc()): ?>

<div class="card">

    <strong><?php echo htmlspecialchars($msg['subject']); ?></strong><br>
    <p><?php echo nl2br(htmlspecialchars($msg['content'])); ?></p>
    <small><?php echo $msg['created_at']; ?></small>

    <!-- FETCH REPLIES -->
    <?php
    $stmt2 = $conn->prepare("
        SELECT * FROM replies 
        WHERE message_id=? 
        ORDER BY created_at ASC
    ");
    $stmt2->bind_param("i", $msg['id']);
    $stmt2->execute();
    $replies = $stmt2->get_result();

    while($rep = $replies->fetch_assoc()):
    ?>
        <div class="reply">
            <strong>Admin Reply:</strong><br>
            <?php echo htmlspecialchars($rep['reply']); ?>
            <br>
            <small><?php echo $rep['created_at']; ?></small>
        </div>
    <?php endwhile; ?>

</div>

<?php endwhile; ?>

</div>

</body>
</html>
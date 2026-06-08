<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $event_date = $_POST['event_date'];

    $stmt = $conn->prepare("INSERT INTO events (title, location, event_date) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $location, $event_date);
    
    if ($stmt->execute()) { echo "<script>alert('Event Added!'); window.location='voters.php';</script>"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body >
    

<div class="admin-card" style="max-width: 500px; margin: 100px auto; font-family: sans-serif;">
    <h2 style="color: #0b1c3d;">Post New Event</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Event Title" required style="width:100%; padding:10px; margin:10px 0;">
        <input type="text" name="location" placeholder="Location" required style="width:100%; padding:10px; margin:10px 0;">
        <input type="date" name="event_date" required style="width:100%; padding:10px; margin:10px 0;">
        <button type="submit" style="background: #d60000; color:white; border:none; padding:10px 20px; cursor:pointer;">Publish Event</button>
    </form>
</div>
</body>
</html>
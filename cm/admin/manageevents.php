<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit(); }
include '../config.php';

// Handle Delete Request
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM events WHERE id = $id");
    header("Location: manage_events.php");
}

$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Events</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 40px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #0b1c3d; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        .btn-add { background: #d60000; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; float: right; }
        .btn-delete { color: #d60000; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <a href="add_event.php" class="btn-add">+ Add New Event</a>
    <h2 style="color: #0b1c3d;">Manage Campaign Events</h2>

    <table>
        <tr>
            <th>Event Title</th>
            <th>Location</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        <?php while($row = $events->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']); ?></td>
            <td><?= htmlspecialchars($row['location']); ?></td>
            <td><?= date('d M Y', strtotime($row['event_date'])); ?></td>
            <td>
                <a href="manageevents.php?delete=<?= $row['id']; ?>" 
                   class="btn-delete" 
                   onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <br>
    <a href="volunteers.php">← Back to Dashboard</a>
</div>

</body>
</html>
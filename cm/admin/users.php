<?php
session_start();
include '../config.php';
if(!isset($_SESSION['admin'])){
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supporters Page</title>
    <link rel="stylesheet" href="../styles/voter.css">
</head>
<body>
    <header class="main-header">
        
        <nav>
            <a href="manageevents.php">Manage Events</a>
            <a href="add_event.php">Add Events</a>
            <a href="inquiries.php">View Messages</a>
            <a href="sms.php">Send Messages</a>
        </nav>
    </header>

<?php 
// 1. Get distinct wards from the 'volunteers' table

$ward_query = "SELECT DISTINCT ward FROM volunteers";
$ward_result = $conn->query($ward_query);

if ($ward_result && $ward_result->num_rows > 0) {

    while ($ward_row = $ward_result->fetch_assoc()) {
        $ward = $ward_row['ward'];

        echo "<h2 class='ward-header'>" . htmlspecialchars($ward) . " Ward Supporters</h2>";

        // 2. Use Prepared Statement for security
        $stmt = $conn->prepare("SELECT * FROM volunteers WHERE ward = ?");
        $stmt->bind_param("s", $ward);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<table border='1' width='100%' cellpadding='8' class='admin-table'>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Phone</th>
                        <th>Polling Station</th>
                        <th>Skills</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

        // 3. Fetch each volunteer for this specific ward
        while ($row = $result->fetch_assoc()) {
            echo "<tr>

                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['name'] ?? $row['full_name']) . "</td>
                    <td>" . htmlspecialchars($row['phone']) . "</td>
                    <td>" . htmlspecialchars($row['polling_station']) . "</td> 
                    <td>" . htmlspecialchars($row['skills']) . "</td>
                    <td>
                        <form action='delete.php' method='post' style='display:inline;' 
                              onsubmit=\"return confirm('Are you sure you want to remove this supporter?');\">
                            <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                            <button type='submit' class='btn-delete'>Remove</button>
                        </form>
                    </td>
                  </tr>";
        }
        
        echo "</tbody></table><br>";
        $stmt->close();
    }
} else {
    echo "<div class='no-data-alert'>
            <h3>No supporters registered yet.</h3>
            <p>New supporters will appear here once they submit their details.</p>
          </div>";
}
?>

</body>
</html>
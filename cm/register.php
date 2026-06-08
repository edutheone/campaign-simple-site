
<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $polling_station = $_POST['polling_station'];
    $skills = $_POST['skills'];

    //  Phone validation (basic: only digits, 10–15 chars)
    if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
        echo "<script>alert('Invalid phone number format');window.history.back();</script>";
        exit();
    }

    //  Check if phone already exists
    $check = $conn->prepare("SELECT id FROM volunteers WHERE phone = ?");
    $check->bind_param("s", $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('This phone number is already registered');window.history.back();</script>";
        exit();
    }

    //  Insert if not duplicate
    $stmt = $conn->prepare("INSERT INTO volunteers (name, phone, polling_station, skills) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $phone, $polling_station, $skills);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for joining team Nyakerario..you will be contacted any time!');window.location.href='index.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width  initial-scale=1.0">
    <title>supporters</title>

<link rel="stylesheet" href="styles/register.css">
</head>

<body>

<div class="form-container">
    <h2>Join team Kennedy nyamwanda</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
         <input type="text" name="ward" placeholder="your ward" required>
        <input type="text" name="polling_station" placeholder="polling station" required>

        <textarea name="skills" placeholder="Skills (Mobilization, Driving, Social Media...)"></textarea>
         
        <button type="submit">Join Now</button>
    </form>
</div>

</body>
</html>
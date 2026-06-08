
<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO inquiries (name, phone, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message Sent Successfully!'); window.location='index.php';</script>";
    }
}
?>
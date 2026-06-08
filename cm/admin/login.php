<?php
session_start();
include '../config.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: users.php");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { background: #0b1c3d; display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; }
        .login-box { background: white; padding: 30px; border-radius: 8px; width: 350px; text-align: center; }
        h2 { color: #d60000; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background: #d60000; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .error { color: red; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Portal</h2>
        
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
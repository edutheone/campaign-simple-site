
<?php
session_start();
include 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $phone = trim($_POST['phone']);


    // Check if user exists
    $stmt = $conn->prepare("SELECT id, phone FROM volunteers WHERE phone=?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        // ✅ LOGIN
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['phone'] = $user['phone'];
        

        header("Location: dashboard.php");
        exit();

    } else {
        // ❌ NOT FOUND → GO TO REGISTER
        echo "<script> alert('Phone not founnd'); window.location.href='register.php' </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>

<style>
body{
    font-family: Arial;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}

.container{
    background:#fff;
    padding:30px;
    border-radius:10px;
    width:320px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

h2{text-align:center;}

input{
    width:100%;
    padding:12px;
    margin-top:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    width:100%;
    padding:12px;
    margin-top:20px;
    background:#3498db;
    color:#fff;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.error{
    color:red;
    text-align:center;
    font-size:14px;
}
</style>
</head>

<body>

<div class="container">
    <h2>Login</h2>

    

    <form action="" method="POST">
        <input 
            type="text" 
            name="phone" 
            placeholder="07XXXXXXXX"
            pattern="^(07|01)[0-9]{8}$"
            required
        >

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
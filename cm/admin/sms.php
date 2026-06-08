<?php
// 1. ALWAYS place 'use' and 'require' at the top level

use AfricasTalking\SDK\AfricasTalking;

session_start();
include '../config.php';

// Protect page (admin only)
if (!isset($_SESSION['admin'])) {
     header("Location: login.php"); 
     exit(); 
    }
$status_message = "";

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])){

    $message = $_POST['message'];

    // Fetch phone numbers
    $result = $conn->query("SELECT phone FROM volunteers");

    if ($result) {
        $phones = [];

        while($row = $result->fetch_assoc()){
            $phones[] = $row['phone'];
        }

        // Remove duplicates
        $phones = array_unique($phones);

        // Format numbers (Kenya example)
        $formatted = [];
        foreach($phones as $p){
            // Remove non-numeric characters
            $p = preg_replace('/[^0-9]/', '', $p);

            // Convert local 07... to international 2547...
            if(substr($p, 0, 1) == "0"){
                $p = "254" . substr($p, 1);
            }

            if(!empty($p)){
                $formatted[] = $p;
            }
        }

        if(!empty($formatted)){
            // Africa's Talking expects a comma-separated string or an array
            $numbers = $formatted; 

            // ==== SMS API (Africa's Talking) ====
            $username = "YOUR_USERNAME"; // Replace with your AT username
            $apiKey   = "YOUR_API_KEY";  // Replace with your AT API Key

            $AT = new AfricasTalking($username, $apiKey);
            $sms = $AT->sms();

            try {
                $sms->send([
                    'to'      => $numbers,
                    'message' => $message
                ]);
                $status_message = "<p style='color: green;'>Messages sent successfully to " . count($numbers) . " recipients!</p>";
            } catch (Exception $e) {
                $status_message = "<p style='color: red;'>API Error: " . $e->getMessage() . "</p>";
            }
        } else {
            $status_message = "<p style='color: orange;'>No valid phone numbers found.</p>";
        }
    } else {
        $status_message = "<p style='color: red;'>Database Error: " . $conn->error . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Send Bulk SMS</title>
    <style>
        body { font-family: sans-serif; margin: 40px; line-height: 1.6; }
        .container { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        textarea { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h3>Send Message to All supporters</h3>
    
    <?php echo $status_message; ?>

    <form action="" method="POST">
        <label for="message">Your Message:</label>
        <textarea name="message" id="message" rows="5" placeholder="Type your message here..." required></textarea>
        <br><br>
        <button type="submit">Send SMS</button>
        
    </form>
</div>

</body>
</html>
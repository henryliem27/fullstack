<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $profile = 'user'; 
    $checkUsername = $conn->query("SELECT * FROM member WHERE username = '$username'");
    
    if ($checkUsername->num_rows > 0) {
        $message = "Username already exists. Please choose another.\n";
        $messageClass = "error";
    } else {
        $sql = "INSERT INTO member (fname, lname, username, password, profile) 
                VALUES ('$fname', '$lname', '$username', '$password', 'member')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful! You can now <a href='login.php'>login</a>. \n";
            $messageClass = "success";
        } else {
            $message = "Error: " . $sql . "\n<br>" . $conn->error;
            $messageClass = "error";
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Member</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .register-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            color: #666;
        }
        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px; 
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-message {
            color: green; 
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <?php
        if(isset($message)){
            echo "". $message ." ".$messageClass;
        } ?>
        <h1>Register Member</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
        <label for="fname">First Name:</label>
            <input type="text" id="fname" name="fname" required>
            <label for="lname">Last Name:</label>
            <input type="text" id="lname" name="lname" required>
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <button type="submit">Register</button>
        </form>
        <div style="text-align: center; margin-top: 15px;">
            <a href="login.php">Already have an account? Login here</a>
        </div>
    </div>
</body>
</html>

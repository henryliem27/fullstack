<?php
include('config.php'); 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - eSport</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white; 
            padding: 20px; 
            border-radius: 5px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); 
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .menu {
            list-style-type: none;
            padding: 0;
            text-align: center;
            margin: 20px 0;
        }
        .menu li {
            display: inline-block;
            margin: 0 15px; 
        }
        .menu li a {
            text-decoration: none;
            background-color: #007bff; 
            color: white; 
            padding: 10px 20px; 
            border-radius: 5px; 
            transition: background-color 0.3s;
        }
        .menu li a:hover {
            background-color: #0056b3;
        }
        .logout-link {
            text-align: center;
            margin-top: 20px;
        }
        .logout-link a {
            color: #ff0000; 
            text-decoration: none;
            font-weight: bold; 
        }
        .logout-link a:hover {
            text-decoration: underline; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <ul class="menu">
            <li><a href="manage_team.php">Manage Teams</a></li>
            <li><a href="manage_game.php">Manage Games</a></li>
            <li><a href="manage_event.php">Manage Events</a></li>
            <li><a href="manage_achievement.php">Manage Achievements</a></li>
            <br>
            <br>
            <br>
            <li><a href="manage_proposals.php">Manage Join Proposals</a></li>
        </ul>
        
        <div class="logout-link">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

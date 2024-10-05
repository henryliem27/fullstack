<?php
// Koneksi ke database
include('config.php'); // Pastikan path sesuai dengan lokasi config.php

// Periksa apakah koneksi berhasil
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
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        h1 {
            text-align: center;
        }
        .menu {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }
        .menu li {
            display: inline-block;
            margin: 0 10px;
        }
        .menu li a {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .menu li a:hover {
            background-color: #0056b3;
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .logout-link a {
            color: #ff0000;
            text-decoration: none;
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
        </ul>

        <div class="logout-link">
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

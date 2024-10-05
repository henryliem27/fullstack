<?php
if (!file_exists( 'config.php' ) ) 
{
    die( 'Restricted access' ); 
}

require_once('config.php');

$sql = "SELECT t.idteam, g.idgame ,t.name AS team_name, g.name AS game_name
        FROM team t
        JOIN game g ON t.idgame = g.idgame";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - e-Sport</title>
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
        .team-list {
            list-style: none;
            padding: 0;
        }
        .team-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .team-item h2 {
            margin: 0;
        }
        .team-item p {
            margin: 5px 0;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to e-Sport</h1>
        <?php if ($result->num_rows > 0): ?>
            <ul class="team-list">
                <?php while($row = $result->fetch_assoc()): ?>
                    <li class="team-item">
                        <h2><?php echo htmlspecialchars($row['team_name']); ?></h2>
                        <p><strong>Game:</strong> <?php echo htmlspecialchars($row['game_name']); ?></p>
                        <?php
                        $idteam = $row['idteam'];
                        $idgame = $row['idgame'];
                        echo "<p><a href='game_detail.php?idgame=$idgame &idteam=$idteam'>View Details</a></p>"?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No teams found.</p>
        <?php endif; ?>
        <div class="login-link">
            <a href="login.php">Login</a>
        </div>
    </div>

    <?php $conn->close(); ?>
</body>
</html>

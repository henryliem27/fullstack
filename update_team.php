<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php
    if (!file_exists('config.php')) {
        die('Restricted access');
    }

    require_once('config.php');

    if (isset($_GET['idteam'])) {
        $id = $_GET['idteam'];
        $sql = "SELECT * FROM team WHERE idteam= ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$row = $res->fetch_assoc()) {
            die("Invalid ID");
        }
    }
    //game data dropdown list
    $gameQuery = "SELECT * FROM game";
    $resTeam = $conn->query($gameQuery);
    ?>
    <form action="manage_team.php" method="post">
        <h2>Update Team</h2>
        <div class="form-group">
            <label for="team_name">Team Name:</label>
            <input type="hidden" name="team_id" value="<?=$row['idteam']?>">
            <input type="text" id="team_name" name="team_name" value="<?php echo $row['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="game_id">Game:</label>
            <select id="game_id" name="game_id" required>
               
                <?php while ($teamRow = $resTeam->fetch_assoc()): ?>
                <option value="<?php echo $teamRow['idgame']; ?>"
                    <?php if ($teamRow['idgame'] == $row['idgame']) echo 'selected'; ?>>
                    <?php echo $teamRow['name']; ?>
                </option>
            <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" name="update_team">Update Team</button>
        </div>
    </form>

</body>

</html>
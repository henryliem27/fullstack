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

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
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

        .form-group input,
        .form-group select {
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

    if (isset($_GET['idgame'])) {
        $id = $_GET['idgame'];
        $sql = "SELECT * FROM game WHERE idgame = ?";
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
    <form action="manage_game.php" method="post">
        <h2>Update game</h2>
        <div class="form-group">
            <label for="game_id">Game Name:</label>
            <select name="game_id" aria-readonly="true">
                <?php while ($teamRow = $resTeam->fetch_assoc()): ?>
                    <option value="<?php echo $teamRow['idgame']; ?>" <?php if ($teamRow['idgame'] == $row['idgame'])
                           echo 'selected'; ?> aria-readonly="true">
                        <?php echo $teamRow['name']; ?>
                        <?php endwhile; ?>
                    </option>
               
            </select>
        </div>
        <div class="form-group">
            <label for="game_description">Game Description:</label>
            <input type="text" name="game_description" value="<?php echo $row['description']?>">
            <input type="hidden" name="game_name" value="<?= $row['name'] ?>">
        </div>
        <div class="form-group">
            <button type="submit" name="update_game">Update game</button>
        </div>
    </form>
</body>

</html>
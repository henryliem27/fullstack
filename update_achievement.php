<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Achievement</title>
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
    if (isset($_GET['idachievement'])) {
        $id = $_GET['idachievement'];
        $sql = "SELECT * FROM achievement WHERE idachievement = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$row = $res->fetch_assoc()) {
            die("Invalid ID");
        }
    }

    $teamQuery = "SELECT * FROM team";
    $resTeam = $conn->query($teamQuery);
    ?>
    <div class="container">
    <h1>Update Achievements</h1>
    <form method="POST" action="achievement_proses.php">
    <div class="form-group">
        <label for="achivement_name">Name:</label>
        <input type="text" value="<?php echo $row['name']; ?>" name="achievement_name">
        <label for="team_id">Team:</label>
            <select name="team_id" required>
                <?php while ($team = $resTeam->fetch_assoc()): ?>
                    <option value="<?php echo $team['idteam']; ?>" <?php if ($team['idteam'] == $row['idteam'])
                           echo 'selected'; ?>>
                        <?php echo $team['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <label for="date">Date:</label>
        <input type="date" name="date" value="<?php echo $row['date']; ?>" required>

        <label for="description">Description:</label>
        <input type="text" name="description" value="<?php echo $row['description']; ?>">
        <input type="hidden" name="idachievement" value="<?= $row['idachievement'] ?>">
        <input type="submit" name="update_achievement" value="Edit Achievement">
        </div>
    </form>
    </div>
    
</body>

</html>
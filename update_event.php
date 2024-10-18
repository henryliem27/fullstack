<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
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

    $idteams = [];
    if (isset($_GET['idevent'])) {
        $id = $_GET['idevent'];
        $sql = "SELECT e.idevent, e.name as event_name, t.name as team_name, t.idteam as teams_id, e.date, e.description 
                FROM event e 
                INNER JOIN event_teams et ON e.idevent=et.idevent 
                INNER JOIN team t ON et.idteam=t.idteam 
                WHERE e.idevent = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$res->num_rows) {
            die("Invalid ID");
        }
        while ($row = $res->fetch_assoc()) {
            $idteams[] = $row;
        }
    }

    $teamQuery = "SELECT * FROM team";
    $resTeam = $conn->query($teamQuery);
    ?>
    <form action="manage_event.php" method="post">
        <h2>Edit Event</h2>
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" name="event_name" value="<?php echo $idteams[0]['event_name']; ?>">
        </div>
        <div class="form-group">
            <label for="date">Event Date:</label>
            <input type="date" name="date" value="<?php echo $idteams[0]['date']; ?>" required>
        </div>
        <div class="form-group">
            <label for="team_name">Team:</label>
            <?php if (!empty($idteams)): ?>
                <?php while ($team = $resTeam->fetch_assoc()): ?>
                    <input type="checkbox" name="teams_id[]" value="<?php echo $team['idteam']; ?>" 
                        <?php echo in_array($team['idteam'], array_column($idteams, 'teams_id')) ? 'checked' : ''; ?>>
                    <?php echo $team["name"]; ?><br>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No teams associated with this event.</p>
            <?php endif; ?>
            <br>
            <label for="description">Description:</label>
            <input type="text" name="description" value="<?php echo htmlspecialchars($idteams[0]['description']); ?>">
        </div>
        <input type="hidden" name="event_id" value="<?= htmlspecialchars($idteams[0]['idevent']); ?>">
        <div class="form-group">
            <button type="submit" name="update_event">Update Event</button>
        </div>
    </form>

</body>

</html>

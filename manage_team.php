<?php
// Koneksi ke database
include('config.php');

if (isset($_POST['add_team'])) {
    $team_name = $_POST['team_name'];
    $game_id = $_POST['game_id'];

    $sql = "INSERT INTO team (name, idgame) VALUES (?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $team_name, $game_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Update
if (isset($_POST['update_team'])) {
    $team_id = $_POST['team_id'];
    $team_name = $_POST['team_name'];
    $game_id = $_POST['game_id'];

    $sql = "UPDATE team SET name = ?, idgame = ? WHERE idteam = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sii", $team_name, $game_id, $team_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete
if (isset($_GET['delete_team'])) {
    $team_id = intval($_GET['delete_team']);

    $sql = "DELETE FROM team WHERE idteam = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $team_id);
        $stmt->execute();
        $stmt->close();
    }
}

$limit = 3;
$query1 = "SELECT COUNT(idteam) AS total FROM team";
$result1 = $conn->query($query1);
$row1 = $result1->fetch_assoc();
$total_teams = $row1['total'];
// Fetch games for dropdown
$games = $conn->query("SELECT idgame, name FROM game");


$total_pages = ceil($total_teams / $limit);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;
$start = ($page - 1) * $limit;
$sql = "SELECT t.idteam, t.name AS team_name, g.name AS game_name
        FROM team t
        JOIN game g ON t.idgame = g.idgame LIMIT $start, $limit";
$teams = $conn->query($sql);
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teams</title>
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
    <div class="container">
        <h1>Manage Teams</h1>

        <!-- Form Add Team -->
        <form action="manage_team.php" method="post">
            <h2>Add New Team</h2>
            <div class="form-group">
                <label for="team_name">Team Name:</label>
                <input type="text" id="team_name" name="team_name" required>
            </div>
            <div class="form-group">
                <label for="game_id">Game:</label>
                <select id="game_id" name="game_id" required>
                    <?php while ($game = $games->fetch_assoc()): ?>
                        <option value="<?php echo $game['idgame']; ?>"><?php echo htmlspecialchars($game['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="add_team">Add Team</button>
            </div>
        </form>
        <h2>Existing Teams</h2>
        <table>
            <tr>
                <th>Team Name</th>
                <th>Game</th>
                <th>Actions</th>
            </tr>
            <?php while ($team = $teams->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($team['team_name']); ?></td>
                    <td><?php echo htmlspecialchars($team['game_name']); ?></td>
                    <td>
                        <a href="update_team.php?idteam=<?php echo $team['idteam']; ?>">Edit</a>
                        <a href="manage_team.php?delete_team=<?php echo $team['idteam']; ?>"
                            onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <div>
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($page == $i)
                       echo 'style="font-weight: bold;"'; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
        <button onclick="window.location.href='admin_dashboard.php';">Go Back</button>
    </div>
</body>

</html>
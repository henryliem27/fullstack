<?php

include('config.php');

$query = "SELECT COUNT(a.idachievement) AS total FROM achievement a
        INNER JOIN team t ON a.idteam = t.idteam";
$result = $conn->query($query);
if (!$result) {
    die('Error: ' . $conn->error);
}

$row = $result->fetch_assoc();
$total_teams = $row['total'];

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 3;
$total_pages = ceil($total_teams / $limit);
if ($page < 1)
    $page = 1;

$start = ($page * $limit) - $limit;
$sql = "SELECT a.idachievement, a.name AS achievement_name, t.name AS team_name , a.description
        FROM achievement a
        JOIN team t ON a.idteam = t.idteam LIMIT $start, $limit";
$achievements = $conn->query($sql);
$teams = $conn->query("SELECT idteam, name FROM team");
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Achievements</title>
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
    <div class="container">
        <h1>Manage Achievements</h1>
        <form action="achievement_proses.php" method="post">
            <h2>Add New Achievement</h2>
            <div class="form-group">
                <label for="achievement_name">Achievement Name:</label>
                <input type="text" id="achievement_name" name="achievement_name" required>
            </div>
            <div class="form-group">
                <label for="team_id">Team:</label>
                <select id="team_id" name="team_id" required>
                    <?php while ($team = $teams->fetch_assoc()): ?>
                        <option value="<?php echo $team['idteam']; ?>"><?php echo $team['name']; ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="date">Date:</label>
                <input type="date" name="date"required>
                <label for="description">Description:</label>
                <input type="text" name="description" id="textareadesc">
            </div>
            <div class="form-group">
                <button type="submit" name="add_achievement">Add Achievement</button>
            </div>
        </form>

        <h2>Existing Achievements</h2>
        <table>
            <tr>
                <th>Achievement Name</th>
                <th>Team</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php while ($achievement = $achievements->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($achievement['achievement_name']); ?></td>
                    <td><?php echo htmlspecialchars($achievement['team_name']); ?></td>
                    <td><?php echo $achievement['description']?></td>
                    <td>
                        <a href="update_achievement.php?idachievement=<?php echo $achievement['idachievement']; ?>">Edit</a>
                        <a href="achievement_proses.php?delete_achievement=<?php echo $achievement['idachievement']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
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

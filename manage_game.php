<?php
include('config.php');

// Handle Insert
if (isset($_POST['add_game'])) {
    $game_name = $_POST['game_name'];
    $game_description = $_POST['game_description'];
    $sql = "INSERT INTO game (name,description) VALUES (?,?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $game_name, $game_description);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Update
if (isset($_POST['update_game'])) {
    $game_id = $_POST['game_id'];
    $game_name = $_POST['game_name'];
    $game_description = $_POST['game_description'];
    $sql = "UPDATE game SET name = ?, description= ? WHERE idgame = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $game_name, $game_description, $game_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete_game'])) {
    $game_id = intval($_GET['delete_game']);

    $sql = "DELETE FROM game WHERE idgame = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $game_id);
        $stmt->execute();
        $stmt->close();
    }
}

$query = "SELECT COUNT(idgame) AS total FROM game";
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

// Fetch games
$sql = "SELECT *FROM game LIMIT $start, $limit";
$games = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Games</title>
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

        .form-group input {
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
        <h1>Manage Games</h1>
        <form action="manage_game.php" method="post">
            <h2>Add New Game</h2>
            <div class="form-group">
                <label for="game_name">Game Name:</label>
                <input type="text" id="game_name" name="game_name" required>
            </div>
            <div class="form-group">
                <label for="game_description">Description:</label>
                <input type="text" id="description" name="game_description" required>
            </div>
            <div class="form-group">
                <button type="submit" name="add_game">Add Game</button>
            </div>
        </form>
        <h2>Existing Games</h2>
        <table>
            <tr>
                <th>Game Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php while ($game = $games->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $game['name']; ?></td>
                    <td><?php echo $game['description'] ?></td>
                    <td>
                        <a href="update_game.php?idgame=<?php echo $game['idgame']; ?>">Edit</a>
                        <a href="manage_game.php?delete_game=<?php echo $game['idgame']; ?>"
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
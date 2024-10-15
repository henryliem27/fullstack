<?php
include('config.php');


$query = "SELECT COUNT(idteam) AS total FROM team";
$result = $conn->query($query);
if (!$result) {
    die('Error: ' . $conn->error);
}

$row = $result->fetch_assoc();
$total_teams = $row['total'];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3;
$total_pages = ceil($total_teams / $limit); 
if ($page < 1) $page = 1; 

$start = ($page* $limit)-$limit;
$sql = "SELECT t.idteam, t.name AS team_name, g.name AS game_name
        FROM team t
        JOIN game g ON t.idgame = g.idgame LIMIT $start, $limit";
$teams = $conn->query($sql);
?>
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
        <a href="?page=<?php echo $i; ?>" <?php if ($page == $i) echo 'style="font-weight: bold;"'; ?>>
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next</a>
    <?php endif; ?>
</div>

<button onclick="window.location.href='admin_dashboard.php';">Go Back</button>

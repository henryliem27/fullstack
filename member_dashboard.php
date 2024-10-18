<?php
session_start();
include('config.php');
$id = $_SESSION['idmember'];
if (!isset($_SESSION['idmember'])) {
    header("Location: login.php");
    exit();
}

$fetchTeamsSql = "SELECT t.idteam AS team_id, t.name AS team_name, g.name AS game_name 
                  FROM team t 
                  JOIN game g ON t.idgame = g.idgame";
$teamsResult = $conn->query($fetchTeamsSql);

if (!$teamsResult) {
    die("Query failed: " . $conn->error);
}
//ambil idmember dari session
// mengecek apakah member sudah memilih tim
//bila sudah maka tidak dapat mengajukan proposal lagi

$team_members=[];
$sql = "SELECT t.idteam AS team_id, t.name AS team_name, g.name AS game_name 
                  FROM team t 
                  JOIN game g ON t.idgame = g.idgame INNER JOIN team_members tm ON tm.idteam = t.idteam WHERE idmember = ?";
$stmt =$conn-> prepare($sql);
$stmt ->bind_param("i", $id);
$stmt -> execute();
$res = $stmt->get_result();
if (!$res->num_rows) {
    die("Invalid ID");
}else{
    while ($row2 = $res->fetch_assoc()):
        $team_members=$row2;
    endwhile;
}
//fetch team_members
$sql1 = "SELECT GROUP_CONCAT(t.name) AS tim ,m.username AS name ,m.idmember,tm.description FROM team_members tm INNER JOIN
team t ON t.idteam=tm.idteam INNER JOIN member m ON m.idmember = tm.idmember WHERE tm.idmember = ? GROUP BY idmember;";
$stmt1 =$conn-> prepare($sql1);
$stmt1 ->bind_param("i", $id);
$stmt1 -> execute();
$res1 = $stmt1->get_result();
if (!$res1->num_rows) {
    die("Invalid ID");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['join_team'])) {
    $idTeam = $_POST['idteam'];
    $idMember = $_SESSION['idmember'];
    $description = $_POST['description'] ?? '';

    $insertProposalSql = "INSERT INTO join_proposal (idmember, idteam, description, status) 
                          VALUES (?, ?, ?, 'waiting')";
    $insertStmt = $conn->prepare($insertProposalSql);

    if ($insertStmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $insertStmt->bind_param("iis", $idMember, $idTeam, $description);

    if ($insertStmt->execute()) {
        echo "<script>alert('Join proposal submitted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to submit proposal: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 5px 10px;
            margin-top: 10px;
            border: none;
            cursor: pointer;
            background-color: #007bff;
            color: white;
        }

        button:hover {
            background-color: #0056b3;
        }

        .back {
            display: block;
            margin: 20px 0;
            padding: 10px;
            background-color: #28a745;
            color: white;
            text-align: center;
            text-decoration: none;
        }

        .back:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <h1>Welcome to the Member Dashboard</h1>
    <h1><?=$_SESSION['user']?></h1>
    <br>
    <h2>Select a Team to Join</h2>
    <table>
        <thead>
            <tr>
                <th>Team Name</th>
                <th>Game Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <!--  -->
        <!--  -->
            <!--  -->
        <!-- masih belum bisa menghilangkan tim yang dipilih oleh Member -->
            <?php if ($teamsResult->num_rows > 0): ?>
                <?php while ($row = $teamsResult->fetch_assoc()):
                if(isset($team_members['idteam'])){
                    if ($row['team_id'] != $team_members['idteam'] && $team_members['team_id'] ==$id ):
                        echo'<tr>
                            <td> '. $row['team_name'].'</td>
                            <td>'. $row['game_name'].'</td>
                            <td>
                                <form method="POST" action="member_dashboard.php" style="display:inline;">
                                    <input type="hidden" name="idteam" value="'. $row['team_id'].'">
                                    <input type="text" name="description" placeholder="Reason for joining" required>
                                    <button type="submit" name="join_team">Join</button>
                                </form>
                            </td>
                        </tr>';
                    endif;
                } else{
                    echo'<tr>
                            <td> '. $row['team_name'].'</td>
                            <td>'. $row['game_name'].'</td>
                            <td>
                                <form method="POST" action="member_dashboard.php" style="display:inline;">
                                    <input type="hidden" name="idteam" value="'. $row['team_id'].'">
                                    <input type="text" name="description" placeholder="Reason for joining" required>
                                    <button type="submit" name="join_team">Join</button>
                                </form>
                            </td>
                        </tr>';
                }
                     ?>
                        
                    <?php
                endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No teams available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <h2> Display Team member</h2>
    <div class="container">
        <table>
            <tr>
                <th>Team Name</th>
                <th>Game Name</th>
                <th>Description</th>
            </tr>
            <?php while ($game = $res1->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $game['tim']; ?></td>
                    <td><?php echo $game['name']?></td>
                    <td><?php echo $game['description'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <a href="logout.php" class="back">Logout</a>

</body>
</html>
<?php
$conn->close();
?>
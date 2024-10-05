<?php
if (!file_exists('config.php')) {
    die('Restricted access');
}

require_once('config.php');
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>

<body>
    <?php
    $idgame = $_GET['idgame'];
    $sql = 'SELECT g.name as game_name,t.name as team_name FROM team t RIGHT JOIN game g ON g.idgame = t.idgame WHERE g.idgame = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $idgame);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        // Fetch the first row to display the game name once
        $firstRow = $res->fetch_assoc();
        $game = $firstRow['game_name'];

        echo "<form method='POST' action='gamedetailproses.php'>";
        echo "<div class='container'>";
        echo "<h2>{$game}</h2>";
        echo "<ul class='team-list'>";
        $prevTeam = '';

        // Output the first row data
        do {
            $team = $firstRow['team_name'];
            if ($team != $prevTeam) {
                echo "<li class='game-list'>";
                echo "<p><strong>{$team}</strong></p>";
            }
            $prevTeam = $team;
            echo "</li>";
        } while ($firstRow = $res->fetch_assoc());
       
        echo "</ul>";
        echo "</div>";
        echo "</form>";
    }


    $sql2 = "SELECT e.name as event_name, e.date FROM event e 
         INNER JOIN event_teams et ON et.idevent = e.idevent
         INNER JOIN team t ON t.idteam = et.idteam
         INNER JOIN game g ON g.idgame = t.idgame 
         WHERE g.idgame = ?";

$stmt2 = $conn->prepare($sql2);
$stmt2->bind_param('i', $idgame);
$stmt2->execute();
$res2 = $stmt2->get_result();

if ($res2->num_rows > 0) {
    $prevEvent = '';
    $prevDate = '';

    // Fetch the results and display
    while ($row = $res2->fetch_assoc()) {
        $event =$row['event_name'];
        $date =$row['date'];

        // Only output if event and date are not repeated
        if ($event != $prevEvent || $date != $prevDate) {
            $datenow = new DateTime();
            $dateevent = new DateTime($date);

            if ($dateevent > $datenow) {
                echo "<li><strong>Upcoming event:</strong></li>";
                echo "<p>{$event}</p>";
                echo "<p>{$date}</p>";
            } else {
                echo "<li><strong>Latest event:</strong></li>";
                echo "<p>{$event}</p>";
                echo "<p>{$date}</p>";
            }
        }
        // Update previous event and date for comparison
        $prevEvent = $event;
        $prevDate = $date;
    }
}
?>
    </form>
</body>

</html>
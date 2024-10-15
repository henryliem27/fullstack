<?php
// Koneksi ke database
include('config.php'); // Adjust path if necessary

if (isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $team_id = $_POST['team_id'];
    $date = $_POST['event_date'];
    $event_description = $_POST['event_description'];
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert new event
        $sqlEvent = "INSERT INTO event (name, date, description) VALUES (?, ?,?)";
        if ($stmtEvent = $conn->prepare($sqlEvent)) {
            $stmtEvent->bind_param("sss", $event_name, $date, $event_description);
            $stmtEvent->execute();
            $event_id = $conn->insert_id; // Get the last inserted event ID
            $stmtEvent->close();
        } else {
            throw new Exception("Error preparing event insert: " . $conn->error);
        }

        // Check if the provided team_id exists in the `team` table
        $sqlCheckTeam = "SELECT idteam FROM team WHERE idteam = ?";
        if ($stmtCheckTeam = $conn->prepare($sqlCheckTeam)) {
            $stmtCheckTeam->bind_param("i", $team_id);  // Use `i` for integer type
            $stmtCheckTeam->execute();
            $stmtCheckTeam->store_result();
            if ($stmtCheckTeam->num_rows > 0) {
                // Team exists, continue to associate with the event
                $stmtCheckTeam->close();
            } else {
                // Team does not exist, throw an error or handle it accordingly
                throw new Exception("Team ID does not exist.");
            }
        } else {
            throw new Exception("Error preparing team check: " . $conn->error);
        }

        // Insert the association into `event_teams` table
        $sqlEventTeam = "INSERT INTO event_teams (idevent, idteam) VALUES (?, ?)";
        if ($stmtEventTeam = $conn->prepare($sqlEventTeam)) {
            $stmtEventTeam->bind_param("ii", $event_id, $team_id);
            $stmtEventTeam->execute();
            $stmtEventTeam->close();
        } else {
            throw new Exception("Error preparing event_teams insert: " . $conn->error);
        }

        // Commit the transaction
        $conn->commit();
        echo "Insert successful";

    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        $conn->rollback();
        echo "Insert failed: " . $e->getMessage();
    }
}

// Handle Update
if (isset($_POST['update_event'])) {
    $event_id = $_POST['event_id'];
    $team_id = $_POST['team_id'];
    $event_name = $_POST['event_name'];
    $date = $_POST['date'];
    $description = $_POST['description'];

    try {
        // Update the event details
        $sqlEvent = "UPDATE event SET name = ?, date = ?, description=? WHERE idevent = ?";
        if ($stmtEvent = $conn->prepare($sqlEvent)) {
            $stmtEvent->bind_param("sssi", $event_name, $date, $description, $event_id);
            $stmtEvent->execute();
            $stmtEvent->close();
        } else {
            throw new Exception("Error preparing event update statement: " . $conn->error);
        }

        // Check if team needs to be updated
        $sqlTeam = "UPDATE event_teams SET idteam= ? WHERE idevent = ?";
        if ($stmtTeam = $conn->prepare($sqlTeam)) {
            $stmtTeam->bind_param("si", $team_id, $event_id);
            $stmtTeam->execute();
            $stmtTeam->close();
        } else {
            throw new Exception("Error preparing team update statement: " . $conn->error);
        }

        // // Commit the transaction
        // $conn->commit();
        echo "Update Successful";

    } catch (Exception $e) {
        echo "Update Failed: " . $e->getMessage();
    }
}
if (isset($_GET['delete_event'])) {
    $event_id = intval($_GET['delete_event']);

    $sql = "DELETE FROM event WHERE idevent = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();
    }
}

$query = "SELECT COUNT(e.idevent) AS total FROM event e INNER JOIN event_teams et ON e.idevent=et.idevent 
        INNER JOIN team t  ON et.idteam=t.idteam";
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
$sql = "SELECT e.idevent, e.name as event_name, t.name as team_name, e.date, e.description 
        FROM event e 
        INNER JOIN event_teams et ON e.idevent=et.idevent 
        INNER JOIN team t  ON et.idteam=t.idteam LIMIT $start, $limit";
$events = $conn->query($sql);
$teams = $conn->query("SELECT idteam, name FROM team");
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
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
        <h1>Manage Events</h1>
        <form action="manage_event.php" method="post">
            <h2>Add New Event</h2>
            <div class="form-group">
                <label for="event_name">Event Name:</label>
                <input type="text" name="event_name" required>
            </div>
            <div class="form-group">
                <label for="event_date">Event Date:</label>
                <input type="date" name="event_date" required>
            </div>
            <div class="form-group">
                <label for="team_id">Team:</label>
                <select id="team_id" name="team_id" required>
                    <?php while ($team = $teams->fetch_assoc()): ?>
                        <option value="<?php echo $team['idteam']; ?>"><?php echo $team['name']; ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="description">Description:</label>
                <input type="text" name="event_description">
            </div>
            <div class="form-group">
                <button type="submit" name="add_event">Add Event</button>
            </div>
        </form>

        <h2>Existing Events</h2>
        <table>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Team</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php while ($event = $events->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $event['event_name']; ?></td>
                    <td><?php echo $event['date']; ?></td>
                    <td><?php echo $event['team_name']; ?></td>
                    <td><?php echo $event['description']; ?></td>
                    <td>
                        <a href="update_event.php?idevent=<?php echo $event['idevent']; ?>">Edit</a>
                        <a href="manage_event.php?delete_event=<?php echo $event['idevent']; ?>"
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
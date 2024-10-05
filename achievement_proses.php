<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    require_once('config.php');
    if (isset($_POST['update_achievement'])) {
        $achievement_id = $_POST['idachievement'];
        $achievement_name = $_POST['achievement_name'];
        $team_id = $_POST['team_id'];
        $date = $_POST['date'];
        $description = $_POST['description'];

        $sql = "UPDATE achievement SET name = ?, idteam = ?, date = ?, description = ? WHERE idachievement = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sissi", $achievement_name, $team_id, $date, $description, $achievement_id);
            $stmt->execute();
            if (!$stmt->error) {
                echo "Update Sukses";
            } else {
                echo "Update Gagal";
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }

    if (isset($_GET['delete_achievement'])) {
        $achievement_id = $_GET['delete_achievement'];
        $sql = "DELETE FROM achievement WHERE idachievement = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $achievement_id);
            $stmt->execute();
            if ($stmt->error) {
                echo "Delete Failed: " . $stmt->error;
            } else {
                echo "Delete Successful";
            }
            $stmt->close();
        } else {
            echo "Error preparing delete statement: " . $conn->error;
        }
    }

    if (isset($_POST['add_achievement'])) {
        $achievement_name = $_POST['achievement_name'];
        $team_id = $_POST['team_id'];
        $date = $_POST['date'];
        $description = $_POST['description'];
        $sql = "INSERT INTO achievement (name, idteam, date, description) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("siss", $achievement_name, $team_id, $date, $description);
            $stmt->execute();
            if ($stmt->error) {
                echo "Insert Failed: " . $stmt->error;
            } else {
                echo "Insert Successful";
            }
            $stmt->close();
        } else {
            echo "Error preparing insert statement: " . $conn->error;
        }
    }
    $conn->close();
    header("Location: manage_achievement.php");
    ?>
</body>

</html>
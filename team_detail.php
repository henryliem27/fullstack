<?php
require_once('config.php');
// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if (isset($_GET['idteam'])) {
    $idteam = intval($_GET['idteam']);
    $sql = "SELECT t.name AS team_name, m.name AS member_name, g.name AS game_name
            FROM team t
            JOIN team_member tm ON t.idteam = tm.idteam
            JOIN member m ON tm.idmember = m.idmember
            JOIN game g ON t.idgame = g.idgame
            WHERE t.idteam = ?";

    // Persiapkan dan eksekusi statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $idteam);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $team_details = [];
            while ($row = $result->fetch_assoc()) {
                $team_details[] = $row;
            }
        } else {
            $team_details = null;
        }

        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
} else {
    echo "ID team tidak ditemukan.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tim</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        h1, h3, h2 {
            text-align: center;
        }
        .team-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #007bff;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($team_details): ?>
            <h1>Tim: <?php echo ($team_details[0]['team_name'])?></h1>
            <h3>Game: <?php echo ($team_details[0]['game_name'])?></h3>

            <h2>Anggota Tim:</h2>
            <ul>
                <?php foreach ($team_details as $detail): ?>
                    <li><?php echo htmlspecialchars($detail['member_name']); ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>! Anda telah login.</p>
            <?php else: ?>
                <div class="login-link">
                    <a href="login.php">Login</a> untuk bergabung dengan tim ini.
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>Tim tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</body>
</html>

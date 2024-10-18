<?php
session_start();
include('config.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['action'])) {
    $proposalId = $_POST['proposal_id']; 
    $action = $_POST['action'];

    if ($action === 'approve') {
        $updateStatusSql = "UPDATE join_proposal SET status = 'approved' WHERE idjoin_proposal = ?";
        $updateStmt = $conn->prepare($updateStatusSql);

        if ($updateStmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $updateStmt->bind_param("i", $proposalId);
        $updateStmt->execute();

        if ($updateStmt->error) {
            die("Error executing statement: " . $updateStmt->error);
        }
        $addMemberSql = "INSERT INTO team_members (idteam, idmember, description) 
                         SELECT jp.idteam, jp.idmember, 'Approved team member' 
                         FROM join_proposal jp 
                         WHERE jp.idjoin_proposal = ?";
        $insertStmt = $conn->prepare($addMemberSql);

        if ($insertStmt === false) {
            die("Error preparing insert statement: " . $conn->error);
        }

        $insertStmt->bind_param("i", $proposalId);
        $insertStmt->execute();

        if ($insertStmt->error) {
            die("Error executing insert statement: " . $insertStmt->error);
        }
    } elseif ($action === 'reject') {
        $rejectStatusSql = "UPDATE join_proposal SET status = 'rejected', updated_at = NOW() WHERE idjoin_proposal = ?";
        $rejectStmt = $conn->prepare($rejectStatusSql);

        if ($rejectStmt === false) {
            die("Error preparing reject statement: " . $conn->error);
        }

        $rejectStmt->bind_param("i", $proposalId);
        $rejectStmt->execute();

        if ($rejectStmt->error) {
            die("Error executing reject statement: " . $rejectStmt->error);
        }
    }
}
$query = "SELECT COUNT(*) AS total FROM (SELECT jp.idjoin_proposal, m.fname, m.lname, 
                             t.name AS team_name, jp.description, jp.status 
                      FROM join_proposal jp 
                      JOIN member m ON jp.idmember = m.idmember 
                      JOIN team t ON jp.idteam = t.idteam 
                      WHERE jp.status = 'waiting' 
                      ORDER BY jp.idjoin_proposal DESC) as id_proposal;";
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
//fetch proposal
$fetchProposalsSql = "SELECT jp.idjoin_proposal, m.fname, m.lname, 
                             t.name AS team_name, jp.description, jp.status 
                      FROM join_proposal jp 
                      JOIN member m ON jp.idmember = m.idmember 
                      JOIN team t ON jp.idteam = t.idteam 
                      WHERE jp.status = 'waiting' 
                      ORDER BY jp.idjoin_proposal DESC LIMIT $start, $limit";  
$result = $conn->query($fetchProposalsSql);
if (!$result) {
    die("Query failed: " . $conn->error);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Join Proposals</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            cursor: pointer;
        }
        .approve {
            background-color: #4CAF50;
            color: white;
        }
        .reject {
            background-color: #f44336;
            color: white;
        }
        .back {
            display: block;
            margin: 20px 0;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-align: center;
            text-decoration: none;
        }
        .back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Manage Join Proposals</h1>

    <table>
        <thead>
            <tr>
                <th>Member Name</th>
                <th>Team Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo$row['fname'] . ' ' . $row['lname']; ?></td>
                        <td><?php echo$row['team_name']; ?></td>
                        <td><?php echo$row['description']; ?></td>
                        <td>
                            <form method="POST" action="manage_proposals.php">
                                <input type="hidden" name="proposal_id" value="<?php echo $row['idjoin_proposal']; ?>">
                                <button type="submit" name="action" value="approve" class="approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No proposals found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
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
    <a href="admin_dashboard.php" class="back">Back to Dashboard</a>

</body>
</html>

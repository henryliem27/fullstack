<?php
session_start();
 
if (isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}
if (!file_exists( 'config.php' ) ) 
	{
		die( 'Restricted access' ); 
	}
  
  require_once('config.php');

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT idmember, profile FROM member WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows >0) {
        $stmt->bind_result($idmember, $profile);
        $stmt->fetch();

        // Simpan data ke session
        $_SESSION['username'] = $row['username'];
        
        // Arahkan berdasarkan profil
        if ($profile === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: member_dashboard.php");
        }
        exit();
    } else {
        $error = 'Invalid username or password.';
    }

    $stmt->close();
}

$conn->close();
?>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>

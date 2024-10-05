<?php
// Start the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// If you want to destroy the session cookie as well (optional but recommended for complete logout)
// if (ini_get("session.use_cookies")) {
//     $params = session_get_cookie_params();
//     setcookie(session_name(), '', time() - 42000,
//         $params["path"], $params["domain"],
//         $params["secure"], $params["httponly"]
//     );
// }

// Finally, destroy the session
session_destroy();

// Redirect to login or homepage
header("Location: login.php");
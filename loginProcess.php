<?php
session_start();

require_once("settings.php"); // SAME AS YOUR WORKING FILE

// Connect to database (same style as jobs page)
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get form data safely
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Simple validation
if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty");
    exit();
}

// Prepare SQL (SAFE against injection)
$sql = "SELECT User_ID, Username, Password FROM manager WHERE Username = ?";
$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("Query failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Check login
if ($user && $password === $user['Password']) {

    $_SESSION['user_id'] = $user['User_ID'];
    $_SESSION['username'] = $user['Username'];

    mysqli_close($conn);

    header("Location: Manager.php");
    exit();

} else {

    mysqli_close($conn);

    header("Location: login.php?error=invalid");
    exit();
}
?>
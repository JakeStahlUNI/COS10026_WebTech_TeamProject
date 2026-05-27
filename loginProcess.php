<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="Styles/login.css">
    <title>Using PHP Variables, arrays and operators</title>

</head>
<body>
    <h1>Login</h1>
    <?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == 'admin' && $password == 'password')
{
    $_SESSION['username'] = $username;
    header('Location: Manager.php');
    exit();
}
else
{
    echo "
    <div class='login-container'>
        <h1>Login Failed</h1>
        <p>Invalid username or password.</p>
        <a href='login.php'>Try again</a>
    </div>
    ";
}
?>
</body>
</html>
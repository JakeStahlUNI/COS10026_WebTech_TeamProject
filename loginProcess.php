<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <title>Using PHP Variables, arrays and operators</title>
</head>
<body>
    <h1>Login</h1>
    <?php
    session_start();
    $username = $_POST['username'];
    $password = $_POST['password'];

    if($username =='admin' && $password == 'password') 
        {
        $_SESSION['username'] = $username;
        header('Location: Manager.php');
        } 
    else 
        {
        echo "Invalid username or password.<a href='login.html'>Try again</a>";
        }
    ?>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <title>Using PHP Variables, arrays and operators</title>
</head>
<body>
    <?php
    session_start();
    if(isset($_SESSION['username']))
        {
        echo "Welcome, " . $_SESSION['username'] . "!";
        } 
    else 
        {
        header('Location: login.html');
        }
    ?>
</body>
</html>
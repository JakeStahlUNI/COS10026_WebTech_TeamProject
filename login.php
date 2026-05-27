// use Lab08 as ref
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <title>Using PHP Variables, arrays and operators</title>
</head>
<body>
    <h1>Login</h1>
    <form method="post" action ="loginProcess.php">
        <p>
            <label for="username">Username:</label>
            <input type="text" name="username" required> <br>
        </p>
        <p>
            <label for="password">Password:</label>
            <input type="password" name="password" required> <br>
        </p>
        <input type="hidden" name="token" value="abc123">
        <input type="submit" value="Login">
    </form>
</body>
</html>
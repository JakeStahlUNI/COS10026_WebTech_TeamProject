<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="Styles/login.css">
    <title>Using PHP Variables, arrays and operators</title>
</head>
<body>

    <div class="login-container">

        <h1>Login</h1>

        <form method="post" action="loginProcess.php">

            <p>
                <label for="username">Username:</label>
                <input type="text" name="username" required>
            </p>

            <p>
                <label for="password">Password:</label>
                <input type="password" name="password" required>
            </p>

            <input type="hidden" name="token" value="abc123">

            <input type="submit" value="Login">

        </form>

    </div>

</body>
</html>
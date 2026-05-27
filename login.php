<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">

    <title>Login</title>

    <link rel="stylesheet" href="Styles/login.css">

</head>

<body>

    <div class="login-container">

        <h1>Manager Login</h1>

        <form method="post" action="loginProcess.php">

            <p>
                <label for="username">Username:</label>

                <input 
                    type="text" 
                    name="username" 
                    id="username"
                    required>
            </p>

            <p>
                <label for="password">Password:</label>

                <input 
                    type="password" 
                    name="password" 
                    id="password"
                    required>
            </p>

            <input 
                type="hidden" 
                name="token" 
                value="abc123">

            <input 
                type="submit" 
                value="Login">

            <a href="index.php" class="home-button">
                Back to Home
            </a>

        </form>

    </div>

</body>
</html>
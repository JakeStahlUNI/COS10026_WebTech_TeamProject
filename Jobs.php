<?php
/* -------------------- Central Settings Inclusion -------------------- */
if (file_exists("settings.php")) {
    require_once("settings.php");
}

// Fallback configuration layer if variables are not predefined or named differently
$database_host     = isset($host) ? $host : "127.0.0.1";
$database_user     = isset($user) ? $user : "root";
$database_password = isset($pwd) ? $pwd : (isset($password) ? $password : "");
$database_name     = isset($sql_db) ? $sql_db : (isset($dbname) ? $dbname : "groupproject");

/* -------------------- Helper function -------------------- */
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/* -------------------- Connect to database -------------------- */
// Uses the dynamically verified configuration elements safely
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* -------------------- Collect and sanitise search query -------------------- */
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = sanitise_input($_GET['search']);
}

/* -------------------- Execute Prepared Statements -------------------- */
if ($search_query !== "") {
    // Queries data across your real case-sensitive database columns: Title or Description
    $search_sql = "SELECT * FROM jobs WHERE Title LIKE ? OR Description LIKE ?";
    $stmt = mysqli_prepare($conn, $search_sql);
    
    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($conn));
    }
    
    $search_param = "%" . $search_query . "%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    
    // Correctly saves the SQL statement data object pool into $result
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Default fallback if no search query has been submitted yet
    $sql = "SELECT * FROM jobs";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jobs | PixelCraft</title>
</head>
<body>

<header class="header">
    <a href="index.php" class="logo-link">
        <img src="images/Logo.png" alt="PixelCraft" class="logo">
    </a>

    <div class="search-and-nav">
        <nav class="nav-links">
            <a href="about.php">About</a>
            <a href="Jobs.php">Jobs</a>
            <a href="Apply.php">Apply</a>
        </nav>
        
        <div class="search-bar">
            <form action="Jobs.php" method="GET" style="display: inline;">
                <input type="text" name="search" placeholder="Search.." value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
    </div>
</header>

<main>
    <h1>Available Positions</h1>

    <?php
    // Safely verify data rows and map out elements iteratively using procedural fetchers
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <article>
                <h2>JOB #<?php echo htmlspecialchars($row['job_id'] . ' - ' . $row['Title']); ?></h2>
                
                <p><strong>Description:</strong></p>
                <p><?php echo htmlspecialchars($row['Description']); ?></p>
                
                <p><strong>Salary:</strong> $<?php echo number_format($row['Salary']); ?> AUD</p>

                <h3>Key Responsibilities</h3>
                <ul>
                    <?php 
                    // Explodes raw data text blocks from your DB by line-breaks into HTML bullet listings
                    $responsibilities = explode("\n", trim($row['Responsibilities']));
                    foreach ($responsibilities as $line) {
                        if (!empty(trim($line))) {
                            echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
                        }
                    }
                    ?>
                </ul>

                <h3>Qualifications</h3>
                <ul>
                    <?php 
                    // Explodes qualifications paragraphs into clean list items
                    $qualifications = explode("\n", trim($row['Qualifications']));
                    foreach ($qualifications as $line) {
                        if (!empty(trim($line))) {
                            echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
                        }
                    }
                    ?>
                </ul>
                
                <p><a href="Apply.php">Apply for this Position</a></p>
            </article>
            <hr>
            <?php
        }
    } else {
        // Output graceful information string if user search parameters yield zero entries
        echo "<p>No positions found matching your criteria.</p>";
        echo "<p><a href='Jobs.php'>Clear Search Criteria</a></p>";
    }

    // Close references and release connection assets cleanly
    if (isset($stmt) && $search_query !== "") {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    ?>
</main>

</body>
</html>
<?php
// Load your group's central credential settings file
require_once("settings.php");

/* -------------------- Helper function -------------------- */
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/* -------------------- Connect to database -------------------- */
// Using the exact procedural variables ($host, $user, $pwd, $sql_db) from your settings file
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
    // Searches against your case-sensitive columns 'Title' or 'Description'
    $search_sql = "SELECT * FROM jobs WHERE Title LIKE ? OR Description LIKE ?";
    $stmt = mysqli_prepare($conn, $search_sql);
    
    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($conn));
    }
    
    $search_param = "%" . $search_query . "%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    // Fallback if search string is empty or unsubmitted
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
    // Loop out retrieved job records dynamically
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
                    // Explodes multi-line database blocks back into item nodes cleanly
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
                    // Explodes multi-line qualification blocks into separate layout points
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
        // Safe display output if keyword yields 0 entries
        echo "<p>No positions found matching your criteria.</p>";
        echo "<p><a href='Jobs.php'>Clear Search Criteria</a></p>";
    }

    // Free results and close the connection resource cleanups
    if (isset($stmt) && $stmt) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    ?>
</main>

</body>
</html>
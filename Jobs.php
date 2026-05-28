<?php

// 1. Fetch global server setup variables
require_once("settings.php");

/* -------------------- Helper Functions -------------------- */

// Removes trailing spaces, slashes, and turns HTML tags into harmless text
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Breaks up text blocks by line breaks (\n) and echoes them as HTML list items
function print_list_items($text_data) {
    $lines = explode("\n", trim($text_data));
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
        }
    }
}

/* -------------------- Database Connection -------------------- */

// Start connection to MySQL database; stop page execution if it fails
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* -------------------- Process Search Form -------------------- */

$search_query = ""; 
if (isset($_GET['search'])) {
    $search_query = sanitise_input($_GET['search']);
}

/* -------------------- SQL Queries -------------------- */

if ($search_query !== "") {
    // Search Mode: Query using placeholders (?) to prevent SQL injection (security)
    $search_sql = "SELECT * FROM jobs WHERE Title LIKE ? OR Description LIKE ?";
    $stmt = mysqli_prepare($conn, $search_sql);
    
    if (!$stmt) {
        die("Statement preparation failed: " . mysqli_error($conn));
    }
    
    // Attach wildcards (%) so can search part of a job and would still return listing
    $search_param = "%" . $search_query . "%";
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} else {
    // Normal Mode: Fetch all job rows directly
    $sql = "SELECT * FROM jobs";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore active career opportunities and available job positions at PixelCraft. Join our creative team today.">
    <meta name="keywords" content="PixelCraft, careers, job listings, hiring, employment, developer, designer jobs">
    <meta name="author" content="PixelCraft Team">
    <title>Jobs | PixelCraft</title>
    <link rel="stylesheet" href="styles/jobs.css">
    <link rel="stylesheet" href="styles/Main.css">
</head>
<body>

<?php include 'header.inc'; ?>



<main>
    <section class="intro-section">
        <h1>Available Positions</h1>
        <p>Explore active career opportunities at PixelCraft.</p>
    </section>

    <?php
    // If jobs are found, process them row-by-row into an array
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <article class="job-listing">
                <h2>JOB #<?php echo htmlspecialchars($row['job_id'] . ' - ' . $row['Title']); ?></h2>
                
                <span class="job-section-title">Description</span>
                <p class="job-description"><?php echo htmlspecialchars($row['Description']); ?></p>
                
                <span class="job-section-title">Salary</span>
                <p class="job-salary">$<?php echo number_format($row['Salary']); ?> AUD</p>

                <span class="job-section-title">Key Responsibilities</span>
                <ul>
                    <?php print_list_items($row['Responsibilities']); ?>
                </ul>

                <span class="job-section-title">Qualifications</span>
                <ul>
                    <?php print_list_items($row['Qualifications']); ?>
                </ul>
                
                <div class="apply-container">
                    <a href="Apply.php?jobref=<?php echo urlencode($row['job_id']); ?>" class="apply-btn">Apply for this Position</a>
                </div>
            </article>
            <?php
        }
    } else {
        echo "<div class='job-listing'><p>No positions found matching your criteria.</p>";
        echo "<p><a href='Jobs.php'>Clear Search Criteria</a></p></div>";
    }

    /* -------------------- Closing Links -------------------- */
    if (isset($stmt) && $search_query !== "") {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    ?>
</main>

<?php include 'footer.inc'; ?>

</body>
</html>
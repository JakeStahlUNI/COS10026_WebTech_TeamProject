<?php
// 1. Load your group's central settings file exactly as required
require_once("settings.php");

/* -------------------- Helper function -------------------- */
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/* -------------------- Connect to database -------------------- */
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
    $sql = "SELECT * FROM jobs";
    $result = mysqli_query($conn, $sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs | PixelCraft</title>
    <link rel="stylesheet" href="styles/jobs.css">
</head>
<body>

<header class="header">
  <a href="index.php" class="logo-link">
    <img src="images/Logo.png" alt="PixelCraft" class="logo">
  </a>

  <div class="search-and-nav">
    <nav class="nav-links">
      <a href="about.php">About</a>
      <a href="Jobs.php" class="active">Jobs</a>
      <a href="Apply.php">Apply</a>
    </nav>
    
    <div class="search-bar">
      <form action="Jobs.php" method="GET">
        <input type="text" name="search" placeholder="Search.." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
      </form>
    </div>
  </div>
</header>

<main>
    <section class="intro-section">
        <h1>Available Positions</h1>
        <p>Explore active career opportunities at PixelCraft. Review requirements and submit an application using the details below.</p>
    </section>

    <?php
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
                    <?php 
                    $responsibilities = explode("\n", trim($row['Responsibilities']));
                    foreach ($responsibilities as $line) {
                        if (!empty(trim($line))) {
                            echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
                        }
                    }
                    ?>
                </ul>

                <span class="job-section-title">Qualifications</span>
                <ul>
                    <?php 
                    $qualifications = explode("\n", trim($row['Qualifications']));
                    foreach ($qualifications as $line) {
                        if (!empty(trim($line))) {
                            echo "<li>" . htmlspecialchars(trim($line)) . "</li>";
                        }
                    }
                    ?>
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

    if (isset($stmt) && $search_query !== "") {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    ?>
</main>

<footer class="footer">
    <div class="footer-left">
        <a href="index.php">Home</a>
        <span>•</span>
        <a href="about.php">About Us</a>
        <span>•</span>
        <a href="Jobs.php">Careers</a>
    </div>
    <div class="footer-right">
        <p>&copy; <?php echo date("Y"); ?> PixelCraft Agency. All rights reserved.</p>
    </div>
</footer>

</body>
</html>
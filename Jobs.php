<?php
// 1. Establish connection to your local XAMPP database
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "groupproject";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// 2. Read the search term submitted from the search form
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// 3. Select SQL query structure based on whether user typed a search term
if (!empty($search_query)) {
    // Searches strictly across your real database columns: Title and Description
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE Title LIKE ? OR Description LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default fallback to show all records if search bar is empty
    $sql = "SELECT * FROM jobs";
    $result = $conn->query($sql);
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
    // 4. Verify that records exist matching the search/view state
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <article>
                <h2>JOB #<?php echo htmlspecialchars($row['job_id'] . ' - ' . $row['Title']); ?></h2>
                
                <p><strong>Description:</strong></p>
                <p><?php echo htmlspecialchars($row['Description']); ?></p>
                
                <p><strong>Salary:</strong> $<?php echo number_format($row['Salary']); ?> AUD</p>

                <h3>Key Responsibilities</h3>
                <ul>
                    <?php 
                    // Converts multi-line plain text strings inside your database into real bullet listings
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
                    // Converts multi-line plain text fields into individual HTML list item bullets
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
        // Output gracefully if zero job listings match criteria
        echo "<p>No listings found matching your search term.</p>";
        echo "<p><a href='Jobs.php'>View All Jobs</a></p>";
    }
    
    // Close worker database interface thread
    $conn->close();
    ?>
</main>

</body>
</html>
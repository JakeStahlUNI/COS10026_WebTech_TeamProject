<?php
// Database connection setup using your credentials
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "groupproject";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Check if a search term was submitted from the header search bar
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

// Query implementation targeting your exact case-sensitive column titles
if (!empty($search_query)) {
    // Search matching against Title or Description
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE Title LIKE ? OR Description LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
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
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <article>
                <h2><?php echo htmlspecialchars($row['Title']); ?></h2>
                
                <p><strong>Description:</strong></p>
                <p><?php echo htmlspecialchars($row['Description']); ?></p>
                
                <p><strong>Salary:</strong> $<?php echo number_format($row['Salary']); ?> AUD</p>

                <h3>Key Responsibilities</h3>
                <ul>
                    <?php 
                    // Splits plain multi-line block text from the database into distinct array entries
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
                    // Splits plain multi-line block text into standard list item nodes
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
        echo "<p>No job listings found matching your search term.</p>";
        echo "<p><a href='Jobs.php'>View All Jobs</a></p>";
    }
    
    $conn->close();
    ?>
</main>

</body>
</html>
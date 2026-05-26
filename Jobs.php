<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "groupproject";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = trim($_GET['search']);
}

if (!empty($search_query)) {
    // Search filters through Title, Reference Code, or Description
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE title LIKE ? OR job_reference LIKE ? OR description LIKE ?");
    $search_param = "%" . $search_query . "%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
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
    <title>PixelCraft Agency - Jobs</title>
</head>
<body>

<header>
    <div>PixelCraft</div>
    <nav>
        <a href="#">about</a>
        <a href="#">jobs</a>
        <a href="#">apply</a>
        
        <form action="jobs.php" method="GET" style="display: inline;">
            <input type="text" name="search" placeholder="Search positions..." value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </nav>
</header>

<main>
    <div>
        <h2>Join PixelCraft</h2>
        <p>A creative agency providing web design, branding, and digital content services. We are recruiting front-end developers and designers to support client-focused web projects.</p>
    </div>

    <h3>Available Positions</h3>

    <?php
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            ?>
            <section>
                <h4><?php echo htmlspecialchars($row['job_reference'] . ' - ' . $row['title']); ?></h4>
                
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($row['salary_range']); ?></p>
                <p><strong>Reports to:</strong> <?php echo htmlspecialchars($row['reports_to']); ?></p>

                <h5>Key Responsibilities</h5>
                <ul>
                    <?php 
                    // Converts database lines back into single bullet points
                    $resp_items = explode("\n", trim($row['responsibilities']));
                    foreach ($resp_items as $item) {
                        if (!empty(trim($item))) {
                            echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                        }
                    }
                    ?>
                </ul>

                <h5>Requirements</h5>
                <ul>
                    <?php 
                    // Converts database lines back into requirements listings
                    $req_items = explode("\n", trim($row['requirements']));
                    foreach ($req_items as $item) {
                        if (!empty(trim($item))) {
                            echo "<li>" . htmlspecialchars(trim($item)) . "</li>";
                        }
                    }
                    ?>
                </ul>
            </section>
            <hr>
            <?php
        }
    } else {
        echo "<p>No positions found matching your criteria.</p>";
        echo "<p><a href='jobs.php'>Clear Search Criteria</a></p>";
    }
    $conn->close();
    ?>

    <footer>
        <h3>Why Work With Us?</h3>
        <p>PixelCraft offers a collaborative environment, flexible working arrangements, and opportunities to work on diverse client projects.</p>
    </footer>
</main>

</body>
</html>
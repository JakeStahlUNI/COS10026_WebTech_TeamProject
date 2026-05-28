<?php
session_start();
require_once("settings.php");

/* ---------------- DATABASE CONNECTION ---------------- */

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn)
{
    die("Database connection failed: " . mysqli_connect_error());
}

/* ---------------- LOGIN PROTECTION ---------------- */

if (!isset($_SESSION['username']))
{
    header("Location: login.php");
    exit();
}

/* ---------------- DISPLAY TABLE FUNCTION ---------------- */

function displayTable($result)
{
    if ($result && mysqli_num_rows($result) > 0)
    {
        echo "<table>";

        echo "<tr>
                <th>EOI Number</th>
                <th>Job Reference</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Skills</th>
                <th>Other Skills</th>
                <th>Status</th>
              </tr>";

        while ($row = mysqli_fetch_assoc($result))
        {
            echo "<tr>";

            echo "<td>" . $row["EOInumber"] . "</td>";
            echo "<td>" . $row["jobref"] . "</td>";
            echo "<td>" . $row["firstname"] . "</td>";
            echo "<td>" . $row["lastname"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>" . $row["skills"] . "</td>";
            echo "<td>" . $row["otherskills"] . "</td>";
            echo "<td>" . $row["status"] . "</td>";

            echo "</tr>";
        }

        echo "</table>";
    }
    else
    {
        echo "<p>No results found.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Manage EOIs</title>

<link rel="stylesheet" href="Styles/manager.css">

</head>

<body>

<div class="manager-container">

<h1>HR Manager Page</h1>

<a href="index.php" class="logout-btn">Logout</a>

<hr>

<!-- LIST ALL -->

<h2>List All EOIs</h2>

<form method="post">

    <input type="submit"
           name="list_all"
           value="Show All">

</form>

<hr>

<!-- SEARCH JOB REFERENCE -->

<h2>Search by Job Reference</h2>

<form method="post">

    <input type="text"
           name="job_ref"
           placeholder="Job Reference"
           required>

    <input type="submit"
           name="search_job"
           value="Search">

</form>

<hr>

<!-- SEARCH NAME -->

<h2>Search by Applicant Name</h2>

<form method="post">

    <input type="text"
           name="fname"
           placeholder="First Name">

    <input type="text"
           name="lname"
           placeholder="Last Name">

    <input type="submit"
           name="search_name"
           value="Search">

</form>

<hr>

<!-- DELETE -->

<h2>Delete EOIs by Job Reference</h2>

<form method="post">

    <input type="text"
           name="delete_ref"
           placeholder="Job Reference"
           required>

    <input type="submit"
           name="delete_btn"
           value="Delete">

</form>

<hr>

<!-- UPDATE STATUS -->

<h2>Update EOI Status</h2>

<form method="post">

    <input type="number"
           name="eoi_id"
           placeholder="EOI Number"
           required>

    <select name="status">

        <option value="New">New</option>
        <option value="Current">Current</option>
        <option value="Final">Final</option>
        <option value="Rejected">Rejected</option>

    </select>

    <input type="submit"
           name="update_status"
           value="Update">

</form>

<hr>

<!-- SORT -->

<h2>Sort EOIs</h2>

<form method="post">

    <select name="sort_field">

        <option value="EOInumber">EOI Number</option>
        <option value="firstname">First Name</option>
        <option value="lastname">Last Name</option>
        <option value="jobref">Job Reference</option>
        <option value="status">Status</option>

    </select>

    <input type="submit"
           name="sort_btn"
           value="Sort">

</form>

<hr>

<h2>Results</h2>

<?php

/* ---------------- LIST ALL ---------------- */

if (isset($_POST["list_all"]))
{
    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}

/* ---------------- SEARCH JOB REF ---------------- */

elseif (isset($_POST["search_job"]))
{
    $job_ref = trim($_POST["job_ref"]);

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM eoi WHERE jobref = ?");

    mysqli_stmt_bind_param($stmt, "s", $job_ref);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    displayTable($result);
}

/* ---------------- SEARCH NAME ---------------- */

elseif (isset($_POST["search_name"]))
{
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);

    $sql = "SELECT * FROM eoi WHERE 1=1";

    $params = [];
    $types = "";

    if ($fname != "")
    {
        $sql .= " AND firstname = ?";

        $params[] = $fname;

        $types .= "s";
    }

    if ($lname != "")
    {
        $sql .= " AND lastname = ?";

        $params[] = $lname;

        $types .= "s";
    }

    $stmt = mysqli_prepare($conn, $sql);

    if (count($params) > 0)
    {
        mysqli_stmt_bind_param($stmt,
            $types,
            ...$params);
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    displayTable($result);
}

/* ---------------- DELETE ---------------- */

elseif (isset($_POST["delete_btn"]))
{
    $delete_ref = trim($_POST["delete_ref"]);

    $stmt = mysqli_prepare($conn,
        "DELETE FROM eoi WHERE jobref = ?");

    mysqli_stmt_bind_param($stmt, "s", $delete_ref);

    mysqli_stmt_execute($stmt);

    echo "<p>EOIs deleted successfully.</p>";

    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}

/* ---------------- UPDATE STATUS ---------------- */

elseif (isset($_POST["update_status"]))
{
    $eoi_id = $_POST["eoi_id"];

    $status = $_POST["status"];

    $stmt = mysqli_prepare($conn,
        "UPDATE eoi
         SET status = ?
         WHERE EOInumber = ?");

    mysqli_stmt_bind_param($stmt,
        "si",
        $status,
        $eoi_id
    );

    mysqli_stmt_execute($stmt);

    echo "<p>Status updated successfully.</p>";

    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}

/* ---------------- SORT ---------------- */

elseif (isset($_POST["sort_btn"]))
{
    $allowed = [
        "EOInumber",
        "firstname",
        "lastname",
        "jobref",
        "status"
    ];

    $sort = $_POST["sort_field"];

    if (in_array($sort, $allowed))
    {
        $sql = "SELECT * FROM eoi ORDER BY $sort";

        $result = mysqli_query($conn, $sql);

        displayTable($result);
    }
}

/* ---------------- DEFAULT ---------------- */

else
{
    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}

/* ---------------- CLOSE DB ---------------- */

mysqli_close($conn);

?>

</div>

</body>
</html>
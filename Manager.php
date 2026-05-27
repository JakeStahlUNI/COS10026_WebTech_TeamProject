<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage EOIs</title>
</head>
<body>

<h1>HR Manager Page</h1>

<a href="logout.php">Logout</a>

<hr>

<!-- SEARCH BY JOB REFERENCE -->

<h2>Search by Job Reference</h2>

<form method="post">

    <input type="text" name="job_ref" placeholder="Enter Job Reference" required>

    <input type="submit" name="search_job" value="Search">

</form>

<!-- SEARCH BY NAME -->

<h2>Search by Applicant Name</h2>

<form method="post">

    <input type="text" name="fname" placeholder="First Name">

    <input type="text" name="lname" placeholder="Last Name">

    <input type="submit" name="search_name" value="Search">

</form>

<!-- DELETE EOIs -->

<h2>Delete EOIs by Job Reference</h2>

<form method="post">

    <input type="text" name="delete_ref" placeholder="Job Reference" required>

    <input type="submit" name="delete_btn" value="Delete EOIs">

</form>

<!-- UPDATE STATUS -->

<h2>Update EOI Status</h2>

<form method="post">

    <input type="number" name="eoi_id" placeholder="EOI Number" required>

    <select name="status">

        <option value="New">New</option>
        <option value="Current">Current</option>
        <option value="Final">Final</option>
        <option value="Rejected">Rejected</option>

    </select>

    <input type="submit" name="update_status" value="Update Status">

</form>

<!-- SORT -->

<h2>Sort EOIs</h2>

<form method="post">

    <select name="sort_field">

        <option value="EOI_number">EOI Number</option>
        <option value="first_name">First Name</option>
        <option value="last_name">Last Name</option>
        <option value="job_reference">Job Reference</option>
        <option value="status">Status</option>

    </select>

    <input type="submit" name="sort_btn" value="Sort">

</form>

<hr>

<h2>Results</h2>

<?php

function displayTable($result)
{
    if (mysqli_num_rows($result) > 0)
    {
        echo "<table>";

        echo "<tr>
                <th>EOI Number</th>
                <th>Job Reference</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Status</th>
              </tr>";

        while ($row = mysqli_fetch_assoc($result))
        {
            echo "<tr>";

            echo "<td>" . $row["EOI_number"] . "</td>";
            echo "<td>" . $row["job_reference"] . "</td>";
            echo "<td>" . $row["first_name"] . "</td>";
            echo "<td>" . $row["last_name"] . "</td>";
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






// SEARCH BY JOB REFERENCE

if (isset($_POST["search_job"]))
{
    $job_ref = trim($_POST["job_ref"]);

    $stmt = mysqli_prepare($conn,
        "SELECT * FROM eoi WHERE job_reference = ?");

    mysqli_stmt_bind_param($stmt, "s", $job_ref);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    displayTable($result);
}






// SEARCH BY NAME

elseif (isset($_POST["search_name"]))
{
    $fname = trim($_POST["fname"]);
    $lname = trim($_POST["lname"]);

    $sql = "SELECT * FROM eoi WHERE 1=1";

    if ($fname != "")
    {
        $sql .= " AND first_name = ?";
    }

    if ($lname != "")
    {
        $sql .= " AND last_name = ?";
    }

    $stmt = mysqli_prepare($conn, $sql);

    if ($fname != "" && $lname != "")
    {
        mysqli_stmt_bind_param($stmt, "ss", $fname, $lname);
    }
    elseif ($fname != "")
    {
        mysqli_stmt_bind_param($stmt, "s", $fname);
    }
    elseif ($lname != "")
    {
        mysqli_stmt_bind_param($stmt, "s", $lname);
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    displayTable($result);
}






// DELETE EOIs

elseif (isset($_POST["delete_btn"]))
{
    $delete_ref = trim($_POST["delete_ref"]);

    $stmt = mysqli_prepare($conn,
        "DELETE FROM eoi WHERE job_reference = ?");

    mysqli_stmt_bind_param($stmt, "s", $delete_ref);

    mysqli_stmt_execute($stmt);

    echo "<p class='message'>EOIs deleted successfully.</p>";

    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}






// UPDATE STATUS

elseif (isset($_POST["update_status"]))
{
    $eoi_id = $_POST["eoi_id"];
    $status = $_POST["status"];

    $stmt = mysqli_prepare($conn,
        "UPDATE eoi SET status = ? WHERE EOI_number = ?");

    mysqli_stmt_bind_param($stmt, "si",
        $status,
        $eoi_id
    );

    mysqli_stmt_execute($stmt);

    echo "<p class='message'>Status updated successfully.</p>";

    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}






// SORT RESULTS

elseif (isset($_POST["sort_btn"]))
{
    $allowed = array(
        "EOI_number",
        "first_name",
        "last_name",
        "job_reference",
        "status"
    );

    $sort = $_POST["sort_field"];

    if (in_array($sort, $allowed))
    {
        $sql = "SELECT * FROM eoi ORDER BY $sort";

        $result = mysqli_query($conn, $sql);

        displayTable($result);
    }
}






// DEFAULT = SHOW ALL EOIs

else
{
    $sql = "SELECT * FROM eoi";

    $result = mysqli_query($conn, $sql);

    displayTable($result);
}

?>

</body>
</html>

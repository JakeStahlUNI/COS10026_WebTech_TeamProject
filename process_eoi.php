<?php
require_once("settings.php");

/* -------------------- Block direct URL access -------------------- */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: apply.php");
    exit();
}

/* -------------------- Helper function -------------------- */
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/* -------------------- Collect and sanitise form input -------------------- */
$jobref        = isset($_POST["jobref"]) ? sanitise_input($_POST["jobref"]) : "";
$firstname     = isset($_POST["firstname"]) ? sanitise_input($_POST["firstname"]) : "";
$lastname      = isset($_POST["lastname"]) ? sanitise_input($_POST["lastname"]) : "";
$dob           = isset($_POST["dob"]) ? sanitise_input($_POST["dob"]) : "";
$gender        = isset($_POST["gender"]) ? sanitise_input($_POST["gender"]) : "";
$streetaddress = isset($_POST["streetaddress"]) ? sanitise_input($_POST["streetaddress"]) : "";
$suburb        = isset($_POST["suburb"]) ? sanitise_input($_POST["suburb"]) : "";
$state         = isset($_POST["state"]) ? sanitise_input($_POST["state"]) : "";
$postcode      = isset($_POST["postcode"]) ? sanitise_input($_POST["postcode"]) : "";
$email         = isset($_POST["email"]) ? sanitise_input($_POST["email"]) : "";
$phone         = isset($_POST["phone"]) ? sanitise_input($_POST["phone"]) : "";
$otherskills   = isset($_POST["otherskills"]) ? sanitise_input($_POST["otherskills"]) : "";
$declaration   = isset($_POST["declaration"]) ? sanitise_input($_POST["declaration"]) : "";

/* -------------------- Handle checkbox array -------------------- */
$skills = "";
if (isset($_POST["skills"]) && is_array($_POST["skills"])) {
    $safe_skills = array();
    foreach ($_POST["skills"] as $skill) {
        $safe_skills[] = sanitise_input($skill);
    }
    $skills = implode(", ", $safe_skills);
}

/* -------------------- Validation -------------------- */
$errors = array();

/* Required field checks */
if ($jobref === "") $errors[] = "Job reference number is required.";
if ($firstname === "") $errors[] = "First name is required.";
if ($lastname === "") $errors[] = "Last name is required.";
if ($dob === "") $errors[] = "Date of birth is required.";
if ($gender === "") $errors[] = "Gender is required.";
if ($streetaddress === "") $errors[] = "Street address is required.";
if ($suburb === "") $errors[] = "Suburb/Town is required.";
if ($state === "") $errors[] = "State is required.";
if ($postcode === "") $errors[] = "Postcode is required.";
if ($email === "") $errors[] = "Email address is required.";
if ($phone === "") $errors[] = "Phone number is required.";
if ($declaration === "") $errors[] = "Declaration must be accepted.";

/* Format checks */
if (!preg_match("/^[A-Za-z0-9]{5}$/", $jobref)) {
    $errors[] = "Job reference number must be exactly 5 alphanumeric characters.";
}

if (!preg_match("/^[A-Za-z]{1,20}$/", $firstname)) {
    $errors[] = "First name must contain letters only and be no more than 20 characters.";
}

if (!preg_match("/^[A-Za-z]{1,20}$/", $lastname)) {
    $errors[] = "Last name must contain letters only and be no more than 20 characters.";
}

if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $dob)) {
    $errors[] = "Date of birth must be in DD/MM/YYYY format.";
}

if (strlen($streetaddress) > 40) {
    $errors[] = "Street address must be no more than 40 characters.";
}

if (strlen($suburb) > 40) {
    $errors[] = "Suburb/Town must be no more than 40 characters.";
}

$valid_states = array("VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT");
if (!in_array($state, $valid_states)) {
    $errors[] = "A valid state or territory must be selected.";
}

if (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}

if (!preg_match("/^[0-9]{8,12}$/", $phone)) {
    $errors[] = "Phone number must contain 8 to 12 digits only.";
}

if (strlen($otherskills) > 300) {
    $errors[] = "Other skills must be no more than 300 characters.";
}

/* -------------------- Redirect if validation fails -------------------- */
if (!empty($errors)) {
    header("Location: apply.php?error=1");
    exit();
}

/* -------------------- Connect to database -------------------- */
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* -------------------- Create eoi table if not exists -------------------- */
$create_table_sql = "
CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    jobref VARCHAR(5) NOT NULL,
    firstname VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    dob VARCHAR(10) NOT NULL,
    gender VARCHAR(20) NOT NULL,
    streetaddress VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state VARCHAR(3) NOT NULL,
    postcode VARCHAR(4) NOT NULL,
    email VARCHAR(50) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT DEFAULT NULL,
    otherskills VARCHAR(300) DEFAULT NULL,
    declaration VARCHAR(10) NOT NULL,
    status ENUM('New', 'Current', 'Final') NOT NULL DEFAULT 'New'
)";

if (!mysqli_query($conn, $create_table_sql)) {
    die("Error creating eoi table: " . mysqli_error($conn));
}

/* -------------------- Insert record using prepared statement -------------------- */
$insert_sql = "
INSERT INTO eoi
(jobref, firstname, lastname, dob, gender, streetaddress, suburb, state, postcode, email, phone, skills, otherskills, declaration)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $insert_sql);

if (!$stmt) {
    die("Statement preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "ssssssssssssss",
    $jobref,
    $firstname,
    $lastname,
    $dob,
    $gender,
    $streetaddress,
    $suburb,
    $state,
    $postcode,
    $email,
    $phone,
    $skills,
    $otherskills,
    $declaration
);

if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_stmt_error($stmt));
}

/* -------------------- Get auto-generated EOInumber -------------------- */
$eoi_number = mysqli_insert_id($conn);

mysqli_stmt_close($stmt);
mysqli_close($conn);

/* -------------------- Redirect after success -------------------- */
header("Location: apply.php?success=1&eoi=" . urlencode($eoi_number));
exit();
?>
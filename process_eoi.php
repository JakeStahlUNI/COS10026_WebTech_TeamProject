<?php
require_once("settings.php");

/* -------------------- Block direct URL access -------------------- */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: apply.php");
    exit();
}//检查请求方法是否为POST，如果不是，则重定向回申请页面，防止直接通过URL访问此处理脚本

/* -------------------- Helper function -------------------- */
function sanitise_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}//清洗用户输入，去除多余空格、反斜杠，并转换特殊字符为HTML实体，以防止XSS攻击

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
//读取用户输入数据，并使用sanitise_input函数进行清洗，确保数据安全和格式正确，isset检查每个字段是否存在，避免未定义变量错误，如果字段不存在则赋值为空字符串
/* -------------------- Handle checkbox array -------------------- */
$skills = "";
if (isset($_POST["skills"]) && is_array($_POST["skills"])) {
    $safe_skills = array();//初始化一个空数组来存储清洗后的技能输入
    foreach ($_POST["skills"] as $skill) {
        $safe_skills[] = sanitise_input($skill);
    }
    $skills = implode(", ", $safe_skills);//将清洗后的技能输入连接成一个逗号分隔的字符串，以便存储在数据库中
}
//处理技能复选框数组，检查是否存在且为数组，然后逐个清洗每个技能输入，并将它们连接成一个逗号分隔的字符串，以便存储在数据库中
/* -------------------- Validation -------------------- */
$errors = array();//初始化一个空数组来存储验证错误信息，如果输入数据不符合要求，错误信息将被添加到这个数组中

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
}//使用正则表达式检查工作参考编号是否为5个字母或数字的组合，如果不符合要求，则添加错误信息到$errors数组中

if (!preg_match("/^[A-Za-z]{1,20}$/", $firstname)) {
    $errors[] = "First name must contain letters only and be no more than 20 characters.";
}//使用正则表达式检查名字是否仅包含字母且不超过20个字符，如果不符合要求，则添加错误信息到$errors数组中

if (!preg_match("/^[A-Za-z]{1,20}$/", $lastname)) {
    $errors[] = "Last name must contain letters only and be no more than 20 characters.";
}//使用正则表达式检查姓氏是否仅包含字母且不超过20个字符，如果不符合要求，则添加错误信息到$errors数组中

if (!preg_match("/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/[0-9]{4}$/", $dob)) {
    $errors[] = "Date of birth must be in DD/MM/YYYY format.";
}//使用正则表达式检查出生日期是否符合DD/MM/YYYY格式，如果不符合要求，则添加错误信息到$errors数组中

if (strlen($streetaddress) > 40) {
    $errors[] = "Street address must be no more than 40 characters.";
}
//检查街道地址是否超过40个字符，如果超过则添加错误信息到$errors数组中
if (strlen($suburb) > 40) {
    $errors[] = "Suburb/Town must be no more than 40 characters.";
}//检查郊区/城镇是否超过40个字符，如果超过则添加错误信息到$errors数组中

$valid_states = array("VIC", "NSW", "QLD", "NT", "WA", "SA", "TAS", "ACT");
if (!in_array($state, $valid_states)) {
    $errors[] = "A valid state or territory must be selected.";
}//定义一个包含有效州/领地的数组，然后检查用户输入的州是否在这个数组中，如果不在则添加错误信息到$errors数组中

if (!preg_match("/^[0-9]{4}$/", $postcode)) {
    $errors[] = "Postcode must be exactly 4 digits.";
}//使用正则表达式检查邮政编码是否为4位数字，如果不符合要求，则添加错误信息到$errors数组中

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "A valid email address is required.";
}//使用PHP内置的filter_var函数和FILTER_VALIDATE_EMAIL过滤器检查电子邮件地址是否有效，如果不符合要求，则添加错误信息到$errors数组中

if (!preg_match("/^[0-9]{8,12}$/", $phone)) {
    $errors[] = "Phone number must contain 8 to 12 digits only.";
}//使用正则表达式检查电话号码是否仅包含8到12位数字，如果不符合要求，则添加错误信息到$errors数组中

if (strlen($otherskills) > 300) {
    $errors[] = "Other skills must be no more than 300 characters.";
}//检查其他技能描述是否超过300个字符，如果超过则添加错误信息到$errors数组中

/* -------------------- Redirect if validation fails -------------------- */
if (!empty($errors)) {
    header("Location: apply.php?error=1");
    exit();
}//如果$errors数组不为空，说明存在验证错误，此时重定向回申请页面，并附加一个错误参数，以便在前端显示错误消息

/* -------------------- Connect to database -------------------- */
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}//使用mysqli_connect函数连接到数据库，如果连接失败，则输出错误信息并终止脚本执行

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
}//定义一个SQL语句来创建eoi表，如果表不存在的话，包含所有必要的字段和数据类型，并设置EOInumber为自动递增的主键，如果创建表失败，则输出错误信息并终止脚本执行

/* -------------------- Insert record using prepared statement -------------------- */
$insert_sql = "
INSERT INTO eoi
(jobref, firstname, lastname, dob, gender, streetaddress, suburb, state, postcode, email, phone, skills, otherskills, declaration)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";
//使用预处理语句插入记录，定义一个SQL插入语句，其中包含占位符（?）用于绑定参数，以防止SQL注入攻击，并确保数据安全
$stmt = mysqli_prepare($conn, $insert_sql);

if (!$stmt) {
    die("Statement preparation failed: " . mysqli_error($conn));
}//准备预处理语句，如果准备失败，则输出错误信息并终止脚本执行

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
);//绑定参数到预处理语句，指定参数类型（s表示字符串）和对应的变量，如果绑定失败，则输出错误信息并终止脚本执行

if (!mysqli_stmt_execute($stmt)) {
    die("Insert failed: " . mysqli_stmt_error($stmt));
}//执行预处理语句，如果执行失败，则输出错误信息并终止脚本执行

/* -------------------- Get auto-generated EOInumber -------------------- */
$eoi_number = mysqli_insert_id($conn);

mysqli_stmt_close($stmt);
mysqli_close($conn);
//获取自动生成的EOInumber，使用mysqli_insert_id函数获取最后插入记录的ID值，并将其存储在$eoi_number变量中，然后关闭预处理语句和数据库连接
/* -------------------- Redirect after success -------------------- */
header("Location: apply.php?success=1&eoi=" . urlencode($eoi_number));
exit();
?>
//如果插入成功重定向回申请页面并附加一个成功参数和EOInumber以便在前端显示成功消息和EOInumber
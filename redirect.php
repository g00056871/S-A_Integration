<?php
require_once 'config.php';
// get username, userpassword and user email from posted form
$username     = mysql_real_escape_string($_POST['username']);
$userpassword = mysql_real_escape_string($_POST['userpassword']);
$useremail    = mysql_real_escape_string($_POST['useremail']);

//connection to the database
    $dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
    $selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");
// insert new user to database
$sql = 'INSERT INTO user ' . '(user_name,user_email, user_password) ' . 'VALUES ( "' . $username . '", "' . $useremail . '", "' . $userpassword . '")';

$retval = mysql_query($sql, $dbhandle);
if (!$retval) {
    die('Could not enter data: ' . mysql_error());
}
echo "<div><br /><b>You have successfully created an account, you can now <a href='index.php'>LOG IN</a> using this account</b></div>";
mysql_close($dbhandle);
?>
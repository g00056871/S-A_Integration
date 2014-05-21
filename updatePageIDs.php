<?php
require_once 'config.php';
$pageID = $_GET['pid'];
$qid = $_GET['qid'];

$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql = "UPDATE smilequestions SET page_id=$pageID where q_id='$qid'";
//$msg = "Successful";
$result = mysql_query($sql) or die('error');
mysql_close($dbhandle);
//echo json_encode($msg);
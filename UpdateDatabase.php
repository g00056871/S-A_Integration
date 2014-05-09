<?php
require_once 'config.php';
$pageID = $_POST['pid'];
$question = mysql_real_escape_string($_POST['q']);
$correctAnswer = $_POST['cor'];
$op1 = mysql_real_escape_string($_POST['op1']);
$op2 = mysql_real_escape_string($_POST['op2']);
$op3 = mysql_real_escape_string($_POST['op3']);
$op4 = mysql_real_escape_string($_POST['op4']);
// insert SMILE question to our database
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql = "UPDATE smilequestions SET question='$question', correctAns='$correctAnswer', op1='$op1', op2='$op2', op3='$op3', op4='$op4', is_updated='true', fetch_date=CURDATE() WHERE page_id=$pageID";
//$sql = "INSERT INTO smilequestions(q_id, question, correctAns, op1,op2, op3, op4) VALUES ('$qid','$question', '$correctAnsIndex', '$op1', '$op2', '$op3', '$op4')";
$result = mysql_query($sql) or die('error');

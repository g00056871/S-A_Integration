<?php
require_once 'config.php';
$qid      = $_POST['qid'];
$question = mysql_real_escape_string($_POST['q']);
$correctAnsIndex = $_POST['correctAns'];
$op1      = mysql_real_escape_string($_POST['op1']);
$op2      = mysql_real_escape_string($_POST['op2']);
$op3      = mysql_real_escape_string($_POST['op3']);
$op4      = mysql_real_escape_string($_POST['op4']);
// insert SMILE question to our database
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql = "INSERT INTO smilequestions(q_id, question, correctAns, op1,op2, op3, op4) VALUES ('$qid','$question', '$correctAnsIndex', '$op1', '$op2', '$op3', '$op4')";
$result = mysql_query($sql) or die('error');

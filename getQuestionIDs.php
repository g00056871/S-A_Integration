<?php
require_once 'config.php';

$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql2 = "SELECT q_id,page_id FROM smilequestions";
$resultq = mysql_query($sql2) or die('error');
$questions = array();
while ($row = mysql_fetch_array($resultq)){
    $questions[] = array(
      'pid' => $row['page_id'],
      'qid' => $row['q_id'],
    );
}

mysql_close($dbhandle);

echo json_encode($questions);
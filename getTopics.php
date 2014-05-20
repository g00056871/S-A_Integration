<?php
require_once 'config.php';
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBnamewiki, $dbhandle) or die("Could not select examples");
$sql = "SELECT cat_title, cat_pages FROM category WHERE cat_pages!=0 AND cat_subcats=0";
$result = mysql_query($sql) or die('error');
$topics = array();
while ($row = mysql_fetch_array($result)){
    $topics[] = array(
      'topic' => str_replace('_',' ',$row['cat_title']),
      'noq' => $row['cat_pages'],
    );
}
mysql_close($dbhandle);
echo json_encode($topics);
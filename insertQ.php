<?php
require_once 'config.php';
$qid      = $_POST['qid'];
$question = mysql_real_escape_string($_POST['q']);
$correctAnsIndex = $_POST['correctAns'];
$op1      = mysql_real_escape_string($_POST['op1']);
$op2      = mysql_real_escape_string($_POST['op2']);
$op3      = mysql_real_escape_string($_POST['op3']);
$op4      = mysql_real_escape_string($_POST['op4']);
//$pageID = $_POST['pid'];
// insert SMILE question to our database
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql = "INSERT INTO smilequestions(q_id, question, correctAns, op1,op2, op3, op4) VALUES ('$qid','$question', '$correctAnsIndex', '$op1', '$op2', '$op3', '$op4')";
$result = mysql_query($sql) or die('error');
mysql_close($dbhandle);
// This is the moved code from fetchQ.php page which is requested 
// from main page when we want pushing questions to assess wiki
/*
$qid = 0;
$sql = "SELECT * FROM smilequestions WHERE q_id='$qid'";
$result = mysql_query($sql) or die('error');
$row          = mysql_fetch_array($result);
$question     = $row['question'];
$option1      = $row['op1'];
$option2      = $row['op2'];
$option3      = $row['op3'];
$option4      = $row['op4'];
$correctAns   = $row['correctAns'];
$correctValue = 'option_' . $correctAns;

// now create the XML string
$XML = '<?xml version="1.0"?>
<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1 http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" toolName="Eqiat" toolVersion="0.7" adaptive="false" timeDependent="false" identifier="ITEM_2c3390b4e0e3455706aaeca645a4853a" title="Uncategorized.Q1">
  <responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">
    <correctResponse>
        <value>' . $correctValue . '</value>
        </correctResponse>
  </responseDeclaration>
  <outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer">
    <defaultValue>
      <value>0</value>
    </defaultValue>
  </outcomeDeclaration>
  <itemBody>
    <div class="eqiat-mcr">
      <choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="1">
        <prompt>' . $question . '</prompt>
        <simpleChoice identifier="option_0">' . $option1 . '</simpleChoice>
        <simpleChoice identifier="option_1">' . $option2 . '</simpleChoice>';
if ($option3 !== "") {
    $XML .= '<simpleChoice identifier="option_2">' . $option3 . '</simpleChoice>';
}
if ($option4 !== "") {
    $XML .= '<simpleChoice identifier="option_3">' . $option4 . '</simpleChoice>';
}
$XML .= '</choiceInteraction>
    </div>
  </itemBody>
  <responseProcessing>
    <responseCondition>
      <responseIf>
        <match>
          <variable identifier="RESPONSE"/>
          <correct identifier="RESPONSE"/>
        </match>
        <setOutcomeValue identifier="SCORE">
          <baseValue baseType="integer">1</baseValue>
        </setOutcomeValue>
      </responseIf>
      <responseElse>
        <setOutcomeValue identifier="SCORE">
          <baseValue baseType="integer">0</baseValue>
        </setOutcomeValue>
      </responseElse>
    </responseCondition>
  </responseProcessing>
</assessmentItem>';
$XML = trim($XML);

$dbhandle2 = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected2 = mysql_select_db($DBnamewiki, $dbhandle2) or die("Could not select examples");

$sql2 = "INSERT INTO page(page_id, page_namespace, page_title, page_is_redirect, page_is_new, page_latest, page_len, page_random, page_touched) VALUES (13,0,'Uncategorized.Q1',0,1,45,1800,'0.694533812339', '20140507132957')";
$result2 = mysql_query($sql2) or die('error');

$XML .= '[[Category:Uncategorized]]';

$sql3 = "INSERT INTO revision(rev_id, rev_page, rev_text_id, rev_comment, rev_user, rev_user_text, rev_timestamp, rev_minor_edit, rev_deleted, rev_len, rev_parent_id) VALUES (45,13,48,'Created page with',1,'Admin','20140507122957',0,0,1800,0)";
$result3 = mysql_query($sql3) or die('error');

$sql4 = "INSERT INTO text(old_id, old_text, old_flags) VALUES (48,'$XML','utf-8')";
$result4 = mysql_query($sql4) or die('error');

mysql_close($dbhandle);
mysql_close($dbhandle2);
*/
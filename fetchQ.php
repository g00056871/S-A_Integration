<?php
$dbhandle = mysql_connect('localhost', 'root', '') or die("Unable to connect to MySQL");
$selected = mysql_select_db('sa_integration', $dbhandle) or die("Could not select examples");
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
if ($option3 !== " ") {
    $XML .= '<simpleChoice identifier="option_2">' . $option3 . '</simpleChoice>';
}
if ($option4 !== " ") {
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

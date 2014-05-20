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

// here we will read database and update MSILE files
// update smile question
// first fetch updated fields from our database 
// then update questions html files
// finally delete questions from database
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");

$sql = "SELECT * FROM smilequestions";
$result = mysql_query($sql) or die('error');
while ($row = mysql_fetch_array($result))
    $rows[] = $row;
foreach ($rows as $row) {
    if ($row['is_updated'] == 'true') {
        $qid        = $row['q_id'];
        $question   = $row['question'];
        $correctAns = $row['correctAns'] - 1;
        $op1        = $row['op1'];
        $op2        = $row['op2'];
        $op3        = $row['op3'];
        $op4        = $row['op4'];
        
        // now update qid.html file
        $prevQuestion   = "";
        $prevCorrectAns = "";
        $prevOp1        = "";
        $prevOp2        = "";
        $prevOp3        = "";
        $prevOp4        = "";
        
        // Open the file to get existing content
        $smilefileurl1 = "c:/wamp/www/SMILE/current/" . $qid . ".html";
        $file          = fopen($smilefileurl1, "r");
        if (!$file) {
            echo "<p>Unable to open remote file for writing.\n";
            exit;
        }
        $getQuestion = false;
        $prevContent = "";
        while (!feof($file)) {
            $line = fgets($file);
            $prevContent .= $line;
            if ($getQuestion === true) {
                if (strpos($line, '<') !== false) {
                    list($part1, $part2) = explode('<', $line);
                    $prevQuestion = $part1;
                } else {
                    $prevQuestion = $line;
                }
                $getQuestion = false;
            }
            if (strpos($line, 'Question:') !== false) {
                $getQuestion = true;
            }
            if (strpos($line, '(1)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp1 = $part3;
            }
            if (strpos($line, '(2)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp2 = $part3;
            }
            if (strpos($line, '(3)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp3 = $part3;
            }
            if (strpos($line, '(4)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp4 = $part3;
            }
        }
        fclose($file);
        $newContent = str_replace($prevQuestion, $question, $prevContent);
        $newContent = str_replace($prevOp1, '(1) '.$op1, $newContent);
        $newContent = str_replace($prevOp2, '(2) '.$op2, $newContent);
        $newContent = str_replace($prevOp3, '(3) '.$op3, $newContent);
        $newContent = str_replace($prevOp4, '(4) '.$op4, $newContent);
        /* Write the data here. */
        $file       = fopen($smilefileurl1, "w");
        if (!$file) {
            echo "<p>Unable to open remote file for writing.\n";
            exit;
        }
        fwrite($file, $newContent);
        fclose($file);
        
        // now update qid_reault.html file
        
        $prevQuestion   = "";
        $prevCorrectAns = "";
        $prevOp1        = "";
        $prevOp2        = "";
        $prevOp3        = "";
        $prevOp4        = "";
        
        
        // Open the file to get existing content
        $smilefileurl2 = "c:/wamp/www/SMILE/current/" . $qid . "_result.html";
        $file          = fopen($smilefileurl2, "r");
        if (!$file) {
            echo "<p>Unable to open remote file for writing.\n";
            exit;
        }
        $getQuestion = false;
        $prevContent = "";
        while (!feof($file)) {
            $line = fgets($file);
            $prevContent .= $line;
            if ($getQuestion === true) {
                $prevQuestion = $line;
                $getQuestion  = false;
            }
            if (strpos($line, 'Question:') !== false) {
                $getQuestion = true;
            }
            if (strpos($line, '(1)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp1 = $part3;
                if (strpos($line, '(Correct Answer)') !== false) {
                    //
                    $prevCorrectAns = 0;
                }
            }
            if (strpos($line, '(2)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp2 = $part3;
                if (strpos($line, '(Correct Answer)') !== false) {
                    //
                    $prevCorrectAns = 1;
                }
            }
            if (strpos($line, '(3)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp3 = $part3;
                if (strpos($line, '(Correct Answer)') !== false) {
                    //
                    $prevCorrectAns = 2;
                }
            }
            if (strpos($line, '(4)') !== false) {
                //list($part1, $part2) = explode(')', $line);
                list($part3, $part4) = explode('<', $line);
                $prevOp4 = $part3;
                if (strpos($line, '(Correct Answer)') !== false) {
                    //
                    $prevCorrectAns = 3;
                }
            }
        }
        fclose($file);
        
        $correctAnsIndicator = '<font color = red>&#10004;</font> (Correct Answer)';
        $newContent          = str_replace($prevQuestion, $question, $prevContent);
        $newContent          = str_replace($correctAnsIndicator, '', $newContent);
        if ($correctAns == 0) {
            $newContent = str_replace($prevOp1, '(1) '.$op1 . $correctAnsIndicator, $newContent);
        } else {
            $newContent = str_replace($prevOp1, '(1) '.$op1, $newContent);
        }
        if ($correctAns == 1) {
            $newContent = str_replace($prevOp2, '(2) '.$op2 . $correctAnsIndicator, $newContent);
        } else {
            $newContent = str_replace($prevOp2, '(2) '.$op2, $newContent);
        }
        if ($correctAns == 2) {
            $newContent = str_replace($prevOp3, '(3) '.$op3 . $correctAnsIndicator, $newContent);
        } else {
            $newContent = str_replace($prevOp3, '(3) '.$op3, $newContent);
        }
        if ($correctAns == 3) {
            $newContent = str_replace($prevOp4, '(4) '.$op4 . $correctAnsIndicator, $newContent);
        } else {
            $newContent = str_replace($prevOp4, '(4) '.$op4, $newContent);
        }
        // Write the data here. 
        $file = fopen($smilefileurl2, "w");
        if (!$file) {
            echo "<p>Unable to open remote file for writing.\n";
            exit;
        }
        fwrite($file, $newContent);
        fclose($file);
        
        $sql = "DELETE FROM smilequestions WHERE q_id='$qid'";
        $result = mysql_query($sql) or die('error');
    }
}


<?php
require_once 'config.php';
// update smile question
// first fetch updated fields from our database then update question html file
$smilefileurl1 = "c:/wamp/www/SMILE/current/0.html";
$smilefileurl2 = "c:/wamp/www/SMILE/current/0_result.html";
$dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
$selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");
$qid = 0;
$sql = "SELECT is_updated FROM smilequestions WHERE q_id='$qid'";
$result = mysql_query($sql) or die('error');
$row = mysql_fetch_array($result);
if ($row['is_updated'] == 'true') {
    // the question is updated so update its html file in smile server
    $sql = "SELECT question,op1,op2,op3,op4,correctAns FROM smilequestions WHERE q_id='$qid'";
    $result = mysql_query($sql) or die('error');
    $row        = mysql_fetch_array($result);
    $question   = $row['question'];
    $correctAns = $row['correctAns']-1;
    $op1        = $row['op1'];
    $op2        = $row['op2'];
    $op3        = $row['op3'];
    $op4        = $row['op4'];
    
    // now update 0.html file
    
    $prevQuestion   = "";
    $prevCorrectAns = "";
    $prevOp1        = "";
    $prevOp2        = "";
    $prevOp3        = "";
    $prevOp4        = "";
    
    
    // Open the file to get existing content
    $file = fopen($smilefileurl1, "r");
    if (!$file) {
        echo "<p>Unable to open remote file for writing.\n";
        exit;
    }
    $getQuestion = false;
    $prevContent = "";
    while (!feof($file)) {
        $line = fgets($file);
        $prevContent .= $line;
        //echo $line;
        if ($getQuestion === true) {
            list($part1, $part2) = explode('<', $line);
            $prevQuestion = $part1;
            $getQuestion  = false;
        }
        if (strpos($line, 'Question:') !== false) {
            $getQuestion = true;
        }
        if (strpos($line, '(1)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp1 = $part3;
        }
        if (strpos($line, '(2)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp2 = $part3;
        }
        if (strpos($line, '(3)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp3 = $part3;
        }
        if (strpos($line, '(4)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp4 = $part3;
        }
    }
    fclose($file);
    $newContent = str_replace($prevQuestion, $question, $prevContent);
    $newContent = str_replace($prevOp1, $op1, $newContent);
    $newContent = str_replace($prevOp2, $op2, $newContent);
    $newContent = str_replace($prevOp3, $op3, $newContent);
    $newContent = str_replace($prevOp4, $op4, $newContent);
    /* Write the data here. */
    $file       = fopen($smilefileurl1, "w");
    if (!$file) {
        echo "<p>Unable to open remote file for writing.\n";
        exit;
    }
    fwrite($file, $newContent);
    fclose($file);
    
    // now update 0_reault.html file
    
    $prevQuestion   = "";
    $prevCorrectAns = "";
    $prevOp1        = "";
    $prevOp2        = "";
    $prevOp3        = "";
    $prevOp4        = "";
    
    
    // Open the file to get existing content
    $file = fopen($smilefileurl2, "r");
    if (!$file) {
        echo "<p>Unable to open remote file for writing.\n";
        exit;
    }
    $getQuestion = false;
    $prevContent = "";
    while (!feof($file)) {
        $line = fgets($file);
        $prevContent .= $line;
        //echo $line;
        if ($getQuestion === true) {
            $prevQuestion = $line;
            $getQuestion  = false;
        }
        if (strpos($line, 'Question:') !== false) {
            $getQuestion = true;
        }
        if (strpos($line, '(1)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp1 = $part3;
            if (strpos($line, '(Correct Answer)') !== false) {
                //
                $prevCorrectAns = 0;
            }
        }
        if (strpos($line, '(2)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp2 = $part3;
            if (strpos($line, '(Correct Answer)') !== false) {
                //
                $prevCorrectAns = 1;
            }
        }
        if (strpos($line, '(3)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
            $prevOp3 = $part3;
            if (strpos($line, '(Correct Answer)') !== false) {
                //
                $prevCorrectAns = 2;
            }
        }
        if (strpos($line, '(4)') !== false) {
            list($part1, $part2) = explode(')', $line);
            list($part3, $part4) = explode('<', $part2);
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
        $newContent = str_replace($prevOp1, $op1 . $correctAnsIndicator, $newContent);
    } else {
        $newContent = str_replace($prevOp1, $op1, $newContent);
    }
    if ($correctAns == 1) {
        $newContent = str_replace($prevOp2, $op2 . $correctAnsIndicator, $newContent);
    } else {
        $newContent = str_replace($prevOp2, $op2, $newContent);
    }
    if ($correctAns == 2) {
        $newContent = str_replace($prevOp3, $op3 . $correctAnsIndicator, $newContent);
    } else {
        $newContent = str_replace($prevOp3, $op3, $newContent);
    }
    if ($correctAns == 3) {
        $newContent = str_replace($prevOp4, $op4 . $correctAnsIndicator, $newContent);
    } else {
        $newContent = str_replace($prevOp4, $op4, $newContent);
    }
    
    // Write the data here. 
    $file = fopen($smilefileurl2, "w");
    if (!$file) {
        echo "<p>Unable to open remote file for writing.\n";
        exit;
    }
    fwrite($file, $newContent);
    fclose($file);
    
}
<?php
// check if form is submitted
require_once 'config.php';
if (isset($_POST['username']) && isset($_POST['password'])) {
    // check if user exists in database
    $dbhandle = mysql_connect($DBserver, $DBuser, $DBpassword) or die("Unable to connect to MySQL");
    $selected = mysql_select_db($DBname, $dbhandle) or die("Could not select examples");
    
    $username     = mysql_real_escape_string($_POST['username']);
    $userpassword = mysql_real_escape_string($_POST['password']);
    $sql          = "SELECT user_group FROM user WHERE user_name='" . $username . "' AND user_password='" . $userpassword . "'";
    $result = mysql_query($sql) or die('error');
    
    if (mysql_num_rows($result) > 0) {
        // user exists, create session variable for user_group and redirect to main page
        $row = mysql_fetch_array($result);
        session_start();
        $_SESSION['usergroup'] = $row['user_group'];
        //no need to specify the path, because main.php is in the same directory as the index.php
        header('Location: main.php');
    } else {
        echo "<div><br /><b> please check your name and password</b></div>";
    }
}
?>

<!DOCTYPE html>
<!--
   To change this license header, choose License Headers in Project Properties.
   To change this template file, choose Tools | Templates
   and open the template in the editor.
   -->
<html>
   <head>
      <link type="text/css" rel="stylesheet" href="css/style.css" media="screen" />
      <title>Assessment Gateway</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <!-- Begin Page Content -->
   <div style="text-align: center;">
       <div style="font-family: verdana,arial; color: #000099; font-size: 1.00em;"><br /><br /><b>Welcome to Assessment Gateway</b></div>
      <div style="box-sizing: border-box; display: inline-block; width: auto; max-width: 480px; background-color: #FFFFFF; border: 2px solid #0361A8; border-radius: 5px; box-shadow: 0px 0px 8px #0361A8; margin: 50px auto auto;">
         <div style="background: #0361A8; border-radius: 5px 5px 0px 0px; padding: 15px;"><span style="font-family: verdana,arial; color: #D4D4D4; font-size: 1.00em; font-weight:bold;">Enter your login and password</span></div>
         <div style="background: ; padding: 15px">
            <style type="text/css" scoped>
               td { text-align:left; font-family: verdana,arial; color: #064073; font-size: 1.00em; }
               input { border: 1px solid #CCCCCC; border-radius: 5px; color: #666666; display: inline-block; font-size: 1.00em;  padding: 5px; width: 100%; }
               input[type="button"], input[type="reset"], input[type="submit"] { height: auto; width: auto; cursor: pointer; box-shadow: 0px 0px 5px #0361A8; float: right; margin-top: 10px; }
               table.center { margin-left:auto; margin-right:auto; }
               .error { font-family: verdana,arial; color: #D41313; font-size: 1.00em; }
            </style>
            <form method="post" action="#" name="aform" target="_top">
               <input type="hidden" name="action" value="login">
               <input type="hidden" name="hide" value="">
               <table class='center'>
                  <tr>
                     <td>User Name:</td>
                     <td><input type="text" name="username"></td>
                  </tr>
                  <tr>
                     <td>Password:</td>
                     <td><input type="password" name="password"></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                     <td><input type="submit" value="login"></td>
                  </tr>
                  <tr>
                     <td colspan=2>&nbsp;</td>
                  </tr>
                  <tr>
                     <td colspan=2>New user? Click <a href="newUser.php">here</a> to register.</td>
                  </tr>
               </table>
            </form>
         </div>
      </div>
   </div>
</html>
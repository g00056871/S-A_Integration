<?php
// check if form is submitted
if (isset($_POST['username']) && isset($_POST['password'])) {
    // check if user exists in database
    $dbhandle = mysql_connect('localhost', 'root', '') or die("Unable to connect to MySQL");
    $selected = mysql_select_db('sa_integration', $dbhandle) or die("Could not select examples");
    
    $username     = mysql_real_escape_string($_POST['username']);
    $userpassword = mysql_real_escape_string($_POST['password']);
    $sql          = "SELECT user_group FROM user WHERE user_name='" . $username . "' AND user_password='" . $userpassword . "'";
    $result = mysql_query($sql) or die('error');
    
    if (mysql_num_rows($result) > 0) {
        // user exists, create session variable for user_group and redirect to main page
        $row = mysql_fetch_array($result);
        session_start();
        $_SESSION['usergroup'] = $row['user_group'];
        header("Location: http://localhost/ourIntegratedSystem/main.php");
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
      <title>SMILE and Assessment wiki integration</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>
      <form method="POST" action="#">
         <div align="center">
            <div> <b>Integrating SMILE questions into the editorial cycle of Assessment Wiki </b></div>
            <br />
            Username: <input type="text" name="username" size="15" /><br />
            Password: <input type="password" name="password" size="15" /><br />
            <a href="newUser.php">Create account</a>
            <p><input type="submit" value="Login" /></p>
         </div>
      </form>
   </body>
</html>
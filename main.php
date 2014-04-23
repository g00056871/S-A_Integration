<?php
/*
 you can check if user is admin or ordinary user by checking value of $_SESSION['usergroup'] as follw:
session_start();
if ($_SESSION['usergroup']==0){ 
}
else if($_SESSION['usergroup']==1){
} 
 */
?>
<html>
   <head>
      <title>SMILE and Assessment wiki integration</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>
      <div align="center">
         <div>
            <div> <b>Integrating SMILE questions into the editorial cycle of Assessment Wiki </b></div>
            <br />
            <input type="button" id="fetchSMILE" value="Fetch SMILE questions"/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="pushAssess" value="Push questions to Assessment wiki"/>
         </div>
      </div>
   </body>
</html>
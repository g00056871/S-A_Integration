<html>
   <head>
      <title>Assessment Gateway</title>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
   </head>
   <body>
      <div style="text-align: center;">
         <div style="box-sizing: border-box; display: inline-block; width: auto; max-width: 480px; background-color: #FFFFFF; border: 2px solid #0361A8; border-radius: 5px; box-shadow: 0px 0px 8px #0361A8; margin: 50px auto auto;">
            <div style="background: #0361A8; border-radius: 5px 5px 0px 0px; padding: 15px;"><span style="font-family: verdana,arial; color: #D4D4D4; font-size: 1.00em; font-weight:bold;">Create new account </span></div>
            <div style="background: ; padding: 15px">
               <style type="text/css" scoped>
                  td { text-align:left; font-family: verdana,arial; color: #064073; font-size: 1.00em; }
                  input { border: 1px solid #CCCCCC; border-radius: 5px; color: #666666; display: inline-block; font-size: 1.00em;  padding: 5px; width: 100%; }
                  input[type="button"], input[type="reset"], input[type="submit"] { height: auto; width: auto; cursor: pointer; box-shadow: 0px 0px 5px #0361A8; float: right; margin-top: 10px; }
                  table.center { margin-left:auto; margin-right:auto; }
                  .error { font-family: verdana,arial; color: #D41313; font-size: 1.00em; }
               </style>
               <form method="post" action="redirect.php" name="aform" target="_top">
                  <input type="hidden" name="action" value="login">
                  <input type="hidden" name="hide" value="">
                  <table class='center'>
                     <tr>
                        <td>User Name:</td>
                        <td><input type="text" name="username"></td>
                     </tr>
                     <tr>
                        <td>Password:</td>
                        <td><input type="password" name="userpassword"></td>
                     </tr>
                     <tr>
                        <td>Email:</td>
                        <td><input type="text" name="useremail"></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" value="Create"></td>
                     </tr>
                     <tr>
                        <td colspan=2>&nbsp;</td>
                     </tr>
                  </table>
               </form>
            </div>
         </div>
      </div>
   </body>
</html>
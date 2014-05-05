<?php
require_once 'config.php';
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
            <input type="button" id="fetchSMILE" value="Fetch SMILE questions" onclick='getSMILEQuestions("<?php echo $smileServer ?>")'/>&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="pushAssess" value="Push questions to Assessment wiki"/>
            <input type="button" id="fetchAssess" value="fetch updated questions from Assessment wiki"/>
            <input type="button" id="updatesmile" value="update SMILE questions" onclick='updateSMILE("<?php echo $smileServer ?>")'/>
         </div>
      </div>
   </body>
</html>

<script type="text/javascript">
    function getHTTPObject() {
        var xhr = false;
        if (window.XMLHttpRequest) { //see if a XMLHttpRequest exists
            xhr = new XMLHttpRequest(); //if it exists change "xhr" to a new instance of the object
        } else if (window.ActiveXObject) { //see if ActiveX exists (for IE)
            try { //Allows newer versions of IE to use the newer ActiveX object
                xhr = new ActiveXObject("Msxml2.AMLHTTP"); //if it exists change "xhr" to a new instance of the object
            } catch (e) { //catches an error and resets "xhr" to false
                try { //Allows older versions of IE to fall back onto the older version using "try...catch"
                    xhr = new ActiveXObject("Microsoft.XMLHTTP"); //if it exists change "xhr" to a new instance of the object
                } catch (e) {
                    xhr = false;
                }
            }
        }
        return xhr;
    }

    function getSMILEQuestions(smileServer) {
        var file = smileServer+'/SMILE/current/0_result.html';
        var request = getHTTPObject();
        if (request) {
            request.onreadystatechange = function () {
                displayResponse(request);
            };
            request.open("GET", file, true);
            request.send(null);
        }
    }

    function displayResponse(request) {
        if (request.readyState === 4) {
            if (request.status === 200 || request.status === 304) {
                var div = document.createElement('div');
                div.innerHTML = request.responseText;
                var elements = div.childNodes;
                // get question id, question text and all options from the question html file in SMILE server
                var qid = 0;
                var question = elements[7].innerText.split('\n')[1];
                var correctAnswer = 0;
                var option1 = "";
                if(elements[9].innerText.split('\n')[1].indexOf("(Correct Answer)") > -1){
                    correctAnswer = 0;
                    option1 = elements[9].innerHTML.split('\n')[1].split('<')[0].split(')')[1];
                }
                else { option1 = elements[9].innerText.split('\n')[1].split(')')[1];}
                var option2 = "";
                if(elements[9].innerText.split('\n')[3].indexOf("(Correct Answer)") > -1){
                    correctAnswer = 1;
                    option2 = elements[9].innerHTML.split('\n')[3].split('<')[0].split(')')[1];
                }
                else { option2 = elements[9].innerText.split('\n')[3].split(')')[1];}
                var option3 = "";
                if(elements[9].innerText.split('\n')[5].indexOf("(Correct Answer)") > -1){
                    correctAnswer = 2;
                    option3 = elements[9].innerHTML.split('\n')[5].split('<')[0].split(')')[1];
                }
                else { option3 = elements[9].innerText.split('\n')[5].split(')')[1];}
                var option4 = "";
                if(elements[9].innerText.split('\n')[7].indexOf("(Correct Answer)") > -1){
                    correctAnswer = 3;
                    option4 = elements[9].innerHTML.split('\n')[7].split('<')[0].split(')')[1];
                }
                else { option4 = elements[9].innerText.split('\n')[7].split(')')[1];}
               
                var request2 = getHTTPObject();
                if (request2) {
                    request.onreadystatechange = function () {};
                    // post all question details to insertQ.php file to be inserted into database
                    request2.open("POST", "insertQ.php", true);
                    request2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    request2.send("q=" + question + "&op1=" + option1 + "&op2=" + option2 + "&op3=" + option3 + "&op4=" + option4 + "&qid=" + qid+"&correctAns="+correctAnswer);
                }


            }
        }
    }
    
    function updateSMILE(smileServer){
        // now update 0.html and 0_result.html files
        var file = smileServer+'/SMILE/current/0.html';
        var request = getHTTPObject();
        if (request) {
            request.onreadystatechange = function () {
                
            };
            request.open("POST", "updateQ.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("fileurl="+file);
        }
    }
</script>
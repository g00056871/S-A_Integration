<?php
require_once 'config.php';
$apiurl = $wikiServer.$wikiPath."/api.php?action=query&prop=revisions&pageids=13&rvprop=timestamp|user|comment|content";
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
            <input type="button" id="pushAssess" value="Push questions to Assessment Wiki" onclick='pushQuestionsToAssess()'/>
            <input type="button" id="fetchAssess" value="Fetch updated questions from Assessment Wiki" onclick='setGetRequest("<?php echo $apiurl ?>")'/>
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

    function getHttpObject() {
        var request = false;
        //Create the HttpRequest Object
        if (window.XMLHttpRequest) { // Mozilla, Safari,...
            request = new XMLHttpRequest();
            if (request.overrideMimeType) {
                request.overrideMimeType('text/xml');
            }
        }
        else if (window.ActiveXObject) { // IE
            try 
            {
                request = new ActiveXObject("Msxml2.XMLHTTP");
            } 
            catch (e) {
                try {
                    request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }
        if (!request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }
        return request;
    }

    function setGetRequest(url)
    {
        var request= getHttpObject();

        if(request)
        {
            request.onreadystatechange = function()
            {
                getContents(request);
            };
            request.open('GET', url, true);
            request.send(null);
        }
    }

    function getContents(request) {
        if (request.readyState == 4) {
            if (request.status == 200) {
                var parseXml;

                if (window.DOMParser) {
                    parseXml = function(xmlStr) {
                        return ( new window.DOMParser() ).parseFromString(xmlStr, "text/xml");
                    };
                } else if (typeof window.ActiveXObject != "undefined" && new window.ActiveXObject("Microsoft.XMLDOM")) {
                    parseXml = function(xmlStr) {
                        var xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
                        xmlDoc.async = "false";
                        xmlDoc.loadXML(xmlStr);
                        return xmlDoc;
                    };
                } else {
                    parseXml = function() { return null; }
                }
                
                var xmlDoc = parseXml(request.responseText);
                var pageID;
                var pageTitle = "";
                var correctResponseID = "";
                var questionText = "";
                var choices = [];
                var choicesIDs = [];
                if (xmlDoc) {
                    //window.alert(xmlDoc.documentElement.getElementsByTagName("pre")[0].textContent.trim());
                    var responseXML = parseXml(xmlDoc.documentElement.getElementsByTagName("pre")[0].textContent.trim());
                    //window.alert(responseXML.documentElement.firstChild.textContent);
                    pageID = responseXML.documentElement.getElementsByTagName("page")[0].getAttribute("pageid");
                    pageTitle = responseXML.documentElement.getElementsByTagName("page")[0].getAttribute("title");
                    
                    
                    var questionXML = parseXml(responseXML.documentElement.getElementsByTagName("rev")[0].textContent.trim());
                    correctResponseID = questionXML.documentElement.getElementsByTagName("correctResponse")[0].getElementsByTagName("value")[0].textContent.trim();
                    questionText = questionXML.documentElement.getElementsByTagName("itemBody")[0].getElementsByTagName("prompt")[0].textContent.trim();
                    var choicesD = questionXML.documentElement.getElementsByTagName("itemBody")[0].getElementsByTagName("simpleChoice");
                    for (var i=0;i<choicesD.length;i++)
                    { 
                        choices.push(choicesD[i].textContent.trim());
                        choicesIDs.push(choicesD[i].getAttribute("identifier"));
                    }
                    
                    //alert(responseXML.documentElement.getElementsByTagName("rev")[0].textContent.trim());
                    //window.alert(xmlDoc.documentElement.nodeName);
                }
                if(pageID){
                    //alert(pageID);
                }
                if(pageTitle)
                {
                    //alert(pageTitle);
                }
                if(correctResponseID)
                {
                    //alert(correctResponseID);
                }
                if(questionText)
                {
                    //alert(questionText);
                }
                if(choices && choicesIDs)
                {
                    //for (var i=0;i<choices.length;i++)
                    //{ 
                    //    alert(choicesIDs[i] + " " + choices[i]);
                    //}
                }
                
                var request = getHttpObject();
                if (request) {
                    request.onreadystatechange = function () {};
                    
                    request.open("POST", "UpdateDatabase.php", true);
                    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    //questionText = "Paris is the Capital of France.";
                    var updateParams = "q=" + questionText;
                    var correctAnswer = 0;
                    for (var i=0;i<choices.length;i++)
                    { 
                        updateParams += "&op" + (i + 1) + "=" + choices[i];
                        if (correctResponseID === choicesIDs[i])
                        {
                            correctAnswer = i+1;
                        }
                    }
                    
                    updateParams += "&cor=" + correctAnswer + "&pid=" + pageID;
                    //alert(updateParams);
                    request.send(updateParams);
                    //request.send("q=" + question + "&op1=" + option1 + "&op2=" + option2 + "&op3=" + option3 + "&op4=" + option4 + "&qid=" + qid);
                }
                //alert("Here, I should get the response text and update the smilequestions table in our database.");
            } 
            else {
                alert('There was a problem with the request.');
            }
        }
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
                var option2 = "";
                var option3 = "";
                var option4 = "";
                var elementsArr = elements[9].innerHTML.split('\n');
                for(var i=0;i<elementsArr.length;i++){
                    var element = elementsArr[i];
                    if(element.indexOf("(1)")>-1){
                        if(element.indexOf("(Correct Answer)") > -1){
                            option1 = element.split('<')[0].split(')')[1];
                            correctAnswer = 0;}
                        else {
                            option1 = elements[9].innerText.split('\n')[1].split(')')[1];
                        }
                    }
                    else if(element.indexOf("(2)")>-1){
                        if(element.indexOf("(Correct Answer)") > -1){
                            option2 = element.split('<')[0].split(')')[1];
                            correctAnswer = 1;}
                        else {
                            option2 = elements[9].innerText.split('\n')[3].split(')')[1];
                        }
                    }
                    else if(element.indexOf("(3)")>-1){
                        if(element.indexOf("(Correct Answer)") > -1){
                            option3 = element.split('<')[0].split(')')[1];
                            correctAnswer = 2;}
                        else {
                            option3 = elements[9].innerText.split('\n')[5].split(')')[1];
                        }
                    }
                    else if(element.indexOf("(4)")>-1){
                        if(element.indexOf("(Correct Answer)") > -1){
                            option4 = element.split('<')[0].split(')')[1];
                            correctAnswer = 3;}
                        else {
                            option4 = elements[9].innerText.split('\n')[7].split(')')[1];
                        }
                    }   
                }
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
    function pushQuestionsToAssess(){
        var request = getHttpObject();
        if (request) {
            request.onreadystatechange = function () {
            };
            request.open("POST", "fetchQ.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send();
        }
    }
</script>
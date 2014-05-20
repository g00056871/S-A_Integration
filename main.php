<?php
require_once 'config.php';
$apiurl = $wikiServer.$wikiPath."/api.php?action=query&prop=revisions&pageids=13&rvprop=timestamp|user|comment|content";
?>
<!--
this page has the following buttons with the following functionalities:
1.fetchSMILEAndPushToWiki : this will fetch smile questions and insert them to our database, 
then it will fetch them again from database and push them to assess wiki.
2.fetchUpdatedQuestionsFromWikiAndUpdateSMILE: this will fetch updated questions from assess wiki and push them to our databse, 
then it will read them from database and update SMILE files.
3.fetchWikiQuestionsAndPushToSMILE: this will fetch specific questions from wiki (based on topics specified by teacher)
then push them to SMILE session

-->
<html>
   <head>
        <title>Assessment Gateway</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--Use either an offline copy of JQuery at the local server or load an online copy from Google-->
        <!--<script type="text/javascript" src="jquery.js"></script>-->
        <script type="text/javascript">
            document.write("\<script src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' type='text/javascript'>\<\/script>");
        </script>
   </head>
   <body>
      <div style="text-align: center;">
      <div style="font-family: verdana,arial; color: #000099; font-size: 1.00em;"><br /><br /><b>Welcome to Assessment Gateway</b></div>
      <br />
      <br />
      <div style="background: ; padding: 15px">
            <style type="text/css" scoped>
               td { font-family: verdana,arial; color: #064073; font-size: 1.00em; }
               tr {text-align:center; }
               input { border: 1px solid #CCCCCC; border-radius: 5px; color: #666666; display: inline-block; font-size: 1.00em;  padding: 5px; width: 100%; }
               input[type="button"] { height: auto; width: auto; cursor: pointer; box-shadow: 0px 0px 5px #0361A8; margin-top: 10px; }
               table.center { margin-left:auto; margin-right:auto; }
               .error { font-family: verdana,arial; color: #D41313; font-size: 1.00em; }
            </style>
               <table class='center'>
                  <tr>
                     <td><input type="button" id="fetchSMILE" value="Fetch SMILE questions and push them to Assessment Wiki" onclick='fetchSMILEAndPushToWiki("<?php echo $smileServer ?>")'/>&nbsp;&nbsp;&nbsp;&nbsp;
                     </td>
                  </tr>
                  <tr>
                     <td><input type="button" id="fetchAssess" value="Fetch updated questions from Assessment Wiki and update them in SMILE" onclick='fetchUpdatedQuestionsFromWikiAndUpdateSMILE("<?php echo $apiurl ?>")'/>
                     </td>
                  </tr>
                  <tr>
                     <td><input type="button" id="pushToSMILE" value="Fetch Wiki questions and push them to SMILE" onclick=' window.location = "getWikiQs.php";'/>
                      </td>
                  </tr>
                  <tr>
                     <td colspan=2>&nbsp;</td>
                  </tr>
               </table>
         </div>
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

    function fetchUpdatedQuestionsFromWikiAndUpdateSMILE(url)
    {
        var request= getHttpObject();

        if(request)
        {
            request.onreadystatechange = function()
            {
                getContents(request);
            };
            request.open('GET', url, false);
            request.send(null);
        }
    }

    function getContents(request) {
        if (request.readyState === 4) {
            if (request.status === 200) {
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
                    request.onreadystatechange = function () {
                        if (request.readyState == 4) {
                            if (request.status == 200) {
                                alert ("operation completed successfully");
                            }
                            else {
                                alert ("error while doing the operation");
                            }
                        }
                    };
                    
                    request.open("POST", "UpdateDatabase.php", false);
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
    
    /* This function will read all questions in SMILE and insert them to our database
    * then it will read  them frm database and push them to Assess wiki
     * @returns {undefined}     */
    function fetchSMILEAndPushToWiki(smileServer) {
    var sendParam = new Array();
    processNext(0);

        function processNext(fileIndex) {
            var file = smileServer + '/SMILE/current/' + fileIndex + '_result.html';
            var request = getHTTPObject();
            if (request) {
                request.onreadystatechange = function () {
                    if (request.readyState === 4) {
                        if (request.status === 200 || request.status === 304) {
                            /******************************************/
                            var div = document.createElement('div');
                            div.innerHTML = request.responseText;
                            var elements = div.childNodes;
                            // get question id, question text and all options from the question html file in SMILE server
                            var qid = fileIndex;
                            //var pageID = 13;
                            var question = elements[7].innerText.split('\n')[1];
                            var correctAnswer = 0;
                            var option1 = "";
                            var option2 = "";
                            var option3 = "";
                            var option4 = "";
                            var elementsArr = elements[9].innerHTML.split('\n');
                            for (var i = 0; i < elementsArr.length; i++) {
                                var element = elementsArr[i];
                                if (element.indexOf("(1)") > -1) {
                                    if (element.indexOf("(Correct Answer)") > -1) {
                                        option1 = element.split('<')[0].split(')')[1];
                                        correctAnswer = 0;
                                    } else {
                                        option1 = elements[9].innerText.split('\n')[1].split(')')[1];
                                    }
                                } else if (element.indexOf("(2)") > -1) {
                                    if (element.indexOf("(Correct Answer)") > -1) {
                                        option2 = element.split('<')[0].split(')')[1];
                                        correctAnswer = 1;
                                    } else {
                                        option2 = elements[9].innerText.split('\n')[3].split(')')[1];
                                    }
                                } else if (element.indexOf("(3)") > -1) {
                                    if (element.indexOf("(Correct Answer)") > -1) {
                                        option3 = element.split('<')[0].split(')')[1];
                                        correctAnswer = 2;
                                    } else {
                                        option3 = elements[9].innerText.split('\n')[5].split(')')[1];
                                    }
                                } else if (element.indexOf("(4)") > -1) {
                                    if (element.indexOf("(Correct Answer)") > -1) {
                                        option4 = element.split('<')[0].split(')')[1];
                                        correctAnswer = 3;
                                    } else {
                                        option4 = elements[9].innerText.split('\n')[7].split(')')[1];
                                    }
                                }
                            }
                            sendParam[fileIndex] = "q=" + question + "&op1=" + option1 + "&op2=" + option2 + "&op3=" + option3 + "&op4=" + option4 + "&qid=" + qid + "&correctAns=" + correctAnswer;
                            /******************************************/
                            fileIndex++;
                            processNext(fileIndex);
                        } else {
                            // finish reading files and request params are ready in sendParam
                            // now we should send multiple requests to isnertQ
                            //updatePageIDs('22','1');
                            insertSMILEQuestionsToDB(sendParam, fileIndex,request);
                            pushQuestionsToAssess();
                            
                            
                            alert ("Operation Completed Successfully");
                        }
                    }
                };
                request.open("GET", file, false);
                request.send(null);
            }
        }
    }

    function insertSMILEQuestionsToDB(requestsParams, fileNumbers) {
        for (var i = 0; i < fileNumbers; i++) {
            var request2 = getHTTPObject();
            if (request2) {
                request2.onreadystatechange = function () {
                    if (request2.readyState === 4) {
                        if (request2.status === 200) {
                            //alert ("Operation Completed Successfully");
                            //pushQuestionsToAssess();
                        }
                        else {
                            alert ("Error");
                        }
                    }
                };
                // post all question details to insertQ.php file to be inserted into database
                request2.open("POST", "insertQ.php", false);
                request2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request2.send(requestsParams[i]);
            }
        }
    }
    
    function pushQuestionsToAssess()
    {
        requestEditToken();
        //alert("Successful");
    }
    function requestEditToken() 
    {
        var request= getHttpObject();
        if(request)
        {
            request.onreadystatechange = function()
            {
                getEditToken(request);
            };
            var url = "<?php echo $wikiServer.$mediawikiPath; ?>" + "/api.php?action=query&prop=info|revisions&intoken=edit&titles=Main%20Page";
            request.open('GET', url, false);
            request.send(null);
        }
    }

    function getEditToken(request) {
        if (request.readyState === 4) {
            if (request.status === 200) {
                //request.abort();
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
                if (xmlDoc) {
                    var responseXML = parseXml(xmlDoc.documentElement.getElementsByTagName("pre")[0].textContent.trim());
                    var token = responseXML.documentElement.getElementsByTagName("page")[0].getAttribute("edittoken").trim();
                    
                    $.getJSON('getQuestionDetails.php', function(data){

                        var len = data.length;
                        for (var i = 0; i< len; i++) {
                            performEdit(token,data[i].qtext,data[i].qtitle,data[i].qid);
                        }
                    });      
                }
            }
        }
    }
    function performEdit(editToken,qtext,qtitle,qid)
    {
        $.ajax({
        url: '<?php echo $wikiServer.$mediawikiPath; ?>' + '/api.php',
        data: {
            format: 'json',
            action: 'edit',
            recreate: 'true',
            title: qtitle,
            text: qtext,
            //createonly: 'true',
            //section: 'new',
            summary: 'New Question',
            //redirect: '',
            notminor: 'true',
            //contentformat: 'text/plain',
            //contentmodel: 'text',
            //appendtext: 'append',
            //prependtext: 'prepend',
            token: editToken
        },
        dataType: 'json',
        type: 'POST',
        success: function( data ) {
            if ( data && data.edit && data.edit.result === 'Success' ) {
                updatePageIDs(data.edit.pageid,qid);
                //alert('Successful');
                //window.location.reload(); // reload page if edit was successful
            } else if ( data && data.error ) {
                alert( 'Error: API returned error code "' + data.error.code + '": ' + data.error.info );
            } else {
                alert( 'Error: Unknown result from API.' );
            }
        },
        error: function( xhr ) {
            alert( 'Error: Request failed.' );
            }
        });
    }
    
    function updatePageIDs(pageid,quesid)
    {
//        $.getJSON('updatePageIDs.php',{ pid: pageid, qid: quesid }, function(data){
//            var len = data.length;
//            for (var i = 0; i< len; i++) {
//                alert(data[i].msg);
//            }
//        });
        var request = getHttpObject();
        if (request) {
            request.onreadystatechange = function () 
            {
                if (request.readyState === 4)
                {
                    if (request.status === 200)
                    {
                        
                        //alert("Successful");
                    }
                    else 
                    {
                        alert ("Error");
                    }
                }
            };
            var url = "updatePageIDs.php?pid="+pageid+"&qid="+quesid;
            request.open("GET", url, false);
            //request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //var params = "pid=" + pageid + "&qid=" + quesid;
            request.send(null);
        }
        else
            alert("cannot create request");
    }
    
    /*
    * this function will read updated SMILE questions from our database and update the corresponding questions in smile system
    * it will finally delete questions from our database after reading them
     */
    //function updateSMILE(smileServer){
        // this part moved to updateDatabase.php
        // now update 0.html and 0_result.html files
        /*var request = getHTTPObject();
        if (request) {
            request.onreadystatechange = function () {
                
            };
            request.open("POST", "updateQ.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(null);
        }*/
    //}
    //function pushQuestionsToAssess(){
        // work moved to insertQ.php instead of fetchQ.php
        /*var request = getHttpObject();
        if (request) {
            request.onreadystatechange = function () {
            };
            request.open("POST", "fetchQ.php", true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send();
        }*/
    //}    
</script>
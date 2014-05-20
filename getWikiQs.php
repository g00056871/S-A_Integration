<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
require_once 'config.php';
//*Limit is 10 questions
/*<div id="Topics"></div>*/
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assessment Gateway</title>
        <!--Use either an offline copy of JQuery at the local server or load an online copy from Google-->
        <!--<script type="text/javascript" src="jquery.js"></script>-->
        <script type="text/javascript">
            document.write("\<script src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js' type='text/javascript'>\<\/script>");
        </script>
        <script type="text/javascript">
	$(document).ready(function(){
            //$('<option>John Smith</option>').appendTo('#topics');
            $.getJSON('getTopics.php', function(data){
                var html = '';
                var len = data.length;
                for (var i = 0; i< len; i++) {
                    html += '<option value="' + data[i].topic + '">' + data[i].topic + '(' +data[i].noq + ')' + '</option>';
                }
                $('#topics').append(html);
            });
            //$("body").html("jQuery is working");
	});
        </script>
<!--        <script type="text/javascript">
            $.getJSON('getTopics.php', function(data){
                var html = '';
                var len = data.length;
                for (var i = 0; i< len; i++) {
                    html += '<option value="' + data[i].topic + '">' + data[i].topic + '(' +data[i].noq + ')' + '</option>';
                }
                $('select.topics').append(html);
            });
        </script>-->
    </head>
    <body>
        <div><b>Specify the topic and the number of questions you want to fetch:</b></div>
        Topic: <select id="topics">
            <option id="default" selected="selected">None</option>
        </select>*This list shows the topics and the number of questions available for each topic<br>
        Number of Questions: <input type="text" id="qn">*Limit is 500 questions<br>
        <input type="button" value="Submit" onclick="do_stuff()">
    </body>
</html>
<script type="text/javascript">
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
    function do_stuff()
    {
        var e = document.getElementById("topics");
        var topic = e.options[e.selectedIndex].value;
        var qn = document.getElementById('qn').value;
        get_categories(topic,qn);
        alert("Your Request was Completed Successfully");
        //window.alert(topic);
        //window.alert(qn);
    }
    function get_categories(topic,qn)
    {
        var request= getHttpObject();
        if(request)
        {
            request.onreadystatechange = function()
            {
                getQuestions(request);
            };
            //Math%20G6
            var url = "<?php echo $wikiServer.$wikiPath; ?>" + "/api.php?action=query&list=categorymembers&cmtitle=Category:" + topic + "&cmsort=timestamp&cmdir=asc&cmlimit="+qn;
            //alert(url);
            request.open('GET', url, true);
            request.send(null);
        }
    }
    function getQuestions(request) {
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
                if (xmlDoc) {
                    var responseXML = parseXml(xmlDoc.documentElement.getElementsByTagName("pre")[0].textContent.trim());
                    var categoriesXML = responseXML.documentElement.getElementsByTagName("cm");
                    var questionTitles = [];
                    var questionIDs = [];
                    for (var i=0;i<categoriesXML.length;i++)
                    { 
                        questionTitles.push(categoriesXML[i].getAttribute("title").trim());
                        questionIDs.push(categoriesXML[i].getAttribute("pageid").trim());
                    }
                    for (var i=0;i<questionIDs.length;i++)
                    {
                        //alert(questionTitles[i]+" "+questionIDs[i]);
                        var requestQD = getHttpObject();
                        if(requestQD)
                        {
                            requestQD.onreadystatechange = function()
                            {
                                getQuestionDetails(requestQD);
                            };
                            var url = "<?php echo $wikiServer.$wikiPath; ?>" + "/api.php?action=query&prop=revisions&pageids=" + questionIDs[i].toString() + "&rvprop=timestamp|user|comment|content";
                            //alert(url);
                            requestQD.open('GET', url, false);
                            requestQD.send(null);
                        }
                    }
                }   
            }
            else {
                alert('There was a problem with the request');
            }
        }
    }
    function getQuestionDetails(request) {
        if (request.readyState === 4) {
            if (request.status === 200) {
                //alert("Entered");
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
                    var responseXML = parseXml(xmlDoc.documentElement.getElementsByTagName("pre")[0].textContent.trim());
                    pageID = responseXML.documentElement.getElementsByTagName("page")[0].getAttribute("pageid").trim();
                    pageTitle = responseXML.documentElement.getElementsByTagName("page")[0].getAttribute("title").trim();
                    
                    
                    var questionXML = parseXml(responseXML.documentElement.getElementsByTagName("rev")[0].textContent.trim());
                    correctResponseID = questionXML.documentElement.getElementsByTagName("correctResponse")[0].getElementsByTagName("value")[0].textContent.trim();
                    questionText = questionXML.documentElement.getElementsByTagName("itemBody")[0].getElementsByTagName("prompt")[0].textContent.trim();
                    var choicesD = questionXML.documentElement.getElementsByTagName("itemBody")[0].getElementsByTagName("simpleChoice");
                    for (var i=0;i<choicesD.length;i++)
                    { 
                        choices.push(choicesD[i].textContent.trim());
                        choicesIDs.push(choicesD[i].getAttribute("identifier").trim());
                    }
                }
                if(pageID)
                {
                }
                if(pageTitle)
                {
                }
                if(correctResponseID)
                {
                }
                if(questionText)
                {
                }
                if(choices && choicesIDs)
                {
                }
                

                
                fetchWikiQuestionsAndPushToSMILE(questionText,choices,correctResponseID,pageID,"<?php echo $smileServer ?>");
                
//                var updateParams = "q=" + questionText;
//                var correctAnswer = 0;
//                for (var i=0;i<choices.length;i++)
//                { 
//                    updateParams += "&op" + (i + 1) + "=" + choices[i];
//                    if (correctResponseID === choicesIDs[i])
//                    {
//                        correctAnswer = i+1;
//                    }
//                }
//
//                updateParams += "&cor=" + correctAnswer + "&pid=" + pageID;
//                alert(updateParams);
            }
            else 
            {
                alert('There was a problem with the request');
            }
        }
    }
    
    /*
    * this function will add user to SMILE (wiki user)
    * then this wiki user will insert multiple questions to SMILE
    * questions will be fetched from Assessment Wiki depending on topics specified from the teacher
     */ 
    function fetchWikiQuestionsAndPushToSMILE(questionText,choices,correctResponseID,pageID,smileServer) {
        // add assess wiki user to SMILE with specific name and IP
        var WikiUserName = "Wiki";
        var WikiIP = "192.168.1.7";
        var SMILEpushurl = smileServer + '/SMILE/pushmsg.php';
        var JSONUser = {
            "TYPE": "HAIL",
            "IP": WikiIP,
            "NAME": WikiUserName
        };
        var correctAns = 1;
        if(correctResponseID=='option_0'){
            correctAns = 1;
        }
        else if(correctResponseID=='option_1'){
            correctAns = 2;
        }
        else if(correctResponseID=='option_2'){
            correctAns = 3;
        }
        if(correctResponseID=='option_3'){
            correctAns = 4;
        }

        var request = getHttpObject();
        if (request) {
            request.onreadystatechange = function() {
                if (request.readyState == 4) {
                if (request.status == 200) {
                    // push assess questions to SMILE
                        var jsonQuestion = {
                            "TYPE": "QUESTION",
                            "NAME": "Wiki",
                            "IP": "192.168.1.7",
                            "Q": questionText,
                            "O1": choices[0],
                            "O2": choices[1],
                            "O3": choices[2],
                            "O4": choices[3],
                            "A": correctAns
                        };
                        var request2 = getHttpObject();
                        if (request2) {
                            request2.onreadystatechange = function() {};
                                request2.open("POST", SMILEpushurl, true);
                                request2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                request2.send("MSG=" + JSON.stringify(jsonQuestion));
                        
                        }
                    }
                }
            };

            request.open("POST", SMILEpushurl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send("MSG=" + JSON.stringify(JSONUser));
        }
    }
</script>
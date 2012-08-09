/*
to use:
initialise connection with load() reccommended putting in <body onload="load()" ....
twitTone function:
 ('twitter feed (string OR array)', 'php twitTone function(string OR array)', 'function to return the response', 'array of key:value to be sent to php')
ignore function wait() unless debugging

*/
function load() {//load new http object on body onload
if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}
else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}}

function twitTone(hash, func, callback, dict) {//send request to php
resp=function() {wait(this, callback);}
xmlhttp.onreadystatechange=function(){if (xmlhttp.readyState==4 && xmlhttp.status==200){
resp.call(xmlhttp.responseText);
}}
var extra="";if (dict){extra="&vars="+dict;}
xmlhttp.open("GET","atom/response.php?twit="+hash+"&func="+func+extra,true);
xmlhttp.send();
}

function wait(data, callback){//wait for response and push to array
var img=document.getElementById("loading").style;
if (data==""){img.display="block";setTimeout(function(){wait(data, callback);}, 10)}//if no response
else {//once a response is recieved
var arr=new Array();
img.display="none";
var newval=data.split(",");
data="";
for (var i=1;i<=newval.length-1;i++) {
arr.push(newval[i-1]);
}callback.call(arr)}}//close function

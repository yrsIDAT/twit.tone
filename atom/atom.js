/*
to use:
initialise connection with load() reccommended putting in <body onload="load()" ....
twitTone function:
 ('twitter feed (string OR array)', 'php twitTone function(string OR array)', 'function to return the response', 'array of key:value to be sent to php')
ignore function recieve() unless debugging

*/
function load() {//load new http object on body onload
if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}
else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}}

function twitTone(hash, func, callback, dict) {//send request to php
var img=document.getElementById("loading").style;
document.getElementById('hz').style.display="none";
img.display="block";img.textAlign="center";
resp=function() {recieve(this, callback);}
xmlhttp.onreadystatechange=function(){if (xmlhttp.readyState==4 && xmlhttp.status==200){
resp.call(xmlhttp.responseText);
}}
var extra="";if (dict){extra="&vars="+dict;}
xmlhttp.open("GET","atom/response.php?twit="+hash+"&func="+func+extra,true);
xmlhttp.send();
}

function recieve(data, callback){//recieve response and push to array
var arr=new Array();
document.getElementById("loading").style.display="none";
document.getElementById('hz').style.display="block";
var newval=data.split(",");
data="";
for (var i=1;i<=newval.length-1;i++) {
arr.push(newval[i-1]);
}callback.call(arr)}//close function

<?php
include "twittone.php";
foreach (explode(",", $_GET["twit"]) as $twit) {//support for multiple feeds
 $tt=new twitTone($twit); //create new instance
 if(isset($_GET["vars"])) {                          // \
 foreach (explode(",", $_GET["vars"]) as $var) {     //  |-> accept dictionary pairs to be sent  to the class, eg debug:1,twitter_hash:Example2012
  $dict=explode(":", $var);$tt->$dict[0]=$dict[1];}} // /

 foreach (explode(",", $_GET["func"]) as $function) {//loop over GET 'func'
  echo $tt->$function().",";}} //do each function
?>

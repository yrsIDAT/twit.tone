<?php
include "twittone.php";
foreach (explode(",", $_GET["twit"]) as $twit) {//support for multiple feeds
 $tt=new twitTone($twit); //create new instance
 foreach (explode(",", $_GET["func"]) as $function) {//loop over GET 'func'
  echo $tt->$function().",";}} //do each function
?>

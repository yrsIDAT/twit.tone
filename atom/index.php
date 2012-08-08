<html>
	<head>
		<style>
			.tweet-container {
				background:#eee;
				border:1px inset rgba(116,116,116,0.36);
				margin:0 0 20px;
				padding:10px;
				width:650px;
			}
			.tweet {
				font-family: sans-serif;
			}
		</style>
		<meta http-equiv="refresh" content="5" />
	</head>
	<body>
<?PHP
 date_default_timezone_set('UTC');
  include "class.myatomparser.php";

  $twitter_hash=$_GET["twit"];
  $amount=$_GET["amt"];
  $atom_parser = new myAtomParser("http://search.twitter.com/search.atom?q=$twitter_hash&rpp=100");
  $raw = $atom_parser->getRawOutput();
  $count=0;
 foreach ($raw["FEED"]["ENTRY"] as $entry) {
    $timestamp=strtotime($entry["PUBLISHED"]);
    if ($timestamp <= strtotime('-5 minutes')) {
    }
    else{;
    $count=$count+1;}
    }
$url = "http://search.twitter.com/search.atom?q=$twitter_hash&rpp=$count";
$atom = new myAtomParser($url);
  $output = $atom->getOutput();
  if ($count>0){
  echo $output.PHP_EOL.PHP_EOL.PHP_EOL;
  //print_r($atom->getRawOutput());
  }else{
  echo "no feeds in the last 5 minutes";}
?>
	</body>
</html>
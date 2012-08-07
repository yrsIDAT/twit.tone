<?PHP
  include "class.myatomparser.php";

  # where is the feed located?
  $url = "http://search.twitter.com/search.atom?q=yrs2012&rpp=100";

  # create object to hold data and display output
  $atom_parser = new myAtomParser($url);

  $output = $atom_parser->getOutput();	# returns string containing HTML
  echo $output;
?>
<?PHP
class twitTone {
  function __construct($hash) {
  if ($this->atom){$this->atom=null;}
  include_once "class.myatomparser.php";
  date_default_timezone_set('UTC');
  $this->twitter_hash=$hash;
  $this->debug=0;
  $this->time_limit="-5 minutes";
  }
  private function getFeeds($time) {
  $t_hash=$this->twitter_hash;
  $data=new myAtomParser("http://search.twitter.com/search.atom?q=$t_hash&rpp=100");
  $raw=$data->getRawOutput();
  $count=0;
  if ($this->debug>1){print_r($raw);}
  foreach ($raw["FEED"]["ENTRY"] as $entry){$ts=strtotime($entry["PUBLISHED"]);if($ts<=strtotime($time)){}else{$count=$count+1;}}
  if ($this->debug>0){echo $count."<br>";}
  return $count;
  }
  
  public function returnTweets() {
  $count=$this->getFeeds($this->time_limit);
  $url="http://search.twitter.com/search.atom?q=".$this->twitter_hash."&rpp=".$count;
  if ($this->debug>0){echo $url."<br>";}
  $this->atom=new myAtomParser($url);
  $output = $this->atom->getOutput();
  if ($count>0){return $output;}else{return "no feeds in the past ".substr($this->time_limit, 1);}
  }
  
  public function countChars() {
  if (!$this->atom){$this->returnTweets();}
  $feeds=$this->atom->getRawOutput();
  foreach ($feeds["FEED"]["ENTRY"] as $entry) {$all=$all.$entry["CONTENT"];}
  return strlen($all);
  }
}
?>
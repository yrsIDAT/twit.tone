<?php
/*
*      Copyright (C) 2009 - 2010 Erik Borra
*
*      Simple Twitter scraper
*
*      @author: Erik Borra - mail [didelidoo] erikborra.net
*
*      This program is free software; you can redistribute it and/or modify
*      it under the terms of the GNU General Public License as published by
*      the Free Software Foundation; either version 2 of the License, or (at
*      your option) any later version.
*
*      This program is distributed in the hope that it will be useful, but
*      WITHOUT ANY WARRANTY; without even the implied warranty of
*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
*      General Public License for more details.
*
*      You should have received a copy of the GNU General Public License
*      along with this program; if not, write to the Free Software Foundation,
*      Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

/*
* This script scrapes twitter results for a searchterm, up till 2 weeks ago
*
* call from commandline like this:
* php twitterScraper.php query OR php twitterScraper.php '#hashtag'
*
* create a postgres database called rsstweets with the following fields:
* id bigint primary key, tweetid bigint, lang varchar(255), name varchar(255), tweetname text, avatar text, msg text
*/

// (or create a mysql database but change all postgres calls to mysql)
pg_connect('host=HOST dbname=DBNAME user=USER password=PASSWORD'); // connect to PostGresQL

date_default_timezone_set('Europe/Berlin');

$search = $argv[1];	// the search query
$donext = true; // tells whether we need to continue scraping or not
$counter = $maxid = 0;	// keeps track of counts and the id we need to scrape from

// main loop
while($donext) {
	getTweets($search,$maxid,$donext,$tmp,$counter);
}

// builds url and scrapes the tweets
function getTweets($search,&$maxid,&$donext,&$out,&$counter) {

		// build the url
		$url = "http://search.twitter.com/search.atom?q=".urlencode($search).($maxid!=0?"&max_id=$maxid":"")."&rpp=100";
		// print some info to keep track of where we are
		print $url." maxid $maxid, donext $donext, counter $counter\n";

		// fetch through curl
		$ch = curl_init($url);
		curl_setopt($ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0"); // mask as firefox 3
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);  //disable ssl certificate validation
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch,CURLOPT_FAILONERROR,1);
		curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);	// allow redirects
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);	// return into a variable
		$page = curl_exec($ch);

		if(!$page) { // try again
			print "couldn't fetch page $url: ".$snoopy->error;
			getTweets($search,$maxid,$donext,$out,$counter);
		}

		// parse
		$dom = new DOMDocument;
		@$dom->loadHTML($page);
		$xpath = new DOMXPath($dom);
		$lis = $xpath->query("//entry");
		if($lis->length > 0) {
			print $lis->length."\n";
			foreach($lis as $li) {
				$links = $li->getElementsByTagName('link');
				$tweeturl = $links->item(0)->getAttribute('href');
				$id = preg_replace("/.*statuses\//","",$tweeturl);
				$avatar = $links->item(1)->getAttribute('href');
				$msg = html_entity_decode($li->getElementsByTagName('content')->item(0)->nodeValue);
				$from = preg_replace("/\">.*/","",preg_replace("/.*href=\"/","",$li->getElementsByTagName('source')->item(0)->nodeValue));
				$lang = $li->getElementsByTagName('lang')->item(0)->nodeValue;
				$time = $li->getElementsByTagName('published')->item(0)->nodeValue;
				$unixtime = strtotime($time);
				$name = $li->getElementsByTagName('name')->item(0)->nodeValue;
				if(preg_match("/(.+)\s\((.+)\)/",$name,$match)) {
					$tweetname = $match[1];
					$name = $match[2];
				} else $tweetname = $name;

				$tmp  = array();
				$tmp[$id]['lang'] = $lang;
				$tmp[$id]['hashtag'] = $search;
				$tmp[$id]['name'] = $name;
				$tmp[$id]['tweetname'] = $tweetname;
				$tmp[$id]['avatar'] = $avatar;
				$tmp[$id]['msg'] = trim($msg);
				$tmp[$id]['tweettime'] = $time;
				$tmp[$id]['fromplatform'] = $from;
				$tmp[$id]['timestamp'] = $unixtime;

				$donext = writeResults($tmp);
				if(!$donext) print "writing of results failed\n";
				$maxid = $id; // keep track of where to start scraping next
				$counter++;
			}
		} else { // if nothing found, either there is an error, or the scraping has finished
			$donext = false;
			print "finished\n $url, maxid $maxid, donext $donext, counter $counter\n";
		}
                sleep(1); // give Twitter a break
}

// write results to db
function writeResults($out) {
	$ins = "INSERT INTO rsstweets ";
	$select = "SELECT id FROM rsstweets WHERE ";
	foreach($out as $tweetid => $tmp) {
		$ins .= "(".implode(",",array_keys($tmp)).", tweetid) VALUES (";
		$select .= "tweetid = $tweetid AND tweettime = '".mysql_escape_string($tmp['tweettime'])."' AND tweetname = '".mysql_escape_string($tmp['tweetname'])."'";
		foreach($tmp as $k => $v) {
			$v = mysql_escape_string(trim($v));
			$tmp[$k] = $v;
		}
		$ins .= "'".implode("','",$tmp)."', $tweetid)";
	}
	$rec = pg_query($select);
	if($rec && pg_num_rows($rec) == 0) {
		pg_query($ins);
		return true;
	} else {
		return false;
	}
}
?>
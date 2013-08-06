<?php
$memcache = new Memcache;
$memcache->connect('localhost', 11211);
class Spotify {
	
	static function track($q){
		global $memcache;
		if( ($re = $memcache->get(array($q)) ) !== false ){
			return $re;
		}
		
		$resp=file_get_contents("http://ws.spotify.com/search/1/track.json?q=$q");
		if($resp==null){
			return array();
		}
		$obj=json_decode($resp);
		if( !isset($obj->tracks) || empty($obj->tracks) ){
			return array();
		}
		$memcache->set($q, $obj->tracks, 0, 3600);
		return $obj->tracks;
	}
	
	
}
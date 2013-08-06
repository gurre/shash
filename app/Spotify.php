<?php
$memcache_obj = new Memcache;
$memcache_obj->connect('localhost', 11211);
class Spotify {
	
	static function track($q){
		global $memcache_obj;
		if( ($re = $memcache_obj->get( array($q) ) !== false ){
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
		$memcache_obj->set($q, $obj->tracks, 0, 3600);
		return $obj->tracks;
	}
	
	
}
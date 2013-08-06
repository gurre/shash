<?php

class Spotify {
	
	static function track($q){
		$m = new Memcached();
		$m->addServer('localhost', 11211);
		
		if( ($re = $m->get($q) ) !== false ){
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
		$m->set($q, $obj->tracks, 3600);
		return $obj->tracks;
	}
	
	
}
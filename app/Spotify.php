<?php

class Spotify {
	
	static function track($q){
		
		$resp=file_get_contents("http://ws.spotify.com/search/1/track.json?q=$q");
		if($resp==null){
			return array();
		}
		$obj=json_decode($resp);
		if( !isset($obj->tracks) || empty($obj->tracks) ){
			return array();
		}
		return $obj->tracks;
	}
	
	
}
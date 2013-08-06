<?php
/*
Your API Key: BBMRBVEOASYLHY60X 
Your Consumer Key: 331db9732552f9b3a55bb6b41fd63552 
Your Shared Secret: KbC5Y0s3SK2C87YtNGA4Bg
*/
require 'Redis.php';


class Shash {

	static function spotifyIdForTag($spotify_id){
		return Redis::db()->hget($spotify_id);
	}
	
	static function tagsForText($text){
		require '../app/php-echonest-api/lib/EchoNest/Client.php';
		
		$echonest = new EchoNest_Client();
		$echonest->authenticate('BBMRBVEOASYLHY60X');
		$songApi = $echonest->getSongApi();
		$songs=$songApi->search( array('title'=>$text) );
		print_r($songs);
		if( !empty($songs) ){
			return array();
		}
		
		foreach( $songs as $song ){
			
			
		}
		
		
	}

}
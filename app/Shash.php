<?php
/*
Your API Key: BBMRBVEOASYLHY60X 
Your Consumer Key: 331db9732552f9b3a55bb6b41fd63552 
Your Shared Secret: KbC5Y0s3SK2C87YtNGA4Bg
*/

/*
spotify_id -> tags 

spotify:track:12 : {
	123123 : (int)123123
}

ids : {
	(int)123123 : tag
}

tags : {
	tag : (int)123123
}
*/


require 'Redis.php';


class Shash {

	static function spotifyIdForTag($spotify_id){
		return Redis::db()->hget($spotify_id);
	}
	
	static function tagsForText($text){
		/*require '/usr/local/nowplaying/app/php-echonest-api/lib/EchoNest/Autoloader.php';
		EchoNest_Autoloader::register();
		
		$echonest = new EchoNest_Client();
		$echonest->authenticate('BBMRBVEOASYLHY60X');
		$songApi = $echonest->getSongApi();
		$songs=$songApi->search( array('title'=>$text, 'results'=>20, 'bucket'=>'id:spotify-WW') );
		print_r($songs);
		if( !empty($songs) ){
			return array();
		}*/
		
		$tracks=Spotify::track($text);
		
		foreach( $tracks as $song ){
			list(, $type, $id)=explode(':', $song->href);
			Redis::db()->hSetNx( "$type:".substr($id,0,2), substr($id,2), Redis::db()->incr('id') );
		}
		
		return $tracks;
	}

}
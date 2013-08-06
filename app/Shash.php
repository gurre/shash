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
		
		$re=array();
		
		$tracks=Spotify::track($text);
		foreach( $tracks as &$song ){
			$t=array();
			list(, $type, $id)=explode(':', $song->href);
			Redis::db()->hSetNx( "$type:".substr($id,0,2), substr($id,2), Redis::db()->incr('id') );
			$artist=array();
			foreach( $song->artists as &$a ){
				list(, $type, $id)=explode(':', $a->href);
				$int_id=Redis::db()->incr('id');
				Redis::db()->hSetNx( "$type:".substr($a->href,0,2), substr($a->href,2), $int_id);
				
				$artist_tag=self::normalizeTag(array($a->name));
				
				Redis::db()->hSetNx( "tags".($int_id%12), $artist_tag, $int_id);
				Redis::db()->hSetNx( "tags".($int_id%12), $int_id, $artist_tag);
				
				$artist[]=$a->name;
				
			}
			print_r($song);
			$t->artists = $song->artists;
			$t->artist = implode(', ',$artist);
			$t->name = $song->name;
			$t->shash = self::normalizeTag( array( $song->artists, $t->name ) );
			if( !empty($t) )
				$re[]=$t;
		}
		
		
		
		return $re;
	}
	
	static function normalizeTag(array $parts){
		foreach($parts as &$p){
			$part = strtolower($p);
			$part = preg_replace('/&.+?;/', '', $part); // kill entities
			$part = preg_replace('/[^a-z0-9 _-]/', '', $part);
			$part = preg_replace('/\s+/', '', $part);
			$part = preg_replace('|-+|', '', $part);
			$part = trim($part, '-');
			$p=ucfirst($part);
		}
		return implode('', $parts);
	}
	

}
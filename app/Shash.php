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
			$t=new stdClass;
			list(, $type, $id)=explode(':', $song->href);
			Redis::db()->hSetNx( "$type:".substr($id,0,2), substr($id,2), Redis::db()->incr('id') );
			$artist=array();
			foreach( $song->artists as &$a ){
				list(, $type, $id)=explode(':', $a->href);
				$int_id=Redis::db()->incr('id');
				Redis::db()->hSetNx( "$type:".substr($id,0,2), substr($id,2), $int_id);
				
				$artist_tag=self::normalizeTag(array($a->name));
				
				Redis::db()->hSetNx( "tags".($int_id%12), $artist_tag, $int_id);
				Redis::db()->hSetNx( "tags".($int_id%12), $int_id, $artist_tag);
				
				$artist[]=$a->name;
				
			}
			$t->artists = $song->artists;
			$t->artist = implode( ', ', $artist );
			$t->name = ($song->name);
			$t->href = $song->href;
			$t->shash = self::normalizeTag( $artist, $t->name );
			if( !empty($t) )
				$re[]=$t;
		}
		
		
		
		return $re;
	}
	
	static function normalizeTag(array $artists, $track=null, $album=null){
		$re=array();
		
		if(!empty($artists)){
			$re[0]=array();
			foreach($artists as $p){
				$re[0][]=slug($p);
			}
			$re[0]=implode(', ',$re[0]);
		}
		if($track!=null)
			$re[1]=slug($track);
		if($album!=null)
			$re[2]=slug($album);
		return implode('-',$re);
	}
	

}
setlocale(LC_ALL, 'en_US.UTF8');
function slug($str, $replace=array(), $delimiter='') {
	if( !empty($replace) ) {
		$str = str_replace((array)$replace, ' ', $str);
	}
	$clean = ucwords($clean);
	$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

	return $clean;
}
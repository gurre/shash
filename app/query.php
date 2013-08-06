<?php

require 'Shash.php';

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


if( isset($_GET['text']) && ($query=addslashes($_GET['text']))!==false ){
	
	Redis::db()->hExists();
	
}

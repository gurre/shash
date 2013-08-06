<?php





if( isset($_GET['text']) && ($text=addslashes($_GET['text']))!==false ){
	
	$obj=Shash::tagsForText($text);
	echo json_encode($obj);
	
}

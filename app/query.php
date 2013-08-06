<?php





if( isset($_GET['text']) && ($text=addslashes($_GET['text']))!==false ){
	
	$obj=Shash::tagsForText($text);
	if( ($callback=Sanitize::match_implode(REGEX_ALPHANUMERICSIGNS,isIdxSetNotNull($_GET,"callback"))) ){
		header('Content-Type: application/javascript');
		echo $callback.'('.json_encode($obj).')';
	}else{
		header('Content-Type: application/json');
		echo json_encode($obj);
	}
	
	
}

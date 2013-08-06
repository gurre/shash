<?php

spl_autoload_register(function ($class) {
	if(file_exists('/usr/local/nowplaying/app/'.$class.'.php'))
		require '/usr/local/nowplaying/app/'.$class.'.php';
});

class FrontControllerRouter {
	
	protected	$routes = array(),
				$allowed_host=null;
				
	public function __construct($host=null){
		$this->allowed_host=$host;
	}
	
	public function addRoute($method, $url, $callback){
		$this->routes[] = array(0 => $method, 1 => $url, 2 => $callback);
	}
	
	public function addNotFound($callback){
		$this->notFound=$callback;
	}
	
	public function doRouting(){
		if( !isset($_SERVER['HTTP_HOST']) || $_SERVER['HTTP_HOST'] != $this->allowed_host ){
			header('Status: 301 Moved Permanently');
			header('Location: https://'. ($this->allowed_host) . isIdxSetNotNull($_SERVER,'REQUEST_URI') );
			return false;
		}
		$reqUrl = strtok($_SERVER["REQUEST_URI"],'?');
		$reqMet = $_SERVER['REQUEST_METHOD'];
		foreach($this->routes as  $route) {
			if($reqMet == $route[0] || strpos($route[0],$reqMet) !== false ){
				// convert urls like '/users/:uid/posts/:pid' to regular expression
				$pattern = "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($route[1])) . "$@D";
				$matches = Array();
				// check if the current request matches the expression
				if(preg_match($pattern, $reqUrl, $matches)) {
					// remove the first match
					array_shift($matches);
					// call the callback with the matched positions as params
					return call_user_func_array($route[2], $matches);
				}
			}
		}
		return call_user_func($this->notFound);
	}

}



$router=new FrontControllerRouter('54.217.222.192');
$router->addRoute('GET','/v0/tags',function(){
	require '../app/query.php';
});
$router->doRouting();

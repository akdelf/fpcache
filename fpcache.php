<?
	
	if (!defined(FPTIME))
		define('FPTIME', 1800); //по умалчанию 30 минут


	/* получаем URI */
	$uri = trim($_SERVER['REQUEST_URI']); 
	$uri = trim($uri,'/');
		
	/* обработка GET */
	if ($_SERVER['QUERY_STRING'] !== ''){ 
		$get_cache_line = str_replace('=','_', $_SERVER['QUERY_STRING']);
		$pos_query = mb_strpos($uri, '?');
		$uri = mb_substr($uri, 0, $pos_query);
	}
	else
		$get_cache_line = '';

	if (URI !== '') 
		$fcache = CPATH.URI;
	else 	
		$fcache =  CPATH.'index';  //определяем файл кеша гл страницы
		
	if 	($get_cache_line !== '')
		$fcache .= $get_cache_line;
	
	define('FCACHE', $fcache.'.html');	


	if (sizeof($_POST) == 0) { //если пришли данные из формы кэш не нужен
		if (LINKCACHE > 0) {
			if (file_exists(FCACHE) and ((filemtime(FCACHE) + LINKCACHE) > $_SERVER['REQUEST_TIME'])){
				echo file_get_contents(FCACHE);
				exit;
			}	
		}
	}


	header("X-Accel-Expires: ".FPTIME); //nginx
	

	//save page
	function fp_save(){

	}

	

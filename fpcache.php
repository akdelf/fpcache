<?php

	/**
	* FULL PAGE PHP CASHED IN FILES
	*config default
	*/
	if (!defined('FPCTIME'))
		define('FPCTIME', 1800); //по умoлчанию 30 минут

	if (!defined('FPCDIR'))
		define('FPCDIR', 'cache/html/');


	/*
	* time 
	* cache - mode cache only
	*/
	if (!defined('FPCMODE'))
		define('FPCMODE', 'time');


	/* получаем URI */
	if (isset($_SERVER['REQUEST_URI'])) {
		$fpc_uri = trim($_SERVER['REQUEST_URI']); 
		$fpc_uri = trim($fpc_uri,'/');
	}
	else
		$fpc_uri = '';	

	/* обработка GET */
	if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== ''){ 
		$fpc_get_line = str_replace('=','_', $_SERVER['QUERY_STRING']);
		$fpc_query = mb_strpos($fpc_uri, '?');
		$fpc_uri = mb_substr($fpc_uri, 0, $fpc_query);
	}
	else
		$fpc_get_line = '';

	if ($fpc_uri !== '') 
		$fpcache = FPCDIR.$fpc_uri;
	else 	
		$fpcache =  FPCDIR.'index';  //определяем файл кеша гл страницы

	if 	($fpc_get_line !== '')
		$fpcache .= $fpc_get_line;

	define('FPCFILE', $fpcache.'.html'); //текущий файл кеширования	

	if (sizeof($_POST) == 0) { //если пришли данные из формы кэш не нужен
		
		if (FPCTIME > 0) {
									
			if (file_exists(FPCFILE)) {
				
				
				if (FPCMODE == 'cache') {
					echo file_get_contents(FPCFILE);
					exit;
				}
			
				$fp_endtime = filemtime(FPCFILE) + FPCTIME;

				if ($fp_endtime > $_SERVER['REQUEST_TIME']){
					$fp_rest = $fp_endtime - $_SERVER['REQUEST_TIME'];
					fpc_headers($fp_rest);
					echo file_get_contents(FPCFILE); //выводим файл кеша и обрываем выполнение скрипта
					exit;
				}	
		
			}
		}

	}


	/*
	* FPCACHE saved function
	*/

	function fpc_headers($time){
		header("X-Accel-Expires: ".$time); //nginx
	} 


	function fpc_save($content = '', $key = ''){
		
		$dir = dirname(FPCFILE); 
		
		if (!is_dir($dir)){
			if (!mkdir($dir, 0777, True))
				return False;
		}		
				
		return file_put_contents(FPCFILE, $content);

	
	}


	//savepage in html
	function fpc_save_include($include, $print = True) {
			
		ob_start();
			include($include);
			$content = trim(ob_get_contents());
		ob_end_clean();	
		
		if ($print)
			echo $content;
		
		return fpc_save($content, $print); //saved cache;

	}
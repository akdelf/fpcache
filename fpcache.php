<?php

	/**
	* FULL PAGE PHP CASHED IN FILES
	*config default
	*/
	if (!defined('FPCTIME'))
		define('FPCTIME', 1800); //по умoлчанию 30 минут

	if (!defined('FPCDIR'))
		define('FPCDIR', $_SERVER['DOCUMENT_ROOT'].'/cache/');

	
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
		$fpcache = FPCDIR.'html/'.$fpc_uri;
	else 	
		$fpcache =  FPCDIR.'html/'.'index';  //определяем файл кеша гл страницы

	if 	($fpc_get_line !== '')
		$fpcache .= $fpc_get_line;

	define('FPCFILE', $fpcache.'.html'); //текущий файл кеширования	

	if (sizeof($_POST) == 0 or FPCTIME == -1) { //если пришли данные из формы кэш не нужен
		
		if (FPCTIME !== 0) {
									
	

			header("Expires: ".gmdate("D, d M Y H:i:s", time()+FPCTIME)." GMT");
			header("Cache-Control: max-age="+FPCTIME);
			header("X-Accel-Expires: "+FPCTIME);

			
			if (file_exists(FPCFILE)) {
								
				if (FPCTIME == -1) {
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


	
	// create cache directory
	function fpc_dir($dir){
		
		if (!is_dir($dir)){
			if (!mkdir($dir, 0777, True))
				return False;
		}

		return True;	
	}


	function fpc_save($content = '', $key = ''){
		
		fpc_dir(dirname(FPCFILE));
		return file_put_contents(FPCFILE, $content);
	
	}



	// savepage in html
	function fpc_save_include($include, $print = True) {
			
		ob_start();
			include($include);
			$content = trim(ob_get_contents());
		ob_end_clean();	
		
		if ($print)
			echo $content;
		
		return fpc_save($content, $print); //saved cache;

	}


	function fpc_array($key, $value, $test = False) {

		
		// create directory
		$ex_dir = FPCDIR.'export/';
		fpc_dir($ex_dir);

		// cache file
		$fcache = FPCDIR.'export/'.md5($key).'.json';

		if (is_array($value)) {  
			return file_put_contents($fcache, json_encode($value));
		}	 
		
		elseif (is_int($value)) {
			
			if (file_exists($fcache) && filemtime($fcache)+$value > $_SERVER['REQUEST_TIME']){
				if ($test)
					echo "FPCACHE\CACHE:\n\n";
				return json_decode(file_get_contents($fcache), True);
			}

		}
		
		if ($test)
			echo "FPCACHE\ORIGINAL:\n\n";

		return null;

	}



	function fpc_file($key, $time = 3600) {
		
		$fcache = FPCDIR.'blocks/'.$key.'.html';
		
		if (filemtime($fpiece)+$time > $_SERVER['REQUEST_TIME'] && file_exists($fcache))
			return file_get_contents($fcache); 
		else {
			
			ob_start();
			include($file);
			$result = trim(ob_get_contents());
			ob_end_clean();
			
			file_put_contents($fpiece, $result);
			return $result;

		}
	
	}